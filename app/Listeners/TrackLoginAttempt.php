<?php

namespace App\Listeners;

use App\Models\BlockedIp;
use App\Models\LoginAttempt;
use App\Models\SecurityEvent;
use App\Services\Security\SecurityEventLogger;
use App\Services\Security\SecuritySettingsService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Listens to Laravel's built-in auth events and records login attempts.
 *
 * Hooks:
 *   - Illuminate\Auth\Events\Login   → successful login
 *   - Illuminate\Auth\Events\Failed  → failed login attempt
 *   - Illuminate\Auth\Events\Logout  → logout (informational)
 *
 * Note: Filament uses Livewire for the login form.
 * Auth events (Login/Failed) are still dispatched by Laravel's Auth system
 * regardless of whether Filament or a custom form triggered them.
 */
class TrackLoginAttempt
{
    public function __construct(
        private Request $request,
        private SecurityEventLogger $logger,
        private SecuritySettingsService $settings
    ) {}

    /**
     * Handle successful login.
     */
    public function handleLogin(Login $event): void
    {
        try {
            $ip = $this->request->ip() ?? '0.0.0.0';
            $email = $event->user->email ?? null;
            $ua = substr((string) $this->request->userAgent(), 0, 512);

            LoginAttempt::create([
                'email' => $email,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'successful' => true,
                'failure_reason' => null,
            ]);

            // Clear fail counters for this IP/email so they can log in again fresh
            Cache::forget("security:failed_ip:{$ip}");
            if ($email) {
                Cache::forget("security:failed_email:{$email}");
            }

            $this->logger->info(
                SecurityEvent::TYPE_SUCCESSFUL_LOGIN,
                "Đăng nhập thành công: {$email}",
                ['email' => $email],
                $this->request,
                $event->user->id ?? null
            );
        } catch (\Throwable $e) {
            Log::warning('TrackLoginAttempt::handleLogin failed: '.$e->getMessage());
        }
    }

    /**
     * Handle failed login.
     */
    public function handleFailed(Failed $event): void
    {
        try {
            $ip = $this->request->ip() ?? '0.0.0.0';
            $email = $event->credentials['email'] ?? null;
            $ua = substr((string) $this->request->userAgent(), 0, 512);

            // Determine failure reason safely (no password in context)
            $reason = 'Invalid credentials';

            LoginAttempt::create([
                'email' => $email,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'successful' => false,
                'failure_reason' => $reason,
            ]);

            // Increment cache counters
            $ipKey = "security:failed_ip:{$ip}";
            $emailKey = $email ? "security:failed_email:{$email}" : null;

            $lockoutMinutes = $this->settings->lockoutMinutes();
            $failCountIp = Cache::increment($ipKey);
            Cache::put($ipKey, $failCountIp, now()->addMinutes($lockoutMinutes));

            $failCountEmail = 0;
            if ($emailKey) {
                $failCountEmail = Cache::increment($emailKey);
                Cache::put($emailKey, $failCountEmail, now()->addMinutes($lockoutMinutes));
            }

            $maxPerIp = $this->settings->maxFailedPerIp();
            $maxPerEmail = $this->settings->maxFailedPerEmail();

            // Log single failed login event
            $this->logger->log(
                SecurityEvent::TYPE_FAILED_LOGIN,
                SecurityEvent::SEVERITY_LOW,
                'Đăng nhập thất bại: '.($email ?? 'unknown'),
                ['email' => $email, 'fail_count_ip' => $failCountIp],
                $this->request,
                null,
                false // Don't deduplicate individual login failures
            );

            // Check IP threshold
            if ($failCountIp >= $maxPerIp) {
                $this->handleThresholdExceeded($ip, $email, 'ip', $failCountIp);
            }

            // Check email threshold
            if ($emailKey && $failCountEmail >= $maxPerEmail) {
                $this->handleThresholdExceeded($ip, $email, 'email', $failCountEmail);
            }
        } catch (\Throwable $e) {
            Log::warning('TrackLoginAttempt::handleFailed failed: '.$e->getMessage());
        }
    }

    /**
     * Handle logout (informational only).
     */
    public function handleLogout(Logout $event): void
    {
        // Minimal log, no dedup
        try {
            $ip = $this->request->ip() ?? '0.0.0.0';
            $email = $event->user->email ?? null;

            $this->logger->info(
                'logout',
                "Đăng xuất: {$email}",
                ['email' => $email],
                $this->request,
                $event->user->id ?? null
            );
        } catch (\Throwable $e) {
            // Silent — logout tracking is informational only
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function handleThresholdExceeded(string $ip, ?string $email, string $thresholdType, int $count): void
    {
        $allowlisted = $this->settings->allowlistedIps();
        if (in_array($ip, $allowlisted, true)) {
            // Still log but never auto-block allowlisted IPs
            $this->logger->high(
                SecurityEvent::TYPE_BRUTE_FORCE,
                "Brute force từ IP được whitelist ({$thresholdType}): {$ip} — {$count} lần thất bại",
                ['email' => $email, 'threshold_type' => $thresholdType, 'count' => $count],
                $this->request
            );

            return;
        }

        // Log brute force security event
        $this->logger->high(
            SecurityEvent::TYPE_BRUTE_FORCE,
            "Ngưỡng đăng nhập thất bại vượt quá ({$thresholdType}): IP {$ip} — {$count} lần",
            ['email' => $email, 'threshold_type' => $thresholdType, 'count' => $count],
            $this->request,
            null,
            false // Don't deduplicate brute force alerts
        );

        // Only auto-block if admin has explicitly enabled it
        if ($this->settings->autoBlockEnabled()) {
            $lockoutMinutes = $this->settings->lockoutMinutes();
            $isPermanent = (bool) $this->settings->get('permanent_block_enabled', false);

            BlockedIp::blockIp(
                ip: $ip,
                reason: "Auto-blocked: {$count} failed logins ({$thresholdType} threshold)",
                minutes: $isPermanent ? null : $lockoutMinutes,
                permanent: $isPermanent,
                createdBy: null
            );

            $this->logger->high(
                SecurityEvent::TYPE_IP_BLOCKED,
                "IP tự động bị chặn: {$ip} ({$lockoutMinutes} phút)",
                ['email' => $email, 'duration_minutes' => $lockoutMinutes, 'permanent' => $isPermanent],
                $this->request,
                null,
                false
            );
        }
    }
}

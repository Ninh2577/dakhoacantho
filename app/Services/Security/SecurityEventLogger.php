<?php

namespace App\Services\Security;

use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Centralised logger for all security events.
 *
 * Features:
 * - Redacts sensitive fields from context before storing.
 * - Deduplicates identical events (same type+IP+URL) within a configurable window.
 * - Silently fails and writes to laravel.log if DB is unavailable.
 */
class SecurityEventLogger
{
    /**
     * Sensitive keys whose values are replaced with [REDACTED].
     */
    private array $redactFields;

    /**
     * Deduplication window in seconds (default 300 = 5 min).
     */
    private int $dedupSeconds;

    public function __construct()
    {
        $this->redactFields = config('security.redact_fields', []);
        $this->dedupSeconds = (int) config('security.event_dedup_seconds', 300);
    }

    /**
     * Log a security event.
     *
     * @param  string  $type  Event type (use SecurityEvent::TYPE_* constants)
     * @param  string  $severity  Event severity (use SecurityEvent::SEVERITY_* constants)
     * @param  string  $message  Human-readable description
     * @param  array  $context  Optional metadata (will be sanitized)
     * @param  Request|null  $request  Current request (extracts ip, ua, url, method)
     * @param  int|null  $userId  Authenticated user ID if known
     * @param  bool  $deduplicate  Whether to apply dedup window
     */
    public function log(
        string $type,
        string $severity,
        string $message,
        array $context = [],
        ?Request $request = null,
        ?int $userId = null,
        bool $deduplicate = true
    ): ?SecurityEvent {
        $ip = $request ? $this->extractIp($request) : null;
        $ua = $request ? substr((string) $request->userAgent(), 0, 512) : null;
        $url = $request ? substr($request->fullUrl(), 0, 1024) : null;
        $method = $request ? $request->method() : null;

        // Deduplication: don't create duplicate rows for spam/bots
        if ($deduplicate && $ip && $url) {
            $dedupKey = "security:dedup:{$type}:{$ip}:".md5($url);
            if (Cache::has($dedupKey)) {
                return null; // Already logged recently, skip
            }
            Cache::put($dedupKey, 1, $this->dedupSeconds);
        }

        // Sanitize context before storing
        $safeContext = $this->sanitizeContext($context);

        try {
            return SecurityEvent::create([
                'type' => $type,
                'severity' => $severity,
                'ip_address' => $ip,
                'user_id' => $userId,
                'user_agent' => $ua,
                'url' => $url,
                'method' => $method,
                'message' => $message,
                'context' => $safeContext ?: null,
            ]);
        } catch (\Throwable $e) {
            // Never let security logging crash the app
            Log::warning("SecurityEventLogger: failed to write event [{$type}]: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Shorthand for critical events.
     */
    public function critical(string $type, string $message, array $context = [], ?Request $request = null, ?int $userId = null): ?SecurityEvent
    {
        return $this->log($type, SecurityEvent::SEVERITY_CRITICAL, $message, $context, $request, $userId);
    }

    /**
     * Shorthand for high-severity events.
     */
    public function high(string $type, string $message, array $context = [], ?Request $request = null, ?int $userId = null): ?SecurityEvent
    {
        return $this->log($type, SecurityEvent::SEVERITY_HIGH, $message, $context, $request, $userId);
    }

    /**
     * Shorthand for medium-severity events.
     */
    public function medium(string $type, string $message, array $context = [], ?Request $request = null, ?int $userId = null): ?SecurityEvent
    {
        return $this->log($type, SecurityEvent::SEVERITY_MEDIUM, $message, $context, $request, $userId);
    }

    /**
     * Shorthand for info events.
     */
    public function info(string $type, string $message, array $context = [], ?Request $request = null, ?int $userId = null): ?SecurityEvent
    {
        return $this->log($type, SecurityEvent::SEVERITY_INFO, $message, $context, $request, $userId, false);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Sanitize a context array: redact sensitive keys, truncate long strings,
     * remove deeply nested structures to keep the JSON compact.
     */
    private function sanitizeContext(array $context): array
    {
        $safe = [];
        foreach ($context as $key => $value) {
            if ($this->isSensitiveKey((string) $key)) {
                $safe[$key] = '[REDACTED]';

                continue;
            }

            if (is_array($value)) {
                $safe[$key] = $this->sanitizeContext($value);
            } elseif (is_string($value)) {
                // Truncate very long strings (e.g. HTML body)
                $safe[$key] = mb_substr($value, 0, 500);
            } elseif (is_scalar($value) || $value === null) {
                $safe[$key] = $value;
            }
            // Skip objects/resources silently
        }

        return $safe;
    }

    private function isSensitiveKey(string $key): bool
    {
        $keyLower = strtolower($key);
        foreach ($this->redactFields as $field) {
            if (str_contains($keyLower, strtolower($field))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract real client IP, respecting trusted proxies.
     */
    private function extractIp(Request $request): string
    {
        return $request->ip() ?? '0.0.0.0';
    }
}

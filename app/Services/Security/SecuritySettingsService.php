<?php

namespace App\Services\Security;

use App\Models\SecuritySetting;

/**
 * Thin wrapper that resolves security settings from:
 * 1. DB cache (security_settings table via SecuritySetting::allCached())
 * 2. config/security.php fallback
 * 3. Hardcoded defaults
 *
 * Admin can override any setting from the UI; it's stored in DB and takes precedence.
 */
class SecuritySettingsService
{
    private array $cache = [];

    private bool $loaded = false;

    /**
     * Get a setting value by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->load();

        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        // Fallback to config/security.php
        $configValue = config("security.{$key}");
        if ($configValue !== null) {
            return $configValue;
        }

        return $default;
    }

    /**
     * Convenience: get firewall mode.
     */
    public function firewallMode(): string
    {
        return $this->get('firewall_mode', 'log_only');
    }

    /**
     * Convenience: check if firewall is in block mode.
     */
    public function isBlockMode(): bool
    {
        return $this->firewallMode() === 'block';
    }

    /**
     * Convenience: auto-block enabled?
     */
    public function autoBlockEnabled(): bool
    {
        return (bool) $this->get('auto_block_enabled', false);
    }

    /**
     * Convenience: max failed logins per IP.
     */
    public function maxFailedPerIp(): int
    {
        return (int) $this->get('max_failed_logins_per_ip', 10);
    }

    /**
     * Convenience: max failed logins per email.
     */
    public function maxFailedPerEmail(): int
    {
        return (int) $this->get('max_failed_logins_per_email', 5);
    }

    /**
     * Convenience: lockout duration in minutes.
     */
    public function lockoutMinutes(): int
    {
        return (int) $this->get('lockout_minutes', 15);
    }

    /**
     * Convenience: get allowlisted IPs.
     */
    public function allowlistedIps(): array
    {
        $val = $this->get('allowlisted_ips', []);
        if (is_string($val)) {
            return array_filter(array_map('trim', explode(',', $val)));
        }

        return (array) $val;
    }

    /**
     * Convenience: email alerts enabled?
     */
    public function emailAlertsEnabled(): bool
    {
        return (bool) $this->get('email_alerts_enabled', false);
    }

    /**
     * Reload from DB on next call (call after updating settings).
     */
    public function flush(): void
    {
        $this->loaded = false;
        $this->cache = [];
        SecuritySetting::flushCache();
    }

    private function load(): void
    {
        if ($this->loaded) {
            return;
        }

        // Safely try to load from DB; if DB is not available, use empty array
        try {
            $this->cache = SecuritySetting::allCached();
        } catch (\Throwable) {
            $this->cache = [];
        }

        $this->loaded = true;
    }
}

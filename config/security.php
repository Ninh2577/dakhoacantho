<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firewall
    |--------------------------------------------------------------------------
    */
    'firewall_mode' => env('SECURITY_FIREWALL_MODE', 'log_only'), // log_only | block

    /*
    |--------------------------------------------------------------------------
    | Login Brute-Force Protection
    |--------------------------------------------------------------------------
    */
    'max_failed_logins_per_ip' => (int) env('SECURITY_MAX_FAILED_IP', 10),
    'max_failed_logins_per_email' => (int) env('SECURITY_MAX_FAILED_EMAIL', 5),
    'lockout_minutes' => (int) env('SECURITY_LOCKOUT_MINUTES', 15),
    'auto_block_enabled' => (bool) env('SECURITY_AUTO_BLOCK', false),
    'permanent_block_enabled' => (bool) env('SECURITY_PERMANENT_BLOCK', false),

    /*
    |--------------------------------------------------------------------------
    | Traffic Logging
    |--------------------------------------------------------------------------
    */
    'traffic_logging' => env('SECURITY_TRAFFIC_LOGGING', 'suspicious_only'), // off | suspicious_only | all

    /*
    |--------------------------------------------------------------------------
    | File Scanner
    |--------------------------------------------------------------------------
    */
    'scan_uploads' => (bool) env('SECURITY_SCAN_UPLOADS', true),
    'scan_public' => (bool) env('SECURITY_SCAN_PUBLIC', true),
    'max_scan_file_size_mb' => (int) env('SECURITY_MAX_SCAN_FILE_MB', 2),

    /*
    |--------------------------------------------------------------------------
    | Email Alerts
    |--------------------------------------------------------------------------
    */
    'email_alerts_enabled' => (bool) env('SECURITY_EMAIL_ALERTS', false),
    'admin_security_email' => env('SECURITY_ALERT_EMAIL', ''),

    /*
    |--------------------------------------------------------------------------
    | Log Retention (days)
    |--------------------------------------------------------------------------
    */
    'retention_days_info' => (int) env('SECURITY_RETENTION_INFO', 30),
    'retention_days_low' => (int) env('SECURITY_RETENTION_LOW', 30),
    'retention_days_medium' => (int) env('SECURITY_RETENTION_MEDIUM', 60),
    'retention_days_high' => (int) env('SECURITY_RETENTION_HIGH', 180),
    'retention_days_critical' => (int) env('SECURITY_RETENTION_CRITICAL', 365),
    'retention_days_login' => (int) env('SECURITY_RETENTION_LOGIN', 60),

    /*
    |--------------------------------------------------------------------------
    | Sensitive fields to redact from logged context
    |--------------------------------------------------------------------------
    */
    'redact_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        '_token',
        'csrf',
        'authorization',
        'cookie',
        'session',
        'remember',
        'remember_token',
        'secret',
        'api_key',
        'access_token',
        'refresh_token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hard-blocked paths (always 403, regardless of firewall_mode)
    |--------------------------------------------------------------------------
    */
    'hard_blocked_paths' => [
        '.env',
        '.git',
        'vendor',
        'storage/logs',
        'wp-login.php',
        'wp-admin',
        'xmlrpc.php',
        'phpMyAdmin',
        'phpmyadmin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Whitelisted paths (firewall skips payload scanning for these)
    |--------------------------------------------------------------------------
    */
    'whitelisted_path_prefixes' => [
        'admin',
        'livewire',
    ],

    /*
    |--------------------------------------------------------------------------
    | Event deduplication window (seconds)
    | Same type+IP+URL within this window will not create duplicate rows.
    |--------------------------------------------------------------------------
    */
    'event_dedup_seconds' => (int) env('SECURITY_DEDUP_SECONDS', 300), // 5 min

];

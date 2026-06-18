<?php

return [
    // Application base URL (no trailing slash). Leave empty for auto-detection.
    'base_url' => '',

    // Subdirectory path if not at domain root (e.g. "go" for example.com/go).
    'base_path' => '',

    // Optional custom prefix for asset URLs (e.g. "public/assets" on some cPanel/nginx setups).
    'assets_prefix' => '',

    // Redirect homepage (/) to this URL. Leave empty to redirect to admin login.
    'home_url' => '',

    // Database driver: "sqlite" or "mysql"
    'db_driver' => 'sqlite',

    // SQLite settings (used when db_driver = sqlite)
    'db_path' => __DIR__ . '/../storage/database.sqlite',

    // MySQL settings (used when db_driver = mysql)
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'shortlink',
    'db_user' => 'root',
    'db_pass' => '',
    'db_charset' => 'utf8mb4',

    // Admin password hash. Generate with: php -r "echo password_hash('your-password', PASSWORD_DEFAULT);"
    'admin_password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',

    // Salt for hashing visitor IPs (change this in production).
    'ip_salt' => 'change-this-to-a-random-string',

    // Session name for admin auth.
    'session_name' => 'shortlink_admin',

    // Max failed login attempts before lockout.
    'login_max_attempts' => 5,

    // Lockout duration in seconds.
    'login_lockout_seconds' => 300,

    // Redirect countdown in seconds.
    'redirect_delay' => 10,
];

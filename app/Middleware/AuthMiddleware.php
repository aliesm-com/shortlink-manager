<?php

namespace App\Middleware;

use App\Helpers;

class AuthMiddleware
{
    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function handle()
    {
        if (!empty($_SESSION['admin_logged_in'])) {
            return true;
        }

        Helpers::redirect(Helpers::url($this->config, 'admin/login'));
        return false;
    }

    public static function login(array $config)
    {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_locked_until'] = 0;
    }

    public static function logout()
    {
        unset($_SESSION['admin_logged_in']);
    }

    public static function isLocked()
    {
        $lockedUntil = isset($_SESSION['login_locked_until']) ? (int) $_SESSION['login_locked_until'] : 0;
        return $lockedUntil > time();
    }

    public static function lockRemainingSeconds()
    {
        $lockedUntil = isset($_SESSION['login_locked_until']) ? (int) $_SESSION['login_locked_until'] : 0;
        return max(0, $lockedUntil - time());
    }

    public static function recordFailedAttempt(array $config)
    {
        $attempts = isset($_SESSION['login_attempts']) ? (int) $_SESSION['login_attempts'] : 0;
        $attempts++;
        $_SESSION['login_attempts'] = $attempts;

        $max = isset($config['login_max_attempts']) ? (int) $config['login_max_attempts'] : 5;
        $lockout = isset($config['login_lockout_seconds']) ? (int) $config['login_lockout_seconds'] : 300;

        if ($attempts >= $max) {
            $_SESSION['login_locked_until'] = time() + $lockout;
            $_SESSION['login_attempts'] = 0;
        }
    }
}

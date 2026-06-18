<?php

namespace App\Controllers;

use App\Helpers;
use App\Middleware\AuthMiddleware;

class AuthController extends BaseController
{
    public function showLogin(array $params = [])
    {
        if (!empty($_SESSION['admin_logged_in'])) {
            Helpers::redirect(Helpers::url($this->config, 'admin/dashboard'));
        }

        $error = null;
        $locked = AuthMiddleware::isLocked();

        if ($locked) {
            $remaining = AuthMiddleware::lockRemainingSeconds();
            $error = 'تعداد تلاش‌های ناموفق زیاد بود. لطفاً ' . ceil($remaining / 60) . ' دقیقه دیگر تلاش کنید.';
        }

        Helpers::render('admin/login', [
            'config' => $this->config,
            'error' => $error,
            'locked' => $locked,
        ]);
    }

    public function login(array $params = [])
    {
        if (AuthMiddleware::isLocked()) {
            Helpers::redirect(Helpers::url($this->config, 'admin/login'));
        }

        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!Helpers::verifyCsrf($token)) {
            Helpers::flash('error', 'درخواست نامعتبر است. لطفاً دوباره تلاش کنید.');
            Helpers::redirect(Helpers::url($this->config, 'admin/login'));
        }

        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $hash = isset($this->config['admin_password_hash']) ? $this->config['admin_password_hash'] : '';

        if ($password !== '' && password_verify($password, $hash)) {
            AuthMiddleware::login($this->config);
            Helpers::redirect(Helpers::url($this->config, 'admin/dashboard'));
        }

        AuthMiddleware::recordFailedAttempt($this->config);
        Helpers::flash('error', 'رمز عبور اشتباه است.');
        Helpers::redirect(Helpers::url($this->config, 'admin/login'));
    }

    public function logout(array $params = [])
    {
        AuthMiddleware::logout();
        Helpers::redirect(Helpers::url($this->config, 'admin/login'));
    }
}

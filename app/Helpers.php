<?php

namespace App;

class Helpers
{
    public static function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function basePath(array $config)
    {
        if (!empty($config['base_path'])) {
            $path = '/' . trim($config['base_path'], '/');
            return $path === '/' ? '' : $path;
        }

        $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($dir === '' || $dir === '/') {
            return '';
        }

        if (substr($dir, -7) === '/public') {
            $dir = substr($dir, 0, -7);
        }

        return $dir === '' || $dir === '/' ? '' : $dir;
    }

    public static function baseUrl(array $config)
    {
        if (!empty($config['base_url'])) {
            return rtrim($config['base_url'], '/');
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $path = self::basePath($config);

        return $scheme . '://' . $host . $path;
    }

    public static function url(array $config, $path = '')
    {
        $path = ltrim($path, '/');
        $base = self::baseUrl($config);

        return $path === '' ? $base : $base . '/' . $path;
    }

    /**
     * URL for static assets (css, js). Handles subdirectory installs on nginx/cPanel.
     */
    public static function assetUrl(array $config, $path = '')
    {
        $path = ltrim($path, '/');

        if (!empty($config['assets_prefix'])) {
            return self::url($config, rtrim($config['assets_prefix'], '/') . '/' . $path);
        }

        if (!empty($config['base_path'])) {
            return self::url($config, 'public/assets/' . $path);
        }

        return self::url($config, 'assets/' . $path);
    }

    public static function shortUrl(array $config, $slug)
    {
        return self::baseUrl($config) . '/' . $slug;
    }

    public static function redirect($url, $status = 302)
    {
        header('Location: ' . $url, true, $status);
        exit;
    }

    public static function csrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf($token)
    {
        return isset($_SESSION['csrf_token'])
            && is_string($token)
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function flash($key, $message = null)
    {
        if ($message !== null) {
            $_SESSION['flash'][$key] = $message;
            return;
        }

        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }

        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);

        return $value;
    }

    public static function isValidUrl($url)
    {
        if (!is_string($url) || $url === '') {
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parts = parse_url($url);

        return isset($parts['scheme'])
            && in_array(strtolower($parts['scheme']), ['http', 'https'], true);
    }

    public static function isValidSlug($slug)
    {
        return is_string($slug)
            && $slug !== ''
            && preg_match('/^[a-zA-Z0-9_-]{2,64}$/', $slug) === 1;
    }

    public static function clientIp()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    public static function render($view, array $data = [])
    {
        extract($data, EXTR_SKIP);
        $viewFile = APP_PATH . '/Views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo 'View not found.';
            exit;
        }

        require $viewFile;
    }
}

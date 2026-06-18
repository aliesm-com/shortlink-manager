<?php

namespace App;

class StaticAssetServer
{
    private static $mimeTypes = [
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];

    /**
     * Serve files from public/assets/ when Apache rewrite fails (common in subdirectories).
     *
     * @param string $publicPath Absolute path to public/ directory
     * @return bool True if a file was served
     */
    public static function serve($publicPath)
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $path = $path !== null ? $path : '/';

        if (!preg_match('#/(?:public/)?assets/([a-zA-Z0-9_./-]+)$#', $path, $matches)) {
            return false;
        }

        $relative = $matches[1];

        if (strpos($relative, '..') !== false) {
            return false;
        }

        $assetsRoot = realpath($publicPath . '/assets');
        $file = realpath($publicPath . '/assets/' . $relative);

        if ($assetsRoot === false || $file === false || !is_file($file)) {
            return false;
        }

        if (strpos($file, $assetsRoot) !== 0) {
            return false;
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mime = isset(self::$mimeTypes[$ext]) ? self::$mimeTypes[$ext] : 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=86400');
        readfile($file);

        return true;
    }
}

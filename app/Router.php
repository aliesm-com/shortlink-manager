<?php

namespace App;

use App\Helpers;

class Router
{
    /** @var array */
    private $routes = [];

    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get($pattern, $handler)
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post($pattern, $handler)
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    private function addRoute($method, $pattern, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $uri = $this->getRequestUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['pattern'], $uri);
            if ($params === false) {
                continue;
            }

            return $this->invoke($route['handler'], $params);
        }

        http_response_code(404);
        echo '404 Not Found';
        return null;
    }

    private function getRequestUri()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = $uri !== null ? $uri : '/';

        $basePath = Helpers::basePath($this->config);
        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        $scriptBase = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($scriptBase !== '' && $scriptBase !== '/' && strpos($uri, $scriptBase) === 0) {
            $uri = substr($uri, strlen($scriptBase));
        }

        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        return $uri === '' ? '/' : $uri;
    }

    /**
     * @param string $pattern
     * @param string $uri
     * @return array|false
     */
    private function match($pattern, $uri)
    {
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return false;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    private function invoke($handler, array $params)
    {
        if (is_callable($handler)) {
            return call_user_func($handler, $params);
        }

        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($class, $method) = explode('@', $handler, 2);
            $class = 'App\\Controllers\\' . $class;

            if (!class_exists($class)) {
                throw new \RuntimeException('Controller not found: ' . $class);
            }

            $controller = new $class($this->config);
            return call_user_func_array([$controller, $method], [$params]);
        }

        throw new \RuntimeException('Invalid route handler.');
    }
}

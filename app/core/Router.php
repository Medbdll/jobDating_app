<?php

namespace App\Core;

class Router
{
    private static $router = null;

    private $routes = [
        "GET" => [],
        "POST" => []
    ];

    private function __construct() {}

    public static function getRouter(): Router
    {
        if (!isset(self::$router)) {
            self::$router = new Router();
        }

        return self::$router;
    }

    public function get($path, $callback)
    {
        $this->routes["GET"][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes["POST"][$path] = $callback;
    }

    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] as $path => $callback) {

            $routeRegex = preg_replace_callback('/{\w+(:([^}]+))?}/', function ($matches) {
                return isset($matches[1]) ? '(' . $matches[2] . ')' : '([^/]+)';
            }, $path);

            $routeRegex = '@^' . $routeRegex . '$@';

            if (preg_match($routeRegex, $uri, $matches)) {

                array_shift($matches);

                if (is_array($callback)) {
                    [$class, $methodName] = $callback;
                    $controller = new $class();
                    call_user_func_array([$controller, $methodName], $matches);
                } else {
                    call_user_func_array($callback, $matches);
                }

                return;
            }
        }

        if (isset($this->routes["GET"]['/404'])) {
            call_user_func($this->routes["GET"]['/404']);
        } else {
            echo "404";
        }
    }



}

<?php
// File: app/core/Router.php

class Router {
    private $routes = [
        'GET'  => [],
        'POST' => []
    ];

    /**
     * Define a GET route
     */
    public function get($action, $callback) {
        $this->routes['GET'][$action] = $callback;
    }

    /**
     * Define a POST route
     */
    public function post($action, $callback) {
        $this->routes['POST'][$action] = $callback;
    }

    /**
     * Define a route that accepts both GET and POST
     */
    public function any($action, $callback) {
        $this->routes['GET'][$action] = $callback;
        $this->routes['POST'][$action] = $callback;
    }

    /**
     * Dispatch the request to the correct controller and method
     */
    public function dispatch($action, $method) {
        if (!isset($this->routes[$method][$action])) {
            // Check if there is a GET route instead (fallback or error)
            if (isset($this->routes['GET'][$action])) {
                die("Method Not Allowed");
            }
            // Fallback to 404 or home
            $this->abort(404);
        }

        $callback = $this->routes[$method][$action];

        if (is_callable($callback)) {
            // It's a closure/anonymous function
            call_user_func($callback);
        } elseif (is_array($callback) && count($callback) == 2) {
            // It's an array: [ControllerClass::class, 'methodName']
            $controllerClass = $callback[0];
            $methodName = $callback[1];

            // Instantiate controller and call method
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $methodName)) {
                    $controller->$methodName();
                } else {
                    die("Method $methodName not found in $controllerClass");
                }
            } else {
                die("Controller class $controllerClass not found");
            }
        } else {
            die("Invalid route callback");
        }
    }

    private function abort($code = 404) {
        http_response_code($code);
        echo "404 Not Found";
        exit;
    }
}
?>

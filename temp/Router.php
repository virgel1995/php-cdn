<?php
class Router
{
    private $routes = [];
    private $main_url = '/cdn/temp';
    public function addRoute($path, $handler)
    {
        $this->routes[$this->main_url . $path] = $handler;
    }
    public function handleRequest($path)
    {
        foreach ($this->routes as $route => $handler) {
            $regex = '#^' . preg_replace('/:\w+/', '([^/]+)', $route) . '$#';
            if (preg_match($regex, $path, $matches)) {
                array_shift($matches);
                call_user_func_array($handler, $matches);
                return;
            }
        }
        include __DIR__ . '/views/NotFound.php';
    }
}
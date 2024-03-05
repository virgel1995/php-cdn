<?php
class Router
{
    private $routes = [];
    private $main_url = '/cc';
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
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'code' => 404,
                'message' => 'Not Found',
                'path' => $path
            )
        );

    }
}
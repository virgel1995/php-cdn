<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include('./Router.php');
$router = new Router();

$router->addRoute('/', function () {
    include __DIR__ . '/config.php';
    include __DIR__ . '/connection.php';
    include __DIR__ . '/views/main.php';
});

$router->addRoute('/upload', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include_once __DIR__ . '/config.php';
        include_once __DIR__ . '/connection.php';
        include __DIR__ . '/views/upload.php';
    } else {
        // Handle non-POST requests, e.g., redirect or return an error
        http_response_code(405); // Method Not Allowed
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Method not allowed. Only POST requests are accepted.'
        ]);
    }
});

$router->addRoute('/download/:fileId', function ($fileId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include_once __DIR__ . '/config.php';
        include_once __DIR__ . '/connection.php';
        include __DIR__ . '/views/download.php';
    } else {
        // Handle non-POST requests, e.g., redirect or return an error
        http_response_code(405); // Method Not Allowed
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Method not allowed. Only POST requests are accepted.'
        ]);
    }

});

$router->addRoute('/:slug', function ($slug) {
    include __DIR__ . '/config.php';
    include __DIR__ . '/connection.php';
    include __DIR__ . '/views/slug.php';
});

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
print_r($requestPath);
$router->handleRequest($requestPath);
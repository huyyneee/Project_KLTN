<?php
// Route definitions
$routes = [
    '/' => ['controller' => 'HomeController', 'method' => 'index'],
    '/contact' => ['controller' => 'ContactController', 'method' => 'index'],
];

function route($uri, $routes)
{
    $uri = parse_url($uri, PHP_URL_PATH);

    if (array_key_exists($uri, $routes)) {
        $route = $routes[$uri];
        $controllerName = $route['controller'];
        $methodName = $route['method'];
        $controllerFile = __DIR__ . '/../app/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();
            $controller->$methodName();
        } else {
            http_response_code(404);
            echo 'Controller not found';
        }
    } else {
        http_response_code(404);
        echo 'Page not found';
    }
}

// Handle request
$requestUri = $_SERVER['REQUEST_URI'];
route($requestUri, $routes);

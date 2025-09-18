<?php
// Định nghĩa các route
$routes = [
    '/' => ['controller' => 'HomeController', 'method' => 'index'],
    '/trang-chu' => ['controller' => 'HomeController', 'method' => 'index'],
    '/contact' => ['controller' => 'ContactController', 'method' => 'index'],
    '/about' => ['controller' => 'AboutController', 'method' => 'index'],
    // Thêm các route khác nếu cần
];

function route($uri, $routes)
{
    // Loại bỏ /index.php nếu có trong URI
    $uri = preg_replace('#^/index\\.php#', '', $uri);
    $uri = $uri === '' ? '/' : $uri;
    $uri = parse_url($uri, PHP_URL_PATH);

    if (array_key_exists($uri, $routes)) {
        $route = $routes[$uri];
        $controllerName = $route['controller'];
        $methodName = $route['method'];
        $controllerFile = __DIR__ . '/../app/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();
            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
            } else {
                http_response_code(404);
                echo 'Method not found';
            }
        } else {
            http_response_code(404);
            echo 'Controller not found';
        }
    } else {
        http_response_code(404);
        echo 'Page not found';
    }
}


// Xử lý request
$requestUri = $_SERVER['REQUEST_URI'];
route($requestUri, $routes);


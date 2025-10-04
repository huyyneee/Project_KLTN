<?php
// Định nghĩa các route
$routes = [
    '/' => ['controller' => 'HomeController', 'method' => 'index'],
    '/trang-chu' => ['controller' => 'HomeController', 'method' => 'index'],
    '/contact' => ['controller' => 'ContactController', 'method' => 'index'],
    '/about' => ['controller' => 'AboutController', 'method' => 'index'],
    // Thêm các route khác nếu cần
    '/test' => ['controller' => 'TestController', 'method' => 'index'],
    '/danh-muc' => ['controller' => 'CategoryController', 'method' => 'show'],
    '/login' => ['controller' => 'LoginController', 'method' => 'index'],
    '/register' => ['controller' => 'RegisterController', 'method' => 'index'],
    '/account/register' => ['controller' => 'RegisterController', 'method' => 'store'],
    '/account/send-code' => ['controller' => 'RegisterController', 'method' => 'sendCode'],
    '/account/check-email' => ['controller' => 'RegisterController', 'method' => 'checkEmail'],
    '/account/login' => ['controller' => 'LoginController', 'method' => 'authenticate'],
    '/privacy' => ['controller' => 'PrivacyController', 'method' => 'index'],
    '/terms' => ['controller' => 'TermsController', 'method' => 'index'],
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
        $controllerClass = 'App\\Controllers\\' . $controllerName;
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
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

    // Đã định nghĩa route /test ở mảng $routes phía trên, không cần $router->get

// Xử lý request
$requestUri = $_SERVER['REQUEST_URI'];
route($requestUri, $routes);


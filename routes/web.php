<?php
// Định nghĩa các route
$routes = [
    '/' => ['controller' => 'HomeController', 'method' => 'index'],
    '/trang-chu' => ['controller' => 'HomeController', 'method' => 'index'],
    '/contact' => ['controller' => 'ContactController', 'method' => 'index'],
    '/login' => ['controller' => 'HomeController', 'method' => 'login'],

    // Admin Routes
    '/admin' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    '/admin/dashboard' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    '/admin/categories' => ['controller' => 'AdminController', 'method' => 'categories'],
    '/admin/categories/create' => ['controller' => 'AdminController', 'method' => 'createCategory'],
    '/admin/categories/edit' => ['controller' => 'AdminController', 'method' => 'editCategory'],
    '/admin/categories/delete' => ['controller' => 'AdminController', 'method' => 'deleteCategory'],
    '/admin/products' => ['controller' => 'AdminController', 'method' => 'products'],
    '/admin/products/create' => ['controller' => 'AdminController', 'method' => 'createProduct'],
    '/admin/products/view' => ['controller' => 'AdminController', 'method' => 'viewProduct'],
    '/admin/products/edit' => ['controller' => 'AdminController', 'method' => 'editProduct'],
    '/admin/products/delete' => ['controller' => 'AdminController', 'method' => 'deleteProduct'],
];

function route($uri, $routes)
{
    // Loại bỏ /index.php nếu có trong URI
    $uri = preg_replace('#^/index\\.php#', '', $uri);
    $uri = $uri === '' ? '/' : $uri;
    $uri = parse_url($uri, PHP_URL_PATH);
    $queryString = parse_url($uri, PHP_URL_QUERY);

    // Handle exact matches first
    if (array_key_exists($uri, $routes)) {
        $route = $routes[$uri];
        $controllerName = $route['controller'];
        $methodName = $route['method'];
        $controllerFile = __DIR__ . '/../app/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerClass = 'App\\Controllers\\' . $controllerName;
            $controller = new $controllerClass();

            // Check if method needs parameters
            if (isset($_GET['id']) && in_array($methodName, ['editCategory', 'deleteCategory', 'viewProduct', 'editProduct', 'deleteProduct'])) {
                $controller->$methodName($_GET['id']);
            } else {
                $controller->$methodName();
            }
        } else {
            http_response_code(404);
            echo 'Controller not found';
        }
    } else {
        // Handle parameterized routes
        $found = false;
        foreach ($routes as $routePattern => $route) {
            // Simple pattern matching for view/edit/delete with IDs
            if (preg_match('/^' . str_replace(['/view', '/edit', '/delete'], ['/view.*', '/edit.*', '/delete.*'], preg_quote($routePattern, '/')) . '$/', $uri)) {
                $controllerName = $route['controller'];
                $methodName = $route['method'];
                $controllerFile = __DIR__ . '/../app/Controllers/' . $controllerName . '.php';

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $controllerClass = 'App\\Controllers\\' . $controllerName;
                    $controller = new $controllerClass();

                    // Extract ID from URL or GET parameter
                    $id = $_GET['id'] ?? null;
                    if ($id && in_array($methodName, ['viewProduct', 'editCategory', 'deleteCategory', 'editProduct', 'deleteProduct'])) {
                        $controller->$methodName($id);
                    } else {
                        $controller->$methodName();
                    }
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            http_response_code(404);
            echo 'Page not found';
        }
    }
}

// Đã định nghĩa route /test ở mảng $routes phía trên, không cần $router->get

// Xử lý request
$requestUri = $_SERVER['REQUEST_URI'];
route($requestUri, $routes);


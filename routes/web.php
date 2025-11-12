<?php
// Định nghĩa các route
$routes = [
    // Public Routes
    '/'                     => ['controller' => 'HomeController', 'method' => 'index'],
    '/trang-chu'            => ['controller' => 'HomeController', 'method' => 'index'],
    '/contact'              => ['controller' => 'ContactController', 'method' => 'index'],
    '/about'                => ['controller' => 'AboutController', 'method' => 'index'],
    '/test'                 => ['controller' => 'TestController', 'method' => 'index'],
    '/danh-muc'             => ['controller' => 'CategoryController', 'method' => 'show'],
    '/san-pham'             => ['controller' => 'ProductController', 'method' => 'show'],
    '/login'                => ['controller' => 'LoginController', 'method' => 'index'],
    '/register'             => ['controller' => 'RegisterController', 'method' => 'index'],
    '/privacy'              => ['controller' => 'PrivacyController', 'method' => 'index'],
    '/terms'                => ['controller' => 'TermsController', 'method' => 'index'],

    // Account Routes
    '/account'              => ['controller' => 'AccountController', 'method' => 'index'],
    '/account/edit'         => ['controller' => 'AccountController', 'method' => 'edit'],
    '/account/update'       => ['controller' => 'AccountController', 'method' => 'update'],
    '/account/register'     => ['controller' => 'RegisterController', 'method' => 'store'],
    '/account/send-code'    => ['controller' => 'RegisterController', 'method' => 'sendCode'],
    '/account/check-email'  => ['controller' => 'RegisterController', 'method' => 'checkEmail'],
    '/account/login'        => ['controller' => 'LoginController', 'method' => 'authenticate'],
    '/account/logout'       => ['controller' => 'LoginController', 'method' => 'logout'],

    // Address Routes
    '/account/address'          => ['controller' => 'AddressController', 'method' => 'address'],
    '/account/address/add'      => ['controller' => 'AddressController', 'method' => 'addAddress'],
    '/account/address/edit'     => ['controller' => 'AddressController', 'method' => 'editAddress'],
    '/account/address/update'   => ['controller' => 'AddressController', 'method' => 'updateAddress'],
    '/account/address/delete'   => ['controller' => 'AddressController', 'method' => 'deleteAddress'],
    '/addresses'                => ['controller' => 'AccountController', 'method' => 'address'],

    //forgot password
    '/login/forgot-password' => ['controller' => 'LoginController', 'method' => 'forgotPasswordForm'],
    '/login/send-reset-link' => ['controller' => 'LoginController', 'method' => 'sendResetLink'],
    '/login/reset-password' => ['controller' => 'LoginController', 'method' => 'resetPasswordForm'],
    '/login/reset-password-post' => ['controller' => 'LoginController', 'method' => 'resetPassword'],


    // Admin Routes
    '/admin'                    => ['controller' => 'AdminController', 'method' => 'dashboard'],
    '/admin/dashboard'          => ['controller' => 'AdminController', 'method' => 'dashboard'],
    '/admin/categories'         => ['controller' => 'AdminController', 'method' => 'categories'],
    '/admin/categories/create'  => ['controller' => 'AdminController', 'method' => 'createCategory'],
    '/admin/categories/edit'    => ['controller' => 'AdminController', 'method' => 'editCategory'],
    '/admin/categories/delete'  => ['controller' => 'AdminController', 'method' => 'deleteCategory'],
    '/admin/products'           => ['controller' => 'AdminController', 'method' => 'products'],
    '/admin/products/create'    => ['controller' => 'AdminController', 'method' => 'createProduct'],
    '/admin/products/view'      => ['controller' => 'AdminController', 'method' => 'viewProduct'],
    '/admin/products/edit'      => ['controller' => 'AdminController', 'method' => 'editProduct'],
    '/admin/products/delete'    => ['controller' => 'AdminController', 'method' => 'deleteProduct'],

    // Cart Routes
    '/cart'                 => ['controller' => 'CartController', 'method' => 'index'],
    '/cart/add'             => ['controller' => 'CartController', 'method' => 'add'],
    '/cart/update'          => ['controller' => 'CartController', 'method' => 'updateQuantity'],
    '/cart/remove'          => ['controller' => 'CartController', 'method' => 'remove'],

    //Checkout
    '/checkout'             => ['controller' => 'OrderController', 'method' => 'index'],
    '/checkout/placeOrder' => ['controller' => 'OrderController', 'method' => 'placeOrder'],
    '/checkout/vnpayReturn' => ['controller' => 'OrderController', 'method' => 'vnpayReturn'],
    '/receipt' => ['controller' => 'OrderController', 'method' => 'receipt'],

    //Order
    '/order'                => ['controller' => 'AccountController', 'method' => 'order'],
    '/account/order_detail'         => ['controller' => 'AccountController', 'method' => 'orderDetail'],
    '/account/cancel-order'         => ['controller' => 'AccountController', 'method' => 'cancelOrder'],
    '/cart/count'           => ['controller' => 'CartController', 'method' => 'count'],
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
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// --- Centralized auth protection --------------------------------------------------
// Protect these prefixes: if an unauthenticated user requests a protected URL,
// redirect them to /login with a return=... parameter.
$protectedPrefixes = ['/cart', '/checkout', '/orders', '/account'];

// Paths to exclude from protection to avoid redirect loops (login/register endpoints)
$unprotectedPaths = [
    '/login',
    '/register',
    '/account/login',
    '/account/edit',
    '/account/register',
    '/account/send-code',
    '/account/check-email',
    '/account/logout',
    '/account/address',
    '/cart/add',
    '/cart/update',
    '/cart/remove'
];

// Helper: attempt to restore session from remember-me cookies (same validation used elsewhere)
function _restore_session_from_cookies_if_needed()
{
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }

    if (empty($_SESSION['account_id']) && !empty($_COOKIE['account_id']) && !empty($_COOKIE['account_email'])) {
        $expiresOk = false;
        if (!empty($_COOKIE['account_expires']) && ctype_digit($_COOKIE['account_expires'])) {
            $expiresTs = (int) $_COOKIE['account_expires'];
            if ($expiresTs > time()) $expiresOk = true;
        }

        if ($expiresOk) {
            try {
                $db = (new \App\Core\Database())->getConnection();
                $stmt = $db->prepare('SELECT id, email, status FROM accounts WHERE id = :id LIMIT 1');
                $stmt->execute([':id' => $_COOKIE['account_id']]);
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($row && ($row['email'] === $_COOKIE['account_email']) && ($row['status'] ?? 'active') === 'active') {
                    $_SESSION['account_id'] = $row['id'];
                    $_SESSION['account_email'] = $row['email'];
                }
            } catch (\Throwable $e) {
                // ignore errors and leave session empty
            }
        } else {
            // clear expired cookies
            setcookie('account_id', '', time() - 3600, '/', '', false, true);
            setcookie('account_email', '', time() - 3600, '/', '', false, true);
            setcookie('account_expires', '', time() - 3600, '/', '', false, true);
        }
    }
}

// Normalize path
$path = preg_replace('#^/index\\.php#', '', $requestUri);
$path = $path === '' ? '/' : $path;
$path = parse_url($path, PHP_URL_PATH);

// Try to restore session once from cookies
_restore_session_from_cookies_if_needed();

foreach ($protectedPrefixes as $prefix) {
    if ($path === $prefix || strpos($path, $prefix . '/') === 0) {
        // allow explicit unprotected paths
        if (in_array($path, $unprotectedPaths, true)) {
            break;
        }

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (empty($_SESSION['account_id'])) {
            $return = $requestUri;
            if ($return === '' || strpos($return, '/') !== 0) {
                $return = '/';
            }
            header('Location: /login?return=' . urlencode($return));
            exit();
        }
        break;
    }
}

route($requestUri, $routes);

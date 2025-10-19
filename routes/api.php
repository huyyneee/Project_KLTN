<?php
// API Routes
require_once __DIR__ . '/../app/Controllers/ProductApiController.php';
require_once __DIR__ . '/../app/Controllers/CategoryApiController.php';
require_once __DIR__ . '/../app/Controllers/DashboardApiController.php';
require_once __DIR__ . '/../app/Controllers/ImageUploadController.php';
require_once __DIR__ . '/../app/Controllers/UserApiController.php';

// Set CORS headers for all API requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove query string and get path
$path = parse_url($uri, PHP_URL_PATH);

// Handle different API path formats
if (strpos($path, '/api/') === 0) {
    $path = substr($path, 4); // Remove '/api' prefix
} elseif (strpos($path, '/api') === 0) {
    $path = substr($path, 4); // Remove '/api' prefix
}

// Remove trailing slash
$path = rtrim($path, '/');

// If path is empty, set to root
if (empty($path)) {
    $path = '/';
}

// Route the request
switch ($path) {
    // Dashboard routes
    case '/dashboard/stats':
        if ($method === 'GET') {
            $controller = new \App\Controllers\DashboardApiController();
            $controller->getStats();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/dashboard/best-selling':
        if ($method === 'GET') {
            $controller = new \App\Controllers\DashboardApiController();
            $controller->getBestSelling();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/dashboard/recent-activity':
        if ($method === 'GET') {
            $controller = new \App\Controllers\DashboardApiController();
            $controller->getRecentActivity();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // Product routes
    case '/products':
        $controller = new \App\Controllers\ProductApiController();
        switch ($method) {
            case 'GET':
                $controller->index();
                break;
            case 'POST':
                $controller->store();
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('/^\/products\/(\d+)$/', $path, $matches) ? true : false):
        $controller = new \App\Controllers\ProductApiController();
        $id = $matches[1];
        switch ($method) {
            case 'GET':
                $controller->show($id);
                break;
            case 'PUT':
                $controller->update($id);
                break;
            case 'DELETE':
                $controller->destroy($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('/^\/products\/category\/(\d+)$/', $path, $matches) ? true : false):
        if ($method === 'GET') {
            $controller = new \App\Controllers\ProductApiController();
            $categoryId = $matches[1];
            $controller->getByCategory($categoryId);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/products/deleted':
        if ($method === 'GET') {
            $controller = new \App\Controllers\ProductApiController();
            $controller->getDeleted();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('/^\/products\/(\d+)\/restore$/', $path, $matches) ? true : false):
        if ($method === 'POST') {
            $controller = new \App\Controllers\ProductApiController();
            $id = $matches[1];
            $controller->restore($id);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // Category routes
    case '/categories':
        $controller = new \App\Controllers\CategoryApiController();
        switch ($method) {
            case 'GET':
                $controller->index();
                break;
            case 'POST':
                $controller->store();
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('/^\/categories\/(\d+)$/', $path, $matches) ? true : false):
        $controller = new \App\Controllers\CategoryApiController();
        $id = $matches[1];
        switch ($method) {
            case 'GET':
                $controller->show($id);
                break;
            case 'PUT':
                $controller->update($id);
                break;
            case 'DELETE':
                $controller->destroy($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/categories/deleted':
        if ($method === 'GET') {
            $controller = new \App\Controllers\CategoryApiController();
            $controller->getDeleted();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('/^\/categories\/(\d+)\/restore$/', $path, $matches) ? true : false):
        if ($method === 'POST') {
            $controller = new \App\Controllers\CategoryApiController();
            $id = $matches[1];
            $controller->restore($id);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // Image upload routes
    case '/upload':
        if ($method === 'POST') {
            $controller = new \App\Controllers\ImageUploadController();
            $controller->upload();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/upload/multiple':
        if ($method === 'POST') {
            $controller = new \App\Controllers\ImageUploadController();
            $controller->uploadMultiple();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('/^\/upload\/(.+)$/', $path, $matches) ? true : false):
        if ($method === 'DELETE') {
            $controller = new \App\Controllers\ImageUploadController();
            $filename = $matches[1];
            $controller->delete($filename);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // User routes
    case '/users':
        $controller = new \App\Controllers\UserApiController();
        switch ($method) {
            case 'GET':
                $controller->index();
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('/^\/users\/(\d+)$/', $path, $matches) ? true : false):
        $controller = new \App\Controllers\UserApiController();
        $id = $matches[1];
        switch ($method) {
            case 'GET':
                $controller->show($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/users/search':
        if ($method === 'GET') {
            $controller = new \App\Controllers\UserApiController();
            $controller->search();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/users/paginated':
        if ($method === 'GET') {
            $controller = new \App\Controllers\UserApiController();
            $controller->paginated();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // Default route
    default:
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found']);
        break;
}
?>
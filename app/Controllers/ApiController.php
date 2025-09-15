<?php
require_once __DIR__ . '/../Core/Controller.php';

class ApiController extends Controller
{
    protected function setCorsHeaders()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: 86400'); // 24 hours
    }

    protected function sendResponse($data = null, $message = 'Success', $status = 200)
    {
        // Set CORS headers first
        $this->setCorsHeaders();

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode([
            'success' => $status < 400,
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }

    protected function sendError($message = 'Error', $status = 400, $errors = null)
    {
        // Set CORS headers first
        $this->setCorsHeaders();

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]);
        exit();
    }

    protected function getJsonInput()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    protected function validateRequired($data, $required)
    {
        $missing = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        return $missing;
    }
}

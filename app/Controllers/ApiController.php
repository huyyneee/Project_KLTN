<?php
namespace App\Controllers;

require_once __DIR__ . '/../Core/Controller.php';

use App\Core\Controller;

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

    protected function getInput()
    {
        // Check if it's FormData (multipart/form-data)
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
            // For FormData, use $_POST and $_FILES
            $data = $_POST;

            // Handle file uploads
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $data['main_image'] = $this->handleFileUpload($_FILES['main_image']);
            }

            if (isset($_FILES['detail_images']) && is_array($_FILES['detail_images']['name'])) {
                $detailImages = [];
                for ($i = 0; $i < count($_FILES['detail_images']['name']); $i++) {
                    if ($_FILES['detail_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $_FILES['detail_images']['name'][$i],
                            'type' => $_FILES['detail_images']['type'][$i],
                            'tmp_name' => $_FILES['detail_images']['tmp_name'][$i],
                            'size' => $_FILES['detail_images']['size'][$i]
                        ];
                        $detailImages[] = $this->handleFileUpload($file);
                    }
                }
                $data['detail_images'] = json_encode($detailImages);
            }

            return $data;
        } else {
            // For JSON, use getJsonInput
            return $this->getJsonInput();
        }
    }

    private function handleFileUpload($file)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/';

        // Tạo thư mục upload nếu chưa tồn tại
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type');
        }

        // Validate file size (5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception('File too large');
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return '/uploads/' . $filename;
        } else {
            throw new Exception('Failed to save uploaded file');
        }
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

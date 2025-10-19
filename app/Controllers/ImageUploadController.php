<?php
namespace App\Controllers;

require_once __DIR__ . '/ApiController.php';

use App\Controllers\ApiController;
use Exception;

class ImageUploadController extends ApiController
{
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../public/uploads/products/';

        // Tạo thư mục upload nếu chưa tồn tại
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }


    // POST /api/upload - Upload single image
    public function upload()
    {
        try {
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $this->sendError('No image file uploaded or upload error', 400);
            }

            $file = $_FILES['image'];

            // Validate file type
            if (!in_array($file['type'], $this->allowedTypes)) {
                $this->sendError('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed', 400);
            }

            // Validate file size
            if ($file['size'] > $this->maxFileSize) {
                $this->sendError('File too large. Maximum size is 5MB', 400);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
                $imageUrl = $baseUrl . '/uploads/products/' . $filename;
                $this->sendResponse([
                    'url' => $imageUrl,
                    'filename' => $filename,
                    'size' => $file['size'],
                    'type' => $file['type']
                ], 'Image uploaded successfully');
            } else {
                $this->sendError('Failed to save uploaded file', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Upload failed: ' . $e->getMessage(), 500);
        }
    }

    // POST /api/upload/multiple - Upload multiple images
    public function uploadMultiple()
    {
        try {
            // DEBUG: Log what we received
            error_log("=== ImageUploadController::uploadMultiple ===");
            error_log("Available fields in _FILES: " . implode(', ', array_keys($_FILES)));
            foreach ($_FILES as $key => $value) {
                if (is_array($value['name'])) {
                    error_log("Field '$key' has " . count($value['name']) . " files");
                } else {
                    error_log("Field '$key' has 1 file: " . $value['name']);
                }
            }

            // Check for different possible field names
            $filesField = null;

            // Check images field with flexible validation (handles both single and multiple files)
            // Support both 'images' and 'images[]' field names
            if (isset($_FILES['images'])) {
                // Handle both single file and multiple files
                if (is_array($_FILES['images']['name'])) {
                    // Multiple files: $_FILES['images']['name'] is an array
                    $filesField = 'images';
                } elseif (is_string($_FILES['images']['name']) && !empty($_FILES['images']['name'])) {
                    // Single file: convert to array format for consistency
                    $filesField = 'images';
                    // Convert single file to array format
                    $_FILES['images'] = [
                        'name' => [$_FILES['images']['name']],
                        'type' => [$_FILES['images']['type']],
                        'tmp_name' => [$_FILES['images']['tmp_name']],
                        'error' => [$_FILES['images']['error']],
                        'size' => [$_FILES['images']['size']]
                    ];
                }
            }

            // Support 'images[]' notation for multiple files (common in JavaScript/FormData)
            if (!$filesField && isset($_FILES['images_'])) {
                // PHP converts 'images[]' to 'images_' in $_FILES
                if (is_array($_FILES['images_']['name'])) {
                    $filesField = 'images_';
                }
            }

            if (!$filesField) {
                // Check other possible field names
                if (isset($_FILES['files']) && (is_array($_FILES['files']['name']) || is_string($_FILES['files']['name']))) {
                    $filesField = 'files';
                } elseif (isset($_FILES['upload']) && (is_array($_FILES['upload']['name']) || is_string($_FILES['upload']['name']))) {
                    $filesField = 'upload';
                }
            }

            if (!$filesField) {
                $availableFields = array_keys($_FILES);
                $this->sendError('No images uploaded. Expected field: images, files, or upload. Available fields: ' . implode(', ', $availableFields), 400);
            }

            $uploadedImages = [];
            $errors = [];

            for ($i = 0; $i < count($_FILES[$filesField]['name']); $i++) {
                if ($_FILES[$filesField]['error'][$i] !== UPLOAD_ERR_OK) {
                    $errors[] = "Image " . ($i + 1) . ": Upload error";
                    continue;
                }

                $file = [
                    'name' => $_FILES[$filesField]['name'][$i],
                    'type' => $_FILES[$filesField]['type'][$i],
                    'tmp_name' => $_FILES[$filesField]['tmp_name'][$i],
                    'size' => $_FILES[$filesField]['size'][$i]
                ];

                // Validate file type
                if (!in_array($file['type'], $this->allowedTypes)) {
                    $errors[] = "Image " . ($i + 1) . ": Invalid file type";
                    continue;
                }

                // Validate file size
                if ($file['size'] > $this->maxFileSize) {
                    $errors[] = "Image " . ($i + 1) . ": File too large";
                    continue;
                }

                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '_' . $i . '.' . $extension;
                $filepath = $this->uploadDir . $filename;

                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
                    $uploadedImages[] = [
                        'url' => $baseUrl . '/uploads/products/' . $filename,
                        'filename' => $filename,
                        'size' => $file['size'],
                        'type' => $file['type']
                    ];
                } else {
                    $errors[] = "Image " . ($i + 1) . ": Failed to save";
                }
            }

            if (empty($uploadedImages)) {
                $this->sendError('No images were uploaded successfully. Errors: ' . implode(', ', $errors), 400);
            }

            $response = [
                'images' => $uploadedImages,
                'count' => count($uploadedImages)
            ];

            if (!empty($errors)) {
                $response['warnings'] = $errors;
            }

            $this->sendResponse($response, 'Images uploaded successfully');
        } catch (Exception $e) {
            $this->sendError('Upload failed: ' . $e->getMessage(), 500);
        }
    }

    // DELETE /api/upload/{filename} - Delete uploaded image
    public function delete($filename)
    {
        try {
            $filepath = $this->uploadDir . $filename;

            if (file_exists($filepath)) {
                if (unlink($filepath)) {
                    $this->sendResponse(null, 'Image deleted successfully');
                } else {
                    $this->sendError('Failed to delete image', 500);
                }
            } else {
                $this->sendError('Image not found', 404);
            }
        } catch (Exception $e) {
            $this->sendError('Delete failed: ' . $e->getMessage(), 500);
        }
    }
}
?>
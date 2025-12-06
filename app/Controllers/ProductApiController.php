<?php
namespace App\Controllers;

require_once __DIR__ . '/ApiController.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Models/ProductImage.php';

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Exception;
class ProductApiController extends ApiController
{

    private $productModel;
    private $categoryModel;
    private $productImageModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->productImageModel = new ProductImage();
    }

    private function handleImageUpload($imageData, $productId, $isMain = false)
    {
        if (empty($imageData)) {
            error_log("handleImageUpload: Empty image data");
            return null;
        }

        // Extract just the path from full URL if needed
        $imageUrl = $imageData;

        // If it's a full URL, extract just the path
        if (strpos($imageData, 'http://') === 0 || strpos($imageData, 'https://') === 0) {
            $parsedUrl = parse_url($imageData);
            $imageUrl = $parsedUrl['path'];
        }

        // If it's a blob URL, skip it (should not happen if upload flow is correct)
        if (strpos($imageData, 'blob:') === 0) {
            error_log("handleImageUpload: Blob URL detected, skipping: " . $imageData);
            return null;
        }

        // Save image record to database
        $imageRecord = [
            'product_id' => $productId,
            'url' => $imageUrl,
            'is_main' => $isMain ? 1 : 0
        ];

        error_log("handleImageUpload: Attempting to save image record: " . json_encode($imageRecord));
        $result = $this->productImageModel->create($imageRecord);
        error_log("handleImageUpload: Result: " . ($result ? 'success' : 'failed'));

        return $result;
    }

    // GET /api/products - Lấy danh sách sản phẩm
    public function index()
    {
        try {
            // Get pagination parameters
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
            $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at';
            $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';

            // Validate pagination parameters
            $page = max(1, $page);
            $limit = max(1, min(100, $limit)); // Limit between 1 and 100
            $offset = ($page - 1) * $limit;

            // Get products with pagination
            $result = $this->productModel->findAllWithCategoryPaginated(
                $limit,
                $offset,
                $search,
                $category_id,
                $sort_by,
                $sort_order
            );

            // Batch load all images for all products in one query to avoid N+1 problem
            $productIds = array_column($result['data'], 'id');
            $allImages = [];
            if (!empty($productIds)) {
                try {
                    $db = \App\Core\Database::getInstance()->getConnection();
                    // Limit to prevent huge queries, process in batches if needed
                    $batchSize = 100;
                    $batches = array_chunk($productIds, $batchSize);

                    foreach ($batches as $batch) {
                        $placeholders = implode(',', array_fill(0, count($batch), '?'));
                        $sql = "SELECT product_id, url, is_main FROM product_images WHERE product_id IN ($placeholders) ORDER BY product_id, is_main DESC, id ASC";
                        $stmt = $db->prepare($sql);
                        $stmt->execute($batch);
                        $imageRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                        // Group images by product_id
                        foreach ($imageRows as $image) {
                            $allImages[$image['product_id']][] = $image;
                        }
                    }
                } catch (\Exception $e) {
                    error_log("Error loading product images: " . $e->getMessage());
                    // Continue without images if there's an error
                }
            }

            // Format dữ liệu để frontend dễ sử dụng
            $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
            $formattedProducts = array_map(function ($product) use ($allImages, $baseUrl) {
                $specifications = [];
                if (!empty($product['specifications'])) {
                    $specifications = json_decode($product['specifications'], true) ?: [];
                }

                // Get images for this product from pre-loaded array
                $images = $allImages[$product['id']] ?? [];
                $mainImage = null;
                $detailImages = [];

                foreach ($images as $image) {
                    $fullUrl = $image['url'];
                    if (strpos($fullUrl, 'http') !== 0) {
                        $fullUrl = $baseUrl . $fullUrl;
                    }
                    if ($image['is_main']) {
                        $mainImage = $fullUrl;
                    } else {
                        $detailImages[] = $fullUrl;
                    }
                }

                return [
                    'id' => (int) $product['id'],
                    'code' => $product['code'],
                    'name' => $product['name'],
                    'price' => (float) $product['price'],
                    'quantity' => isset($product['quantity']) ? (int) $product['quantity'] : 0,
                    'description' => $product['description'],
                    'specifications' => $specifications,
                    'usage' => $product['usage'],
                    'ingredients' => $product['ingredients'],
                    'category_id' => (int) $product['category_id'],
                    'category_name' => $product['category_name'],
                    'main_image' => $mainImage,
                    'detail_images' => $detailImages,
                    'created_at' => $product['created_at'],
                    'updated_at' => $product['updated_at']
                ];
            }, $result['data']);

            // Return paginated response
            $response = [
                'data' => $formattedProducts,
                'pagination' => [
                    'current_page' => $result['current_page'],
                    'total_pages' => $result['total_pages'],
                    'total_items' => $result['total_items'],
                    'per_page' => $result['per_page'],
                    'has_next' => $result['has_next'],
                    'has_prev' => $result['has_prev']
                ]
            ];

            $this->sendResponse($response, 'Products retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve products: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/products/{id} - Lấy sản phẩm theo ID
    public function show($id)
    {
        try {
            $product = $this->productModel->findById($id);

            if (!$product) {
                $this->sendError('Product not found', 404);
            }

            $specifications = [];
            if (!empty($product['specifications'])) {
                $specifications = json_decode($product['specifications'], true) ?: [];
            }

            // Format image URLs to full URLs
            $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
            $mainImage = $product['main_image'];
            if ($mainImage && strpos($mainImage, 'http') !== 0) {
                $mainImage = $baseUrl . $mainImage;
            }

            $detailImages = $product['detail_images'] ? json_decode($product['detail_images'], true) : [];
            if (is_array($detailImages)) {
                $detailImages = array_map(function ($url) use ($baseUrl) {
                    if (strpos($url, 'http') !== 0) {
                        return $baseUrl . $url;
                    }
                    return $url;
                }, $detailImages);
            }

            $formattedProduct = [
                'id' => (int) $product['id'],
                'code' => $product['code'],
                'name' => $product['name'],
                'price' => (float) $product['price'],
                'quantity' => isset($product['quantity']) ? (int) $product['quantity'] : 0,
                'description' => $product['description'],
                'specifications' => $specifications,
                'usage' => $product['usage'],
                'ingredients' => $product['ingredients'],
                'category_id' => (int) $product['category_id'],
                'main_image' => $mainImage,
                'detail_images' => $detailImages,
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at']
            ];

            $this->sendResponse($formattedProduct, 'Product retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve product: ' . $e->getMessage(), 500);
        }
    }

    // POST /api/products - Tạo sản phẩm mới
    public function store()
    {
        try {
            // Handle both JSON and FormData input
            $data = $this->getInput();

            // Validate required fields
            $required = ['name', 'price', 'category_id', 'description'];
            $missing = $this->validateRequired($data, $required);

            if (!empty($missing)) {
                $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
            }

            // Generate product code if not provided
            if (empty($data['code'])) {
                $data['code'] = 'SP' . date('Ymd') . rand(1000, 9999);
            }

            // Format specifications
            if (isset($data['specifications']) && is_array($data['specifications'])) {
                $data['specifications'] = json_encode($data['specifications']);
            }

            // Parse detail_images_urls if it's a JSON string
            $detailImages = [];
            if (isset($data['detail_images_urls'])) {
                // If it's a JSON string, decode it
                if (is_string($data['detail_images_urls'])) {
                    $detailImages = json_decode($data['detail_images_urls'], true) ?: [];
                } elseif (is_array($data['detail_images_urls'])) {
                    $detailImages = $data['detail_images_urls'];
                }
                unset($data['detail_images_urls']);
            } elseif (isset($data['detail_images']) && is_array($data['detail_images'])) {
                $detailImages = $data['detail_images'];
            }

            // Main image URL
            $mainImageUrl = null;
            if (isset($data['main_image_url'])) {
                $mainImageUrl = $data['main_image_url'];
                unset($data['main_image_url']);
            } elseif (isset($data['main_image'])) {
                $mainImageUrl = $data['main_image'];
            }

            // Remove image fields from product data (they will be stored in product_images table)
            unset($data['main_image']);
            unset($data['detail_images']);

            // Create product
            $result = $this->productModel->create($data);

            if ($result) {
                // Handle main image upload to product_images table
                if (!empty($mainImageUrl)) {
                    error_log("Saving main image: " . $mainImageUrl . " for product ID: " . $result);
                    $this->handleImageUpload($mainImageUrl, $result, true);
                }

                // Handle detail images upload to product_images table
                if (!empty($detailImages) && is_array($detailImages)) {
                    error_log("Saving " . count($detailImages) . " detail images for product ID: " . $result);
                    foreach ($detailImages as $detailImage) {
                        if (!empty($detailImage)) {
                            $this->handleImageUpload($detailImage, $result, false);
                        }
                    }
                }

                $this->sendResponse(['id' => $result], 'Product created successfully', 201);
            } else {
                $this->sendError('Failed to create product', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Failed to create product: ' . $e->getMessage(), 500);
        }
    }

    // PUT /api/products/{id} - Cập nhật sản phẩm
    public function update($id)
    {
        try {
            // Handle both JSON and FormData input
            $data = $this->getInput();

            // Check if product exists
            $existingProduct = $this->productModel->findById($id);
            if (!$existingProduct) {
                $this->sendError('Product not found', 404);
            }

            // Format specifications
            if (isset($data['specifications']) && is_array($data['specifications'])) {
                $data['specifications'] = json_encode($data['specifications']);
            }

            // Parse detail_images_urls if it's a JSON string
            $detailImages = [];
            if (isset($data['detail_images_urls'])) {
                // If it's a JSON string, decode it
                if (is_string($data['detail_images_urls'])) {
                    $detailImages = json_decode($data['detail_images_urls'], true) ?: [];
                } elseif (is_array($data['detail_images_urls'])) {
                    $detailImages = $data['detail_images_urls'];
                }
                unset($data['detail_images_urls']);
            } elseif (isset($data['detail_images']) && is_array($data['detail_images'])) {
                $detailImages = $data['detail_images'];
            }

            // Main image URL
            $mainImageUrl = null;
            if (isset($data['main_image_url'])) {
                $mainImageUrl = $data['main_image_url'];
                unset($data['main_image_url']);
            } elseif (isset($data['main_image'])) {
                $mainImageUrl = $data['main_image'];
            }

            // Remove image fields from product data (they will be stored in product_images table)
            unset($data['main_image']);
            unset($data['detail_images']);

            $result = $this->productModel->update($id, $data);

            if ($result) {
                // If images are provided, delete old images and add new ones
                if (!empty($mainImageUrl) || !empty($detailImages)) {
                    // Delete existing images for this product
                    $this->productImageModel->deleteByProductId($id);

                    // Add main image
                    if (!empty($mainImageUrl)) {
                        error_log("Updating main image: " . $mainImageUrl . " for product ID: " . $id);
                        $this->handleImageUpload($mainImageUrl, $id, true);
                    }

                    // Add detail images
                    if (!empty($detailImages) && is_array($detailImages)) {
                        error_log("Updating " . count($detailImages) . " detail images for product ID: " . $id);
                        foreach ($detailImages as $detailImage) {
                            if (!empty($detailImage)) {
                                $this->handleImageUpload($detailImage, $id, false);
                            }
                        }
                    }
                }
            }

            if ($result) {
                $this->sendResponse(null, 'Product updated successfully');
            } else {
                $this->sendError('Failed to update product', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Failed to update product: ' . $e->getMessage(), 500);
        }
    }

    // DELETE /api/products/{id} - Xóa mềm sản phẩm
    public function destroy($id)
    {
        try {
            // Check if product exists
            $existingProduct = $this->productModel->findById($id);
            if (!$existingProduct) {
                $this->sendError('Product not found', 404);
            }

            $result = $this->productModel->delete($id);

            if ($result) {
                $this->sendResponse(null, 'Product soft deleted successfully');
            } else {
                $this->sendError('Failed to delete product', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Failed to delete product: ' . $e->getMessage(), 500);
        }
    }

    // POST /api/products/{id}/restore - Khôi phục sản phẩm đã xóa
    public function restore($id)
    {
        try {
            // Check if product exists (including deleted ones)
            $existingProduct = $this->productModel->findById($id, true);
            if (!$existingProduct) {
                $this->sendError('Product not found', 404);
            }

            // Check if product is actually deleted
            if (empty($existingProduct['deleted_at'])) {
                $this->sendError('Product is not deleted', 400);
            }

            $result = $this->productModel->restore($id);

            if ($result) {
                $this->sendResponse(null, 'Product restored successfully');
            } else {
                $this->sendError('Failed to restore product', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Failed to restore product: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/products/deleted - Lấy danh sách sản phẩm đã xóa
    public function getDeleted()
    {
        try {
            $products = $this->productModel->findDeleted();

            $formattedProducts = array_map(function ($product) {
                $specifications = [];
                if (!empty($product['specifications'])) {
                    $specifications = json_decode($product['specifications'], true) ?: [];
                }

                return [
                    'id' => (int) $product['id'],
                    'code' => $product['code'],
                    'name' => $product['name'],
                    'price' => (float) $product['price'],
                    'quantity' => isset($product['quantity']) ? (int) $product['quantity'] : 0,
                    'description' => $product['description'],
                    'specifications' => $specifications,
                    'usage' => $product['usage'],
                    'ingredients' => $product['ingredients'],
                    'category_id' => (int) $product['category_id'],
                    'category_name' => $product['category_name'],
                    'main_image' => null, // Images are stored in product_images table
                    'detail_images' => [], // Images are stored in product_images table
                    'created_at' => $product['created_at'],
                    'updated_at' => $product['updated_at'],
                    'deleted_at' => $product['deleted_at']
                ];
            }, $products);

            $this->sendResponse($formattedProducts, 'Deleted products retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve deleted products: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/products/category/{categoryId} - Lấy sản phẩm theo danh mục
    public function getByCategory($categoryId)
    {
        try {
            $products = $this->productModel->findByCategory($categoryId);

            $formattedProducts = array_map(function ($product) {
                $specifications = [];
                if (!empty($product['specifications'])) {
                    $specifications = json_decode($product['specifications'], true) ?: [];
                }

                return [
                    'id' => (int) $product['id'],
                    'code' => $product['code'],
                    'name' => $product['name'],
                    'price' => (float) $product['price'],
                    'quantity' => isset($product['quantity']) ? (int) $product['quantity'] : 0,
                    'description' => $product['description'],
                    'specifications' => $specifications,
                    'usage' => $product['usage'],
                    'ingredients' => $product['ingredients'],
                    'category_id' => (int) $product['category_id'],
                    'main_image' => null, // Images are stored in product_images table
                    'detail_images' => [], // Images are stored in product_images table
                    'created_at' => $product['created_at'],
                    'updated_at' => $product['updated_at']
                ];
            }, $products);

            $this->sendResponse($formattedProducts, 'Products retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve products: ' . $e->getMessage(), 500);
        }
    }
}

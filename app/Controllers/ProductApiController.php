<?php
require_once __DIR__ . '/ApiController.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';

class ProductApiController extends ApiController
{

    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    // GET /api/products - Lấy danh sách sản phẩm
    public function index()
    {
        try {
            $products = $this->productModel->findAllWithCategory();

            // Format dữ liệu để frontend dễ sử dụng
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
                    'description' => $product['description'],
                    'specifications' => $specifications,
                    'usage' => $product['usage'],
                    'ingredients' => $product['ingredients'],
                    'category_id' => (int) $product['category_id'],
                    'category_name' => $product['category_name'],
                    'main_image' => $product['main_image'],
                    'detail_images' => $product['detail_images'] ? json_decode($product['detail_images'], true) : [],
                    'created_at' => $product['created_at'],
                    'updated_at' => $product['updated_at']
                ];
            }, $products);

            $this->sendResponse($formattedProducts, 'Products retrieved successfully');
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

            $formattedProduct = [
                'id' => (int) $product['id'],
                'code' => $product['code'],
                'name' => $product['name'],
                'price' => (float) $product['price'],
                'description' => $product['description'],
                'specifications' => $specifications,
                'usage' => $product['usage'],
                'ingredients' => $product['ingredients'],
                'category_id' => (int) $product['category_id'],
                'main_image' => $product['main_image'],
                'detail_images' => $product['detail_images'] ? json_decode($product['detail_images'], true) : [],
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
            $data = $this->getJsonInput();

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

            // Format detail images
            if (isset($data['detail_images']) && is_array($data['detail_images'])) {
                $data['detail_images'] = json_encode($data['detail_images']);
            }

            $result = $this->productModel->create($data);

            if ($result) {
                $this->sendResponse(null, 'Product created successfully', 201);
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
            $data = $this->getJsonInput();

            // Check if product exists
            $existingProduct = $this->productModel->findById($id);
            if (!$existingProduct) {
                $this->sendError('Product not found', 404);
            }

            // Format specifications
            if (isset($data['specifications']) && is_array($data['specifications'])) {
                $data['specifications'] = json_encode($data['specifications']);
            }

            // Format detail images
            if (isset($data['detail_images']) && is_array($data['detail_images'])) {
                $data['detail_images'] = json_encode($data['detail_images']);
            }

            $result = $this->productModel->update($id, $data);

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
                    'description' => $product['description'],
                    'specifications' => $specifications,
                    'usage' => $product['usage'],
                    'ingredients' => $product['ingredients'],
                    'category_id' => (int) $product['category_id'],
                    'category_name' => $product['category_name'],
                    'main_image' => $product['main_image'],
                    'detail_images' => $product['detail_images'] ? json_decode($product['detail_images'], true) : [],
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
                    'description' => $product['description'],
                    'specifications' => $specifications,
                    'usage' => $product['usage'],
                    'ingredients' => $product['ingredients'],
                    'category_id' => (int) $product['category_id'],
                    'main_image' => $product['main_image'],
                    'detail_images' => $product['detail_images'] ? json_decode($product['detail_images'], true) : [],
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

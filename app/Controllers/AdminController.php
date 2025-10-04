<?php
namespace App\Controllers;

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/ProductImage.php';

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;

class AdminController extends Controller
{
    private $categoryModel;
    private $productModel;
    private $productImageModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->productModel = new Product();
        $this->productImageModel = new ProductImage();
    }

    // Dashboard
    public function dashboard()
    {
        $data = [
            'title' => 'Admin Dashboard',
            'categories_count' => count($this->categoryModel->findAll()),
            'products_count' => count($this->productModel->findAll())
        ];
        $this->render('admin/dashboard', $data);
    }

    // Category Management
    public function categories()
    {
        $categories = $this->categoryModel->findAll();
        $data = [
            'title' => 'Quản lý danh mục',
            'categories' => $categories
        ];
        $this->render('admin/categories/index', $data);
    }

    public function createCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description']
            ];

            if ($this->categoryModel->create($data)) {
                header('Location: /admin/categories?success=created');
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi tạo danh mục';
            }
        }

        $data = [
            'title' => 'Thêm danh mục mới'
        ];
        $this->render('admin/categories/create', $data);
    }

    public function editCategory($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            header('Location: /admin/categories?error=notfound');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description']
            ];

            if ($this->categoryModel->update($id, $data)) {
                header('Location: /admin/categories?success=updated');
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật danh mục';
            }
        }

        $data = [
            'title' => 'Sửa danh mục',
            'category' => $category
        ];
        $this->render('admin/categories/edit', $data);
    }

    public function deleteCategory($id)
    {
        if ($this->categoryModel->delete($id)) {
            header('Location: /admin/categories?success=deleted');
        } else {
            header('Location: /admin/categories?error=delete_failed');
        }
        exit;
    }

    // Product Management
    public function products()
    {
        $products = $this->productModel->findAllWithCategory();

        // Load images for each product
        foreach ($products as &$product) {
            $images = $this->productImageModel->findByProductId($product['id']);
            $product['images'] = $images;
            $product['main_image'] = null;
            $product['detail_images'] = [];

            foreach ($images as $image) {
                if ($image['is_main']) {
                    $product['main_image'] = $image['url'];
                } else {
                    $product['detail_images'][] = $image['url'];
                }
            }
        }

        $data = [
            'title' => 'Quản lý sản phẩm',
            'products' => $products
        ];
        $this->render('admin/products/index', $data);
    }

    public function createProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $this->generateProductCode(),
                'name' => $_POST['name'],
                'price' => $_POST['price'],
                'description' => $_POST['description'],
                'specifications' => json_encode([
                    'brand' => $_POST['brand'],
                    'brand_origin' => $_POST['brand_origin'],
                    'manufacturing_location' => $_POST['manufacturing_location'],
                    'volume' => $_POST['volume'],
                    'skin_type' => $_POST['skin_type']
                ]),
                'usage' => $_POST['usage'],
                'ingredients' => $_POST['ingredients'],
                'category_id' => $_POST['category_id']
            ];

            if ($this->productModel->create($data)) {
                header('Location: /admin/products?success=created');
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi tạo sản phẩm';
            }
        }

        $categories = $this->categoryModel->findAll();
        $data = [
            'title' => 'Thêm sản phẩm mới',
            'categories' => $categories
        ];
        $this->render('admin/products/create', $data);
    }

    public function editProduct($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            header('Location: /admin/products?error=notfound');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'price' => $_POST['price'],
                'description' => $_POST['description'],
                'specifications' => json_encode([
                    'brand' => $_POST['brand'],
                    'brand_origin' => $_POST['brand_origin'],
                    'manufacturing_location' => $_POST['manufacturing_location'],
                    'volume' => $_POST['volume'],
                    'skin_type' => $_POST['skin_type']
                ]),
                'usage' => $_POST['usage'],
                'ingredients' => $_POST['ingredients'],
                'category_id' => $_POST['category_id']
            ];

            if ($this->productModel->update($id, $data)) {
                header('Location: /admin/products?success=updated');
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật sản phẩm';
            }
        }

        // Load images for the product
        $images = $this->productImageModel->findByProductId($id);
        $product['images'] = $images;
        $product['main_image'] = null;
        $product['detail_images'] = [];

        foreach ($images as $image) {
            if ($image['is_main']) {
                $product['main_image'] = $image['url'];
            } else {
                $product['detail_images'][] = $image['url'];
            }
        }

        $categories = $this->categoryModel->findAll();
        $data = [
            'title' => 'Sửa sản phẩm',
            'product' => $product,
            'categories' => $categories
        ];
        $this->render('admin/products/edit', $data);
    }

    public function viewProduct($id)
    {
        $product = $this->productModel->findWithCategory($id);

        if (!$product) {
            header('Location: /admin/products?error=notfound');
            exit;
        }

        // Load images for the product
        $images = $this->productImageModel->findByProductId($id);
        $product['images'] = $images;
        $product['main_image'] = null;
        $product['detail_images'] = [];

        foreach ($images as $image) {
            if ($image['is_main']) {
                $product['main_image'] = $image['url'];
            } else {
                $product['detail_images'][] = $image['url'];
            }
        }

        $data = [
            'title' => 'Chi tiết sản phẩm',
            'product' => $product
        ];
        $this->render('admin/products/view', $data);
    }

    public function deleteProduct($id)
    {
        if ($this->productModel->delete($id)) {
            header('Location: /admin/products?success=deleted');
        } else {
            header('Location: /admin/products?error=delete_failed');
        }
        exit;
    }

    private function generateProductCode()
    {
        return 'PRD-' . strtoupper(uniqid());
    }
}

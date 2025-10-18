<?php
namespace App\Controllers;

require_once __DIR__ . '/ApiController.php';
require_once __DIR__ . '/../Models/Category.php';

use App\Models\Category;

class CategoryApiController extends ApiController
{

    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    // GET /api/categories - Lấy danh sách danh mục
    public function index()
    {
        try {
            $categories = $this->categoryModel->findAll();

            $formattedCategories = array_map(function ($category) {
                return [
                    'id' => (int) $category['id'],
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'created_at' => $category['created_at'],
                    'updated_at' => $category['updated_at']
                ];
            }, $categories);

            $this->sendResponse($formattedCategories, 'Categories retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve categories: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/categories/{id} - Lấy danh mục theo ID
    public function show($id)
    {
        try {
            $category = $this->categoryModel->findById($id);

            if (!$category) {
                $this->sendError('Category not found', 404);
            }

            $formattedCategory = [
                'id' => (int) $category['id'],
                'name' => $category['name'],
                'description' => $category['description'],
                'created_at' => $category['created_at'],
                'updated_at' => $category['updated_at']
            ];

            $this->sendResponse($formattedCategory, 'Category retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve category: ' . $e->getMessage(), 500);
        }
    }

    // POST /api/categories - Tạo danh mục mới
    public function store()
    {
        try {
            $data = $this->getJsonInput();

            // Validate required fields
            $required = ['name'];
            $missing = $this->validateRequired($data, $required);

            if (!empty($missing)) {
                $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
            }

            $result = $this->categoryModel->create($data);

            if ($result) {
                $this->sendResponse(null, 'Category created successfully', 201);
            } else {
                $this->sendError('Failed to create category', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Failed to create category: ' . $e->getMessage(), 500);
        }
    }

    // PUT /api/categories/{id} - Cập nhật danh mục
    public function update($id)
    {
        try {
            $data = $this->getJsonInput();

            // Check if category exists
            $existingCategory = $this->categoryModel->findById($id);
            if (!$existingCategory) {
                $this->sendError('Category not found', 404);
            }

            $result = $this->categoryModel->update($id, $data);

            if ($result) {
                $this->sendResponse(null, 'Category updated successfully');
            } else {
                $this->sendError('Failed to update category', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Failed to update category: ' . $e->getMessage(), 500);
        }
    }

    // DELETE /api/categories/{id} - Xóa mềm danh mục
    public function destroy($id)
    {
        try {
            // Check if category exists
            $existingCategory = $this->categoryModel->findById($id);
            if (!$existingCategory) {
                $this->sendError('Category not found', 404);
            }

            // Check if category has active products
            if ($this->categoryModel->hasProducts($id)) {
                $this->sendError('Cannot delete category with existing products', 400);
            }

            $result = $this->categoryModel->delete($id);

            if ($result) {
                $this->sendResponse(null, 'Category soft deleted successfully');
            } else {
                $this->sendError('Failed to delete category', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Failed to delete category: ' . $e->getMessage(), 500);
        }
    }

    // POST /api/categories/{id}/restore - Khôi phục danh mục đã xóa
    public function restore($id)
    {
        try {
            // Check if category exists (including deleted ones)
            $existingCategory = $this->categoryModel->findById($id, true);
            if (!$existingCategory) {
                $this->sendError('Category not found', 404);
            }

            // Check if category is actually deleted
            if (empty($existingCategory['deleted_at'])) {
                $this->sendError('Category is not deleted', 400);
            }

            $result = $this->categoryModel->restore($id);

            if ($result) {
                $this->sendResponse(null, 'Category restored successfully');
            } else {
                $this->sendError('Failed to restore category', 500);
            }
        } catch (Exception $e) {
            $this->sendError('Failed to restore category: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/categories/deleted - Lấy danh sách danh mục đã xóa
    public function getDeleted()
    {
        try {
            $categories = $this->categoryModel->findDeleted();

            $formattedCategories = array_map(function ($category) {
                return [
                    'id' => (int) $category['id'],
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'created_at' => $category['created_at'],
                    'updated_at' => $category['updated_at'],
                    'deleted_at' => $category['deleted_at']
                ];
            }, $categories);

            $this->sendResponse($formattedCategories, 'Deleted categories retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve deleted categories: ' . $e->getMessage(), 500);
        }
    }
}

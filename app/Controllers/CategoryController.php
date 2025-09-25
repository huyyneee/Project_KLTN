<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Core\Controller;

class CategoryController extends Controller
{
    private $categoryModel;
    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }
    public function index()
    {
        $categories = $this->categoryModel->findAll();
        $this->render('categories/index', ['categories' => $categories]);
    }
}

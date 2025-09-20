<?php
namespace App\Controllers;

use App\Models\Category;
use App\Core\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = (new Category())->findAll();
        $this->render('categories/index', ['categories' => $categories]);
    }
}

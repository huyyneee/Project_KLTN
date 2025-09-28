<?php
namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Core\Controller;

class OrderItemController extends Controller
{
    public function index()
    {
    $orderItemModel = new OrderItemModel();
    $orderItems = $orderItemModel->findAll();
        $this->render('order_items/index', ['orderItems' => $orderItems]);
    }
}

<?php
namespace App\Controllers;

use App\Models\OrderItem;
use App\Core\Controller;

class OrderItemController extends Controller
{
    public function index()
    {
        $orderItems = (new OrderItem())->findAll();
        $this->render('order_items/index', ['orderItems' => $orderItems]);
    }
}

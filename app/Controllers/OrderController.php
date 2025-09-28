<?php
namespace App\Controllers;

use App\Models\OrderModel;
use App\Core\Controller;

class OrderController extends Controller
{
    public function index()
    {
    $orderModel = new OrderModel();
    $orders = $orderModel->findAll();
        $this->render('orders/index', ['orders' => $orders]);
    }
}

<?php
namespace App\Controllers;

use App\Models\Order;
use App\Core\Controller;

class OrderController extends Controller
{
    public function index()
    {
        $orders = (new Order())->findAll();
        $this->render('orders/index', ['orders' => $orders]);
    }
}

<?php
namespace App\Controllers;

use App\Models\OrderModel;
use App\Core\Controller;

class OrderController extends Controller
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        $orders = $this->orderModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['orders' => $orders]);
            return;
        }

        $this->render('orders/index', ['orders' => $orders]);
    }
}

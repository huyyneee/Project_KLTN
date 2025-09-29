<?php
namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Core\Controller;

class OrderItemController extends Controller
{
    private $orderItemModel;

    public function __construct()
    {
        $this->orderItemModel = new OrderItemModel();
    }

    public function index()
    {
        $orderItems = $this->orderItemModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['orderItems' => $orderItems]);
            return;
        }

        $this->render('order_items/index', ['orderItems' => $orderItems]);
    }
}

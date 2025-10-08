<?php
namespace App\Controllers;

use App\Models\AddressModel;
use App\Core\Controller;

class AddressController extends Controller
{
    private $addressModel;

    public function __construct()
    {
        $this->addressModel = new AddressModel();
    }

    public function index()
    {
        $this->requireAuth();
        $addresses = $this->addressModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['addresses' => $addresses]);
            return;
        }

        $this->render('addresses/index', ['addresses' => $addresses]);
    }
}

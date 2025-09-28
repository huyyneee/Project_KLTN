<?php
namespace App\Controllers;

use App\Models\AddressModel;
use App\Core\Controller;

class AddressController extends Controller
{
    public function index()
    {
    $addressModel = new AddressModel();
    $addresses = $addressModel->findAll();
        $this->render('addresses/index', ['addresses' => $addresses]);
    }
}

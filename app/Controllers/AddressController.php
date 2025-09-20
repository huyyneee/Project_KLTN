<?php
namespace App\Controllers;

use App\Models\Address;
use App\Core\Controller;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = (new Address())->findAll();
        $this->render('addresses/index', ['addresses' => $addresses]);
    }
}

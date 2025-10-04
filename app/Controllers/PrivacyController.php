<?php
namespace App\Controllers;

use App\Core\Controller;

class PrivacyController extends Controller
{
    public function index()
    {
        $this->render('privacy', []);
    }
}

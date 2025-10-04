<?php
namespace App\Controllers;

use App\Core\Controller;

class TermsController extends Controller
{
    public function index()
    {
        $this->render('terms', []);
    }
}

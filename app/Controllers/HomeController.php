<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
	public function index() {
		// render the home view
		$this->render('home');
	}
}


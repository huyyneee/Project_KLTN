<?php
require_once __DIR__ . '/../Core/Controller.php';

class HomeController extends Controller {
    public function index() {
        $this->view('home', ['title' => 'Trang chủ']);
    }
}

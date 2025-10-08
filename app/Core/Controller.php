<?php
namespace App\Core;

class Controller {
    protected function render($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \Exception("View {$view} not found");
        }
    }
    protected function redirect($url) { header('Location: ' . $url); exit(); }
    protected function json($data) { header('Content-Type: application/json'); echo json_encode($data); exit(); }
}
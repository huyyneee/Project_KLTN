<?php
namespace App\Core;

class Controller {
    protected function render($view, $data = []) {
        // Middleware-like: ensure categories are available to every view
        if (!isset($data['categories'])) {
            try {
                $catModel = new \App\Models\CategoryModel();
                $data['categories'] = $catModel->findAll();
            } catch (\Throwable $e) {
                // if loading categories fails, pass empty array to avoid view errors
                $data['categories'] = [];
            }
        }

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
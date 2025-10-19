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

    /**
     * Ensure PHP session is started and attempt to restore session from
     * remember-cookie values if present. This centralizes the session
     * restore logic so controllers can rely on it.
     */
    protected function ensureSession()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        // If session not set but cookies exist, attempt restore (same logic as header.php)
        if (empty($_SESSION['account_id']) && !empty($_COOKIE['account_id']) && !empty($_COOKIE['account_email'])) {
            $expiresOk = false;
            if (!empty($_COOKIE['account_expires']) && ctype_digit($_COOKIE['account_expires'])) {
                $expiresTs = (int) $_COOKIE['account_expires'];
                if ($expiresTs > time()) $expiresOk = true;
            }

            if ($expiresOk) {
                try {
                    $db = (new \App\Core\Database())->getConnection();
                    $stmt = $db->prepare('SELECT id, email, status FROM accounts WHERE id = :id LIMIT 1');
                    $stmt->execute([':id' => $_COOKIE['account_id']]);
                    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($row && ($row['email'] === $_COOKIE['account_email']) && ($row['status'] ?? 'active') === 'active') {
                        $_SESSION['account_id'] = $row['id'];
                        $_SESSION['account_email'] = $row['email'];
                    }
                } catch (\Throwable $e) {
                    // ignore and leave session empty
                }
            } else {
                // clear expired cookies
                setcookie('account_id', '', time() - 3600, '/', '', false, true);
                setcookie('account_email', '', time() - 3600, '/', '', false, true);
                setcookie('account_expires', '', time() - 3600, '/', '', false, true);
            }
        }
    }

    /**
     * Require that an account is authenticated for the current request.
     * If not authenticated, redirect to login page and include a return
     * URL so the app can return the user after successful login.
     *
     * @param string $loginPath Path to the login page (default: /login)
     */
    protected function requireAuth($loginPath = '/login')
    {
        $this->ensureSession();
        if (empty($_SESSION['account_id'])) {
            // build return URL using the current request URI
            $return = '/';
            if (!empty($_SERVER['REQUEST_URI'])) {
                $return = $_SERVER['REQUEST_URI'];
            }
            // only allow internal return targets (start with '/') to avoid open redirect
            if ($return === '' || strpos($return, '/') !== 0) {
                $return = '/';
            }
            $loc = $loginPath . '?return=' . urlencode($return);
            header('Location: ' . $loc);
            exit();
        }
    }
}
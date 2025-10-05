<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Core\Controller;

class ProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $products = $this->productModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['products' => $products]);
            return;
        }

        $this->render('products/index', ['products' => $products]);
    }

    /**
     * Show product detail. Expects ?product=<id>
     */
    public function show()
    {
        $id = isset($_GET['product']) ? (int) $_GET['product'] : 0;
        if ($id <= 0) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }

        // load main image if present
        $img = null;
        try {
            $db = (new \App\Core\Database())->getConnection();
            $stmt = $db->prepare('SELECT url FROM product_images WHERE product_id = :pid AND is_main = 1 LIMIT 1');
            $stmt->execute([':pid' => $id]);
            $r = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($r && !empty($r['url'])) {
                $img = $r['url'];
                // normalize relative -> absolute if needed (ProductModel already handles join for lists)
                if (strpos($img, '/') === 0) {
                    // try to build full URL using DB_HOST from config
                    if (function_exists('env')) {
                        $dbHost = env('DB_HOST');
                    } else {
                        $cfgPath = __DIR__ . '/../../config/config.php';
                        $cfg = file_exists($cfgPath) ? require $cfgPath : [];
                        $dbHost = $cfg['database']['host'] ?? null;
                    }
                    if ($dbHost) {
                        $img = 'http://' . $dbHost . ':8000' . $img;
                    }
                }
            }
        } catch (\Exception $e) {
            // ignore image load errors
        }

            $this->render('products/show_product', ['product' => $product, 'image' => $img]);
    }
}

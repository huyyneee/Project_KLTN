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

        // load all images for this product (include is_main = 0)
        $img = null;
        $images = [];
        try {
            $db = (new \App\Core\Database())->getConnection();
            $stmt = $db->prepare('SELECT id, url, is_main FROM product_images WHERE product_id = :pid ORDER BY is_main DESC, id ASC');
            $stmt->execute([':pid' => $id]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                if (!empty($r['url'])) $images[] = $r;
            }
        } catch (\Exception $e) {
            // ignore
        }

        // normalize image urls using helper function
        foreach ($images as &$im) {
            $u = trim($im['url'] ?? '');
            if (!empty($u)) {
                $im['url'] = \get_image_url($u);
            } else {
                $im['url'] = null;
            }
        }

        if (!empty($images)) $img = $images[0]['url'];

        $this->render('products/show_product', ['product' => $product, 'image' => $img, 'images' => $images]);
    }
}

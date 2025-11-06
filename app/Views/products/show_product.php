</blockquote><?php /** @var array $product */ ?>
<?php /** @var string|null $image */ ?>
<?php /** @var array $images */ ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
$imagesList = $images ?? [];
if (empty($imagesList)) {
    $imagesList[] = ['id' => 0, 'url' => '/imgs/product/placeholder.svg', 'is_main' => 0];
}
$mainUrl = $image ?? ($imagesList[0]['url'] ?? '/imgs/product/placeholder.svg');
?>

<div class="max-w-6xl mx-auto p-6">
    <div class="shadow-[0px_4px_16px_0px_#14191A14] rounded-[20px] bg-white p-6">
        <div style="display:grid; grid-template-columns: 90px 520px 1fr; gap:28px; align-items:start;">
            <!-- thumbnails column -->
            <div style="overflow:auto; max-height:520px; padding-top:1px;">
                <?php foreach ($imagesList as $i => $thumb): ?>
                    <img data-full="<?= htmlspecialchars($thumb['url']) ?>" class="thumb-img" src="<?= htmlspecialchars($thumb['url']) ?>" alt="thumb" width="72" height="72" style="display:block; margin-bottom:14px; border:2px solid #fff; border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,0.08); cursor:pointer; <?= $i === 0 ? 'border-color:#326E51; box-shadow:0 0 0 3px rgba(50,110,81,0.08);' : '' ?>">
                <?php endforeach; ?>
            </div>

            <!-- main image column (larger) -->
            <div style="display:flex; align-items:center; justify-content:center;">
                <div style="position:relative; width:450px; height:450px;">
                    <!-- small badges over image (NowFree + brand) -->

                    <img id="main-image" src="<?= htmlspecialchars($mainUrl) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:480px; height:480px; object-fit:contain; border-radius:8px; background:#fff;">
                    <div id="zoomed" style="display:none; position:absolute; top:0; right:-500px; width:480px; height:480px; border:3px solid #999; background-size:1600px 1600px; border-radius:8px; z-index:200; background-repeat:no-repeat;"></div>
                </div>
            </div>

            <!-- product meta column -->
            <div>
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <img src="/assets/images/logonowfree.png" alt="NowFree" style="height:22px;">
                </div>

                <h1 style="font-size:20px; margin:4px 0 8px; color:#111; line-height:1.2;"><?= htmlspecialchars($product['name'] ?? '') ?></h1>

                <div style="font-weight:bold; color:#ff6600; font-size:20px; margin-bottom:6px;"><?= isset($product['price']) ? number_format($product['price']) . ' ₫' : '' ?></div>
                <div style="color:#777; font-size:13px; margin-bottom:20px;">
                    <h2>(Đã bao gồm VAT)</h2>
                </div>
                <?php
                $qty = (int)($product['quantity'] ?? 0);
                if ($qty > 10) {
                    echo "<div style='color:#777;font-size:15px;font-weight:400;margin-bottom:10px;'>Còn lại: $qty sản phẩm</div>";
                } elseif ($qty > 0) {
                    echo "<div style='color:#777;font-size:15px;font-weight:400;margin-bottom:10px;'>Chỉ còn lại $qty sản phẩm!</div>";
                } else {
                    echo "<div style='color:#cc0000;font-size:15px;font-weight:400;margin-bottom:10px;'>Hết hàng</div>";
                }
                ?>
                <!-- quantity (compact) -->
                <div style="margin-bottom:35px;">

                    <div style="display:flex; align-items:center; gap:14px;">
                        <a style="font-size:13px; color:#444; margin-bottom:6px;">Số lượng:</a>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <button id="qty-decr" type="button" style="width:34px;height:34px;border:1px solid #e6e6e6;background:#fff;border-radius:6px; font-size:18px; color:#333; cursor:pointer;">−</button>
                            <input id="qty" type="number" value="1" min="1" style="width:64px;height:36px;text-align:center;border:1px solid #e6e6e6;border-radius:6px; font-size:16px;">
                            <button id="qty-incr" type="button" style="width:34px;height:34px;border:1px solid #e6e6e6;background:#fff;border-radius:6px; font-size:18px; color:#333; cursor:pointer;">+</button>
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:10px; align-items:flex-start; margin-bottom:20px;">
                    <img src="/assets/images/logonowfree.png" alt="NowFree" style="height:18px; margin-top:2px;">
                </div>
                <div style="font-size:15px; color:#444;margin-bottom:20px">
                    <div style="color:#ff7a00; font-weight:600;">Giao Nhanh Miễn Phí 2H</div>
                    <div style="color:#777;">Bạn muốn nhận hàng trước <strong>10h</strong> ngày mai. Đặt hàng trước <strong>24h</strong></div>
                </div>

                <div style="margin-bottom:12px;">
                    <div style="display:flex; gap:12px;">
                        <button id="add-to-cart" type="button"
                            data-product-id="<?= $product['id'] ?>"
                            data-product-price="<?= $product['price'] ?>"
                            style="display:inline-flex; align-items:center; gap:8px; background:#ff7a00;color:#fff;border:none;padding:10px 18px;border-radius:6px;cursor:pointer; box-shadow:0 2px 0 rgba(0,0,0,0.04);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M7 4h-2l-1 2" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10 20a1 1 0 100-2 1 1 0 000 2zM18 20a1 1 0 100-2 1 1 0 000 2z" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M3 6h2l1.5 9h11l1.5-6.5H8" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            GIỎ HÀNG
                        </button>
                        <button id="buy-now" type="button"
                            data-product-id="<?= $product['id'] ?>"
                            data-product-price="<?= $product['price'] ?>"
                            style="background:#ff7a00;color:#fff;border:none;padding:10px 18px;border-radius:6px;cursor:pointer; box-shadow:0 2px 0 rgba(0,0,0,0.04);">MUA NGAY NOWFREE 2H</button>
                    </div>
                </div>

                <!-- product description (moved below as full-width card) -->

                <!-- product specifications -->
                <?php
                // build a specs array from common fields and optional `specifications` blob
                $specs = [];
                $specs['Barcode'] = $product['barcode'] ?? $product['code'] ?? '';
                $specs['Thương Hiệu'] = $product['brand'] ?? $product['manufacturer'] ?? $product['brand_name'] ?? '';
                $specs['Xuất xứ thương hiệu'] = $product['brand_origin'] ?? $product['origin'] ?? '';
                $specs['Nơi sản xuất'] = $product['made_in'] ?? $product['place_manufacture'] ?? '';
                $specs['Loại da'] = $product['skin_type'] ?? '';
                $specs['Dung Tích'] = $product['volume'] ?? $product['size'] ?? $product['capacity'] ?? '';

                // try to merge structured specifications if present
                if (!empty($product['specifications'])) {
                    $raw = trim($product['specifications']);
                    // helper to map common keys to Vietnamese labels
                    $mapKey = function ($k) {
                        $k = trim((string)$k);
                        $m = [
                            'brand' => 'Thương Hiệu',
                            'manufacturer' => 'Thương Hiệu',
                            'brand_name' => 'Thương Hiệu',
                            'origin' => 'Xuất xứ thương hiệu',
                            'brand_origin' => 'Xuất xứ thương hiệu',
                            'made_in' => 'Nơi sản xuất',
                            'place_manufacture' => 'Nơi sản xuất',
                            'volume' => 'Dung Tích',
                            'size' => 'Dung Tích',
                            'capacity' => 'Dung Tích',
                            'skin_type' => 'Loại da',
                            'barcode' => 'Barcode',
                        ];
                        $lk = strtolower($k);
                        if (isset($m[$lk])) return $m[$lk];
                        // fallback: replace _ and - with space and uppercase words
                        $label = str_replace(['_', '-'], ' ', $k);
                        $label = trim($label);
                        return mb_convert_case($label, MB_CASE_TITLE, 'UTF-8');
                    };

                    $decoded = @json_decode($raw, true);
                    if (is_array($decoded)) {
                        // merge decoded values (override defaults)
                        foreach ($decoded as $k => $v) {
                            // skip numeric-indexed arrays
                            if (is_int($k)) continue;
                            $label = $mapKey($k);
                            $specs[$label] = is_array($v) ? implode(', ', $v) : (string)$v;
                        }
                    } else {
                        // try parse lines like "Key: value"
                        $lines = preg_split('/\r?\n/', $raw);
                        foreach ($lines as $line) {
                            if (strpos($line, ':') !== false) {
                                list($k, $v) = array_map('trim', explode(':', $line, 2));
                                if ($k !== '') {
                                    $label = $mapKey($k);
                                    $specs[$label] = $v;
                                }
                            }
                        }
                    }
                }

                // filter out empty values
                $specs = array_filter($specs, function ($v) {
                    return trim((string)$v) !== '';
                });
                ?>

                <!-- specs moved to full-width below -->

                <?php
                // try to find ingredients from common fields or specifications
                $ingredients_raw = null;
                if (!empty($product['ingredients'])) $ingredients_raw = $product['ingredients'];
                elseif (!empty($product['composition'])) $ingredients_raw = $product['composition'];
                elseif (!empty($product['ingredients_text'])) $ingredients_raw = $product['ingredients_text'];
                else {
                    // check decoded specifications for an ingredients key
                    if (!empty($product['specifications'])) {
                        $try = @json_decode($product['specifications'], true);
                        if (is_array($try)) {
                            foreach (['ingredients', 'ingredient', 'thanh_phan', 'thành_phần', 'thànhphần'] as $k) {
                                if (!empty($try[$k])) {
                                    $ingredients_raw = $try[$k];
                                    break;
                                }
                            }
                        }
                    }
                }

                // normalize and split into items
                $mainIngredients = [];
                $fullIngredients = '';
                if ($ingredients_raw) {
                    // remove HTML tags
                    $txt = strip_tags($ingredients_raw);
                    // normalize separators: replace bullets and ; with comma
                    $txt = str_replace(["•", "-", ";", "\r\n", "\n"], [",", ",", ",", ",", ","], $txt);
                    // collapse multiple commas
                    $txt = preg_replace('/,+/', ',', $txt);
                    // split by comma
                    $parts = array_map('trim', explode(',', $txt));
                    $parts = array_filter($parts, function ($p) {
                        return $p !== '';
                    });
                    // first 3 as main ingredients
                    $mainIngredients = array_slice($parts, 0, 3);
                    $fullIngredients = implode(', ', $parts);
                }
                ?>

                <!-- ingredients moved to full-width below -->

            </div>
        </div>
    </div>

    <!-- tabs (Mô tả | Thông số | HDSD | Thành phần) -->
    <div style="max-width:1100px; margin:12px auto 6px;">
        <div id="product-tabs" style="display:flex; gap:12px;">
            <a href="#section-description" data-target="section-description" class="product-tab" style="flex:1; padding:18px; text-align:center; border:2px solid #111; border-radius:8px; text-decoration:none; color:#111; font-weight:700;">MÔ TẢ</a>
            <a href="#section-specs" data-target="section-specs" class="product-tab" style="flex:1; padding:18px; text-align:center; border:2px solid #111; border-radius:8px; text-decoration:none; color:#111; font-weight:700;">THÔNG SỐ</a>
            <a href="#section-usage" data-target="section-usage" class="product-tab" style="flex:1; padding:18px; text-align:center; border:2px solid #111; border-radius:8px; text-decoration:none; color:#111; font-weight:700;">HDSD</a>
            <a href="#section-ingredients" data-target="section-ingredients" class="product-tab" style="flex:1; padding:18px; text-align:center; border:2px solid #111; border-radius:8px; text-decoration:none; color:#111; font-weight:700;">THÀNH PHẦN</a>
        </div>
    </div>

    <!-- full-width description card (collapsible: show 30% by default) -->
    <div id="section-description" style="max-width:1100px; margin:18px auto 36px;">
        <div style="background:#fff; border-radius:12px; padding:18px; box-shadow:0 6px 20px rgba(16,24,40,0.06);">
            <h3 style="margin:0 0 12px; font-size:16px;">Mô tả</h3>
            <div id="description-content" style="color:#444; line-height:1.7; overflow:hidden; transition:max-height 0.35s ease;">
                <?= $product['description'] ?? '' ?>
            </div>
            <div style="text-align:center; margin-top:10px;">
                <button id="desc-toggle" type="button" aria-expanded="false" style="background:transparent;border:1px solid #ddd;padding:8px 12px;border-radius:6px;cursor:pointer;">▼ Xem thêm</button>
            </div>
        </div>
    </div>

    <?php
    // find usage instructions from product fields or specifications
    $usage_raw = '';
    if (!empty($product['usage'])) $usage_raw = $product['usage'];
    elseif (!empty($product['how_to_use'])) $usage_raw = $product['how_to_use'];
    elseif (!empty($product['usage_instructions'])) $usage_raw = $product['usage_instructions'];
    else {
        if (!empty($product['specifications'])) {
            $sdec = @json_decode($product['specifications'], true);
            if (is_array($sdec)) {
                foreach (['usage', 'how_to_use', 'usage_instructions', 'huong_dan_su_dung', 'hướng_dẫn'] as $k) {
                    if (!empty($sdec[$k])) {
                        $usage_raw = $sdec[$k];
                        break;
                    }
                }
            } else {
                // try to parse lines that start with 'Hướng' or contain 'sử dụng'
                $txt = trim($product['specifications']);
                if (stripos($txt, 'Hướng dẫn') !== false || stripos($txt, 'sử dụng') !== false) {
                    $usage_raw = $txt;
                }
            }
        }
    }
    ?>

    <?php if (!empty(trim($usage_raw))): ?>
        <div style="max-width:1100px; margin:8px auto 36px;">
            <div style="background:#fff; border-radius:12px; padding:18px; box-shadow:0 6px 20px rgba(16,24,40,0.06);">
                <h3 style="margin:0 0 12px; font-size:16px;">Hướng dẫn sử dụng</h3>
                <div style="color:#444; line-height:1.7;">
                    <?= $usage_raw ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- full-width Thông số sản phẩm -->
    <?php if (!empty($specs)): ?>
        <div id="section-specs" style="max-width:1100px; margin:18px auto 24px;">
            <div style="background:#fff; border-radius:12px; padding:18px; box-shadow:0 6px 20px rgba(16,24,40,0.06);">
                <h3 style="margin:0 0 12px; font-size:16px;">Thông số sản phẩm</h3>
                <table style="width:100%; border-collapse:collapse;">
                    <?php foreach ($specs as $label => $value): ?>
                        <tr style="border-top:1px solid #f0f0f0;">
                            <td style="padding:12px 16px; width:40%; background:#fafafa; vertical-align:top; font-size:14px; color:#333;"><strong><?= htmlspecialchars($label) ?></strong></td>
                            <td style="padding:12px 16px; vertical-align:top; font-size:14px; color:#555;"><?= htmlspecialchars($value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- full-width Thành phần sản phẩm -->
    <?php if (!empty($ingredients_raw)): ?>
        <div id="section-ingredients" style="max-width:1100px; margin:18px auto 36px;">
            <div style="background:#fff; border-radius:12px; padding:18px; box-shadow:0 6px 20px rgba(16,24,40,0.06);">
                <h3 style="margin:0 0 8px; font-size:16px;">Thành phần sản phẩm</h3>
                <div style="margin-top:8px; font-weight:600;">1. <?= htmlspecialchars($product['name'] ?? '') ?></div>

                <?php if (!empty($mainIngredients)): ?>
                    <div style="margin-top:10px; font-weight:600;">Thành phần chính:</div>
                    <ul style="margin:6px 0 12px 20px; color:#333;">
                        <?php foreach ($mainIngredients as $mi): ?>
                            <li><?= htmlspecialchars($mi) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div style="font-weight:600; margin-top:6px;">Thành phần đầy đủ:</div>
                <p style="margin-top:8px; color:#444; line-height:1.6;"><?= htmlspecialchars($fullIngredients) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <script>
        (function() {
            const thumbs = document.querySelectorAll('.thumb-img');
            const main = document.getElementById('main-image');
            const zoomed = document.getElementById('zoomed');
            const mainWrap = main.parentElement; // position:relative wrapper
            const ZOOM_FACTOR = 2.2; // how much larger the background should be

            // Quantity controls
            const qtyInput = document.getElementById('qty');
            const btnDecr = document.getElementById('qty-decr');
            const btnIncr = document.getElementById('qty-incr');

            function normalizeQty(v) {
                v = parseInt(v, 10);
                if (isNaN(v) || v < 1) return 1;
                return v;
            }

            function updateQtyUI(v) {
                const n = normalizeQty(v);
                qtyInput.value = n;
                if (n <= 1) {
                    btnDecr.setAttribute('disabled', 'disabled');
                    btnDecr.style.opacity = '0.6';
                    btnDecr.style.cursor = 'not-allowed';
                } else {
                    btnDecr.removeAttribute('disabled');
                    btnDecr.style.opacity = '1';
                    btnDecr.style.cursor = 'pointer';
                }
            }

            if (qtyInput) {
                // init
                updateQtyUI(qtyInput.value || 1);

                btnDecr.addEventListener('click', function() {
                    updateQtyUI(normalizeQty(qtyInput.value) - 1);
                });
                btnIncr.addEventListener('click', function() {
                    updateQtyUI(normalizeQty(qtyInput.value) + 1);
                });
                qtyInput.addEventListener('change', function() {
                    updateQtyUI(this.value);
                });
                qtyInput.addEventListener('input', function() {
                    // allow only digits while typing
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            function setActiveThumb(el) {
                thumbs.forEach(function(x) {
                    x.style.outline = 'none';
                });
                if (el) el.style.outline = '2px solid #326E51';
            }

            thumbs.forEach(function(t, idx) {
                t.addEventListener('click', function() {
                    const full = this.getAttribute('data-full');
                    if (full) {
                        main.src = full;
                        // update zoom background immediately
                        zoomed.style.backgroundImage = 'url(' + full + ')';
                        // once image loads, update background-size
                        main.addEventListener('load', function _upd() {
                            updateBackgroundSize();
                            main.removeEventListener('load', _upd);
                        });
                    }
                    setActiveThumb(this);
                });
                // keep mouseover on thumbnail only to preview zoom image but don't show zoom area
                t.addEventListener('mouseover', function() {
                    const full = this.getAttribute('data-full');
                    if (full) zoomed.style.backgroundImage = 'url(' + full + ')';
                });
            });

            // compute and set background-size based on the natural image size and zoom factor
            function updateBackgroundSize() {
                const natW = main.naturalWidth || main.width;
                const natH = main.naturalHeight || main.height;
                // fallback to element size if natural not available
                const bW = Math.round(natW * ZOOM_FACTOR);
                const bH = Math.round(natH * ZOOM_FACTOR);
                zoomed.style.backgroundSize = bW + 'px ' + bH + 'px';
            }

            // show/position zoom on mousemove over the main image
            function onMove(e) {
                const rect = main.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const px = Math.max(0, Math.min(1, x / rect.width));
                const py = Math.max(0, Math.min(1, y / rect.height));

                // background-position expects percentages of the background image
                const posX = (px * 100).toFixed(2) + '%';
                const posY = (py * 100).toFixed(2) + '%';
                zoomed.style.backgroundPosition = posX + ' ' + posY;
            }

            // show/hide handlers
            main.addEventListener('mouseenter', function() {
                // ensure background is set
                if (!zoomed.style.backgroundImage) zoomed.style.backgroundImage = 'url(' + main.src + ')';
                updateBackgroundSize();
                zoomed.style.display = 'block';
            });
            main.addEventListener('mouseleave', function() {
                zoomed.style.display = 'none';
            });
            main.addEventListener('mousemove', function(e) {
                onMove(e);
            });

            // Initialize
            zoomed.style.backgroundImage = 'url(' + main.src + ')';
            // set an initial background-size once image is loaded
            if (main.complete) updateBackgroundSize();
            else main.addEventListener('load', updateBackgroundSize);

            // mark first thumb active
            if (thumbs.length) setActiveThumb(thumbs[0]);

            // Tabs: smooth scroll and active highlight
            const tabs = document.querySelectorAll('.product-tab');
            const sections = {};
            tabs.forEach(function(tab) {
                const t = tab.getAttribute('data-target');
                if (!t) return;
                const el = document.getElementById(t);
                if (el) sections[t] = el;
                tab.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    if (!el) return;
                    const top = el.getBoundingClientRect().top + window.scrollY - 20; // offset
                    window.scrollTo({
                        top: top,
                        behavior: 'smooth'
                    });
                });
            });

            function onScrollTabs() {
                const scrollY = window.scrollY + 80; // header offset
                let active = null;
                for (const id in sections) {
                    const r = sections[id].getBoundingClientRect();
                    const top = sections[id].offsetTop;
                    if (scrollY >= top) active = id;
                }
                tabs.forEach(function(tab) {
                    if (tab.getAttribute('data-target') === active) {
                        tab.style.background = '#111';
                        tab.style.color = '#fff';
                    } else {
                        tab.style.background = 'transparent';
                        tab.style.color = '#111';
                    }
                });
            }
            window.addEventListener('scroll', onScrollTabs);
            // run once
            onScrollTabs();

            // Description collapse/expand
            const descContent = document.getElementById('description-content');
            const descToggle = document.getElementById('desc-toggle');
            if (descContent && descToggle) {
                // compute full height after images/styles loaded
                function setCollapsed() {
                    // allow content to size itself
                    descContent.style.maxHeight = 'none';
                    const fullH = descContent.scrollHeight;
                    const collapsedH = Math.round(fullH * 0.30);
                    // if content small, keep it expanded
                    if (fullH <= 200) {
                        descContent.style.maxHeight = fullH + 'px';
                        descToggle.style.display = 'none';
                        return;
                    }
                    // set collapsed height
                    descContent.dataset.fullHeight = fullH;
                    descContent.dataset.collapsedHeight = collapsedH;
                    descContent.style.maxHeight = collapsedH + 'px';
                    descToggle.setAttribute('aria-expanded', 'false');
                    descToggle.textContent = '▼ Xem thêm';
                }

                // initialize after small delay to allow images to load
                setTimeout(setCollapsed, 300);
                window.addEventListener('load', setCollapsed);

                descToggle.addEventListener('click', function() {
                    const full = parseInt(descContent.dataset.fullHeight || descContent.scrollHeight, 10);
                    const col = parseInt(descContent.dataset.collapsedHeight || Math.round(full * 0.3), 10);
                    const expanded = descToggle.getAttribute('aria-expanded') === 'true';
                    if (expanded) {
                        // collapse
                        descContent.style.maxHeight = col + 'px';
                        descToggle.setAttribute('aria-expanded', 'false');
                        descToggle.textContent = '▼ Xem thêm';
                    } else {
                        // expand
                        descContent.style.maxHeight = full + 'px';
                        descToggle.setAttribute('aria-expanded', 'true');
                        descToggle.textContent = '▲ Thu gọn';
                    }
                });
            }
        })();

        // // cart
        // const addToCartBtn = document.getElementById('add-to-cart');
        // if (addToCartBtn) {
        //     addToCartBtn.addEventListener('click', function() {
        //         const productId = this.getAttribute('data-product-id');
        //         const quantity = parseInt(document.getElementById('qty').value) || 1;
        //         const price = parseFloat(this.getAttribute('data-product-price'));

        //         fetch('/cart/add', {
        //                 method: 'POST',
        //                 headers: {
        //                     'Content-Type': 'application/x-www-form-urlencoded'
        //                 },
        //                 body: `product_id=${encodeURIComponent(productId)}&quantity=${encodeURIComponent(quantity)}&price=${encodeURIComponent(price)}`
        //             })
        //             .then(res => res.json())
        //             .then(data => {
        //                 if (data.success) {
        //                     alert('Sản phẩm đã được thêm vào giỏ hàng!');
        //                 } else if (data.redirect) {
        //                     window.location.href = data.redirect;
        //                 } else if (data.error) {
        //                     alert('Lỗi: ' + data.error);
        //                 }
        //             })
        //             .catch(err => {
        //                 console.error(err);
        //                 alert('Có lỗi xảy ra, thử lại sau.');
        //             });
        //     });
        // }

        // // buy now
        // const buyNowBtn = document.getElementById('buy-now');
        // if (buyNowBtn) {
        //     buyNowBtn.addEventListener('click', function() {
        //         const productId = this.getAttribute('data-product-id');
        //         const quantity = parseInt(document.getElementById('qty').value) || 1;
        //         const price = parseFloat(this.getAttribute('data-product-price'));

        //         fetch('/cart/add', {
        //                 method: 'POST',
        //                 headers: {
        //                     'Content-Type': 'application/x-www-form-urlencoded'
        //                 },
        //                 body: `product_id=${encodeURIComponent(productId)}&quantity=${encodeURIComponent(quantity)}&price=${encodeURIComponent(price)}`
        //             })
        //             .then(res => res.json())
        //             .then(data => {
        //                 if (data.success) {
        //                     // Thêm thành công, chuyển đến checkout
        //                     window.location.href = '/checkout';
        //                 } else if (data.redirect) {
        //                     window.location.href = data.redirect;
        //                 } else if (data.error) {
        //                     alert('Lỗi: ' + data.error);
        //                 }
        //             })
        //             .catch(err => {
        //                 console.error(err);
        //                 alert('Có lỗi xảy ra, thử lại sau.');
        //             });
        //     });
        // }
    </script>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</div>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-7xl mx-auto my-8">
    <div class="mb-6">
        <!-- Tên danh mục sẽ hiển thị ở tiêu đề trang bên dưới -->
    </div>

    <div id="category-content">
        <?php if (!$category): ?>
            <div class="text-center text-red-600">Danh mục không tìm thấy.</div>
        <?php else: ?>
            <h1 id="category-title" class="text-2xl font-bold mb-4"><?= htmlspecialchars($category['name'] ?? 'Danh mục') ?></h1>
            <?php if (empty($products)): ?>
                <div id="category-empty" class="text-gray-500">Không có sản phẩm nào trong danh mục này.</div>
            <?php else: ?>
                <div id="products-grid" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.75rem;">
                    <?php
                        // ensure we only render up to pagination perPage (default 16)
                        $perPage = $pagination['perPage'] ?? 16;
                        $renderProducts = array_slice($products, 0, $perPage);
                    ?>
                    <?php foreach ($renderProducts as $p): ?>
                        <div class="px-1 mb-3">
                            <div class="bg-white rounded shadow overflow-hidden text-xs" style="height:100%;">
                            <?php
                                // use our image proxy endpoint which will try DB url, local file or remote
                                $imgEndpoint = '/image.php?product=' . urlencode($p['id']);
                            ?>
                            <?php
                                $imgSrc = $imgEndpoint;
                                if (!empty($p['image_url'])) {
                                    $imgSrc = $p['image_url'];
                                }
                            ?>
                                <a href="/san-pham?product=<?= (int)$p['id'] ?>" class="block product-link">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p['name'] ?? '') ?>" class="w-full h-24 object-cover transition-transform duration-150 hover:scale-105" onerror="this.onerror=null;this.src='/assets/images/no-image.png'"/>
                                </a>
                                <div class="p-2">
                                    <div class="font-semibold text-xs line-clamp-2"><?= htmlspecialchars($p['name'] ?? '') ?></div>
                                    <div class="text-green-700 font-bold mt-1 text-sm"><?= isset($p['price']) ? number_format($p['price']) . ' ₫' : '' ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php // pagination controls ?>
                <?php $pag = $pagination ?? ['page' => 1, 'lastPage' => 1]; ?>
                <?php
                    // determine URL param key and identifier for building links
                    $paramKey = null;
                    if (isset($_GET['id'])) {
                        $paramKey = 'id';
                        $identifier = $_GET['id'];
                    } elseif (isset($_GET['cat'])) {
                        $paramKey = 'cat';
                        $identifier = $_GET['cat'];
                    } else {
                        // fallback to category object
                        if (!empty($category['id'])) {
                            $paramKey = 'id';
                            $identifier = $category['id'];
                        } else {
                            $paramKey = 'cat';
                            $identifier = $category['name'] ?? '';
                        }
                    }
                ?>
                <div id="category-pagination" class="mt-4 flex items-center justify-center space-x-2">
                    <?php if ($pag['page'] > 1): ?>
                        <a href="/danh-muc?<?= htmlspecialchars($paramKey) ?>=<?= urlencode($identifier) ?>&page=<?= $pag['page'] - 1 ?>" data-page="<?= $pag['page'] - 1 ?>" class="px-3 py-1 bg-white border rounded prev-page">&laquo; Prev</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= ($pag['lastPage'] ?? 1); $i++): ?>
                        <a href="/danh-muc?<?= htmlspecialchars($paramKey) ?>=<?= urlencode($identifier) ?>&page=<?= $i ?>" data-page="<?= $i ?>" class="px-3 py-1 <?= ($i == ($pag['page'] ?? 1)) ? 'bg-green-700 text-white' : 'bg-white' ?> border rounded page-btn"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if (($pag['page'] ?? 1) < ($pag['lastPage'] ?? 1)): ?>
                        <a href="/danh-muc?<?= htmlspecialchars($paramKey) ?>=<?= urlencode($identifier) ?>&page=<?= ($pag['page'] ?? 1) + 1 ?>" data-page="<?= ($pag['page'] ?? 1) + 1 ?>" class="px-3 py-1 bg-white border rounded next-page">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function setActiveButton(btn) {
        document.querySelectorAll('.category-pill').forEach(function(b){
            b.classList.remove('bg-green-700','text-white');
            b.classList.add('bg-white','text-gray-700');
        });
        if (btn) {
            btn.classList.add('bg-green-700','text-white');
        }
    }

    async function loadCategoryById(identifier, page = 1, pushUrl=true) {
        console.log('loadCategoryById called', {identifier: identifier, page: page});
        try {
            // identifier can be numeric id or category name (cat)
            let paramKey = /^\d+$/.test(String(identifier)) ? 'id' : 'cat';
            // if identifier is falsy, try to read from current URL
            if (!identifier) {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('id')) {
                    paramKey = 'id';
                    identifier = urlParams.get('id');
                } else if (urlParams.get('cat')) {
                    paramKey = 'cat';
                    identifier = urlParams.get('cat');
                }
            }
            const url = '/danh-muc?xhr=1&' + encodeURIComponent(paramKey) + '=' + encodeURIComponent(identifier) + '&page=' + encodeURIComponent(page);
            const res = await fetch(url);
            console.log('fetch url', url, 'status', res.status);
            const data = await res.json();
            try {
                updateCategoryContent(data);
            } catch (uiErr) {
                console.error('Error updating category content', uiErr);
            }
            if (pushUrl) {
                const pushPath = '/danh-muc?' + encodeURIComponent(paramKey) + '=' + encodeURIComponent(identifier) + '&page=' + encodeURIComponent(page);
                const stateObj = {};
                stateObj[paramKey] = identifier;
                stateObj.page = page;
                history.pushState(stateObj, '', pushPath);
            }
        } catch (e) {
            console.error('Error loading category', e);
        }
    }

    function updateCategoryContent(data) {
        const category = data.category;
        const products = data.products || [];
        const categories = data.categories || [];
        const pagination = data.pagination || null;

    // Update title/desc (guard elements in case they don't exist)
    const titleEl = document.getElementById('category-title');
    if (titleEl) titleEl.textContent = category?.name ?? 'Danh mục';
    const descEl = document.getElementById('category-desc');
    if (descEl) descEl.textContent = category?.description ?? '';

        const grid = document.getElementById('products-grid');
        if (!grid) return;
        grid.innerHTML = '';
                if (products.length === 0) {
            const emptyEl = document.getElementById('category-empty');
            if (emptyEl) {
                emptyEl.style.display = 'block';
            }
        } else {
            const emptyEl = document.getElementById('category-empty');
            if (emptyEl) {
                emptyEl.style.display = 'none';
            }
            // how many items max to show on this page
            const maxItems = (pagination && pagination.perPage) ? pagination.perPage : 16;
            products.slice(0, maxItems).forEach(function(p){
                // create card directly; grid handles 4 columns
                const wrapper = document.createElement('div');
                wrapper.className = 'px-1 mb-3';

                const card = document.createElement('div');
                card.className = 'bg-white rounded shadow overflow-hidden text-xs';
                card.style.height = '100%';

                // image wrapped in anchor to product detail
                const anchor = document.createElement('a');
                anchor.href = '/san-pham?product=' + encodeURIComponent(p.id);
                anchor.className = 'block product-link';

                const img = document.createElement('img');
                // prefer image_url from API, fall back to image proxy by product id
                img.src = p.image_url ? p.image_url : ('/image.php?product=' + encodeURIComponent(p.id));
                img.alt = p.name ?? '';
                img.className = 'w-full h-24 object-cover';
                img.style.transition = 'transform .15s';
                img.addEventListener('error', function(){ this.onerror = null; this.src = '/assets/images/no-image.png'; });
                img.addEventListener('mouseover', function(){ this.style.transform = 'scale(1.05)'; });
                img.addEventListener('mouseout', function(){ this.style.transform = 'scale(1)'; });

                const body = document.createElement('div');
                body.className = 'p-2';

                const title = document.createElement('div');
                title.className = 'font-semibold text-xs line-clamp-2';
                title.textContent = p.name ?? '';

                const price = document.createElement('div');
                price.className = 'text-green-700 font-bold mt-1 text-sm';
                price.textContent = p.price ? new Intl.NumberFormat().format(p.price) + ' ₫' : '';

                body.appendChild(title);
                body.appendChild(price);
                anchor.appendChild(img);
                card.appendChild(anchor);
                card.appendChild(body);
                wrapper.appendChild(card);
                grid.appendChild(wrapper);
            });
            // update pagination UI if provided
                if (pagination) {
                    renderPagination(pagination, category);
            }
        }
    }

    function renderPagination(pagination) {
        const container = document.getElementById('category-pagination');
        if (!container) return;
        container.innerHTML = '';
        const page = pagination.page || 1;
        const last = pagination.lastPage || 1;
        // determine paramKey and identifier for building hrefs
        const urlParams = new URLSearchParams(window.location.search);
        let paramKey = urlParams.has('id') ? 'id' : (urlParams.has('cat') ? 'cat' : null);
        let identifier = urlParams.get('id') || urlParams.get('cat') || (category && (category.id || category.name) ? (category.id || category.name) : '');

        if (page > 1) {
            const prev = document.createElement('a');
            prev.className = 'px-3 py-1 bg-white border rounded prev-page';
            prev.dataset.page = page - 1;
            const pid = page - 1;
            if (paramKey) prev.href = '/danh-muc?' + encodeURIComponent(paramKey) + '=' + encodeURIComponent(identifier) + '&page=' + pid;
            prev.innerHTML = '&laquo; Prev';
            container.appendChild(prev);
        }

        for (let i = 1; i <= last; i++) {
            const a = document.createElement('a');
            a.className = 'px-3 py-1 border rounded page-btn ' + (i === page ? 'bg-green-700 text-white' : 'bg-white');
            a.dataset.page = i;
            if (paramKey) a.href = '/danh-muc?' + encodeURIComponent(paramKey) + '=' + encodeURIComponent(identifier) + '&page=' + i;
            a.textContent = i;
            container.appendChild(a);
        }

        if (page < last) {
            const next = document.createElement('a');
            next.className = 'px-3 py-1 bg-white border rounded next-page';
            next.dataset.page = page + 1;
            const np = page + 1;
            if (paramKey) next.href = '/danh-muc?' + encodeURIComponent(paramKey) + '=' + encodeURIComponent(identifier) + '&page=' + np;
            next.innerHTML = 'Next &raquo;';
            container.appendChild(next);
        }
    }

    // Note: category list is handled in the header. When header triggers loadCategoryById(id),
    // the functions below (loadCategoryById / updateCategoryContent) will update this page.
    // Handle back/forward
    window.addEventListener('popstate', function(e){
        const state = e.state;
        if (state) {
            const identifier = state.id ? state.id : (state.cat ? state.cat : null);
            if (identifier) {
                loadCategoryById(identifier, state.page || 1, false);
            }
        }
    });

    // Pagination click handler attached to the pagination container to avoid conflicts
    (function(){
        const container = document.getElementById('category-pagination');
        if (!container) return;
        container.addEventListener('click', async function(ev){
            const anchor = ev.target.closest('a');
            if (!anchor) return;
            ev.preventDefault();
            const href = anchor.href;
            // build fetch URL that asks for JSON (xhr=1)
            let fetchUrl = href;
            if (fetchUrl.indexOf('?') === -1) fetchUrl += '?xhr=1'; else fetchUrl += '&xhr=1';
            try {
                const res = await fetch(fetchUrl);
                if (!res.ok) {
                    // fallback to full navigation
                    window.location.href = href;
                    return;
                }
                const data = await res.json();
                try {
                    updateCategoryContent(data);
                } catch (uiErr) {
                    console.error('updateCategoryContent error', uiErr);
                    window.location.href = href;
                    return;
                }
                // push the real href into history so URL matches
                history.pushState({page: data.pagination?.page || 1, id: data.category?.id, cat: data.category?.name}, '', href);
            } catch (err) {
                console.error('fetch error for pagination', err);
                window.location.href = href;
            }
        });
    })();
});
</script>

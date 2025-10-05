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
                <div id="products-grid" class="flex flex-wrap -mx-1">
                    <?php foreach ($products as $p): ?>
                        <div class="w-1/2 sm:w-1/3 md:w-1/6 px-1 mb-3">
                            <div class="bg-white rounded shadow overflow-hidden text-xs">
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
                                <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p['name'] ?? '') ?>" class="w-full h-24 object-cover" onerror="this.onerror=null;this.src='/assets/images/no-image.png'"/>
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
                <div id="category-pagination" class="mt-4 flex items-center justify-center space-x-2">
                    <?php if ($pag['page'] > 1): ?>
                        <button data-page="<?= $pag['page'] - 1 ?>" class="px-3 py-1 bg-white border rounded prev-page">&laquo; Prev</button>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= ($pag['lastPage'] ?? 1); $i++): ?>
                        <button data-page="<?= $i ?>" class="px-3 py-1 <?= ($i == ($pag['page'] ?? 1)) ? 'bg-green-700 text-white' : 'bg-white' ?> border rounded page-btn"><?= $i ?></button>
                    <?php endfor; ?>
                    <?php if (($pag['page'] ?? 1) < ($pag['lastPage'] ?? 1)): ?>
                        <button data-page="<?= ($pag['page'] ?? 1) + 1 ?>" class="px-3 py-1 bg-white border rounded next-page">Next &raquo;</button>
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

    async function loadCategoryById(id, page = 1, pushUrl=true) {
        try {
            const res = await fetch('/danh-muc?xhr=1&id=' + encodeURIComponent(id) + '&page=' + encodeURIComponent(page));
            const data = await res.json();
            updateCategoryContent(data);
            if (pushUrl) history.pushState({id: id, page: page}, '', '/danh-muc?id=' + encodeURIComponent(id) + '&page=' + encodeURIComponent(page));
        } catch (e) {
            console.error('Error loading category', e);
        }
    }

    function updateCategoryContent(data) {
        const category = data.category;
        const products = data.products || [];
        const categories = data.categories || [];
        const pagination = data.pagination || null;

        // Update title/desc
        document.getElementById('category-title').textContent = category?.name ?? 'Danh mục';
        document.getElementById('category-desc').textContent = category?.description ?? '';

        const grid = document.getElementById('products-grid');
        if (!grid) return;
        grid.innerHTML = '';
                if (products.length === 0) {
            document.getElementById('category-empty').style.display = 'block';
        } else {
            document.getElementById('category-empty')?.style.display = 'none';
            products.forEach(function(p){
                // outer wrapper controls width so we get 6-per-row on md+
                const outer = document.createElement('div');
                outer.className = 'w-1/2 sm:w-1/3 md:w-1/6 px-1 mb-3';

                const card = document.createElement('div');
                card.className = 'bg-white rounded shadow overflow-hidden text-xs';

                const img = document.createElement('img');
                // prefer image_url from API, fall back to image proxy by product id
                img.src = p.image_url ? p.image_url : ('/image.php?product=' + encodeURIComponent(p.id));
                img.alt = p.name ?? '';
                img.className = 'w-full h-24 object-cover';
                img.onerror = function(){ this.onerror = null; this.src = '/assets/images/no-image.png'; };

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
                card.appendChild(img);
                card.appendChild(body);
                outer.appendChild(card);
                grid.appendChild(outer);
            });
            // update pagination UI if provided
            if (pagination) {
                renderPagination(pagination);
            }
        }
    }

    function renderPagination(pagination) {
        const container = document.getElementById('category-pagination');
        if (!container) return;
        container.innerHTML = '';
        const page = pagination.page || 1;
        const last = pagination.lastPage || 1;

        if (page > 1) {
            const prev = document.createElement('button');
            prev.className = 'px-3 py-1 bg-white border rounded prev-page';
            prev.dataset.page = page - 1;
            prev.innerHTML = '&laquo; Prev';
            container.appendChild(prev);
        }

        for (let i = 1; i <= last; i++) {
            const b = document.createElement('button');
            b.className = 'px-3 py-1 border rounded page-btn ' + (i === page ? 'bg-green-700 text-white' : 'bg-white');
            b.dataset.page = i;
            b.textContent = i;
            container.appendChild(b);
        }

        if (page < last) {
            const next = document.createElement('button');
            next.className = 'px-3 py-1 bg-white border rounded next-page';
            next.dataset.page = page + 1;
            next.innerHTML = 'Next &raquo;';
            container.appendChild(next);
        }
    }

    // Note: category list is handled in the header. When header triggers loadCategoryById(id),
    // the functions below (loadCategoryById / updateCategoryContent) will update this page.
    // Handle back/forward
    window.addEventListener('popstate', function(e){
        const state = e.state;
        if (state && state.id) {
            loadCategoryById(state.id, state.page || 1, false);
        }
    });

    // Delegate pagination clicks
    document.addEventListener('click', function(ev){
        const btn = ev.target.closest('#category-pagination button');
        if (!btn) return;
        const page = btn.dataset.page ? parseInt(btn.dataset.page, 10) : 1;
        const categoryId = (function(){
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('id');
        })();
        if (categoryId) {
            loadCategoryById(categoryId, page, true);
        }
    });
});
</script>

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
            <p id="category-desc" class="mb-6 text-sm text-gray-600"><?= htmlspecialchars($category['description'] ?? '') ?></p>

            <?php if (empty($products)): ?>
                <div id="category-empty" class="text-gray-500">Không có sản phẩm nào trong danh mục này.</div>
            <?php else: ?>
                <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($products as $p): ?>
                        <div class="bg-white rounded shadow overflow-hidden">
                            <img src="/assets/images/<?= rawurlencode($category['name'] ?? '') ?>/<?= htmlspecialchars($p['code'] ?? ($p['id'] . '.png')) ?>" alt="<?= htmlspecialchars($p['name'] ?? '') ?>" class="w-full h-40 object-cover">
                            <div class="p-3">
                                <div class="font-semibold text-sm"><?= htmlspecialchars($p['name'] ?? '') ?></div>
                                <div class="text-green-700 font-bold mt-2"><?= isset($p['price']) ? number_format($p['price']) . ' ₫' : '' ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

    async function loadCategoryById(id, pushUrl=true) {
        try {
            const res = await fetch('/danh-muc?xhr=1&id=' + encodeURIComponent(id));
            const data = await res.json();
            updateCategoryContent(data);
            if (pushUrl) history.pushState({id: id}, '', '/danh-muc?id=' + encodeURIComponent(id));
        } catch (e) {
            console.error('Error loading category', e);
        }
    }

    function updateCategoryContent(data) {
        const category = data.category;
        const products = data.products || [];
        const categories = data.categories || [];

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
                const el = document.createElement('div');
                el.className = 'bg-white rounded shadow overflow-hidden';
                const img = document.createElement('img');
                img.src = '/assets/images/' + encodeURIComponent(category?.name ?? '') + '/' + encodeURIComponent(p.code ?? (p.id + '.png'));
                img.alt = p.name ?? '';
                img.className = 'w-full h-40 object-cover';
                const body = document.createElement('div');
                body.className = 'p-3';
                const title = document.createElement('div');
                title.className = 'font-semibold text-sm';
                title.textContent = p.name ?? '';
                const price = document.createElement('div');
                price.className = 'text-green-700 font-bold mt-2';
                price.textContent = p.price ? new Intl.NumberFormat().format(p.price) + ' ₫' : '';
                body.appendChild(title);
                body.appendChild(price);
                el.appendChild(img);
                el.appendChild(body);
                grid.appendChild(el);
            });
        }
    }

    // Note: category list is handled in the header. When header triggers loadCategoryById(id),
    // the functions below (loadCategoryById / updateCategoryContent) will update this page.
    // Handle back/forward
    window.addEventListener('popstate', function(e){
        const state = e.state;
        if (state && state.id) {
            loadCategoryById(state.id, false);
        }
    });
});
</script>

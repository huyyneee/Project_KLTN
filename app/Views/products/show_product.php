<?php /** @var array $product */ ?>
<?php /** @var string|null $image */ ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-4xl mx-auto p-4">
    <div class="bg-white shadow rounded p-4">
        <div class="mb-4">
            <?php if (!empty($image)): ?>
                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover rounded">
            <?php elseif (!empty($product['image_url'])): ?>
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover rounded">
            <?php else: ?>
                <img src="/image.php?product=<?= (int)$product['id'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover rounded">
            <?php endif; ?>
        </div>

        <h1 class="text-2xl font-semibold mb-2"><?= htmlspecialchars($product['name']) ?></h1>
        <div class="text-lg text-gray-700 font-medium mb-4">
            <?= number_format($product['price'], 0, '.', ',') ?>₫
        </div>

        <div class="prose">
            <?= nl2br(htmlspecialchars($product['description'] ?? '')) ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

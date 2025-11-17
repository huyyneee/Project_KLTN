<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-green-900 mb-8 text-center">TIN TỨC & SỰ KIỆN</h1>
    
    <!-- Blog Grid: 3 columns per row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <?php foreach ($blogs as $blog): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-200">
                <!-- Blog Image -->
                <a href="/blog/detail?id=<?= $blog['id'] ?>">
                    <div class="w-full h-48 bg-gray-200 overflow-hidden relative">
                        <img src="<?= $blog['image'] ?>" 
                             alt="<?= htmlspecialchars($blog['title']) ?>" 
                             class="w-full h-full object-cover hover:scale-110 transition-transform duration-300"
                             loading="lazy">
                    </div>
                </a>
                
                <!-- Blog Content -->
                <div class="p-4">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <span><?= $blog['date'] ?></span>
                        <span class="mx-2">•</span>
                        <span><?= htmlspecialchars($blog['author']) ?></span>
                    </div>
                    
                    <a href="/blog/detail?id=<?= $blog['id'] ?>" class="block">
                        <h3 class="text-lg font-semibold text-green-900 mb-2 hover:text-green-700 transition-colors line-clamp-2">
                            <?= htmlspecialchars($blog['title']) ?>
                        </h3>
                    </a>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        <?= htmlspecialchars($blog['excerpt']) ?>
                    </p>
                    
                    <a href="/blog/detail?id=<?= $blog['id'] ?>" 
                       class="inline-flex items-center text-green-700 hover:text-green-800 font-semibold text-sm transition-colors">
                        Đọc thêm
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>


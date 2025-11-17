<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Back to Blog List -->
    <a href="/blog" class="inline-flex items-center text-green-700 hover:text-green-800 mb-6 font-semibold transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Quay lại danh sách bài viết
    </a>

    <!-- Blog Detail Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <!-- Blog Image -->
        <div class="w-full h-96 bg-gray-200 overflow-hidden">
            <img src="<?= $blog['image'] ?>" 
                 alt="<?= htmlspecialchars($blog['title']) ?>" 
                 class="w-full h-full object-cover"
                 loading="lazy">
        </div>
        
        <!-- Blog Content -->
        <div class="p-8">
            <!-- Meta Information -->
            <div class="flex items-center text-sm text-gray-500 mb-4">
                <span><?= $blog['date'] ?></span>
                <span class="mx-2">•</span>
                <span><?= htmlspecialchars($blog['author']) ?></span>
            </div>
            
            <!-- Title -->
            <h1 class="text-3xl font-bold text-green-900 mb-6">
                <?= htmlspecialchars($blog['title']) ?>
            </h1>
            
            <!-- Content -->
            <div class="prose max-w-none text-gray-700 leading-relaxed">
                <?= $blog['content'] ?>
            </div>
            
            <!-- Divider -->
            <div class="border-t border-gray-200 mt-8 pt-6">
                <a href="/blog" class="inline-flex items-center text-green-700 hover:text-green-800 font-semibold transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Quay lại danh sách bài viết
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .prose h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    
    .prose p {
        margin-bottom: 1rem;
        line-height: 1.75;
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>


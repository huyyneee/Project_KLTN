<?php include __DIR__ . '/layouts/header.php'; ?>
<?php include __DIR__ . '/static/widget/hasaki_chat_widget.php'; ?>

<!-- Banner slider -->
<div class="w-full max-w-7xl mx-auto mt-2 relative">
	<div id="banner-slider" class="overflow-hidden rounded-lg relative">
		<div class="flex transition-transform duration-700" id="banner-track">
			<!-- Thay src các ảnh banner tại đây, mỗi ảnh 1 div -->
			<a href="#" class="min-w-full block"><img src="/assets/images/chamsocdamat.png" alt="Banner 1"
					class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/trangdiem.png" alt="Banner 2"
					class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/chamsocdadau.png" alt="Banner 3"
					class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/chamsoccothe.png" alt="Banner 1"
					class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/chamsoccanhan.png" alt="Banner 2"
					class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/nuochoa.png" alt="Banner 2"
					class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/thucphamchucnang.png" alt="Banner 3"
					class="w-full h-64 object-cover"></a>
		</div>
		<!-- Nút điều hướng -->
		<button id="banner-prev"
			class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2"><svg
				class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
				<path d="M15 19l-7-7 7-7" />
			</svg></button>
		<button id="banner-next"
			class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2"><svg
				class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
				<path d="M9 5l7 7-7 7" />
			</svg></button>
	</div>
</div>

<!-- Danh mục sản phẩm lướt ngang, mỗi danh mục 1 hàng, sản phẩm riêng từng mục -->
<!-- Danh mục sản phẩm -->
<div class="max-w-7xl mx-auto mt-8">
	<?php if (!empty($query)): ?>
		<div class="mb-6 text-center text-gray-600">
			Kết quả tìm kiếm cho từ khóa: <span
				class="font-semibold text-green-800">"<?= htmlspecialchars($query) ?>"</span>
		</div>
	<?php endif; ?>

	<?php
	$hasAnyProduct = false;
	foreach ($categories as $cat):
		$catId = $cat['id'] ?? 0;
		$catName = $cat['name'] ?? 'Danh mục';
		$products = $productsByCategory[$catId] ?? [];

		if (empty($products))
			continue; // ko hiển thị kết quả tìm kiếm thì ẩn cate
		$hasAnyProduct = true;
	?>
		<div class="mb-8">
			<div class="flex items-center justify-between mb-2">
				<div class="font-bold text-lg text-green-800"><?= htmlspecialchars($catName) ?></div>
				<?php if (empty($query)): ?>
					<a href="/danh-muc?cat=<?= urlencode($catName) ?>" class="text-sm text-gray-600">Xem tất cả</a>
				<?php endif; ?>
			</div>

			<div class="overflow-hidden relative group">
				<div class="relative">
					<div class="flex space-x-4 product-carousel" style="will-change: transform;"
						data-category-id="<?= (int) $catId ?>">
						<?php foreach ($products as $p): ?>
							<?php
							$imgSrc = !empty($p['image_url']) ? $p['image_url'] : '/assets/images/no-image.png';
							?>
							<a href="/san-pham?product=<?= (int) $p['id'] ?>"
								class="block product-item w-36 flex-shrink-0 bg-white rounded overflow-hidden hover:shadow">
								<img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p['name'] ?? '') ?>"
									class="w-full h-28 object-cover rounded-t">
								<div class="p-2 text-center text-base truncate"><?= htmlspecialchars($p['name'] ?? '') ?></div>
							</a>
						<?php endforeach; ?>
					</div>

					<!-- Nút điều hướng -->
					<button
						class="product-prev absolute left-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2 z-10"><svg
							class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path d="M15 19l-7-7 7-7" />
						</svg></button>
					<button
						class="product-next absolute right-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2 z-10"><svg
							class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path d="M9 5l7 7-7 7" />
						</svg></button>
				</div>
			</div>
		</div>
	<?php endforeach; ?>

	<?php if (!$hasAnyProduct): ?>
		<div class="text-center text-gray-500 italic py-8">Không tìm thấy sản phẩm nào phù hợp.</div>
	<?php endif; ?>
</div>


<?php include __DIR__ . '/layouts/footer.php'; ?>

<!-- Script slider và scroll ngang -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Banner slider
		const banners = document.querySelectorAll('#banner-track > a');
		let bannerIndex = 0;
		let bannerInterval = null;
		const track = document.getElementById('banner-track');
		const bannerSlider = document.getElementById('banner-slider');

		function showBanner(idx) {
			track.style.transform = `translateX(-${idx * 100}%)`;
		}

		function nextBanner() {
			bannerIndex = (bannerIndex + 1) % banners.length;
			showBanner(bannerIndex);
		}

		function prevBanner() {
			bannerIndex = (bannerIndex - 1 + banners.length) % banners.length;
			showBanner(bannerIndex);
		}

		function startBannerAuto() {
			if (bannerInterval) clearInterval(bannerInterval);
			bannerInterval = setInterval(nextBanner, 5000);
		}

		function stopBannerAuto() {
			if (bannerInterval) clearInterval(bannerInterval);
		}
		document.getElementById('banner-next').onclick = nextBanner;
		document.getElementById('banner-prev').onclick = prevBanner;
		// banners.forEach((a, i) => a.onclick = () => window.location.href = '/danh-muc?cat=' + encodeURIComponent(a.alt || ''));

		// Pause auto slide on hover
		bannerSlider.addEventListener('mouseenter', stopBannerAuto);
		bannerSlider.addEventListener('mouseleave', startBannerAuto);
		startBannerAuto();

		// Product carousels (giống banner)
		document.querySelectorAll('.product-carousel').forEach(function(carousel) {
			const items = carousel.querySelectorAll('a');
			let idx = 0;
			let interval = null;
			// we want to show exactly 6 items per view
			const visibleCount = 6;
			const GAP = 16; // space-x-4 => 16px
			function computeItemWidth() {
				const containerWidth = carousel.parentElement.offsetWidth;
				// subtract gaps between items (visibleCount - 1 gaps)
				const totalGaps = (visibleCount - 1) * GAP;
				const w = Math.floor((containerWidth - totalGaps) / visibleCount);
				return Math.max(80, w); // minimum width fallback
			}

			function applyItemWidth(w) {
				items.forEach(function(it) {
					it.style.width = w + 'px';
					it.style.flex = '0 0 ' + w + 'px';
					const img = it.querySelector('img');
					if (img) {
						img.style.height = Math.round(w * 0.78) + 'px';
						img.style.objectFit = 'cover';
					}
				});
			}

			function show(i) {
				const itemWidth = items[0]?.offsetWidth || computeItemWidth();
				carousel.style.transform = `translateX(-${i * (itemWidth + GAP)}px)`;
			}

			function recalc() {
				const w = computeItemWidth();
				applyItemWidth(w);
				// ensure idx within bounds
				const maxIdx = Math.max(items.length - visibleCount, 0);
				if (idx > maxIdx) idx = maxIdx;
				show(idx);
			}

			function next() {
				idx = (idx + 1);
				const maxIdx = Math.max(items.length - visibleCount, 0);
				if (idx > maxIdx) idx = 0;
				show(idx);
			}

			function prev() {
				idx = (idx - 1);
				const maxIdx = Math.max(items.length - visibleCount, 0);
				if (idx < 0) idx = maxIdx;
				show(idx);
			}

			function startAuto() {
				if (interval) clearInterval(interval);
				interval = setInterval(next, 5000);
			}

			function stopAuto() {
				if (interval) clearInterval(interval);
			}
			carousel.parentElement.querySelector('.product-next').onclick = next;
			carousel.parentElement.querySelector('.product-prev').onclick = prev;
			carousel.addEventListener('mouseenter', stopAuto);
			carousel.addEventListener('mouseleave', startAuto);
			recalc();
			startAuto();
			// Responsive: recompute sizes on resize
			window.addEventListener('resize', function() {
				recalc();
			});
		});
	});
</script>

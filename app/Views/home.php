
<?php include __DIR__ . '/layouts/header.php'; ?>

<!-- Banner slider -->
<div class="w-full max-w-7xl mx-auto mt-2 relative">
	<div id="banner-slider" class="overflow-hidden rounded-lg relative">
		<div class="flex transition-transform duration-700" id="banner-track">
			<!-- Thay src các ảnh banner tại đây, mỗi ảnh 1 div -->
			<a href="#" class="min-w-full block"><img src="/assets/images/chamsocdamat.png" alt="Banner 1" class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/trangdiem.png" alt="Banner 2" class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/chamsocdadau.png" alt="Banner 3" class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/chamsoccothe.png" alt="Banner 1" class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/chamsoccanhan.png" alt="Banner 2" class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/nuochoa.png" alt="Banner 2" class="w-full h-64 object-cover"></a>
			<a href="#" class="min-w-full block"><img src="/assets/images/thucphamchucnang.png" alt="Banner 3" class="w-full h-64 object-cover"></a>
		</div>
		<!-- Nút điều hướng -->
		<button id="banner-prev" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg></button>
		<button id="banner-next" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>
	</div>
</div>

<!-- Danh mục sản phẩm lướt ngang, mỗi danh mục 1 hàng, sản phẩm riêng từng mục -->
<div class="max-w-7xl mx-auto mt-8">
	<?php
	// Dữ liệu sản phẩm từng danh mục, mỗi mục là 1 mảng con
	$productsByCategory = [
		'Chăm Sóc Da Mặt' => [
			['img' => 'teatree.png', 'name' => 'Serum Trà'],
			['img' => 'suaruamatcerave.png', 'name' => 'Sữa rửa mặt Cerave'],
			['img' => 'serumsangda.png', 'name' => "Serum L'Oreal Sáng Da"],
			['img' => 'matnanghe.png', 'name' => 'Mặt Nạ Nghệ'],
			['img' => 'kemsangda.png', 'name' => 'Kem Dưỡng Vichy Sáng Da'],
			['img' => 'gelruamat.png', 'name' => 'Gel Rửa Mặt La Roche-Posay'],
			['img' => 'duongam.png', 'name' => 'Serum Torriden Dưỡng Ẩm Sâ'],
			['img' => 'kemduong.png', 'name' => 'Kem Dưỡng Obagi Retinol'],
			['img' => 'gelduong.png', 'name' => 'Gel Dưỡng Megaduo Plus Giảm Mụn'],
			['img' => 'olay.png', 'name' => 'Kem Dưỡng Olay Ban Ngày Sáng Da'],
			['img' => 'trangdiem2.png', 'name' => 'Phấn phủ 02'],
			['img' => 'chamsocdadau.png', 'name' => 'Dầu gội 01'],
			['img' => 'chamsoccothe.png', 'name' => 'Sữa tắm 01'],
			['img' => 'chamsoccanhan.png', 'name' => 'Bàn chải đánh răng 01'],
			['img' => 'nuochoa.png', 'name' => 'Nước hoa 01'],
			['img' => 'thucphamchucnang.png', 'name' => 'Vitamin C 01'],
			// ... Thêm sản phẩm mới tại đây
		],
		'Trang Điểm' => [
			['img' => 'teatree.png', 'name' => 'Serum Trà'],
			['img' => 'suaruamatcerave.png', 'name' => 'Sữa rửa mặt Cerave'],
			['img' => 'serumsangda.png', 'name' => "Serum L'Oreal Sáng Da"],
			['img' => 'matnanghe.png', 'name' => 'Mặt Nạ Nghệ'],
			['img' => 'kemsangda.png', 'name' => 'Kem Dưỡng Vichy Sáng Da'],
			['img' => 'gelruamat.png', 'name' => 'Gel Rửa Mặt La Roche-Posay'],
			['img' => 'duongam.png', 'name' => 'Serum Torriden Dưỡng Ẩm Sâ'],
			['img' => 'kemduong.png', 'name' => 'Kem Dưỡng Obagi Retinol'],
			['img' => 'gelduong.png', 'name' => 'Gel Dưỡng Megaduo Plus Giảm Mụn'],
			['img' => 'olay.png', 'name' => 'Kem Dưỡng Olay Ban Ngày Sáng Da'],
			['img' => 'trangdiem2.png', 'name' => 'Phấn phủ 02'],
			['img' => 'chamsocdadau.png', 'name' => 'Dầu gội 01'],
			['img' => 'chamsoccothe.png', 'name' => 'Sữa tắm 01'],
			['img' => 'chamsoccanhan.png', 'name' => 'Bàn chải đánh răng 01'],
			['img' => 'nuochoa.png', 'name' => 'Nước hoa 01'],
			['img' => 'thucphamchucnang.png', 'name' => 'Vitamin C 01'],
			// ...
		],
		'Chăm Sóc Tóc Và Da Đầu' => [
			['img' => 'teatree.png', 'name' => 'Serum Trà'],
			['img' => 'suaruamatcerave.png', 'name' => 'Sữa rửa mặt Cerave'],
			['img' => 'serumsangda.png', 'name' => "Serum L'Oreal Sáng Da"],
			['img' => 'matnanghe.png', 'name' => 'Mặt Nạ Nghệ'],
			['img' => 'kemsangda.png', 'name' => 'Kem Dưỡng Vichy Sáng Da'],
			['img' => 'gelruamat.png', 'name' => 'Gel Rửa Mặt La Roche-Posay'],
			['img' => 'duongam.png', 'name' => 'Serum Torriden Dưỡng Ẩm Sâ'],
			['img' => 'kemduong.png', 'name' => 'Kem Dưỡng Obagi Retinol'],
			['img' => 'gelduong.png', 'name' => 'Gel Dưỡng Megaduo Plus Giảm Mụn'],
			['img' => 'olay.png', 'name' => 'Kem Dưỡng Olay Ban Ngày Sáng Da'],
			['img' => 'trangdiem2.png', 'name' => 'Phấn phủ 02'],
			['img' => 'chamsocdadau.png', 'name' => 'Dầu gội 01'],
			['img' => 'chamsoccothe.png', 'name' => 'Sữa tắm 01'],
			['img' => 'chamsoccanhan.png', 'name' => 'Bàn chải đánh răng 01'],
			['img' => 'nuochoa.png', 'name' => 'Nước hoa 01'],
			['img' => 'thucphamchucnang.png', 'name' => 'Vitamin C 01'],
			// ...
		],
		'Chăm Sóc Cơ Thể' => [
			['img' => 'teatree.png', 'name' => 'Serum Trà'],
			['img' => 'suaruamatcerave.png', 'name' => 'Sữa rửa mặt Cerave'],
			['img' => 'serumsangda.png', 'name' => "Serum L'Oreal Sáng Da"],
			['img' => 'matnanghe.png', 'name' => 'Mặt Nạ Nghệ'],
			['img' => 'kemsangda.png', 'name' => 'Kem Dưỡng Vichy Sáng Da'],
			['img' => 'gelruamat.png', 'name' => 'Gel Rửa Mặt La Roche-Posay'],
			['img' => 'duongam.png', 'name' => 'Serum Torriden Dưỡng Ẩm Sâ'],
			['img' => 'kemduong.png', 'name' => 'Kem Dưỡng Obagi Retinol'],
			['img' => 'gelduong.png', 'name' => 'Gel Dưỡng Megaduo Plus Giảm Mụn'],
			['img' => 'olay.png', 'name' => 'Kem Dưỡng Olay Ban Ngày Sáng Da'],
			['img' => 'trangdiem2.png', 'name' => 'Phấn phủ 02'],
			['img' => 'chamsocdadau.png', 'name' => 'Dầu gội 01'],
			['img' => 'chamsoccothe.png', 'name' => 'Sữa tắm 01'],
			['img' => 'chamsoccanhan.png', 'name' => 'Bàn chải đánh răng 01'],
			['img' => 'nuochoa.png', 'name' => 'Nước hoa 01'],
			['img' => 'thucphamchucnang.png', 'name' => 'Vitamin C 01'],
			// ...
		],
		'Chăm Sóc Cá Nhân' => [
			['img' => 'teatree.png', 'name' => 'Serum Trà'],
			['img' => 'suaruamatcerave.png', 'name' => 'Sữa rửa mặt Cerave'],
			['img' => 'serumsangda.png', 'name' => "Serum L'Oreal Sáng Da"],
			['img' => 'matnanghe.png', 'name' => 'Mặt Nạ Nghệ'],
			['img' => 'kemsangda.png', 'name' => 'Kem Dưỡng Vichy Sáng Da'],
			['img' => 'gelruamat.png', 'name' => 'Gel Rửa Mặt La Roche-Posay'],
			['img' => 'duongam.png', 'name' => 'Serum Torriden Dưỡng Ẩm Sâ'],
			['img' => 'kemduong.png', 'name' => 'Kem Dưỡng Obagi Retinol'],
			['img' => 'gelduong.png', 'name' => 'Gel Dưỡng Megaduo Plus Giảm Mụn'],
			['img' => 'olay.png', 'name' => 'Kem Dưỡng Olay Ban Ngày Sáng Da'],
			['img' => 'trangdiem2.png', 'name' => 'Phấn phủ 02'],
			['img' => 'chamsocdadau.png', 'name' => 'Dầu gội 01'],
			['img' => 'chamsoccothe.png', 'name' => 'Sữa tắm 01'],
			['img' => 'chamsoccanhan.png', 'name' => 'Bàn chải đánh răng 01'],
			['img' => 'nuochoa.png', 'name' => 'Nước hoa 01'],
			['img' => 'thucphamchucnang.png', 'name' => 'Vitamin C 01'],
			// ...
		],
		'Nước Hoa' => [
			['img' => 'teatree.png', 'name' => 'Serum Trà'],
			['img' => 'suaruamatcerave.png', 'name' => 'Sữa rửa mặt Cerave'],
			['img' => 'serumsangda.png', 'name' => "Serum L'Oreal Sáng Da"],
			['img' => 'matnanghe.png', 'name' => 'Mặt Nạ Nghệ'],
			['img' => 'kemsangda.png', 'name' => 'Kem Dưỡng Vichy Sáng Da'],
			['img' => 'gelruamat.png', 'name' => 'Gel Rửa Mặt La Roche-Posay'],
			['img' => 'duongam.png', 'name' => 'Serum Torriden Dưỡng Ẩm Sâ'],
			['img' => 'kemduong.png', 'name' => 'Kem Dưỡng Obagi Retinol'],
			['img' => 'gelduong.png', 'name' => 'Gel Dưỡng Megaduo Plus Giảm Mụn'],
			['img' => 'olay.png', 'name' => 'Kem Dưỡng Olay Ban Ngày Sáng Da'],
			['img' => 'trangdiem2.png', 'name' => 'Phấn phủ 02'],
			['img' => 'chamsocdadau.png', 'name' => 'Dầu gội 01'],
			['img' => 'chamsoccothe.png', 'name' => 'Sữa tắm 01'],
			['img' => 'chamsoccanhan.png', 'name' => 'Bàn chải đánh răng 01'],
			['img' => 'nuochoa.png', 'name' => 'Nước hoa 01'],
			['img' => 'thucphamchucnang.png', 'name' => 'Vitamin C 01'],
			// ...
		],
		'Thực Phẩm Chức Năng' => [
			['img' => 'teatree.png', 'name' => 'Serum Trà'],
			['img' => 'suaruamatcerave.png', 'name' => 'Sữa rửa mặt Cerave'],
			['img' => 'serumsangda.png', 'name' => "Serum L'Oreal Sáng Da"],
			['img' => 'matnanghe.png', 'name' => 'Mặt Nạ Nghệ'],
			['img' => 'kemsangda.png', 'name' => 'Kem Dưỡng Vichy Sáng Da'],
			['img' => 'gelruamat.png', 'name' => 'Gel Rửa Mặt La Roche-Posay'],
			['img' => 'duongam.png', 'name' => 'Serum Torriden Dưỡng Ẩm Sâ'],
			['img' => 'kemduong.png', 'name' => 'Kem Dưỡng Obagi Retinol'],
			['img' => 'gelduong.png', 'name' => 'Gel Dưỡng Megaduo Plus Giảm Mụn'],
			['img' => 'olay.png', 'name' => 'Kem Dưỡng Olay Ban Ngày Sáng Da'],
			['img' => 'trangdiem2.png', 'name' => 'Phấn phủ 02'],
			['img' => 'chamsocdadau.png', 'name' => 'Dầu gội 01'],
			['img' => 'chamsoccothe.png', 'name' => 'Sữa tắm 01'],
			['img' => 'chamsoccanhan.png', 'name' => 'Bàn chải đánh răng 01'],
			['img' => 'nuochoa.png', 'name' => 'Nước hoa 01'],
			['img' => 'thucphamchucnang.png', 'name' => 'Vitamin C 01'],
			// ...
		],
	];
	foreach ($productsByCategory as $catName => $products): ?>
		<div class="mb-8">
			<div class="font-bold text-lg mb-2 text-green-800"><?= $catName ?></div>
			<div class="overflow-hidden relative group">
				<div class="relative">
					<div class="flex space-x-4 product-carousel" style="will-change: transform;" data-category="<?= htmlspecialchars($catName) ?>">
						<?php foreach ($products as $sp): ?>
						<a href="#" class="block min-w-[180px] max-w-[180px] bg-white rounded shadow hover:scale-105 transition">
							<img src="/assets/images/<?= rawurlencode($catName) ?>/<?= htmlspecialchars($sp['img']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>" class="w-full h-32 object-cover rounded-t">
							<div class="p-2 text-center text-sm"><?= htmlspecialchars($sp['name']) ?></div>
						</a>
						<?php endforeach; ?>
					</div>
					<button class="product-prev absolute left-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2 z-10"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg></button>
					<button class="product-next absolute right-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2 z-10"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
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
	banners.forEach((a, i) => a.onclick = () => window.location.href = '/danh-muc?cat=' + encodeURIComponent(a.alt || ''));
	// Pause auto slide on hover
	bannerSlider.addEventListener('mouseenter', stopBannerAuto);
	bannerSlider.addEventListener('mouseleave', startBannerAuto);
	startBannerAuto();

	// Product carousels (giống banner)
		document.querySelectorAll('.product-carousel').forEach(function(carousel) {
			const items = carousel.querySelectorAll('a');
			let idx = 0;
			let interval = null;
			const visibleCount = Math.floor(carousel.parentElement.offsetWidth / (items[0]?.offsetWidth || 180));
			const maxIdx = items.length - visibleCount >= 0 ? items.length - visibleCount : 0;
			const show = (i) => {
				const itemWidth = items[0]?.offsetWidth || 180;
				carousel.style.transform = `translateX(-${i * (itemWidth + 16)}px)`;
			};
			function next() {
				idx = (idx + 1);
				if (idx > maxIdx) idx = 0;
				show(idx);
			}
			function prev() {
				idx = (idx - 1);
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
			startAuto();
			// Responsive: reset idx if window resize
			window.addEventListener('resize', () => show(idx));
		});
});
</script>

<!-- Hướng dẫn thêm sản phẩm và hình ảnh cho từng mục -->
<!--
1. Để thêm sản phẩm cho từng mục:
	- Tìm mảng $productsByCategory ở trên.
	- Thêm phần tử mới dạng ['img' => 'ten_anh.png', 'name' => 'Tên sản phẩm'] vào đúng danh mục.
	- Ví dụ: ['img' => 'chamsocdamat4.png', 'name' => 'Sữa rửa mặt 04']
2. Để thêm hình ảnh:
	- Upload file ảnh vào đúng thư mục con theo tên danh mục trong /public/assets/images/ (ví dụ: /public/assets/images/Chăm Sóc Da Mặt/chamsocdamat4.png)
	- Tên thư mục con phải đúng với tên danh mục (có thể copy-paste từ mảng $productsByCategory).
	- Đảm bảo tên file ảnh trùng với giá trị 'img' trong mảng sản phẩm.
3. Để thay đổi tên sản phẩm:
	- Sửa giá trị 'name' trong mảng sản phẩm tương ứng.
4. Sản phẩm sẽ tự động hiển thị và lướt ngang, khi đưa chuột vào sẽ dừng lại.
-->


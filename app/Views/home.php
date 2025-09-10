
<?php include __DIR__ . '/layouts/header.php'; ?>
<!-- Banner và menu ngang -->
<div class="bg-white py-2 flex justify-center gap-8 font-semibold text-sm border-b">
	<a href="#">Trang chủ</a>
	<a href="#">Tùy chọn</a>
	<a href="#">Sữa rửa mặt</a>
	<a href="#">Kem chống nắng</a>
	<a href="#">Serum</a>
	<a href="#">Nước hoa</a>
	<a href="#">Nước tẩy trang</a>
	<a href="#">Sản phẩm khác</a>
</div>

<!-- Banner tin tức, thương hiệu, liên hệ -->
<div class="flex justify-center gap-8 py-6 bg-gray-50">
	<div class="w-60 h-36 bg-white rounded shadow flex flex-col items-center justify-center overflow-hidden">
		<img src="/assets/images/banner-news.jpg" alt="Tin tức" class="w-full h-20 object-cover mb-2">
		<span class="font-bold">Tin tức new news</span>
	</div>
	<div class="w-60 h-36 bg-white rounded shadow flex flex-col items-center justify-center overflow-hidden">
		<img src="/assets/images/banner-brand.jpg" alt="Thương hiệu" class="w-full h-20 object-cover mb-2">
		<span class="font-bold">Thương hiệu</span>
	</div>
	<div class="w-60 h-36 bg-white rounded shadow flex flex-col items-center justify-center overflow-hidden">
		<img src="/assets/images/banner-contact.jpg" alt="Liên hệ" class="w-full h-20 object-cover mb-2">
		<span class="font-bold">Liên hệ</span>
	</div>
</div>

<!-- Sản phẩm mới nhất -->
<div class="max-w-6xl mx-auto py-6">
	<h2 class="text-xl font-bold mb-4">Sản phẩm mới nhất</h2>
	<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
		<?php for($i=0;$i<4;$i++): ?>
		<div class="bg-white rounded-lg shadow p-4 flex flex-col items-center">
			<img src="/assets/images/product.png" alt="Sản phẩm <?php echo chr(97+$i); ?>" class="w-32 h-32 object-contain mb-2">
			<div class="font-bold mb-1">30 đã bán</div>
			<div class="mb-1">Sản phẩm <?php echo chr(97+$i); ?></div>
			<div class="mb-1">xxxx vnd</div>
			<div class="flex gap-1 mb-1">
				<?php for($j=0;$j<5;$j++): ?><span>★</span><?php endfor; ?>
			</div>
		</div>
		<?php endfor; ?>
	</div>
</div>

<!-- Thanh chuyển trang và icon chat -->
<div class="flex justify-end items-center max-w-6xl mx-auto pb-4">
	<div class="flex gap-2">
		<?php for($i=1;$i<=5;$i++): ?>
			<span class="w-4 h-4 rounded-full border border-gray-400 flex items-center justify-center <?php if($i==1) echo 'bg-gray-400'; ?>"></span>
		<?php endfor; ?>
	</div>
	<svg class="w-10 h-10 ml-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h2m10 0V6a4 4 0 00-8 0v2m8 0H7"></path></svg>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>


<footer class="bg-green-900 bg-[url('/assets/images/bg-footer.png')] bg-cover bg-center text-white pt-8 pb-2">
	<div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
		<!-- Thông tin liên hệ -->
		<div>
			<h3 class="text-lg font-bold mb-2 flex items-center"><span class="text-yellow-400 mr-2 text-xl">■</span>THÔNG TIN LIÊN HỆ</h3>
			<div class="font-bold text-xl mb-2">MỸ PHẨM XUÂN HIỆP</div>
			<div class="mb-1">Địa chỉ: 115 Lê Văn Thọ, Phường 8, Gò Vấp, Tp Hồ Chí Minh</div>
			<div class="mb-1">Hotline : <span class="font-semibold">0919.048.955</span></div>
			<div class="mb-1">Mail. <a href="mailto:myphamxuanhiep@gmail.com" class="underline">myphamxuanhiep@gmail.com</a></div>
			<div class="mb-3">Web : <a href="https://myphamxuanhiep.com" class="underline" target="_blank">MYPHAMXUANHIEP.COM</a></div>
			<div class="flex space-x-3 mt-2">
				<a href="#" class="hover:text-yellow-400"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg></a>
				<a href="#" class="hover:text-yellow-400"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="3"/></svg></a>
				<a href="#" class="hover:text-yellow-400"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12h8" stroke="#fff" stroke-width="2"/></svg></a>
				<a href="#" class="hover:text-yellow-400"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M16 8a6 6 0 01-8 8" stroke="#fff" stroke-width="2"/></svg></a>
			</div>
		</div>
		<!-- Bản đồ -->
		<div>
			<h3 class="text-lg font-bold mb-2 flex items-center"><span class="text-yellow-400 mr-2 text-xl">■</span>BẢN ĐỒ</h3>
			<div class="rounded overflow-hidden border-2 border-white shadow-lg">
				<iframe src="https://maps.google.com/maps?q=115%20L%C3%AA%20V%C4%83n%20Th%E1%BB%8D%2C%20Ph%C6%B0%E1%BB%9Dng%208%2C%20G%C3%B2%20V%E1%BA%A5p%2C%20Tp%20H%E1%BB%93%20Ch%C3%AD%20Minh&t=&z=17&ie=UTF8&iwloc=&output=embed" width="100%" height="180" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
		</div>
		<!-- Fanpage Facebook: fix green gap by matching box width to plugin (340px) -->
		<div>
			<h3 class="text-lg font-bold mb-2 flex items-center"><span class="text-yellow-400 mr-2 text-xl">■</span>FANPAGE FACEBOOK</h3>
			<div class="rounded overflow-hidden border-2 border-white shadow-lg mx-auto" style="padding:0; width:340px; height:180px;">
				<div id="fb-root"></div>
				<div class="fb-page" data-href="https://www.facebook.com/myphamxuanhiep" data-tabs="timeline" data-width="340" data-height="180" data-small-header="false" data-adapt-container-width="false" data-hide-cover="false" data-show-facepile="true">
					<blockquote cite="https://www.facebook.com/myphamxuanhiep" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/myphamxuanhiep">Kềm Nghĩa Mỹ Phẩm Xuân Hiệp</a></blockquote>
				</div>
			</div>
		</div>
	</div>
	<div class="text-center text-xs text-white bg-green-950 bg-opacity-80 py-2 mt-6">
		Copyright © 2025 XUAN HIEP COSMETIC. All rights reserved. Web design : HUY&HUNG
	</div>
</footer>

<!-- Load Facebook SDK once (if not already loaded) and force plugin background transparent -->
<script>
if (!window.FB) {
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.async = true; js.defer = true; js.crossOrigin = 'anonymous';
		js.src = 'https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v16.0';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
} else {
	if (window.FB && window.FB.XFBML) window.FB.XFBML.parse();
}
</script>



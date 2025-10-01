<?php
// Component: login form
// Usage: include this component inside a modal or page where needed
?>

<div class="relative bg-white rounded-lg shadow-sm max-w-md mx-auto" style="padding:20px; border:1px solid #eef2f6; box-shadow:0 6px 18px rgba(0,0,0,0.04); overflow:visible;">
    <!-- close button anchored absolute to top-right of the card (visible outside when needed) -->
    <button type="button" class="login-close bg-white border border-gray-200 rounded-full w-7 h-7 flex items-center justify-center shadow-sm focus:outline-none" aria-label="Đóng" style="position:absolute; right:500px; top:1px; z-index:999;">
        <svg class="w-3 h-3 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <h2 class="text-center text-xs font-semibold text-gray-600 mb-4 tracking-wide uppercase">LOGIN YOUR ACCOUNT</h2>

    <div class="w-full">
        <form id="login-form" action="/account/login" method="post" class="space-y-4 w-full text-sm">
            <div class="flex justify-center">
                <input name="identity" type="text" placeholder="Nhập email hoặc số điện thoại" required
                    class="w-64 bg-white border border-gray-200 rounded px-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-0" style="height:34px;" />
            </div>

            <div class="flex justify-center">
                <input name="password" type="password" placeholder="Nhập password" required
                    class="w-64 bg-white border border-gray-200 rounded px-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-0" style="height:34px;" />
            </div>

            <div class="flex justify-center">
                <div class="w-64 flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-700 text-sm"><input type="checkbox" name="remember" class="form-checkbox"/> <span class="ml-2">Nhớ mật khẩu</span></label>
                    <a href="/account/forgot" class="text-green-700 hover:underline text-sm">Quên mật khẩu</a>
                </div>
            </div>

            <div class="flex justify-center">
                <button type="submit" class="bg-green-700 text-white rounded-full w-40 py-2 text-sm font-medium hover:bg-green-800">Đăng nhập</button>
            </div>

            <div class="text-center text-sm text-gray-600 mt-1">
                Bạn chưa có tài khoản? <a href="/register" class="text-green-700 font-semibold hover:underline">ĐĂNG KÝ NGAY</a>
            </div>
        </form>
    </div>
</div>

<script>
// close button behavior: redirect to home
(function(){
    var btn = document.querySelector('.login-close');
    if (!btn) return;
    btn.addEventListener('click', function(){
        window.location.href = '/';
    });
})();
</script>

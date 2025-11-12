<?php include __DIR__ . '/layouts/header.php'; ?>
<div class="flex items-center justify-center bg-gray-100" style="min-height:calc(75vh - 100px)">
    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Quên mật khẩu</h2>
        <p class="text-gray-600 mb-6 text-center">Nhập email của bạn để nhận link đặt lại mật khẩu.</p>

        <form id="forgotPasswordForm" class="space-y-4">
            <input type="email" name="email" placeholder="Nhập email của bạn"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>

            <button type="submit"
                class="w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600 transition">
                Gửi link đặt lại mật khẩu
            </button>
        </form>

        <p id="forgotMessage" class="mt-4 text-sm text-center"></p>

        <p class="mt-6 text-center text-gray-700 text-sm">
            <a href="/login" class="text-blue-600 hover:underline">Quay lại đăng nhập</a>
        </p>
    </div>
</div>
<?php include __DIR__ . '/layouts/footer.php'; ?>


<script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Đang gửi...';

        fetch('/login/send-reset-link', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById('forgotMessage');
                msg.textContent = data.message;
                msg.className = data.ok ? 'text-green-600 mt-4 text-center' : 'text-red-600 mt-4 text-center';
                btn.disabled = false;
                btn.textContent = 'Gửi link đặt lại mật khẩu';
            })
            .catch(err => {
                const msg = document.getElementById('forgotMessage');
                msg.textContent = 'Lỗi mạng, vui lòng thử lại';
                msg.className = 'text-red-600 mt-4 text-center';
                btn.disabled = false;
                btn.textContent = 'Gửi link đặt lại mật khẩu';
            });
    });
</script>
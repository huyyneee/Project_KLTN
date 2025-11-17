<?php
include __DIR__ . '/layouts/header.php'; ?>

<div class="flex items-center justify-center bg-gray-100" style="min-height:calc(80vh - 100px)">
    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Đặt lại mật khẩu</h2>
        <p class="text-gray-600 text-center">Nhập mật khẩu mới của bạn.</p>
        <form id="resetPasswordForm" class="space-y-4">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="password" name="password" placeholder="Mật khẩu mới"
                class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-400" required>
            <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu"
                class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-400" required>
            <button type="submit"
                class="w-full bg-green-700 text-white p-3 rounded-lg hover:bg-green-600 transition">
                Đặt lại mật khẩu
            </button>
        </form>

        <p id="resetMessage" class="mt-4 text-sm"></p>
        <p class="mt-6 text-center text-gray-700 text-sm">
            <a href="/login" class="text-blue-600 hover:underline">Quay lại đăng nhập</a>
        </p>
    </div>
</div>

<script>
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/login/reset-password-post', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById('resetMessage');
                msg.textContent = data.message;
                msg.className = data.ok ? 'text-green-600 mt-4' : 'text-red-600 mt-4';
                if (data.ok) setTimeout(() => window.location.href = '/login', 2000);
            });
    });
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
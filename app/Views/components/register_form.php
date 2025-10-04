<?php
// Component: register form
$errors = $errors ?? [];
$old = $old ?? [];
?>

<div class="relative bg-white rounded-lg shadow-sm max-w-md mx-auto" style="padding:1px; border:1px solid #eef2f6; box-shadow:0 6px 18px rgba(0,0,0,0.04); overflow:visible;">
    <button type="button" class="register-close bg-white border border-gray-200 rounded-full w-7 h-7 flex items-center justify-center shadow-sm focus:outline-none" aria-label="Đóng" style="position:absolute; right:450px; top:10px; z-index:999;">
        <svg class="w-3 h-3 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <h1 class="text-center text-xs font-semibold text-gray-600 mb-4 tracking-wide uppercase">ĐĂNG KÝ TÀI KHOẢN</h1>

    <?php if (!empty($errors)): ?>
        <div class="mb-3 text-sm text-red-600">
            <ul class="list-disc pl-5">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="w-full">
        <form id="register-form" action="/account/register" method="post" class="space-y-4 w-full text-sm">
            <!-- identity (email or phone) -->
            <div class="flex justify-center">
                <input name="email" type="text" placeholder="Nhập email hoặc số điện thoại" required value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>"
                    class="w-64 bg-white border border-gray-200 rounded px-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-0" style="height:36px;" />
            </div>

            <!-- captcha row: input + captcha display -->
            <div class="flex justify-center items-center space-x-2">
                <input name="captcha" type="text" placeholder="Nhập mã captcha" required
                    class="w-40 bg-white border border-gray-200 rounded px-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-0" style="height:32px;" />
                <div id="captcha-box" class="bg-green-700 text-white text-sm px-4 py-1 rounded tracking-widest" style="min-width:40px; text-align:center; letter-spacing:1px;">n v s b</div>
            </div>

            <!-- verification code row: fixed wrapper so button doesn't push input -->
            <div class="flex justify-center">
                <div class="w-64 relative" style="height:32px;">
                    <input name="verification_code" type="text" placeholder="Nhập mã xác thực" maxlength="6"
                        class="w-full bg-white border border-gray-200 rounded px-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-0" style="height:32px; padding-right:90px; box-sizing:border-box;" />
                    <button type="button" id="send-code" class="bg-green-700 text-white rounded px-3 py-1 text-sm" style="position:absolute; right:6px; top:50%; transform:translateY(-50%); min-width:78px; text-align:center;">lấy mã</button>
                </div>
            </div>

            <!-- password with hint -->
            <div class="flex justify-center">
                <input name="password" type="password" placeholder="Nhập mật khẩu (ít nhất 8 ký tự, tối đa 32)" required
                    class="w-64 bg-white border border-gray-200 rounded px-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-0" style="height:36px;" />
            </div>

            <!-- full name -->
            <div class="flex justify-center">
                <input name="full_name" type="text" placeholder="Họ tên" value="<?php echo htmlspecialchars($old['full_name'] ?? ''); ?>"
                    class="w-64 bg-white border border-gray-200 rounded px-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-0" style="height:36px;" />
            </div>

            <!-- gender radios -->
            <div class="flex justify-center">
                <div class="w-64 flex items-center justify-start space-x-4 text-sm">
                    <label class="flex items-center"><input type="radio" name="gender" value="male" checked class="form-radio" /> <span class="ml-2">Nam</span></label>
                    <label class="flex items-center"><input type="radio" name="gender" value="female" class="form-radio" /> <span class="ml-2">Nữ</span></label>
                </div>
            </div>

            <!-- birthday selects -->
            <div class="flex justify-center">
                <div class="w-64 flex items-center justify-between">
                    <select name="birth_year" class="bg-white border border-gray-200 rounded px-2 text-sm" style="height:32px;">
                        <option value="">Năm</option>
                        <?php for($y = date('Y'); $y >= 1900; $y--): ?>
                            <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="birth_month" class="bg-white border border-gray-200 rounded px-2 text-sm" style="height:32px;">
                        <option value="">Tháng</option>
                        <?php for($m=1;$m<=12;$m++): ?>
                            <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="birth_day" class="bg-white border border-gray-200 rounded px-2 text-sm" style="height:32px;">
                        <option value="">Ngày</option>
                        <?php for($d=1;$d<=31;$d++): ?>
                            <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- terms -->
            <div class="flex justify-center">
                <div class="w-64 text-sm">
                                        <label class="flex items-start"><input type="checkbox" name="agree" value="1" class="form-checkbox mt-1"/> <span class="ml-2 text-xs">Tôi đã đọc và đồng ý với <a href="/terms" class="text-green-700 hover:underline">Điều khoản giao dịch chung</a> và <a href="/privacy" class="text-green-700 hover:underline">Chính sách bảo mật</a></span></label>
                </div>
            </div>

            <div class="flex justify-center">
                <button type="submit" class="bg-green-700 text-white rounded-full w-40 py-2 text-sm font-medium hover:bg-green-800">Đăng ký</button>
            </div>

            <div class="text-center text-sm text-gray-600 mt-1">
                Bạn đã có tài khoản? <a href="/login" class="text-green-700 font-semibold hover:underline">ĐĂNG NHẬP</a>
            </div>

            <!-- hidden fields for client-generated codes -->
            <input type="hidden" name="captcha_generated" id="captcha_generated" value="" />
            <input type="hidden" name="generated_code" id="generated_code" value="" />
        </form>
    </div>
</div>

<script>
// close button behavior: redirect to home
(function(){
    var btn = document.querySelector('.register-close');
    if (!btn) return;
    btn.addEventListener('click', function(){
        window.location.href = '/';
    });

    // helpers
    function randomCaptcha() {
        var chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        var out = '';
        for (var i=0;i<4;i++) out += chars.charAt(Math.floor(Math.random()*chars.length));
        return out;
    }
    function randomCode() {
        return Math.floor(100000 + Math.random()*900000).toString();
    }

    var captchaBox = document.getElementById('captcha-box');
    var captchaHidden = document.getElementById('captcha_generated');
    // use server-provided captcha if available
    var serverCaptcha = '<?php echo isset($captcha) ? addslashes($captcha) : ''; ?>';
    var gen = serverCaptcha || randomCaptcha();
    if (captchaBox) captchaBox.textContent = gen;
    if (captchaHidden) captchaHidden.value = gen;

    var sendBtn = document.getElementById('send-code');
    var generatedCodeInput = document.getElementById('generated_code');
    var cooldown = 0; var cooldownTimer = null;
    if (sendBtn) sendBtn.addEventListener('click', function(){
        if (cooldown > 0) return;
        var emailInput = document.querySelector('input[name="email"]');
        if (!emailInput) return alert('Vui lòng nhập email trước khi lấy mã');
        var emailVal = emailInput.value.trim();
        var emailRe = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
        if (!emailRe.test(emailVal)) return alert('Email không hợp lệ');

        // send request to server to send code
        fetch('/account/send-code', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(emailVal)
        }).then(function(res){
            return res.json();
        }).then(function(json){
            if (json.ok) {
                cooldown = 60;
                sendBtn.textContent = 'Gửi lại ('+cooldown+'s)';
                cooldownTimer = setInterval(function(){
                    cooldown--;
                    if (cooldown <= 0) { clearInterval(cooldownTimer); cooldownTimer = null; sendBtn.textContent = 'lấy mã'; }
                    else { sendBtn.textContent = 'Gửi lại ('+cooldown+'s)'; }
                }, 1000);
                alert('Mã xác thực đã được gửi tới email của bạn');
            } else {
                alert(json.message || 'Lỗi khi gửi mã');
            }
        }).catch(function(err){
            alert('Lỗi mạng: ' + err.message);
        });
    });

    // final client-side validation before submit
    var form = document.getElementById('register-form');
    if (!form) return;
    form.addEventListener('submit', function(e){
        var email = form.querySelector('input[name="email"]').value.trim();
        var password = form.querySelector('input[name="password"]').value;
        var captcha = form.querySelector('input[name="captcha"]').value.trim();
        var verification = form.querySelector('input[name="verification_code"]').value.trim();
        var agree = form.querySelector('input[name="agree"]');
        var errors = [];
        var emailRe = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
        if (!emailRe.test(email)) errors.push('Email không hợp lệ.');
        var passRe = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,32}$/;
        if (!passRe.test(password)) errors.push('Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.');
        if (captcha.toLowerCase() !== (captchaHidden ? captchaHidden.value.toLowerCase() : '')) errors.push('Captcha không đúng.');
        if (generatedCodeInput && verification !== generatedCodeInput.value) errors.push('Mã xác thực không đúng.');
        if (!agree || !agree.checked) errors.push('Bạn phải đồng ý với điều khoản.');
        if (errors.length) {
            e.preventDefault();
            alert(errors.join('\n'));
            // regenerate captcha on client after failed submit attempt
            var newc = randomCaptcha();
            if (captchaBox) captchaBox.textContent = newc;
            if (captchaHidden) captchaHidden.value = newc;
        } else {
            // regenerate captcha on submit (so next load has a fresh one if redirected back)
            var newc = randomCaptcha();
            if (captchaBox) captchaBox.textContent = newc;
            if (captchaHidden) captchaHidden.value = newc;
        }
    });
})();
</script>

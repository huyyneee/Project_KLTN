<?php
// Component: register form
$errors = $errors ?? [];
$old = $old ?? [];
?>
<style>
    .register-close {
        display: none !important;
    }
</style>
<div class="relative bg-white rounded-lg shadow-sm max-w-md mx-auto" style="padding:16px; border:1px solid #eef2f6; box-shadow:0 6px 18px rgba(0,0,0,0.04); overflow:visible;">
    <button type="button" class="register-close bg-white border border-gray-200 rounded-full w-7 h-7 flex items-center justify-center shadow-sm focus:outline-none" aria-label="Đóng" style="position:absolute; right:450px; top:10px; z-index:999;">
        <svg class="w-3 h-3 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <h1 class="text-center text-sm font-semibold text-gray-600 mb-4 tracking-wide uppercase">ĐĂNG KÝ TÀI KHOẢN</h1>

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
            <div class="flex justify-center">
                <div style="width:256px; display:flex; gap:4px;">
                    <input name="captcha" type="text" placeholder="Nhập mã captcha"
                        class="flex-1 bg-white border border-gray-200 rounded px-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-0"
                        style="height:32px;" />
                    <canvas id="captcha-canvas" width="80" height="32" style="border-radius:6px; background:#1f9a6e; cursor:pointer; height:32px;"></canvas>
                </div>
            </div>
            <!-- verification code row: fixed wrapper so button doesn't push input -->
            <div class="flex justify-center">
                <div style="width:256px; display:flex; gap:4px;">
                    <input name="verification_code" id="verification_code" type="text" placeholder="Nhập mã xác thực"
                        value="<?php echo htmlspecialchars($old['verification_code'] ?? ''); ?>"
                        class="flex-1 bg-white border border-gray-200 rounded px-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-0"
                        style="height:32px;" />
                    <?php if (!empty($message) && !empty($require_verification)): ?>
                        <!-- inline hint removed as requested; message will be shown via modal. -->
                    <?php endif; ?>
                    <button type="button" id="send-code" class="bg-green-700 text-white rounded px-3 py-1 text-sm" style="height:32px; min-width:60px; text-align:center;">Lấy mã</button>
                </div>
            </div>
            <!-- password with hint -->
            <div class="flex justify-center">
                <input name="password" type="password" placeholder="Nhập mật khẩu (ít nhất 8 ký tự, tối đa 32)" required value="<?php echo htmlspecialchars($old['password'] ?? ''); ?>"
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
                    <label class="flex items-center"><input type="radio" name="gender" value="male" <?php echo (isset($old['gender']) && $old['gender'] === 'male') ? 'checked' : (!isset($old['gender']) ? 'checked' : ''); ?> class="form-radio" /> <span class="ml-2">Nam</span></label>
                    <label class="flex items-center"><input type="radio" name="gender" value="female" <?php echo (isset($old['gender']) && $old['gender'] === 'female') ? 'checked' : ''; ?> class="form-radio" /> <span class="ml-2">Nữ</span></label>
                </div>
            </div>

            <!-- birthday selects -->
            <div class="flex justify-center">
                <div class="w-64 flex items-center justify-between">
                    <select name="birth_year" class="bg-white border border-gray-200 rounded px-2 text-sm" style="height:32px;">
                        <option value="">Năm</option>
                        <?php for ($y = date('Y'); $y >= 1900; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo (isset($old['birth_year']) && $old['birth_year'] == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="birth_month" class="bg-white border border-gray-200 rounded px-2 text-sm" style="height:32px;">
                        <option value="">Tháng</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo (isset($old['birth_month']) && $old['birth_month'] == $m) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="birth_day" class="bg-white border border-gray-200 rounded px-2 text-sm" style="height:32px;">
                        <option value="">Ngày</option>
                        <?php for ($d = 1; $d <= 31; $d++): ?>
                            <option value="<?php echo $d; ?>" <?php echo (isset($old['birth_day']) && $old['birth_day'] == $d) ? 'selected' : ''; ?>><?php echo $d; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- terms -->
            <div class="flex justify-center">
                <div class="w-64 text-sm">
                    <label class="flex items-start"><input type="checkbox" name="agree" value="1" class="form-checkbox mt-1" /> <span class="ml-2 text-xs">Tôi đã đọc và đồng ý với <a href="/terms" class="text-green-700 hover:underline">Điều khoản giao dịch chung</a> và <a href="/privacy" class="text-green-700 hover:underline">Chính sách bảo mật</a></span></label>
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
    (function() {
        var btn = document.querySelector('.register-close');
        if (!btn) return;
        btn.addEventListener('click', function() {
            window.location.href = '/';
        });

        // helpers
        function randomCaptcha() {
            var chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
            var out = '';
            for (var i = 0; i < 4; i++) out += chars.charAt(Math.floor(Math.random() * chars.length));
            return out;
        }

        // draw captcha into canvas with noise/rotation to mimic image-based captcha
        function drawCaptcha(text) {
            var canvas = document.getElementById('captcha-canvas');
            if (!canvas) return;
            var dpr = window.devicePixelRatio || 1;
            var w = 60,
                h = 32; // giống ô lấy mã (w-64, height 32px)
            canvas.style.width = w + 'px';
            canvas.style.height = h + 'px';
            canvas.width = Math.round(w * dpr);
            canvas.height = Math.round(h * dpr);
            var ctx = canvas.getContext('2d');
            ctx.scale(dpr, dpr);

            // background
            ctx.clearRect(0, 0, w, h);
            ctx.fillStyle = '#1f9a6e';
            ctx.fillRect(0, 0, w, h);

            // subtle gradient
            var grad = ctx.createLinearGradient(0, 0, w, 0);
            grad.addColorStop(0, 'rgba(255,255,255,0.04)');
            grad.addColorStop(1, 'rgba(0,0,0,0.02)');
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, w, h);

            // noise lines phù hợp chiều cao nhỏ
            var noiseLines = 10;
            for (var i = 0; i < noiseLines; i++) {
                ctx.beginPath();
                var alpha = 0.06 + Math.random() * 0.12;
                ctx.strokeStyle = 'rgba(255,255,255,' + alpha + ')';
                ctx.lineWidth = 0.6 + Math.random() * 1.2;
                var x1 = Math.random() * w,
                    y1 = Math.random() * h;
                var x2 = Math.random() * w,
                    y2 = Math.random() * h;
                ctx.moveTo(x1, y1);
                ctx.quadraticCurveTo((x1 + x2) / 2 + (Math.random() * 10 - 5), (y1 + y2) / 2 + (Math.random() * 6 - 3), x2, y2);
                ctx.stroke();
            }

            // draw chars với font nhỏ hơn, phù hợp chiều cao 32px
            var len = text.length;
            var baseX = 12;
            for (var i = 0; i < len; i++) {
                var ch = text.charAt(i);
                var fontSize = 14 + Math.floor(Math.random() * 6);
                ctx.font = fontSize + 'px Georgia, serif';
                ctx.textBaseline = 'middle';
                var x = baseX + i * (w - 24) / len + (Math.random() * 4 - 2);
                var y = h / 2 + (Math.random() * 6 - 3);
                var angle = (Math.random() * 25 - 12) * Math.PI / 180;

                ctx.save();
                ctx.translate(x, y);
                ctx.rotate(angle);
                ctx.lineWidth = 1.5;
                ctx.strokeStyle = 'rgba(0,0,0,0.35)';
                ctx.strokeText(ch, 0, 0);
                ctx.fillStyle = '#ffffff';
                ctx.fillText(ch, 0, 0);
                ctx.restore();
            }

            // wavy disturbance line nhỏ
            ctx.beginPath();
            ctx.lineWidth = 1.0;
            ctx.strokeStyle = 'rgba(0,0,0,0.12)';
            for (var xx = 0; xx < w; xx += 6) {
                var yy = h / 2 + Math.sin(xx / 10 + Math.random() * 1.5) * (2 + Math.random() * 2);
                if (xx === 0) ctx.moveTo(xx, yy);
                else ctx.lineTo(xx, yy);
            }
            ctx.stroke();

            // extra dots vừa phải
            for (var i = 0; i < 60; i++) {
                ctx.fillStyle = 'rgba(255,255,255,' + (0.03 + Math.random() * 0.08) + ')';
                ctx.beginPath();
                ctx.arc(Math.random() * w, Math.random() * h, Math.random() * 1.5, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function randomCode() {
            return Math.floor(100000 + Math.random() * 900000).toString();
        }

        var captchaBox = document.getElementById('captcha-canvas');
        var captchaHidden = document.getElementById('captcha_generated');
        // use server-provided captcha if available
        var serverCaptcha = '<?php echo isset($captcha) ? addslashes($captcha) : ''; ?>';
        var gen = serverCaptcha || randomCaptcha();
        if (captchaBox) drawCaptcha(gen);
        if (captchaHidden) captchaHidden.value = gen;

        if (captchaBox) captchaBox.addEventListener('click', function() {
            var newc = randomCaptcha();
            if (captchaHidden) captchaHidden.value = newc;
            drawCaptcha(newc);
        });

        var sendBtn = document.getElementById('send-code');
        var generatedCodeInput = document.getElementById('generated_code');
        var cooldown = 0;
        var cooldownTimer = null;
        if (sendBtn) sendBtn.addEventListener('click', function() {
            if (cooldown > 0) return;
            var emailInput = document.querySelector('input[name="email"]');
            if (!emailInput) return alert('Vui lòng nhập email trước khi lấy mã');
            var emailVal = emailInput.value.trim();
            var emailRe = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            if (!emailRe.test(emailVal)) return alert('Email không hợp lệ');
            // check if email already exists first
            fetch('/account/check-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'email=' + encodeURIComponent(emailVal)
            }).then(function(res) {
                return res.json();
            }).then(function(js) {
                if (js.exists) {
                    // show reusable modal (modelok) with ĐĂNG NHẬP and OK
                    showModelOk('TÀI KHOẢN ĐÃ ĐƯỢC TẠO, VUI LÒNG ĐĂNG NHẬP HOẶC SỬ DỤNG EMAIL KHÁC');
                    return;
                }
                // send request to server to send code
                return fetch('/account/send-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'email=' + encodeURIComponent(emailVal)
                });
            }).then(function(res) {
                if (!res) return;
                return res.json();
            }).then(function(json) {
                if (!json) return;
                if (json.ok) {
                    cooldown = 60;
                    sendBtn.textContent = 'Gửi lại (' + cooldown + 's)';
                    cooldownTimer = setInterval(function() {
                        cooldown--;
                        if (cooldown <= 0) {
                            clearInterval(cooldownTimer);
                            cooldownTimer = null;
                            sendBtn.textContent = 'lấy mã';
                        } else {
                            sendBtn.textContent = 'Gửi lại (' + cooldown + 's)';
                        }
                    }, 1000);
                    showDialog('Mã xác thực đã được gửi tới email của bạn');
                } else {
                    showDialog(json.message || 'Lỗi khi gửi mã');
                }
            }).catch(function(err) {
                showDialog('Lỗi mạng: ' + err.message);
            });
        });

        // final client-side validation and AJAX-send-code flow
        var form = document.getElementById('register-form');
        if (!form) return;

        // dialog helper (matches screenshot style)
        function showDialog(msg) {
            var existing = document.getElementById('simple-dialog');
            if (existing) existing.parentNode.removeChild(existing);
            var d = document.createElement('div');
            d.id = 'simple-dialog';
            d.style.position = 'fixed';
            d.style.left = '50%';
            d.style.top = '18%';
            d.style.transform = 'translateX(-50%)';
            d.style.background = '#fff';
            d.style.border = '1px solid #e5e7eb';
            d.style.boxShadow = '0 6px 20px rgba(0,0,0,0.12)';
            d.style.padding = '10px 12px';
            d.style.zIndex = 9999;
            d.style.borderRadius = '6px';
            d.style.minWidth = '260px';
            d.innerHTML = '<div style="display:flex;align-items:center;gap:8px;"><div style="width:28px;height:28px;background:#fff;border-radius:6px;display:flex;align-items:center;justify-content:center;border:2px solid #f59e0b;color:#f59e0b;font-weight:700">!</div><div style="font-size:13px;color:#111">' + msg + '</div></div>';
            document.body.appendChild(d);
            setTimeout(function() {
                try {
                    d.parentNode.removeChild(d);
                } catch (e) {}
            }, 4500);
        }

        // reusable modal named "modelok" — two-button modal with ĐĂNG NHẬP and OK
        function showModelOk(message) {
            // remove existing modelok if present
            var old = document.getElementById('modelok');
            if (old) old.parentNode.removeChild(old);

            var overlay = document.createElement('div');
            overlay.id = 'modelok';
            overlay.style.position = 'fixed';
            overlay.style.left = '0';
            overlay.style.top = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.background = 'rgba(0,0,0,0.4)';
            overlay.style.display = 'flex';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.zIndex = 10000;

            var box = document.createElement('div');
            box.style.background = '#fff';
            box.style.padding = '18px';
            box.style.borderRadius = '8px';
            box.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
            box.style.maxWidth = '420px';
            box.style.width = '90%';
            box.style.textAlign = 'center';

            var icon = document.createElement('div');
            icon.style.width = '40px';
            icon.style.height = '40px';
            icon.style.margin = '0 auto 8px';
            icon.style.borderRadius = '8px';
            icon.style.display = 'flex';
            icon.style.alignItems = 'center';
            icon.style.justifyContent = 'center';
            icon.style.border = '2px solid #f43f5e';
            icon.style.color = '#f43f5e';
            icon.style.fontWeight = '700';
            icon.textContent = '!';

            var msg = document.createElement('div');
            msg.style.fontSize = '14px';
            msg.style.color = '#111';
            msg.style.marginBottom = '14px';
            msg.textContent = message;

            var actions = document.createElement('div');
            actions.style.display = 'flex';
            actions.style.justifyContent = 'center';
            actions.style.gap = '10px';

            var loginBtn = document.createElement('button');
            loginBtn.type = 'button';
            loginBtn.textContent = 'ĐĂNG NHẬP';
            loginBtn.style.background = '#10b981';
            loginBtn.style.color = '#fff';
            loginBtn.style.border = 'none';
            loginBtn.style.padding = '8px 12px';
            loginBtn.style.borderRadius = '6px';
            loginBtn.style.cursor = 'pointer';

            var okBtn = document.createElement('button');
            okBtn.type = 'button';
            okBtn.textContent = 'OK';
            okBtn.style.background = '#fff';
            okBtn.style.color = '#111';
            okBtn.style.border = '1px solid #e5e7eb';
            okBtn.style.padding = '8px 12px';
            okBtn.style.borderRadius = '6px';
            okBtn.style.cursor = 'pointer';

            actions.appendChild(loginBtn);
            actions.appendChild(okBtn);

            box.appendChild(icon);
            box.appendChild(msg);
            box.appendChild(actions);
            overlay.appendChild(box);
            document.body.appendChild(overlay);

            loginBtn.addEventListener('click', function() {
                // navigate to login
                window.location.href = '/login';
            });
            okBtn.addEventListener('click', function() {
                try {
                    overlay.parentNode.removeChild(overlay);
                } catch (e) {}
                // refresh page
                window.location.reload();
            });
        }

        // if server indicates verification is required, focus the verification input
        <?php if (!empty($require_verification)): ?>
            try {
                var v = document.getElementById('verification_code');
                if (v) {
                    v.focus();
                    v.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            } catch (e) {}
        <?php endif; ?>

        <?php if (!empty($show_modelok)): ?>
            try {
                showModelOk(<?php echo json_encode($modelok_message ?? ''); ?>);
            } catch (e) {}
        <?php endif; ?>

        <?php if (!empty($created_account)): ?>
            try {
                // show a simple success dialog then redirect to login after short delay
                showDialog('ĐĂNG KÝ THÀNH CÔNG');
                setTimeout(function() {
                    window.location.href = '/login';
                }, 5000);
            } catch (e) {}
        <?php endif; ?>

        <?php if (!empty($message) && !empty($require_verification)): ?>
            try {
                showDialog(<?php echo json_encode($message); ?>);
            } catch (e) {}
        <?php endif; ?>

        form.addEventListener('submit', function(e) {
            var email = form.querySelector('input[name="email"]').value.trim();
            var password = form.querySelector('input[name="password"]').value;
            var captcha = form.querySelector('input[name="captcha"]').value.trim();
            var verification = form.querySelector('input[name="verification_code"]').value.trim();
            var agree = form.querySelector('input[name="agree"]');
            var fullname = form.querySelector('input[name="full_name"]').value.trim();
            var by = form.querySelector('select[name="birth_year"]').value;
            var bm = form.querySelector('select[name="birth_month"]').value;
            var bd = form.querySelector('select[name="birth_day"]').value;

            var errors = [];
            var emailRe = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            if (!emailRe.test(email)) errors.push('Email không hợp lệ.');
            if (!email.toLowerCase().endsWith('@gmail.com')) errors.push('Email phải có định dạng @gmail.com');
            var passRe = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,32}$/;
            if (!passRe.test(password)) errors.push('Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.');
            if (!fullname) errors.push('Vui lòng điền họ tên.');
            if (!by || !bm || !bd) {
                errors.push('Vui lòng chọn ngày sinh.');
            } else {
                var birthTs = Date.parse(by + '-' + (bm.padStart ? bm.padStart(2, '0') : ('0' + bm)) + '-' + (bd.padStart ? bd.padStart(2, '0') : ('0' + bd)));
                if (!birthTs) errors.push('Ngày sinh không hợp lệ.');
                else {
                    var age = Math.floor((Date.now() - birthTs) / (365.25 * 24 * 60 * 60 * 1000));
                    if (age < 18) {
                        e.preventDefault();
                        showDialog('Người dùng phải đủ 18 tuổi trở lên.');
                        return;
                    }
                }
            }
            if (captcha.toLowerCase() !== (captchaHidden ? captchaHidden.value.toLowerCase() : '')) errors.push('Captcha không đúng.');
            if (!agree || !agree.checked) errors.push('Bạn phải đồng ý với điều khoản.');

            if (errors.length) {
                e.preventDefault();
                showDialog(errors.join('\n'));
                var newc = randomCaptcha();
                if (captchaBox) drawCaptcha(newc);
                if (captchaHidden) captchaHidden.value = newc;
                return;
            }

            // If verification empty, check email then send code via AJAX, keep form data intact and show prompt
            if (!verification || verification === '') {
                e.preventDefault();
                fetch('/account/check-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'email=' + encodeURIComponent(email)
                }).then(function(res) {
                    return res.json();
                }).then(function(js) {
                    if (js.exists) {
                        showModelOk('TÀI KHOẢN ĐÃ ĐƯỢC TẠO, VUI LÒNG ĐĂNG NHẬP HOẶC SỬ DỤNG EMAIL KHÁC');
                        return;
                    }
                    return fetch('/account/send-code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'email=' + encodeURIComponent(email)
                    });
                }).then(function(res) {
                    if (!res) return;
                    return res.json();
                }).then(function(json) {
                    if (!json) return;
                    if (json.ok) {
                        showDialog('Mã xác thực đã được gửi tới email. Vui lòng nhập mã ở ô "Nhập mã xác thực".');
                        var v = document.getElementById('verification_code');
                        if (v) {
                            v.focus();
                        }
                    } else {
                        showDialog(json.message || 'Lỗi khi gửi mã xác thực');
                    }
                }).catch(function(err) {
                    showDialog('Lỗi mạng: ' + err.message);
                });
                return;
            }

            // normal submit proceeds (browser will send form)
        });
    })();
</script>
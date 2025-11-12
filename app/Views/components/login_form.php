<?php
// Component: login form
// Usage: include this component inside a modal or page where needed
?>

<div class="relative bg-white rounded-lg shadow-sm max-w-md mx-auto" style="padding:20px; border:1px solid #eef2f6; box-shadow:0 6px 18px rgba(0,0,0,0.04); overflow:visible;">
    <!-- close button anchored absolute to top-right of the card (visible outside when needed) -->
    <!-- <button type="button" class="login-close bg-white border border-gray-200 rounded-full w-7 h-7 flex items-center justify-center shadow-sm focus:outline-none" aria-label="Đóng" style="position:absolute; right:500px; top:1px; z-index:999;">
        <svg class="w-3 h-3 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button> -->
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
                    <label class="flex items-center text-gray-700 text-sm"><input type="checkbox" name="remember" class="form-checkbox" /> <span class="ml-2">Nhớ mật khẩu</span></label>
                    <a href="/login/forgot-password" class="text-green-700 hover:underline text-sm">Quên mật khẩu</a>
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
    (function() {
        var btn = document.querySelector('.login-close');
        if (!btn) return;
        btn.addEventListener('click', function() {
            window.location.href = '/';
        });
    })();
</script>

<script>
    // Enhance login form: AJAX submit and modelok handling
    (function() {
        // small dialog helper (reuse showDialog from register form if present)
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

        // reuse modelok modal used in register_form if available, otherwise recreate locally
        function showModelOk(message, onSignup, onCancel) {
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
            var signup = document.createElement('button');
            signup.type = 'button';
            signup.textContent = 'ĐĂNG KÝ';
            signup.style.background = '#10b981';
            signup.style.color = '#fff';
            signup.style.border = 'none';
            signup.style.padding = '8px 12px';
            signup.style.borderRadius = '6px';
            signup.style.cursor = 'pointer';
            var cancel = document.createElement('button');
            cancel.type = 'button';
            cancel.textContent = 'THOÁT';
            cancel.style.background = '#fff';
            cancel.style.color = '#111';
            cancel.style.border = '1px solid #e5e7eb';
            cancel.style.padding = '8px 12px';
            cancel.style.borderRadius = '6px';
            cancel.style.cursor = 'pointer';
            actions.appendChild(signup);
            actions.appendChild(cancel);
            box.appendChild(icon);
            box.appendChild(msg);
            box.appendChild(actions);
            overlay.appendChild(box);
            document.body.appendChild(overlay);
            signup.addEventListener('click', function() {
                try {
                    onSignup && onSignup();
                } catch (e) {}
            });
            cancel.addEventListener('click', function() {
                try {
                    onCancel && onCancel();
                } catch (e) {}
            });
        }

        var form = document.getElementById('login-form');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var id = form.querySelector('input[name="identity"]').value.trim();
            var pw = form.querySelector('input[name="password"]').value;
            if (!id || !pw) {
                showDialog('Vui lòng nhập đầy đủ thông tin');
                return;
            }

            // preserve return query param when present so server can redirect back
            var search = '';
            try {
                var qp = new URLSearchParams(window.location.search);
                if (qp.has('return')) search = '?return=' + encodeURIComponent(qp.get('return'));
            } catch (e) {
                search = '';
            }

            fetch('/account/login' + search, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'identity=' + encodeURIComponent(id) + '&password=' + encodeURIComponent(pw)
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(js) {
                    if (!js) {
                        showDialog('Lỗi server');
                        return;
                    }
                    if (js.ok) {
                        // redirect if provided
                        window.location.href = js.redirect || '/';
                        return;
                    }

                    // handle reasons
                    if (js.reason === 'not_found') {
                        showModelOk('TÀI KHOẢN KHÔNG TỒN TẠI, VUI LÒNG TẠO TÀI KHOẢN', function() {
                            window.location.href = '/register';
                        }, function() {
                            window.location.href = '/';
                        });
                        return;
                    }
                    if (js.reason === 'bad_password') {
                        showDialog('please check password');
                        return;
                    }
                    if (js.reason === 'status') {
                        var st = js.status || 'unknown';
                        showDialog('YOUR ACCOUNT HAS BEEN ' + st.toUpperCase());
                        return;
                    }

                    showDialog(js.message || 'Login failed');
                }).catch(function(err) {
                    showDialog('Lỗi mạng: ' + err.message);
                });
        });
    })();
</script>
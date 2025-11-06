<?php
// Simple Hasaki Chat Bubble Widget (drop-in PHP file)
// Usage: include this file on any PHP page.
// Configure via constants or URL params (e.g., ?api_base=https://api.example.com/api)

// -------------------- Configuration --------------------
// API base URL of your Hasaki chatbot backend
$API_BASE = isset($_GET['api_base']) ? $_GET['api_base'] : 'http://localhost:6060/api';

// Product URL building (choose either path+param or slug style)
// Pattern A (query style): <domain><PRODUCT_DETAIL_PATH>?<PRODUCT_DETAIL_QUERY_PARAM>=<id>
$PRODUCT_DOMAIN = isset($_GET['product_domain']) ? $_GET['product_domain'] : (isset($_SERVER['HTTP_HOST']) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] : '');
$PRODUCT_DETAIL_PATH = isset($_GET['product_detail_path']) ? $_GET['product_detail_path'] : null; // e.g., /san-pham
$PRODUCT_DETAIL_QUERY_PARAM = isset($_GET['product_detail_param']) ? $_GET['product_detail_param'] : null; // e.g., product

// Pattern B (path style): <domain><PRODUCT_DETAIL_PREFIX><id>
$PRODUCT_DETAIL_PREFIX = isset($_GET['product_detail_prefix']) ? $_GET['product_detail_prefix'] : '/product/';

// UI labels
$WIDGET_TITLE = isset($_GET['title']) ? $_GET['title'] : 'Hasaki Chat';
$PLACEHOLDER = isset($_GET['placeholder']) ? $_GET['placeholder'] : 'Nhập câu hỏi của bạn...';
$SEND_LABEL = isset($_GET['send_label']) ? $_GET['send_label'] : 'Gửi';

// Quick prompts (pipe-separated via ?prompts=...) with sensible defaults
$PROMPTS = isset($_GET['prompts']) && $_GET['prompts'] !== ''
    ? array_values(array_filter(array_map('trim', explode('|', $_GET['prompts']))))
    : [
        'Bạn có những thương hiệu nào?',
        'Bạn có những danh mục nào?',
        'Tìm nước hoa Calvin Klein',
        'Nước hoa nam',
        'Nước hoa nữ',
        'Tìm sản phẩm YSL'
    ];

// -------------------------------------------------------
?>
<style>
    .hasaki-chat-widget-container {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 999999;
        font-family: -apple-system, Segoe UI, Roboto, sans-serif;
    }

    .hasaki-chat-bubble {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .hasaki-chat-bubble:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(0, 0, 0, .25);
    }

    .hasaki-chat-window {
        width: 340px;
        max-height: 520px;
        position: absolute;
        right: 0;
        bottom: 72px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, .18);
        overflow: hidden;
        display: none;
    }

    .hasaki-chat-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .hasaki-chat-title {
        font-size: 15px;
        font-weight: 700;
    }

    .hasaki-chat-close {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
    }

    .hasaki-chat-body {
        display: flex;
        flex-direction: column;
        height: 420px;
    }

    .hasaki-chat-messages {
        flex: 1;
        padding: 12px;
        overflow-y: auto;
        background: #f8f9fa;
    }

    .hasaki-msg {
        margin-bottom: 10px;
        display: flex;
    }

    .hasaki-msg-user {
        justify-content: flex-end;
    }

    .hasaki-msg-bot {
        justify-content: flex-start;
    }

    .hasaki-msg-content {
        max-width: 75%;
        padding: 10px 12px;
        border-radius: 14px;
        font-size: 13px;
        line-height: 1.45;
    }

    .hasaki-msg-user .hasaki-msg-content {
        background: #667eea;
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .hasaki-msg-bot .hasaki-msg-content {
        background: #fff;
        color: #333;
        border: 1px solid #e1e5e9;
        border-bottom-left-radius: 4px;
    }

    .hasaki-products {
        margin-top: 8px;
    }

    .hasaki-product-card {
        background: #f8f9fa;
        border: 1px solid #e1e5e9;
        border-radius: 8px;
        padding: 8px;
        margin: 6px 0;
        cursor: pointer;
    }

    .hasaki-product-name {
        font-weight: 700;
        color: #667eea;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .hasaki-product-name a {
        color: inherit;
        text-decoration: none;
    }

    .hasaki-product-name a:hover {
        text-decoration: underline;
    }

    .hasaki-product-details {
        font-size: 12px;
        color: #666;
    }

    .hasaki-chat-input {
        display: flex;
        gap: 8px;
        padding: 10px;
        border-top: 1px solid #e1e5e9;
        background: #fff;
    }

    .hasaki-chat-text {
        flex: 1;
        border: 2px solid #e1e5e9;
        border-radius: 18px;
        padding: 10px 12px;
        font-size: 13px;
        outline: none;
    }

    .hasaki-chat-text:focus {
        border-color: #667eea;
    }

    .hasaki-chat-send {
        background: #667eea;
        color: #fff;
        border: none;
        border-radius: 18px;
        padding: 10px 14px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
    }

    .hasaki-loading {
        display: none;
        text-align: center;
        color: #666;
        font-style: italic;
        font-size: 12px;
        padding: 6px 0;
    }

    .hasaki-quick {
        padding: 8px 10px;
        background: #fff;
        border-top: 1px solid #e1e5e9;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .hasaki-quick-label {
        font-size: 12px;
        color: #333;
        margin-bottom: 6px;
    }

    .hasaki-quick-buttons {
        display: inline-flex;
        flex-wrap: nowrap;
        gap: 6px;
        white-space: nowrap;
    }

    .hasaki-quick-btn {
        background: #fff;
        border: 1px solid #e1e5e9;
        color: #667eea;
        padding: 6px 10px;
        border-radius: 14px;
        font-size: 12px;
        cursor: pointer;
    }

    .hasaki-quick-btn:hover {
        background: #667eea;
        color: #fff;
    }
</style>

<div class="hasaki-chat-widget-container" id="hasakiChatWidget">
    <div class="hasaki-chat-window" id="hasakiChatWindow">
        <div class="hasaki-chat-header">
            <div class="hasaki-chat-title"><?php echo htmlspecialchars($WIDGET_TITLE, ENT_QUOTES); ?></div>
            <button class="hasaki-chat-close" id="hasakiChatClose" aria-label="Đóng">×</button>
        </div>
        <div class="hasaki-chat-body">
            <div class="hasaki-chat-messages" id="hasakiChatMessages"></div>
            <div class="hasaki-loading" id="hasakiChatLoading">Đang tìm kiếm...</div>
            <div class="hasaki-quick" id="hasakiQuick" style="display:none;">
                <div class="hasaki-quick-label">Gợi ý nhanh:</div>
                <div class="hasaki-quick-buttons" id="hasakiQuickButtons"></div>
            </div>
            <div class="hasaki-chat-input">
                <input type="text" id="hasakiChatInput" class="hasaki-chat-text"
                    placeholder="<?php echo htmlspecialchars($PLACEHOLDER, ENT_QUOTES); ?>" />
                <button id="hasakiChatSend"
                    class="hasaki-chat-send"><?php echo htmlspecialchars($SEND_LABEL, ENT_QUOTES); ?></button>
            </div>
        </div>
    </div>
    <div class="hasaki-chat-bubble" id="hasakiChatBubble" title="Chat với Hasaki">
        <!-- chat icon -->
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M12 3C7.03 3 3 6.58 3 11c0 2.03 1.02 3.86 2.67 5.16-.1.81-.44 1.94-1.4 3.15 0 0 1.79.2 3.73-1.1.97.27 2.01.42 3.09.42 4.97 0 9-3.58 9-8s-4.03-8-9-8Z"
                fill="white" opacity=".9" />
        </svg>
    </div>
    <script>
        (function () {
            const API_BASE_URL = <?php echo json_encode($API_BASE); ?>;
            const PRODUCT_DOMAIN = (<?php echo json_encode(rtrim($PRODUCT_DOMAIN, '/')); ?>) || window.location.origin;
            const PRODUCT_DETAIL_PATH = <?php echo json_encode($PRODUCT_DETAIL_PATH); ?>; // may be null
            const PRODUCT_DETAIL_QUERY_PARAM = <?php echo json_encode($PRODUCT_DETAIL_QUERY_PARAM); ?>; // may be null
            const PRODUCT_DETAIL_PREFIX = <?php echo json_encode($PRODUCT_DETAIL_PREFIX); ?>; // default '/product/'
            const QUICK_PROMPTS = <?php echo json_encode(array_values($PROMPTS), JSON_UNESCAPED_UNICODE); ?>;

            function getProductUrl(productId) {
                if (PRODUCT_DETAIL_PATH && PRODUCT_DETAIL_QUERY_PARAM) {
                    return `${PRODUCT_DOMAIN}${PRODUCT_DETAIL_PATH}?${PRODUCT_DETAIL_QUERY_PARAM}=${encodeURIComponent(productId)}`;
                }
                return `${PRODUCT_DOMAIN}${PRODUCT_DETAIL_PREFIX}${productId}`;
            }

            const bubble = document.getElementById('hasakiChatBubble');
            const win = document.getElementById('hasakiChatWindow');
            const closeBtn = document.getElementById('hasakiChatClose');
            const input = document.getElementById('hasakiChatInput');
            const sendBtn = document.getElementById('hasakiChatSend');
            const messages = document.getElementById('hasakiChatMessages');
            const loading = document.getElementById('hasakiChatLoading');
            const quick = document.getElementById('hasakiQuick');
            const quickButtonsWrap = document.getElementById('hasakiQuickButtons');

            function toggle(show) { win.style.display = show ? 'block' : 'none'; }
            bubble.addEventListener('click', () => toggle(true));
            closeBtn.addEventListener('click', () => toggle(false));

            function scrollToBottom() { messages.scrollTop = messages.scrollHeight; }

            function addMsg(text, who) {
                const wrap = document.createElement('div');
                wrap.className = `hasaki-msg ${who === "user" ? "hasaki-msg-user" : "hasaki-msg-bot"}`;
                const content = document.createElement('div');
                content.className = 'hasaki-msg-content';
                content.textContent = text;
                wrap.appendChild(content);
                messages.appendChild(wrap);
                scrollToBottom();
            }

            function addBotMessage(answer, products) {
                addMsg(answer || '', 'bot');
                if (Array.isArray(products) && products.length) {
                    const list = document.createElement('div');
                    list.className = 'hasaki-products';
                    products.forEach((p, idx) => {
                        const card = document.createElement('div');
                        card.className = 'hasaki-product-card';
                        const name = document.createElement('div');
                        name.className = 'hasaki-product-name';
                        const url = getProductUrl(p.id);
                        const a = document.createElement('a');
                        a.href = url; a.target = '_blank'; a.rel = 'noopener';
                        a.textContent = `${idx + 1}. ${p.name || 'Sản phẩm'}`;
                        name.appendChild(a);
                        const details = document.createElement('div');
                        details.className = 'hasaki-product-details';
                        const parts = [];
                        if (p.brand) parts.push(`Thương hiệu: ${p.brand}`);
                        if (p.price) parts.push(`Giá: ${Number(p.price).toLocaleString()} đ`);
                        if (p.gender) parts.push(`Giới tính: ${p.gender}`);
                        if (p.concentration) parts.push(`Nồng độ: ${p.concentration}`);
                        if (p.volume) parts.push(`Dung tích: ${p.volume}`);
                        details.textContent = parts.join(' - ');
                        card.addEventListener('click', (e) => {
                            if (e.target && e.target.tagName && e.target.tagName.toLowerCase() === 'a') return;
                            window.open(url, '_blank', 'noopener');
                        });
                        card.appendChild(name);
                        card.appendChild(details);
                        list.appendChild(card);
                    });
                    const wrap = document.createElement('div');
                    wrap.className = 'hasaki-msg hasaki-msg-bot';
                    const content = document.createElement('div');
                    content.className = 'hasaki-msg-content';
                    content.appendChild(list);
                    wrap.appendChild(content);
                    messages.appendChild(wrap);
                    scrollToBottom();
                }
            }

            function setLoading(s) { loading.style.display = s ? 'block' : 'none'; sendBtn.disabled = s; }

            function attachQuickPrompts() {
                if (!Array.isArray(QUICK_PROMPTS) || !quickButtonsWrap) return;
                quickButtonsWrap.innerHTML = '';
                QUICK_PROMPTS.forEach((label) => {
                    const btn = document.createElement('button');
                    btn.className = 'hasaki-quick-btn';
                    btn.type = 'button';
                    btn.textContent = label;
                    btn.addEventListener('click', () => {
                        input.value = label;
                        hideQuick();
                        send();
                    });
                    quickButtonsWrap.appendChild(btn);
                });
            }

            function showQuickIfEligible() {
                try {
                    const hasSent = sessionStorage.getItem('hasaki_user_has_sent') === '1';
                    if (!hasSent && quick) { quick.style.display = 'block'; }
                } catch (_) { if (quick) { quick.style.display = 'block'; } }
            }

            function hideQuick() {
                if (quick) quick.style.display = 'none';
            }

            async function send() {
                const q = (input.value || '').trim();
                if (!q) return;
                addMsg(q, 'user');
                input.value = '';
                // Mark that user has sent a message and hide quick prompts permanently for this session
                try { sessionStorage.setItem('hasaki_user_has_sent', '1'); } catch (_) { }
                hideQuick();
                setLoading(true);
                try {
                    const res = await fetch(`${API_BASE_URL}/query`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ question: q, top_k: 5 }) });
                    const data = await res.json();
                    if (res.ok) {
                        addBotMessage(data.answer || '', data.products || []);
                    } else {
                        addMsg(`Lỗi: ${data.error || 'Có lỗi xảy ra'}`, 'bot');
                    }
                } catch (err) {
                    addMsg(`Lỗi kết nối: ${err && err.message ? err.message : err}`, 'bot');
                } finally {
                    setLoading(false);
                }
            }

            sendBtn.addEventListener('click', send);
            input.addEventListener('keypress', (e) => { if (e.key === 'Enter') send(); });

            // Greeting
            addBotMessage('👋 Xin chào! Tôi là trợ lý tìm kiếm sản phẩm Hasaki. Hãy hỏi tôi bất kỳ sản phẩm nào bạn quan tâm nhé!', []);
            attachQuickPrompts();
            showQuickIfEligible();
        })();
    </script>
</div>
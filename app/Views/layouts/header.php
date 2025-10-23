<?php
// Ensure session starts before any output to avoid "headers already sent" errors.
if (session_status() === PHP_SESSION_NONE) {
	if (!headers_sent()) {
		session_start();
	}
}
// If session is missing but cookies exist, attempt to restore session (7-day cookie login)
if (empty($_SESSION['account_id']) && !empty($_COOKIE['account_id']) && !empty($_COOKIE['account_email'])) {
	// first validate server-side expiry cookie (stored as unix timestamp)
	$expiresOk = false;
	if (!empty($_COOKIE['account_expires']) && ctype_digit($_COOKIE['account_expires'])) {
		$expiresTs = (int) $_COOKIE['account_expires'];
		if ($expiresTs > time()) $expiresOk = true;
	}
	if ($expiresOk) {
		try {
			$db = (new \App\Core\Database())->getConnection();
			// ensure the cookie values correspond to an active account
			$stmt = $db->prepare('SELECT id, email, status FROM accounts WHERE id = :id LIMIT 1');
			$stmt->execute([':id' => $_COOKIE['account_id']]);
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
			if ($row && ($row['email'] === $_COOKIE['account_email']) && ($row['status'] ?? 'active') === 'active') {
				// restore session
				$_SESSION['account_id'] = $row['id'];
				$_SESSION['account_email'] = $row['email'];
			}
		} catch (\Throwable $e) {
			// ignore - leave session empty
		}
	} else {
		// expired or invalid expiry: clear cookies to avoid repeated attempts
		setcookie('account_id', '', time() - 3600, '/', '', false, true);
		setcookie('account_email', '', time() - 3600, '/', '', false, true);
		setcookie('account_expires', '', time() - 3600, '/', '', false, true);
	}
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= isset($title) ? $title : 'App' ?></title>
	<link rel="stylesheet" href="/assets/css/output.css">

	<style>
		/* Dropdown hover for Tailwind JIT (fallback) */
		.group:hover .group-hover\:block {
			display: block;
		}

		/* JS-controlled open state (adds a small delay hide) */
		.group.open .group-hover\:block {
			display: block !important;
		}
	</style>
</head>

<body class="bg-white">
	<!-- Header image banner -->
	<div class="header-banner w-full bg-[#c2e3ce] flex flex-col items-center mt-16">
		<img src="/assets/images/header.png" alt="Header Banner" class="w-full object-cover" />
	</div>
	<header class="fixed top-0 left-0 w-full z-50 bg-green-800 text-white shadow header-main">
		<div class="max-w-7xl mx-auto flex items-center px-4 h-16">
			<!-- Left: Logo + Danh mục -->
			<div class="flex items-center flex-shrink-0 min-w-max space-x-2">
				<img src="/assets/images/logo.png" alt="Logo"
					class="h-12 w-12 object-contain bg-white rounded-full shadow" />
				<?php
				// Ensure $categories is available in the header. If the current controller
				// didn't pass categories, try to load them here so the dropdown shows on all pages.
				if (!isset($categories) || !is_array($categories) || empty($categories)) {
					try {
						$catModel = new \App\Models\CategoryModel();
						$categories = $catModel->findAll();
					} catch (\Throwable $e) {
						// fail silently — header will render without categories
						$categories = [];
					}
				}
				?>
				<div class="relative group">
					<button class="flex items-center px-3 py-2 focus:outline-none">
						<svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" stroke-width="2"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
						</svg>
						<span class="font-semibold tracking-wide">DANH MỤC</span>
					</button>
					<div
						class="absolute left-0 mt-2 w-64 bg-white text-green-900 rounded shadow-lg border border-green-200 group-hover:block hidden transition-all duration-200 min-w-max z-50">
						<?php if (!empty($categories) && is_array($categories)): ?>
							<?php foreach ($categories as $cat): ?>
								<a href="/danh-muc?cat=<?php echo urlencode($cat['name']); ?>"
									class="flex items-center justify-between px-4 py-2 hover:bg-green-100">
									<?php echo htmlspecialchars($cat['name']); ?>
									<svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
										viewBox="0 0 24 24">
										<path d="M9 5l7 7-7 7" />
									</svg>
								</a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
				<!-- Search box -->
				<form action="/" method="get" class="ml-2 flex items-center bg-white rounded">
					<input type="text" name="search"
						value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
						placeholder="Tìm sản phẩm..."
						class="px-3 py-1 rounded-l outline-none text-green-900 w-44 text-sm" />
					<button type="submit" class="px-2">
						<svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<circle cx="11" cy="11" r="8" />
							<path d="M21 21l-4.35-4.35" />
						</svg>
					</button>
				</form>

			</div>
			<!-- Center: Menu -->
			<div class="flex-1 flex justify-center">
				<nav class="menu flex space-x-6">
					<a href="/" class="font-semibold hover:text-yellow-300">TRANG CHỦ</a>
					<a href="/about" class="font-semibold hover:text-yellow-300">GIỚI THIỆU</a>
					<a href="#" class="font-semibold hover:text-yellow-300">TIN TỨC & SỰ KIỆN</a>
					<a href="/contact" class="font-semibold hover:text-yellow-300">LIÊN HỆ</a>
				</nav>
			</div>
			<!-- Right: Icons -->
			<div class="flex items-center space-x-2 flex-shrink-0 min-w-max">
				<!-- Cart (explicit href, not '#') -->
				<a href="/cart" class="bg-white rounded w-10 h-10 flex items-center justify-center mr-2" title="Giỏ hàng" aria-label="Giỏ hàng">
					<svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path d="M3 3h2l.4 2M7 13h10l4-8H5.4" />
						<circle cx="9" cy="21" r="1" />
						<circle cx="20" cy="21" r="1" />
					</svg>
				</a>
				<?php
				// show account dropdown when logged in; fall back to /login link when not
				$loggedIn = false;
				$userDisplay = '';
				if (!empty($_SESSION['account_id'])) {
					$loggedIn = true;
					// prefer a users.full_name if available, else use session email
					$userDisplay = $_SESSION['account_email'] ?? '';
					try {
						$db = (new \App\Core\Database())->getConnection();
						$stmt = $db->prepare('SELECT full_name FROM users WHERE account_id = :aid LIMIT 1');
						$stmt->execute([':aid' => $_SESSION['account_id']]);
						$u = $stmt->fetch(PDO::FETCH_ASSOC);
						if ($u && !empty($u['full_name'])) {
							$userDisplay = $u['full_name'];
						}
					} catch (\Throwable $e) {
						// ignore DB errors and fall back to session email
					}
				}
				if ($loggedIn): ?>
					<div class="relative group">
						<button class="bg-green-700 rounded-full w-10 h-10 flex items-center justify-center focus:outline-none" aria-haspopup="true" aria-expanded="false" title="Tài khoản">
							<svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
								<circle cx="12" cy="8" r="4" />
								<path d="M4 20c0-4 8-4 8-4s8 0 8 4" />
							</svg>
						</button>
						<div class="absolute right-0 mt-2 w-64 bg-white text-green-900 rounded-lg shadow-lg ring-1 ring-green-100 group-hover:block hidden transition-all duration-200 overflow-hidden z-50">
							<div class="px-4 py-3 bg-green-50">
								<div class="font-semibold whitespace-nowrap truncate">Chào <?php echo htmlspecialchars($userDisplay); ?></div>
								<div class="text-xs text-gray-500">Tài khoản</div>
							</div>
							<nav class="py-2 space-y-2">
								<a href="/account" class="flex items-center px-4 py-3 text-sm text-green-900 hover:bg-green-50 min-w-0">
									<span class="inline-block w-6 text-center mr-3">
										<svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-4 0-6 2-6 4v1h12v-1c0-2-2-4-6-4z" />
										</svg>
									</span>
									<span class="truncate">Tài khoản của bạn</span>
								</a>
								<a href="/orders" class="flex items-center px-4 py-3 text-sm text-green-900 hover:bg-green-50 min-w-0">
									<span class="inline-block w-6 text-center mr-3">
										<svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v4H3zM3 11h18v10H3z" />
										</svg>
									</span>
									<span class="truncate">Đơn hàng của tôi</span>
								</a>
								<a href="/addresses" class="flex items-center px-4 py-3 text-sm text-green-900 hover:bg-green-50 min-w-0">
									<span class="inline-block w-6 text-center mr-3">
										<svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8 2 5 5 5 9c0 6 7 13 7 13s7-7 7-13c0-4-3-7-7-7z" />
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 11a2 2 0 100-4 2 2 0 000 4z" />
										</svg>
									</span>
									<span class="truncate">Số địa chỉ nhận hàng</span>
								</a>
								<a href="/account/logout" class="flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 min-w-0">
									<span class="inline-block w-6 text-center mr-3">
										<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7" />
										</svg>
									</span>
									<span class="truncate">Thoát</span>
								</a>
							</nav>
						</div>
					</div>
				<?php else: ?>
					<div class="relative group">
						<button class="flex items-center space-x-2 px-2 py-1 focus:outline-none" aria-haspopup="true" aria-expanded="false" title="Tài khoản">
							<span class="bg-green-700 rounded-full w-10 h-10 flex items-center justify-center">
								<svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
									<circle cx="12" cy="8" r="4" />
									<path d="M4 20c0-4 8-4 8-4s8 0 8 4" />
								</svg>
							</span>
							<span class="hidden sm:block text-sm font-semibold">Đăng nhập / Đăng ký</span>
						</button>
						<div class="absolute right-0 mt-2 w-56 bg-white text-green-900 rounded-lg shadow-xl ring-1 ring-green-100 group-hover:block hidden transition-all duration-200 overflow-hidden min-w-max z-50">
							<div class="px-4 py-3 bg-green-50 flex items-center space-x-3">

							</div>
							<div class="p-4 space-y-3 bg-white" style="margin-top:10px;margin-bottom: 20px; font-weight:500;">
								<a href="/login" style="margin-top:10px;margin-bottom: 20px; font-weight:600;" class="block w-full text-center bg-green-700 hover:bg-green-800 text-white py-3 rounded-full font-semibold shadow-md transition-colors duration-150">Đăng nhập</a>
								<a href="/register" class="block w-full text-center bg-green-700 hover:bg-green-800 text-white py-3 rounded-full font-semibold shadow-sm transition-colors duration-150">Đăng ký</a>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</header>
	<!-- <div class="h-14"></div> -->


	<?php


	// Add small script to make dropdown easier to interact with (delay hide)
	// This script runs on pages that include header.php
	?>
	<script>
		(function() {
			if (typeof document === 'undefined') return;
			var groups = document.querySelectorAll('.relative.group');
			groups.forEach(function(g) {
				var timeout = null;
				g.addEventListener('mouseenter', function() {
					if (timeout) {
						clearTimeout(timeout);
						timeout = null;
					}
					g.classList.add('open');
				});
				g.addEventListener('mouseleave', function() {
					// delay closing so users can move mouse into the dropdown without it disappearing
					timeout = setTimeout(function() {
						g.classList.remove('open');
						timeout = null;
					}, 250);
				});
				// For accessibility / touch: toggle on click
				var btn = g.querySelector('button');
				if (btn) btn.addEventListener('click', function(e) {
					// On small screens, allow click to toggle
					if (g.classList.contains('open')) {
						g.classList.remove('open');
					} else {
						g.classList.add('open');
					}
				});
			});
		})();
	</script>
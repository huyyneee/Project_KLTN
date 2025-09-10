<!-- Modal đăng nhập tài khoản -->
<div id="loginModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-gray-200 bg-opacity-90 rounded-lg shadow-lg p-10 w-full max-w-md relative">
    <button onclick="closeLoginModal()" class="absolute top-2 right-2 text-xl font-bold">&times;</button>
    <h2 class="text-3xl font-bold text-center mb-6">Đăng Nhập Tài Khoản</h2>
    <form action="/login" method="POST" class="flex flex-col gap-4">
      <input type="text" name="username" placeholder="Username" class="px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-blue-400">
      <input type="password" name="password" placeholder="Password" class="px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-blue-400">
      <div class="flex gap-4 justify-center mt-2">
        <button type="submit" class="bg-blue-400 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-500">Đăng nhập</button>
        <button type="button" onclick="closeLoginModal()" class="bg-white border px-6 py-2 rounded-full font-semibold hover:bg-gray-100">Thoát</button>
      </div>
    </form>
    <div class="text-center mt-4">
      <span>Bạn có tài khoản chưa? </span><a href="/register" class="text-blue-600 font-semibold hover:underline">Đăng kí</a>
    </div>
  </div>
</div>
<script>
function openLoginModal() {
  document.getElementById('loginModal').classList.remove('hidden');
}
function closeLoginModal() {
  document.getElementById('loginModal').classList.add('hidden');
}
</script>

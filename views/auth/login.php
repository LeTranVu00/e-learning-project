<div class="max-w-md mx-auto my-16 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Chào mừng trở lại! 👋</h1>
        <p class="text-gray-500 mt-2">Vui lòng đăng nhập để tiếp tục học tập</p>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form action="?action=handle_login" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email của bạn</label>
            <input type="email" name="email" required placeholder="ví dụ: sinhvien@gmail.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
        </div>
        
        <div>
            <div class="flex justify-between items-center mb-1">
                <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <a href="#" class="text-xs text-primary hover:underline">Quên mật khẩu?</a>
            </div>
            <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-yellow-600 text-white font-bold py-2.5 rounded-lg transition shadow-md">
            Đăng nhập
        </button>
    </form>

    <div class="mt-6 flex items-center justify-between">
        <span class="border-b border-gray-200 w-1/5 lg:w-1/4"></span>
        <span class="text-xs text-center text-gray-500 uppercase">Hoặc đăng nhập bằng</span>
        <span class="border-b border-gray-200 w-1/5 lg:w-1/4"></span>
    </div>

    <div class="mt-6">
        <a href="?action=google_login&intent=login" class="w-full flex items-center justify-center gap-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 rounded-lg transition shadow-sm">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google Logo">
            Tiếp tục với Google
        </a>
    </div>

    <div class="mt-8 text-center text-sm text-gray-600">
        Chưa có tài khoản? <a href="?action=register" class="text-primary font-bold hover:underline">Đăng ký ngay</a>
    </div>
</div>
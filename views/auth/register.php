<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700"
        data-aos="zoom-in" data-aos-duration="600">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-yellow-100 dark:from-primary/20 dark:to-yellow-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-graduation-cap text-3xl text-primary"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tạo tài khoản mới</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Bắt đầu hành trình học tập của bạn ngay hôm nay</p>
        </div>

        <form action="?action=handle_register" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Họ và tên</label>
                <input type="text" name="fullname" placeholder="Nhập họ và tên của bạn"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary dark:focus:border-primary outline-none transition text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email của bạn</label>
                <input type="email" name="email" required placeholder="Nhập email của bạn"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary dark:focus:border-primary outline-none transition text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mật khẩu</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary dark:focus:border-primary outline-none transition text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirm" required placeholder="••••••••"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary dark:focus:border-primary outline-none transition text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
            </div>

            <button type="submit"
                class="w-full bg-primary hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                Đăng ký tài khoản
            </button>
        </form>

        <div class="mt-6 flex items-center gap-3">
            <span class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></span>
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Hoặc đăng ký bằng</span>
            <span class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></span>
        </div>

        <div class="mt-6">
            <a href="?action=google_login&intent=register"
                class="w-full flex items-center justify-center gap-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google Logo">
                Tiếp tục với Google
            </a>
        </div>

        <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            Đã có tài khoản? <a href="?action=login"
                class="text-primary font-bold hover:text-yellow-600 transition">Đăng nhập ngay</a>
        </div>
    </div>
</div>
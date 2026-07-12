<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700" 
         data-aos="zoom-in" data-aos-duration="600">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-yellow-100 dark:from-primary/20 dark:to-yellow-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-graduation-cap text-3xl text-primary"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chào mừng trở lại! 👋</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Vui lòng đăng nhập để tiếp tục học tập</p>
        </div>

        <!-- Login Form -->
        <form action="?action=handle_login" method="POST" class="space-y-5">
            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    <i class="fa-regular fa-envelope text-gray-400 mr-1.5"></i>Email của bạn
                </label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm"></i>
                    <input type="email" name="email" required 
                           placeholder="Nhập email của bạn" 
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary dark:focus:border-primary outline-none transition text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-sm">
                </div>
            </div>
            
            <!-- Password -->
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <i class="fa-solid fa-lock text-gray-400 mr-1.5"></i>Mật khẩu
                    </label>
                    <a href="?action=forgot_password" class="text-xs text-primary hover:text-yellow-600 font-medium transition">Quên mật khẩu?</a>
                </div>
                <div class="relative" x-data="{ show: false }">
                    <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm"></i>
                    <input :type="show ? 'text' : 'password'" name="password" required 
                           placeholder="••••••••" 
                           class="w-full pl-11 pr-12 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary dark:focus:border-primary outline-none transition text-gray-900 dark:text-white placeholder-gray-400 text-sm">
                    <button type="button" @click="show = !show" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition p-1">
                        <i class="fa-solid text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember" 
                       class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary/50 dark:bg-gray-700">
                <label for="remember" class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">Ghi nhớ đăng nhập</label>
            </div>

            <!-- Submit -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-primary to-yellow-500 hover:from-yellow-500 hover:to-primary text-white font-bold py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
            </button>
        </form>

        <!-- Divider -->
        <div class="mt-6 flex items-center gap-3">
            <span class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></span>
            <span class="text-xs text-gray-400 dark:text-gray-500 uppercase font-medium">Hoặc</span>
            <span class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></span>
        </div>

        <!-- Google Login -->
        <div class="mt-6">
            <a href="?action=google_login&intent=login" 
               class="w-full flex items-center justify-center gap-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google Logo">
                Tiếp tục với Google
            </a>
        </div>

        <!-- Register Link -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Chưa có tài khoản? 
                <a href="?action=register" class="text-primary hover:text-yellow-600 font-bold transition ml-1">
                    Đăng ký ngay <i class="fa-solid fa-arrow-right text-xs ml-0.5"></i>
                </a>
            </p>
        </div>
    </div>
</div>
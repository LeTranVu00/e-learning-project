<!-- ==================== PAGE HEADER ==================== -->
<section class="pt-32 pb-16 bg-gradient-to-br from-primary/10 via-yellow-500/5 to-transparent relative overflow-hidden"
    data-aos="fade-down">
    <div class="absolute inset-0 z-0">
        <div
            class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob">
        </div>
        <div
            class="absolute top-1/3 right-1/4 w-96 h-96 bg-yellow-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000">
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4 tracking-tight">Khôi phục <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-yellow-500">Mật khẩu</span></h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Nhập email của bạn để nhận liên kết đặt
            lại mật khẩu</p>
    </div>
</section>

<!-- ==================== FORGOT PASSWORD FORM SECTION ==================== -->
<section class="py-12 bg-white dark:bg-gray-900 relative">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">

        <div class="glass dark:glass-dark rounded-3xl p-8 sm:p-10 shadow-2xl border border-gray-100 dark:border-gray-800 relative z-10"
            data-aos="zoom-in" data-aos-delay="100">
            <form action="?action=handle_forgot_password" method="POST" class="space-y-6">
                <!-- Email Input -->
                <div class="space-y-2 relative group">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 ml-1">Địa chỉ Email</label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors"></i>
                        <input type="email" name="email" required placeholder="Nhập email"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary dark:focus:border-primary outline-none transition text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-sm">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3.5 bg-gradient-to-r from-primary to-yellow-500 hover:from-yellow-500 hover:to-primary text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 relative overflow-hidden group">
                    <span class="relative z-10">Gửi yêu cầu khôi phục</span>
                    <div
                        class="absolute inset-0 h-full w-full bg-white/20 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-out">
                    </div>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Nhớ mật khẩu rồi?
                    <a href="?action=login"
                        class="text-primary font-semibold hover:text-yellow-600 transition underline decoration-2 decoration-primary/30 underline-offset-4 hover:decoration-yellow-600">
                        Đăng nhập ngay
                    </a>
                </p>
            </div>
        </div>

    </div>
</section>
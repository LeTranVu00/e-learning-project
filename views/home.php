<!-- Thêm AOS Library và custom styles -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    .dark ::-webkit-scrollbar-track { background: #1f2937; }
    ::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, #f59e0b, #d97706); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: linear-gradient(to bottom, #d97706, #b45309); }

    html { scroll-behavior: smooth; }

    /* Animation Keyframes */
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.3); }
        50% { box-shadow: 0 0 40px rgba(245, 158, 11, 0.6); }
    }

    .animate-bounce-slow { animation: bounce-slow 3s ease-in-out infinite; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }

    .card-hover {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .gradient-text {
        background: linear-gradient(135deg, #f59e0b, #d97706, #f59e0b);
        background-size: 200% 200%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: shimmer 3s ease-in-out infinite;
    }

    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .glass {
        background: rgba(31, 41, 55, 0.7);
        border: 1px solid rgba(75, 85, 99, 0.3);
    }
    
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<!-- ==================== HERO SECTION ==================== -->
<section id="hero" class="relative flex flex-col-reverse lg:flex-row items-center justify-between py-16 lg:py-24 gap-12 overflow-hidden bg-gradient-to-br from-slate-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-primary/10 to-blue-500/10 dark:from-primary/5 dark:to-blue-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 animate-float"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-gradient-to-tr from-yellow-400/10 to-primary/10 dark:from-yellow-400/5 dark:to-primary/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 animate-bounce-slow"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-gradient-to-r from-purple-500/5 to-pink-500/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    </div>

    <div class="w-full lg:w-1/2 space-y-6 text-center lg:text-left relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-aos="fade-right" data-aos-duration="1000">
        <div class="inline-flex items-center gap-2 bg-gradient-to-r from-primary/10 to-yellow-100 dark:from-primary/20 dark:to-yellow-900/30 text-primary font-semibold px-5 py-2 rounded-full text-sm mb-2 border border-primary/20 shadow-sm animate-pulse-glow">
            <span class="text-lg">🚀</span> Nền tảng học lập trình số 1 Việt Nam
        </div>
        <h1 class="text-4xl lg:text-6xl font-extrabold text-gray-900 dark:text-white leading-tight tracking-tight">
            Nâng tầm kỹ năng <br>
            <span class="gradient-text">Định hình tương lai</span>
        </h1>
        <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed max-w-xl mx-auto lg:mx-0">
            Học lập trình không khó khi bạn có một lộ trình chuẩn. Khám phá hàng trăm khóa học từ cơ bản đến chuyên sâu, thực chiến với các dự án thực tế ngay hôm nay.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-6">
            <a href="?action=courses" class="group bg-gradient-to-r from-primary to-yellow-500 hover:from-yellow-500 hover:to-primary text-white font-semibold py-4 px-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 relative overflow-hidden">
                <span class="relative z-10">Khám phá khóa học</span>
                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
            </a>
            <a href="#how-it-works" class="group bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-primary font-semibold py-4 px-8 rounded-xl transition-all duration-300 hover:shadow-lg relative overflow-hidden">
                <span class="relative z-10 flex items-center gap-2">
                    Tìm hiểu thêm
                    <i class="fa-solid fa-arrow-down group-hover:translate-y-1 transition-transform"></i>
                </span>
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-4 sm:gap-6 pt-8 justify-center lg:justify-start text-sm text-gray-500 dark:text-gray-400">
            <div class="flex items-center gap-2 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-full px-4 py-2 shadow-sm">
                <i class="fa-solid fa-shield-halved text-green-500"></i>
                <span>Bảo mật 100%</span>
            </div>
            <div class="flex items-center gap-2 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-full px-4 py-2 shadow-sm">
                <i class="fa-solid fa-headset text-blue-500"></i>
                <span>Hỗ trợ 24/7</span>
            </div>
            <div class="flex items-center gap-2 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-full px-4 py-2 shadow-sm">
                <i class="fa-solid fa-rotate-left text-purple-500"></i>
                <span>Hoàn tiền 7 ngày</span>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex justify-center relative z-10 px-4" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
        <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-primary to-yellow-500 rounded-3xl blur-xl opacity-20 transform rotate-6 animate-pulse"></div>
            <img src="https://placehold.co/600x400/f59e0b/white?text=E-Learning+Hero+Banner" alt="Hero Banner" class="rounded-3xl shadow-2xl object-cover w-full max-w-lg lg:max-w-full relative transform hover:rotate-1 transition-transform duration-500">
            
            <div class="absolute -top-6 -right-6 glass rounded-2xl shadow-xl p-4 animate-bounce-slow" data-aos="zoom-in" data-aos-delay="600">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-semibold dark:text-white">2,500+ học viên online</span>
                </div>
            </div>
            <div class="absolute -bottom-6 -left-6 glass rounded-2xl shadow-xl p-4 animate-float" data-aos="zoom-in" data-aos-delay="800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-star text-yellow-400"></i>
                    <span class="text-sm font-semibold dark:text-white">4.9/5 đánh giá</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== HOW IT WORKS SECTION ==================== -->
<section id="how-it-works" class="py-20 w-full bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Quy trình</span>
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Cách thức hoạt động</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg max-w-2xl mx-auto">Bắt đầu hành trình học tập của bạn chỉ với 4 bước đơn giản</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            <div class="hidden md:block absolute top-1/2 left-0 right-0 h-0.5 bg-gradient-to-r from-primary/20 via-primary to-primary/20 -translate-y-1/2 z-0"></div>
            
            <!-- Step 1 -->
            <div class="relative z-10" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 text-center card-hover">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary to-yellow-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl transform -rotate-6 hover:rotate-0 transition-transform duration-300">
                        <i class="fa-solid fa-user-plus text-3xl text-white"></i>
                    </div>
                    <div class="absolute -top-4 -right-4 w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg">1</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Đăng ký tài khoản</h3>
                    <p class="text-gray-500 dark:text-gray-400">Tạo tài khoản miễn phí trong 1 phút và nhận ngay ưu đãi khóa học đầu tiên</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="relative z-10" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 text-center card-hover">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl transform rotate-6 hover:rotate-0 transition-transform duration-300">
                        <i class="fa-solid fa-magnifying-glass text-3xl text-white"></i>
                    </div>
                    <div class="absolute -top-4 -right-4 w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg">2</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Chọn khóa học</h3>
                    <p class="text-gray-500 dark:text-gray-400">Khám phá 500+ khóa học chất lượng từ cơ bản đến chuyên sâu</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative z-10" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 text-center card-hover">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl transform -rotate-6 hover:rotate-0 transition-transform duration-300">
                        <i class="fa-solid fa-laptop-code text-3xl text-white"></i>
                    </div>
                    <div class="absolute -top-4 -right-4 w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg">3</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Học & thực hành</h3>
                    <p class="text-gray-500 dark:text-gray-400">Học qua video, làm bài tập thực tế và nhận phản hồi từ giảng viên</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="relative z-10" data-aos="fade-up" data-aos-delay="400">
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 text-center card-hover">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl transform rotate-6 hover:rotate-0 transition-transform duration-300">
                        <i class="fa-solid fa-certificate text-3xl text-white"></i>
                    </div>
                    <div class="absolute -top-4 -right-4 w-10 h-10 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg">4</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Nhận chứng chỉ</h3>
                    <p class="text-gray-500 dark:text-gray-400">Hoàn thành khóa học và nhận chứng chỉ có giá trị toàn quốc</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== STATS SECTION ==================== -->
<section id="stats" class="w-full -mt-10 relative z-20" data-aos="fade-up" data-aos-duration="800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 p-10 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent dark:from-primary/10"></div>
            <div class="relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
                    <div class="p-6 group hover:scale-105 transition-transform duration-300">
                        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-primary/10 to-yellow-100 dark:from-primary/20 dark:to-yellow-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:shadow-xl transition-shadow">
                            <i class="fa-solid fa-users text-3xl text-primary"></i>
                        </div>
                        <div class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2">10,000+</div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Học viên tin tưởng</p>
                    </div>
                    <div class="p-6 group hover:scale-105 transition-transform duration-300">
                        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-800/20 rounded-2xl flex items-center justify-center mb-4 group-hover:shadow-xl transition-shadow">
                            <i class="fa-solid fa-book-open text-3xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2">500+</div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Khóa học chất lượng</p>
                    </div>
                    <div class="p-6 group hover:scale-105 transition-transform duration-300">
                        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-yellow-100 to-orange-50 dark:from-yellow-900/30 dark:to-orange-900/20 rounded-2xl flex items-center justify-center mb-4 group-hover:shadow-xl transition-shadow">
                            <i class="fa-solid fa-star text-3xl text-yellow-500"></i>
                        </div>
                        <div class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2">4.8/5</div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Đánh giá trung bình</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== CATEGORIES SECTION ==================== -->
<section id="categories" class="py-20 w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Danh mục</span>
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Khóa học theo chuyên ngành</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg">Lựa chọn lĩnh vực bạn muốn chinh phục</p>
        </div>
        
        <div class="relative group px-2" x-data="{
            scrollLeft() { 
                let s = $refs.slider;
                if (s.scrollLeft <= 10) { s.scrollTo({ left: s.scrollWidth, behavior: 'smooth' }); }
                else { s.scrollBy({ left: -300, behavior: 'smooth' }); }
            },
            scrollRight() { 
                let s = $refs.slider;
                if (s.scrollLeft + s.clientWidth >= s.scrollWidth - 10) { s.scrollTo({ left: 0, behavior: 'smooth' }); }
                else { s.scrollBy({ left: 300, behavior: 'smooth' }); }
            }
        }" data-aos="fade-up" data-aos-delay="200">
            <button @click="scrollLeft()" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 shadow-xl rounded-full w-12 h-12 flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary z-10 opacity-0 group-hover:opacity-100 transition-all duration-300">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div x-ref="slider" class="flex gap-6 overflow-x-auto snap-x snap-mandatory hide-scrollbar py-4" style="justify-content: <?= count($categories) <= 5 ? 'center' : 'flex-start' ?>;">
                <?php foreach ($categories as $cat): ?>
                    <a href="?action=courses&category=<?= $cat['id'] ?>" class="w-40 sm:w-48 bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-2xl border border-gray-100 dark:border-gray-700 p-6 text-center transition-all duration-300 flex-shrink-0 snap-center group/card flex flex-col items-center justify-center hover:-translate-y-2 hover:border-primary/30">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4 <?= htmlspecialchars($cat['color']) ?> group-hover/card:scale-110 group-hover/card:rotate-6 transition-all duration-300 shadow-lg">
                            <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?> text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-sm mb-1"><?= htmlspecialchars($cat['name']) ?></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-full px-3 py-1"><?= $cat['course_count'] ?> khóa học</p>
                    </a>
                <?php endforeach; ?>
            </div>

            <button @click="scrollRight()" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 shadow-xl rounded-full w-12 h-12 flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary z-10 opacity-0 group-hover:opacity-100 transition-all duration-300">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- ==================== LEARNING PATHS SECTION ==================== -->
<section id="paths" class="py-20 w-full bg-gradient-to-br from-primary/5 via-white to-blue-500/5 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Lộ trình</span>
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Lộ trình học tập chuyên sâu</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg max-w-2xl mx-auto">Lộ trình được thiết kế bài bản giúp bạn đi từ số 0 đến chuyên nghiệp</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-3xl shadow-lg hover:shadow-2xl p-8 transition-all duration-300 card-hover" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fa-solid fa-code text-3xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Frontend Developer</h3>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>HTML/CSS cơ bản</span></div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>JavaScript nâng cao</span></div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>React.js & Next.js</span></div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>5 dự án thực tế</span></div>
                </div>
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400"><i class="fa-regular fa-clock mr-1"></i> 6 tháng</span>
                    <a href="#" class="text-primary font-semibold hover:underline">Xem chi tiết →</a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-3xl shadow-lg hover:shadow-2xl p-8 transition-all duration-300 card-hover" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fa-solid fa-server text-3xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Backend Developer</h3>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>PHP & Laravel</span></div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>MySQL & Database</span></div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>RESTful API</span></div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>Deployment & DevOps</span></div>
                </div>
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400"><i class="fa-regular fa-clock mr-1"></i> 8 tháng</span>
                    <a href="#" class="text-primary font-semibold hover:underline">Xem chi tiết →</a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-3xl shadow-lg hover:shadow-2xl p-8 transition-all duration-300 card-hover" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fa-solid fa-mobile-screen text-3xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Mobile Developer</h3>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>Dart & Flutter</span></div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>UI/UX Mobile</span></div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>State Management</span></div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400"><i class="fa-solid fa-check-circle text-green-500"></i><span>App Store Publishing</span></div>
                </div>
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400"><i class="fa-regular fa-clock mr-1"></i> 7 tháng</span>
                    <a href="#" class="text-primary font-semibold hover:underline">Xem chi tiết →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== FEATURED COURSES SECTION ==================== -->
<section id="featured-courses" class="py-20 w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative mb-16">
            <div class="text-center" data-aos="fade-up">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider">Nổi bật</span>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-2 mt-2">Khóa học được yêu thích</h2>
                <p class="text-gray-500 dark:text-gray-400 text-lg max-w-2xl mx-auto">Những khóa học được đăng ký nhiều nhất tháng qua.</p>
            </div>
            <a href="?action=courses" class="hidden md:flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all bg-primary/5 dark:bg-primary/10 px-6 py-3 rounded-xl hover:bg-primary/10 dark:hover:bg-primary/20 absolute right-0 bottom-0" data-aos="fade-left">
                Xem tất cả <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($courses as $index => $course): ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300 flex flex-col group hover:-translate-y-2" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="relative overflow-hidden">
                        <?php
                        $thumbnail = !empty($course['thumbnail'])
                            ? htmlspecialchars($course['thumbnail'])
                            : 'https://placehold.co/600x400/f59e0b/white?text=E-Learning';
                        ?>
                        <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($course['title']) ?>" class="w-full h-52 object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <?php if (!empty($course['is_featured'])): ?>
                            <div class="absolute top-4 left-4 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold px-4 py-2 rounded-full shadow-lg flex items-center gap-1.5">
                                <i class="fa-solid fa-fire text-yellow-300"></i> Nổi bật
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-4 right-4 bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm rounded-full px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                            <i class="fa-solid fa-users mr-1"></i> 2.5k
                        </div>
                    </div>
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs font-semibold text-primary bg-primary/5 dark:bg-primary/10 px-3 py-1 rounded-full">Khóa học</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500"><i class="fa-solid fa-clock mr-1"></i> 12 tuần</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-primary transition-colors">
                                <?= htmlspecialchars($course['title']) ?>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                                <?= htmlspecialchars(strip_tags($course['description'])) ?>
                            </p>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">(4.8)</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-4">
                            <div class="flex flex-col">
                                <span class="text-2xl font-bold <?= (isset($course['price']) && $course['price'] > 0) ? 'text-primary' : 'text-green-500' ?>">
                                    <?= (isset($course['price']) && $course['price'] > 0) ? number_format($course['price'], 0, ',', '.') . 'đ' : 'Miễn phí' ?>
                                </span>
                                <?php if (!empty($course['original_price']) && $course['original_price'] > ($course['price'] ?? 0)): ?>
                                    <div class="flex items-center gap-2">
                                        <del class="text-sm text-gray-400"><?= number_format($course['original_price'], 0, ',', '.') ?>đ</del>
                                        <span class="text-xs text-red-500 font-semibold bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded">-<?= round((1 - ($course['price'] ?? 0) / $course['original_price']) * 100) ?>%</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <a href="?action=detail&id=<?= $course['id'] ?>" class="bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary hover:text-white font-semibold py-2.5 px-5 rounded-xl transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5">Chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== INSTRUCTORS SECTION ==================== -->
<section id="instructors" class="py-20 w-full bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Giảng viên</span>
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Đội ngũ giảng viên xuất sắc</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg max-w-2xl mx-auto">Học từ những chuyên gia hàng đầu trong ngành công nghệ</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php for($i = 1; $i <= 4; $i++): ?>
                <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-3xl shadow-lg hover:shadow-2xl p-6 text-center transition-all duration-300 card-hover" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                    <div class="relative inline-block mb-4">
                        <img src="https://ui-avatars.com/api/?name=Instructor+<?= $i ?>&size=120&background=random" alt="Instructor <?= $i ?>" class="w-28 h-28 rounded-full mx-auto border-4 border-primary/20 shadow-xl">
                        <div class="absolute bottom-0 right-0 w-8 h-8 bg-green-500 rounded-full border-4 border-white dark:border-gray-800"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Nguyễn Văn <?= chr(64 + $i) ?></h3>
                    <p class="text-primary font-semibold text-sm mb-2">Senior Developer</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">10+ năm kinh nghiệm</p>
                    <div class="flex items-center justify-center gap-1 text-yellow-400 text-sm mb-3">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        <span class="text-gray-500 dark:text-gray-400 ml-1">4.9</span>
                    </div>
                    <div class="flex items-center justify-center gap-2 text-gray-500 dark:text-gray-400 text-xs">
                        <span><i class="fa-solid fa-user-graduate mr-1"></i> 2.5k học viên</span>
                        <span><i class="fa-solid fa-book mr-1"></i> 12 khóa học</span>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- ==================== FORUM SECTION ==================== -->
<?php
require_once __DIR__ . '/../app/models/Forum.php';
$forumModel = new Forum($db);
$topPosts = $forumModel->getFeaturedPosts(3);
?>

<section id="forum" class="py-20 w-full bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 border-t border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative mb-16">
            <div class="text-center" data-aos="fade-up">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider">Cộng đồng</span>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-2 mt-2">Thảo luận nổi bật</h2>
                <p class="text-gray-500 dark:text-gray-400 text-lg max-w-2xl mx-auto">Những chủ đề đang được quan tâm và bình luận nhiều nhất.</p>
            </div>
            <a href="?action=forum" class="hidden sm:flex items-center gap-2 text-primary hover:text-yellow-600 font-bold bg-primary/5 dark:bg-primary/10 px-6 py-3 rounded-xl hover:bg-primary/10 dark:hover:bg-primary/20 transition-all absolute right-0 bottom-0" data-aos="fade-left">
                Xem diễn đàn <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (!empty($topPosts)): ?>
                <?php foreach ($topPosts as $index => $post): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-2xl border border-gray-100 dark:border-gray-700 p-6 transition-all duration-300 flex flex-col h-full group hover:-translate-y-2" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="flex items-center gap-3 mb-5">
                            <?php
                            $postAvatar = !empty($post['author_avatar'])
                                ? $post['author_avatar']
                                : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=random';
                            ?>
                            <img src="<?= htmlspecialchars($postAvatar) ?>" class="w-12 h-12 rounded-xl object-cover border-2 border-gray-200 dark:border-gray-600 shadow-sm shrink-0">
                            <div>
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($post['author_name']) ?></h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i> <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex-grow mb-6">
                            <a href="?action=forum_detail&id=<?= $post['id'] ?>" class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-primary transition line-clamp-2 mb-3 block">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                            <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 leading-relaxed">
                                <?= strip_tags($post['content']) ?>
                            </p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center mt-auto">
                            <span class="text-sm font-bold bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 text-blue-600 dark:text-blue-400 px-4 py-2 rounded-xl flex items-center gap-2">
                                <i class="fa-regular fa-comment-dots"></i> <?= $post['comment_count'] ?> bình luận
                            </span>
                            <a href="?action=forum_detail&id=<?= $post['id'] ?>" class="text-sm font-bold text-gray-400 hover:text-primary transition flex items-center gap-1">
                                Xem thêm <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-16 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <i class="fa-solid fa-comments text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-lg italic">Chưa có bài thảo luận nào sôi nổi.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-8 text-center sm:hidden">
            <a href="?action=forum" class="inline-flex justify-center items-center gap-2 bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-700 dark:to-gray-600 text-white font-bold py-4 px-8 rounded-2xl hover:shadow-xl transition-all w-full">
                Vào Diễn đàn <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- ==================== TESTIMONIALS SECTION ==================== -->
<section id="testimonials" class="py-20 w-full bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Đánh giá</span>
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Học viên nói gì về chúng tôi?</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg">Hàng ngàn học viên đã thay đổi sự nghiệp sau khi tham gia khóa học.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($testimonials as $index => $test): ?>
                <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-3xl p-8 border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 relative flex flex-col group hover:-translate-y-2" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-primary/10 to-transparent rounded-bl-3xl"></div>
                    <i class="fa-solid fa-quote-left text-5xl text-primary/10 absolute top-6 right-8"></i>
                    
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa-<?= $i <= $test['rating'] ? 'solid' : 'regular' ?> fa-star text-sm"></i>
                        <?php endfor; ?>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-300 mb-6 italic leading-relaxed flex-grow relative z-10">
                        "<?= htmlspecialchars($test['content']) ?>"
                    </p>
                    
                    <div class="flex items-center gap-4 mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                        <img src="<?= htmlspecialchars($test['avatar']) ?>" alt="Avatar" class="w-14 h-14 rounded-2xl object-cover shadow-lg ring-4 ring-white dark:ring-gray-700">
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($test['name']) ?></h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($test['role']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== FAQ SECTION ==================== -->
<section id="faq" class="py-20 w-full bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">FAQ</span>
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Câu hỏi thường gặp</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg">Giải đáp những thắc mắc phổ biến về nền tảng của chúng tôi</p>
        </div>

        <div class="space-y-4" x-data="{ active: null }">
            <?php
            $faqs = [
                ['q' => 'Tôi có cần kinh nghiệm lập trình trước khi tham gia không?', 'a' => 'Không! Các khóa học của chúng tôi được thiết kế cho mọi cấp độ, từ người mới bắt đầu đến chuyên gia.'],
                ['q' => 'Tôi có thể học bất cứ lúc nào không?', 'a' => 'Có! Tất cả khóa học đều được học trực tuyến 24/7. Bạn có thể học theo tốc độ của riêng mình.'],
                ['q' => 'Có được hoàn tiền nếu không hài lòng không?', 'a' => 'Có! Chúng tôi có chính sách hoàn tiền 100% trong vòng 7 ngày.'],
                ['q' => 'Sau khi hoàn thành có nhận được chứng chỉ không?', 'a' => 'Có! Bạn sẽ nhận được chứng chỉ có giá trị toàn quốc sau khi hoàn thành khóa học.'],
                ['q' => 'Tôi có thể liên hệ giảng viên để được hỗ trợ không?', 'a' => 'Có! Mỗi khóa học đều có kênh hỗ trợ riêng, giảng viên sẽ phản hồi trong vòng 24h.']
            ];
            ?>
            <?php foreach ($faqs as $index => $faq): ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300" data-aos="fade-up" data-aos-delay="<?= $index * 50 ?>">
                    <button @click="active = active === <?= $index ?> ? null : <?= $index ?>" class="w-full px-8 py-6 flex justify-between items-center text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white pr-8"><?= $faq['q'] ?></span>
                        <i class="fa-solid fa-chevron-down text-gray-400 transition-transform duration-300" :class="active === <?= $index ?> ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="active === <?= $index ?>" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="px-8 pb-6 text-gray-600 dark:text-gray-300 leading-relaxed">
                        <?= $faq['a'] ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== CONTACT SECTION ==================== -->
<section id="contact" class="py-20 w-full border-t border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Liên hệ</span>
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Liên hệ với chúng tôi</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg">Hãy liên hệ với chúng tôi qua các kênh dưới đây hoặc để lại tin nhắn.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div class="space-y-8 flex flex-col h-full" data-aos="fade-right">
                <div class="flex items-start gap-5 group hover:translate-x-2 transition-transform duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/20 text-primary rounded-2xl flex items-center justify-center shrink-0 shadow-lg">
                        <i class="fa-solid fa-location-dot text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">Địa chỉ</h4>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">123 Đường Lập Trình, Quận 1, TP. HCM</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-5 group hover:translate-x-2 transition-transform duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/20 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center shrink-0 shadow-lg">
                        <i class="fa-solid fa-phone text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">Điện thoại</h4>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">0123 456 789</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-5 group hover:translate-x-2 transition-transform duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/20 text-green-600 dark:text-green-400 rounded-2xl flex items-center justify-center shrink-0 shadow-lg">
                        <i class="fa-solid fa-envelope text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">Email</h4>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">contact@e-learning.vn</p>
                    </div>
                </div>
                
                <div class="w-full flex-1 bg-gray-200 dark:bg-gray-700 rounded-3xl overflow-hidden shadow-inner mt-8 min-h-[280px] ring-4 ring-gray-100 dark:ring-gray-700">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4241674197285!2d106.69871631480062!3d10.778810062100863!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f38f9ed887b%3A0x14aded5703768f89!2sNotre%20Dame%20Cathedral%20of%20Saigon!5e0!3m2!1sen!2s!4v1633075218318!5m2!1sen!2s" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 p-8 sm:p-10 relative overflow-hidden" data-aos="fade-left">
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-primary/5 to-transparent rounded-bl-full"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-blue-500/5 to-transparent rounded-tr-full"></div>
                
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 relative z-10">Gửi tin nhắn</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 relative z-10">Chúng tôi sẽ phản hồi trong vòng 24h</p>
                
                <form action="?action=submit_contact" method="POST" class="space-y-6 relative z-10" x-data="{
                    name: '', email: '', message: '',
                    validate() {
                        if(!this.name || !this.email || !this.message) return false;
                        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email);
                    }
                }">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Họ và Tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="name" required class="w-full px-4 py-4 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition" placeholder="Nhập họ và tên">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" x-model="email" required class="w-full px-4 py-4 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition" placeholder="example@gmail.com">
                        <p x-show="email.length > 0 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)" class="text-red-500 text-xs mt-1" x-cloak>Email không đúng định dạng.</p>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nội dung <span class="text-red-500">*</span></label>
                        <textarea name="message" x-model="message" required rows="6" class="w-full px-4 py-4 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition resize-none" placeholder="Hãy mô tả chi tiết yêu cầu..."></textarea>
                    </div>
                    
                    <button type="submit" :disabled="!validate()" :class="!validate() ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-gradient-to-r from-primary to-yellow-500 hover:from-yellow-500 hover:to-primary hover:shadow-2xl transform hover:-translate-y-1'" class="w-full text-white font-bold py-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-3 text-lg">
                        <i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ==================== CTA SECTION ==================== -->
<section class="w-full bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-16 text-center overflow-hidden relative" data-aos="zoom-in">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, white 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>
    
    <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Sẵn sàng bắt đầu hành trình của bạn?</h2>
        <p class="text-gray-400 text-lg mb-10">Tham gia cùng 10,000+ học viên và nâng cấp kỹ năng ngay hôm nay.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#" class="bg-gradient-to-r from-primary to-yellow-500 hover:from-yellow-500 hover:to-primary text-white font-bold py-4 px-10 rounded-xl shadow-2xl transition-all duration-300 text-lg transform hover:-translate-y-1">
                Đăng ký ngay
            </a>
            <a href="#how-it-works" class="glass hover:bg-white/20 text-white border border-white/20 font-bold py-4 px-10 rounded-xl transition-all duration-300 text-lg">
                Tìm hiểu thêm
            </a>
        </div>
    </div>
    
    <div class="absolute top-0 left-0 w-80 h-80 bg-primary rounded-full mix-blend-multiply filter blur-3xl opacity-20 -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-80 h-80 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 translate-x-1/2 translate-y-1/2 animate-pulse"></div>
</section>

<!-- ==================== SCRIPTS ==================== -->
<script>
    AOS.init({ duration: 800, easing: 'ease-in-out', once: true, mirror: false });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
</script>
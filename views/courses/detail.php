<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-8 mb-16">
    <div class="flex flex-col md:flex-row">
        <div class="md:w-1/2">
            <img src="<?= htmlspecialchars($course['image_url']) ?>" alt="<?= htmlspecialchars($course['title']) ?>" class="w-full h-full object-cover">
        </div>
        
        <div class="md:w-1/2 p-8 lg:p-12 flex flex-col justify-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($course['title']) ?></h1>
            
            <div class="flex items-center text-sm text-gray-500 mb-6 gap-4">
                <span><i class="fa-solid fa-users text-primary mr-1"></i> 1,234 học viên</span>
                <span><i class="fa-solid fa-clock text-primary mr-1"></i> 15 giờ học</span>
            </div>
            
            <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                <?= nl2br(htmlspecialchars($course['description'])) ?>
            </p>
            
            <div class="flex items-center gap-6 mt-auto">
                <span class="text-3xl font-extrabold <?= $course['price'] > 0 ? 'text-primary' : 'text-green-500' ?>">
                    <?= $course['price'] > 0 ? number_format($course['price'], 0, ',', '.') . 'đ' : 'Miễn phí' ?>
                </span>
                
                <button onclick="alert('Chức năng ghi danh sẽ được thực hiện sau khi có hệ thống User!')" class="bg-primary hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-1">
                    Đăng ký học ngay
                </button>
            </div>
        </div>
    </div>
</div>


<div class="flex flex-col lg:flex-row gap-10 mt-10">
    
    <div class="lg:w-2/3 space-y-10">
        
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Bạn sẽ học được gì?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Hiểu và áp dụng thuần thục mô hình MVC</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Tương tác Database bằng PHP Data Objects (PDO)</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Xây dựng hệ thống đăng nhập, phân quyền user</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Bảo mật website chống SQL Injection, XSS</span>
                </div>
            </div>
        </div>

        <div class="mt-12">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Nội dung khóa học</h2>
    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Bao gồm 3 chương • 12 bài giảng • Tổng thời lượng 15 giờ</p>
        <button class="text-primary text-sm font-semibold hover:underline">Mở rộng tất cả</button>
    </div>
    
    <div class="flex flex-col gap-3">
        
        <div x-data="{ expanded: true }" class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
            
            <button @click="expanded = !expanded" class="w-full px-5 py-4 flex justify-between items-center text-left bg-gray-50 hover:bg-gray-100 transition duration-200">
                <span class="font-bold text-gray-800">Chương 1: Khởi động với PHP OOP</span>
                <i class="fa-solid fa-chevron-down text-gray-500 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''"></i>
            </button>
            
            <div x-show="expanded" x-collapse class="px-5 py-2 divide-y divide-gray-100">
                <div class="py-3 flex justify-between items-center group cursor-pointer">
                    <span class="text-gray-600 group-hover:text-primary transition">
                        <i class="fa-solid fa-circle-play mr-2 text-primary/70"></i> 
                        Bài 1: Lập trình hướng đối tượng là gì?
                    </span>
                    <span class="text-sm text-gray-400">10:30</span>
                </div>
                <div class="py-3 flex justify-between items-center group cursor-pointer">
                    <span class="text-gray-600 group-hover:text-primary transition">
                        <i class="fa-solid fa-circle-play mr-2 text-primary/70"></i> 
                        Bài 2: Class, Object và các tính chất
                    </span>
                    <span class="text-sm text-gray-400">15:45</span>
                </div>
            </div>
        </div>

        <div x-data="{ expanded: false }" class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
            <button @click="expanded = !expanded" class="w-full px-5 py-4 flex justify-between items-center text-left bg-gray-50 hover:bg-gray-100 transition duration-200">
                <span class="font-bold text-gray-800">Chương 2: Cấu trúc Database & PDO</span>
                <i class="fa-solid fa-chevron-down text-gray-500 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''"></i>
            </button>
            
            <div x-show="expanded" x-collapse x-cloak class="px-5 py-2 divide-y divide-gray-100">
                <div class="py-3 flex justify-between items-center group cursor-pointer">
                    <span class="text-gray-600 group-hover:text-primary transition">
                        <i class="fa-solid fa-circle-play mr-2 text-primary/70"></i> 
                        Bài 3: Thiết kế Database E-learning
                    </span>
                    <span class="text-sm text-gray-400">20:15</span>
                </div>
            </div>
        </div>

    </div>
</div>

    </div>

    <div class="lg:w-1/3 space-y-8">
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Yêu cầu</h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>Máy tính có kết nối Internet</li>
                <li>Đã cài đặt XAMPP hoặc Laragon</li>
                <li>Hiểu biết cơ bản về HTML & CSS</li>
            </ul>
        </div>
    </div>
</div>
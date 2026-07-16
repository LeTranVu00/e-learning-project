<?php
/**
 * DATABASE SEEDER - E-Learning Project
 * Chạy: php database_seed.php (từ thư mục gốc dự án)
 */

// Load environment
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USERNAME'];
$pass = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Kết nối DB thành công!\n\n";
} catch (PDOException $e) {
    die("❌ Kết nối DB thất bại: " . $e->getMessage() . "\n");
}

// ============================================================
// BƯỚC 1: XÓA DỮ LIỆU CŨ (TRUNCATE)
// ============================================================
echo "🧹 Đang xóa dữ liệu cũ...\n";
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['comment_likes', 'comments', 'posts', 'material_completions', 'materials', 'chapters', 'enrollments', 'payments', 'courses', 'categories'];
foreach ($tables as $table) {
    $pdo->exec("TRUNCATE TABLE `$table`");
    echo "  - Đã xóa bảng: $table\n";
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "✅ Xóa dữ liệu cũ xong!\n\n";

// ============================================================
// BƯỚC 2: TẠO DANH MỤC (CATEGORIES)
// ============================================================
echo "📁 Đang thêm danh mục...\n";
$categories = [
    ['name' => 'Lập trình Web',       'icon' => 'fa-globe',          'color' => 'from-blue-500 to-cyan-500'],
    ['name' => 'Lập trình Mobile',    'icon' => 'fa-mobile-screen',  'color' => 'from-green-500 to-emerald-500'],
    ['name' => 'Ngôn ngữ lập trình', 'icon' => 'fa-code',            'color' => 'from-yellow-500 to-orange-500'],
    ['name' => 'Cơ sở dữ liệu',       'icon' => 'fa-database',       'color' => 'from-red-500 to-rose-500'],
    ['name' => 'Tư duy lập trình',   'icon' => 'fa-brain',           'color' => 'from-purple-500 to-violet-500'],
];
$catStmt = $pdo->prepare("INSERT INTO categories (name, icon, color) VALUES (:name, :icon, :color)");
$catIds = [];
foreach ($categories as $cat) {
    $catStmt->execute($cat);
    $catIds[$cat['name']] = $pdo->lastInsertId();
    echo "  + Danh mục: {$cat['name']} (ID: {$catIds[$cat['name']]})\n";
}
echo "✅ Thêm danh mục xong!\n\n";

// ============================================================
// BƯỚC 3: TẠO NGƯỜI DÙNG MẪU (USERS)
// ============================================================
echo "👤 Đang kiểm tra/tạo người dùng mẫu...\n";
$sampleUsers = [
    ['fullname' => 'Nguyễn Văn An',    'email' => 'student1@gmail.com', 'role' => 'student',    'avatar' => 'https://i.pravatar.cc/150?img=1'],
    ['fullname' => 'Trần Thị Bình',   'email' => 'student2@gmail.com', 'role' => 'student',    'avatar' => 'https://i.pravatar.cc/150?img=5'],
    ['fullname' => 'Lê Minh Cường',   'email' => 'student3@gmail.com', 'role' => 'student',    'avatar' => 'https://i.pravatar.cc/150?img=3'],
    ['fullname' => 'Phạm Thu Hà',     'email' => 'student4@gmail.com', 'role' => 'student',    'avatar' => 'https://i.pravatar.cc/150?img=9'],
    ['fullname' => 'Hoàng Đức Khải',  'email' => 'student5@gmail.com', 'role' => 'student',    'avatar' => 'https://i.pravatar.cc/150?img=7'],
];
$userIds = [];
$hashedPass = password_hash('123456', PASSWORD_BCRYPT);
$checkUser = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$insertUser = $pdo->prepare("INSERT INTO users (fullname, email, password, role, avatar) VALUES (:fullname, :email, :password, :role, :avatar)");
foreach ($sampleUsers as $u) {
    $checkUser->execute([':email' => $u['email']]);
    $existing = $checkUser->fetchColumn();
    if ($existing) {
        $userIds[$u['email']] = $existing;
        echo "  ~ Người dùng đã tồn tại: {$u['email']} (ID: $existing)\n";
    } else {
        $insertUser->execute([
            ':fullname' => $u['fullname'],
            ':email'    => $u['email'],
            ':password' => $hashedPass,
            ':role'     => $u['role'],
            ':avatar'   => $u['avatar'],
        ]);
        $userIds[$u['email']] = $pdo->lastInsertId();
        echo "  + Tạo người dùng: {$u['email']} (ID: {$userIds[$u['email']]})\n";
    }
}
echo "✅ Người dùng xong!\n\n";

// ============================================================
// BƯỚC 4: TẠO KHÓA HỌC (COURSES)
// ============================================================
echo "📚 Đang thêm khóa học...\n";
$courses = [
    [
        'title'          => 'Lập trình Android từ Cơ bản đến Nâng cao',
        'thumbnail'      => 'uploads/courses/android.jpg',
        'category'       => 'Lập trình Mobile',
        'instructor'     => 'Nguyễn Thanh Tùng',
        'level'          => 'Trung cấp',
        'price'          => 799000,
        'original_price' => 1200000,
        'duration_hours' => 42,
        'total_lessons'  => 120,
        'contact_phone'  => '0901234567',
        'is_featured'    => 1,
        'description'    => '<p>Khóa học <strong>Lập trình Android toàn tập</strong> giúp bạn nắm vững Kotlin, thiết kế giao diện Material Design và xây dựng các ứng dụng Android thực tế. Bạn sẽ học cách tích hợp Firebase, Room Database, API REST và xuất bản ứng dụng lên Google Play Store.</p>',
        'benefits'       => "✅ Nắm vững lập trình Kotlin từ A-Z\n✅ Xây dựng ứng dụng Android hoàn chỉnh\n✅ Tích hợp Firebase Realtime Database\n✅ Làm việc với API REST và JSON\n✅ Xuất bản ứng dụng lên Google Play Store\n✅ Hỗ trợ trả lời câu hỏi suốt khóa học",
        'requirements'   => "📌 Biết lập trình cơ bản (ít nhất 1 ngôn ngữ)\n📌 Có máy tính Windows/macOS cài Android Studio\n📌 Kiến thức cơ bản về OOP là một lợi thế\n📌 Không cần thiết bị Android vật lý (có thể dùng Emulator)",
        'schedule'       => 'Thứ 3, Thứ 5, Thứ 7 (19:00 - 21:00)',
        'study_time'     => '3 - 4 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-08-05',
    ],
    [
        'title'          => 'Master Lập trình iOS với Swift & SwiftUI',
        'thumbnail'      => 'uploads/courses/ios.jpg',
        'category'       => 'Lập trình Mobile',
        'instructor'     => 'Trần Quốc Bảo',
        'level'          => 'Trung cấp',
        'price'          => 899000,
        'original_price' => 1500000,
        'duration_hours' => 38,
        'total_lessons'  => 105,
        'contact_phone'  => '0912345678',
        'is_featured'    => 1,
        'description'    => '<p>Khóa học <strong>Lập trình iOS với Swift</strong> dành cho những ai muốn phát triển ứng dụng cho iPhone/iPad. Bạn sẽ học Swift từ cơ bản, xây dựng UI với SwiftUI, tích hợp Core Data, và phát triển ứng dụng thực tế sẵn sàng đưa lên App Store.</p>',
        'benefits'       => "✅ Thành thạo ngôn ngữ Swift hiện đại\n✅ Xây dựng UI đẹp với SwiftUI\n✅ Tích hợp Core Data lưu trữ cục bộ\n✅ Làm việc với API và JSON\n✅ Đưa ứng dụng lên App Store\n✅ Học cách debug và tối ưu hiệu năng",
        'requirements'   => "📌 Có máy Mac (bắt buộc để build iOS)\n📌 Biết ít nhất 1 ngôn ngữ lập trình\n📌 Cài đặt Xcode phiên bản mới nhất\n📌 Kiến thức OOP là lợi thế",
        'schedule'       => 'Thứ 2, Thứ 4, Thứ 6 (18:30 - 21:00)',
        'study_time'     => '3 - 5 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-08-10',
    ],
    [
        'title'          => 'Lập trình C/C++ toàn tập – Nền tảng vững chắc',
        'thumbnail'      => 'uploads/courses/c_cpp.png',
        'category'       => 'Ngôn ngữ lập trình',
        'instructor'     => 'Lê Văn Hùng',
        'level'          => 'Sơ cấp',
        'price'          => 499000,
        'original_price' => 800000,
        'duration_hours' => 30,
        'total_lessons'  => 85,
        'contact_phone'  => '0923456789',
        'is_featured'    => 1,
        'description'    => '<p><strong>C/C++</strong> là nền tảng vững chắc cho mọi lập trình viên. Khóa học này đưa bạn từ cú pháp cơ bản đến con trỏ, cấp phát bộ nhớ động, cấu trúc dữ liệu và lập trình hướng đối tượng trong C++. Thích hợp cho sinh viên CNTT và những ai muốn xây dựng nền tảng vững chắc.</p>',
        'benefits'       => "✅ Hiểu sâu về bộ nhớ và cách máy tính hoạt động\n✅ Nắm vững con trỏ và quản lý bộ nhớ\n✅ Lập trình hướng đối tượng trong C++\n✅ Giải quyết bài toán thuật toán hiệu quả\n✅ Nền tảng để học các ngôn ngữ khác nhanh hơn",
        'requirements'   => "📌 Không cần kinh nghiệm lập trình trước\n📌 Cài đặt IDE (Code::Blocks, VSCode)\n📌 Kiên nhẫn và ham học hỏi",
        'schedule'       => 'Thứ 2, Thứ 4 (18:00 - 20:00)',
        'study_time'     => '2 - 3 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-08-01',
    ],
    [
        'title'          => 'Lập trình C# và .NET – Xây dựng ứng dụng thực tế',
        'thumbnail'      => 'uploads/courses/c-sharp.png',
        'category'       => 'Ngôn ngữ lập trình',
        'instructor'     => 'Phạm Ngọc Linh',
        'level'          => 'Trung cấp',
        'price'          => 699000,
        'original_price' => 1100000,
        'duration_hours' => 35,
        'total_lessons'  => 98,
        'contact_phone'  => '0934567890',
        'is_featured'    => 0,
        'description'    => '<p>Khóa học <strong>C# và .NET</strong> trang bị cho bạn đầy đủ kiến thức để phát triển ứng dụng Desktop, Web API và làm việc với Entity Framework. C# được sử dụng rộng rãi trong doanh nghiệp và là ngôn ngữ chính trong phát triển game Unity.</p>',
        'benefits'       => "✅ Thành thạo C# và .NET Framework/.NET Core\n✅ Xây dựng Web API với ASP.NET\n✅ Làm việc với CSDL qua Entity Framework\n✅ Hiểu LINQ và lập trình không đồng bộ\n✅ Nền tảng phát triển game với Unity",
        'requirements'   => "📌 Biết lập trình cơ bản ít nhất 1 ngôn ngữ\n📌 Cài đặt Visual Studio (Community Edition - miễn phí)\n📌 Máy tính Windows khuyến nghị",
        'schedule'       => 'Thứ 3, Thứ 7 (19:00 - 21:30)',
        'study_time'     => '3 - 4 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-08-12',
    ],
    [
        'title'          => 'Java Programming Masterclass – Từ Beginner đến Pro',
        'thumbnail'      => 'uploads/courses/java.jpg',
        'category'       => 'Ngôn ngữ lập trình',
        'instructor'     => 'Nguyễn Minh Quân',
        'level'          => 'Trung cấp',
        'price'          => 749000,
        'original_price' => 1300000,
        'duration_hours' => 45,
        'total_lessons'  => 130,
        'contact_phone'  => '0945678901',
        'is_featured'    => 1,
        'description'    => '<p><strong>Java</strong> là ngôn ngữ hàng đầu trong doanh nghiệp, đặc biệt trong lĩnh vực Backend và Android. Khóa học này đưa bạn từ nền tảng Java SE đến Java EE, Spring Boot và thiết kế hệ thống backend hoàn chỉnh.</p>',
        'benefits'       => "✅ Thành thạo Java SE và Java EE\n✅ Lập trình backend với Spring Boot\n✅ Tích hợp CSDL với Hibernate & JPA\n✅ Viết RESTful API chuẩn doanh nghiệp\n✅ Hiểu các Design Pattern quan trọng\n✅ Chuẩn bị cho phỏng vấn Java Developer",
        'requirements'   => "📌 Không cần kinh nghiệm trước (từ số 0)\n📌 Cài JDK và IDE (IntelliJ IDEA/Eclipse)\n📌 Kiên trì và luyện tập code mỗi ngày",
        'schedule'       => 'Thứ 2, Thứ 4, Thứ 6 (19:00 - 21:00)',
        'study_time'     => '4 - 5 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-08-04',
    ],
    [
        'title'          => 'JavaScript Toàn tập – Từ Vanilla đến ES6+ Modern JS',
        'thumbnail'      => 'uploads/courses/javascript.jpg',
        'category'       => 'Lập trình Web',
        'instructor'     => 'Trần Quang Huy',
        'level'          => 'Sơ cấp',
        'price'          => 599000,
        'original_price' => 950000,
        'duration_hours' => 33,
        'total_lessons'  => 95,
        'contact_phone'  => '0956789012',
        'is_featured'    => 1,
        'description'    => '<p><strong>JavaScript</strong> là ngôn ngữ không thể thiếu của mọi lập trình viên Web. Khóa học này dạy bạn từ cú pháp cơ bản, DOM Manipulation, Event Handling, Async/Await đến các tính năng ES6+ và thực hành xây dựng các dự án thực tế.</p>',
        'benefits'       => "✅ Nắm vững JavaScript từ cơ bản đến nâng cao\n✅ Thành thạo ES6+ (Arrow Function, Destructuring, Modules)\n✅ Làm chủ bất đồng bộ với Promise và Async/Await\n✅ Gọi API và xử lý dữ liệu JSON\n✅ Xây dựng các dự án thực tế: Todo App, Weather App\n✅ Nền tảng vững để học React, Vue, Node.js",
        'requirements'   => "📌 Biết HTML và CSS cơ bản\n📌 Có trình duyệt Chrome và VSCode\n📌 Không cần biết JavaScript trước",
        'schedule'       => 'Thứ 3, Thứ 5 (18:30 - 20:30)',
        'study_time'     => '3 - 4 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-08-06',
    ],
    [
        'title'          => 'Tư duy Lập trình Hướng đối tượng (OOP) – Hiểu thật sự',
        'thumbnail'      => 'uploads/courses/oop.png',
        'category'       => 'Tư duy lập trình',
        'instructor'     => 'Lê Thành Đạt',
        'level'          => 'Trung cấp',
        'price'          => 449000,
        'original_price' => 750000,
        'duration_hours' => 20,
        'total_lessons'  => 60,
        'contact_phone'  => '0967890123',
        'is_featured'    => 0,
        'description'    => '<p><strong>OOP (Lập trình hướng đối tượng)</strong> là nền tảng mà mọi lập trình viên phải nắm vững. Khóa học này không chỉ dạy cú pháp mà giúp bạn tư duy theo hướng đối tượng, hiểu 4 tính chất cốt lõi và áp dụng các Design Pattern phổ biến.</p>',
        'benefits'       => "✅ Hiểu sâu 4 tính chất OOP: Encapsulation, Inheritance, Polymorphism, Abstraction\n✅ Tư duy phân tích và thiết kế theo hướng đối tượng\n✅ Áp dụng các Design Pattern cơ bản (Singleton, Factory...)\n✅ Viết code sạch, dễ bảo trì và mở rộng\n✅ Có thể áp dụng với mọi ngôn ngữ OOP (Java, C#, Python, PHP)",
        'requirements'   => "📌 Biết ít nhất 1 ngôn ngữ lập trình cơ bản\n📌 Đã viết được các chương trình đơn giản",
        'schedule'       => 'Thứ 7, Chủ nhật (09:00 - 11:00)',
        'study_time'     => '2 - 3 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-08-08',
    ],
    [
        'title'          => 'Lập trình Web PHP & MySQL – Xây dựng Website động',
        'thumbnail'      => 'uploads/courses/php.png',
        'category'       => 'Lập trình Web',
        'instructor'     => 'Võ Minh Khoa',
        'level'          => 'Sơ cấp',
        'price'          => 649000,
        'original_price' => 1050000,
        'duration_hours' => 40,
        'total_lessons'  => 115,
        'contact_phone'  => '0978901234',
        'is_featured'    => 1,
        'description'    => '<p><strong>PHP & MySQL</strong> vẫn là bộ đôi được sử dụng rộng rãi nhất trong phát triển web. Khóa học này đưa bạn từ PHP cơ bản, làm việc với MySQL, xây dựng website động có đăng nhập, phân quyền đến triển khai server thực tế.</p>',
        'benefits'       => "✅ Thành thạo PHP 8.x và các tính năng mới\n✅ Làm việc hiệu quả với MySQL và PDO\n✅ Xây dựng website có hệ thống đăng nhập, phân quyền\n✅ Hiểu mô hình MVC và áp dụng vào dự án\n✅ Bảo mật ứng dụng web (SQL Injection, XSS, CSRF)\n✅ Triển khai ứng dụng lên hosting/VPS",
        'requirements'   => "📌 Biết HTML, CSS cơ bản\n📌 Cài XAMPP hoặc Laragon\n📌 Không cần biết PHP trước",
        'schedule'       => 'Thứ 2, Thứ 4, Thứ 6 (19:00 - 21:30)',
        'study_time'     => '4 - 5 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-08-02',
    ],
    [
        'title'          => 'Lập trình Python cho Người mới bắt đầu – Thực chiến',
        'thumbnail'      => 'uploads/courses/python.jpg',
        'category'       => 'Ngôn ngữ lập trình',
        'instructor'     => 'Đinh Thị Hồng Nhung',
        'level'          => 'Sơ cấp',
        'price'          => 0,
        'original_price' => 600000,
        'duration_hours' => 25,
        'total_lessons'  => 72,
        'contact_phone'  => '0989012345',
        'is_featured'    => 1,
        'description'    => '<p><strong>Python</strong> là ngôn ngữ lập trình thân thiện nhất cho người mới bắt đầu và cũng là ngôn ngữ số 1 trong Data Science, AI/ML. Khóa học này dạy Python từ căn bản qua các dự án thực tế thú vị như Web Scraping, Data Analysis.</p>',
        'benefits'       => "✅ Làm chủ Python 3 từ cơ bản đến nâng cao\n✅ Xây dựng các script tự động hóa công việc\n✅ Thu thập dữ liệu web (Web Scraping với BeautifulSoup)\n✅ Phân tích dữ liệu với Pandas và NumPy\n✅ Nền tảng vững để tiến vào Machine Learning\n✅ MIỄN PHÍ – Không mất tiền!",
        'requirements'   => "📌 Không cần bất kỳ kinh nghiệm lập trình nào\n📌 Cài Python 3.x và VSCode\n📌 Chỉ cần máy tính và sự ham học",
        'schedule'       => 'Thứ 3, Thứ 5, Thứ 7 (20:00 - 21:30)',
        'study_time'     => '2 - 3 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-07-20',
    ],
    [
        'title'          => 'TypeScript Từ Cơ bản đến Nâng cao – Viết code an toàn hơn',
        'thumbnail'      => 'uploads/courses/typescript.png',
        'category'       => 'Lập trình Web',
        'instructor'     => 'Bùi Hoàng Long',
        'level'          => 'Trung cấp',
        'price'          => 549000,
        'original_price' => 900000,
        'duration_hours' => 28,
        'total_lessons'  => 80,
        'contact_phone'  => '0990123456',
        'is_featured'    => 0,
        'description'    => '<p><strong>TypeScript</strong> là phiên bản nâng cấp của JavaScript với hệ thống kiểu dữ liệu tĩnh, giúp code an toàn, dễ bảo trì và scale. TypeScript là yêu cầu bắt buộc khi làm việc với Angular và ngày càng phổ biến với React và Node.js trong các dự án lớn.</p>',
        'benefits'       => "✅ Hiểu sâu hệ thống kiểu dữ liệu TypeScript\n✅ Viết code an toàn, ít bug hơn nhờ Static Typing\n✅ Sử dụng Generics, Interfaces, Decorators thành thạo\n✅ Tích hợp TypeScript với React và Node.js\n✅ Debug dễ dàng với các công cụ TypeScript\n✅ Đáp ứng yêu cầu tuyển dụng của các công ty lớn",
        'requirements'   => "📌 Đã biết JavaScript ES6+ cơ bản\n📌 Hiểu khái niệm OOP\n📌 Cài Node.js và VSCode",
        'schedule'       => 'Thứ 4, Thứ 7 (19:30 - 21:30)',
        'study_time'     => '3 - 4 giờ/tuần',
        'language'       => 'Tiếng Việt',
        'start_date'     => '2026-08-15',
    ],
];

$courseStmt = $pdo->prepare("
    INSERT INTO courses (category_id, title, is_featured, price, original_price, instructor, level, duration_hours, total_lessons, language, description, thumbnail, benefits, requirements, start_date, schedule, study_time, contact_phone)
    VALUES (:category_id, :title, :is_featured, :price, :original_price, :instructor, :level, :duration_hours, :total_lessons, :language, :description, :thumbnail, :benefits, :requirements, :start_date, :schedule, :study_time, :contact_phone)
");

$courseIds = [];
foreach ($courses as $course) {
    $catId = $catIds[$course['category']] ?? 1;
    $courseStmt->execute([
        ':category_id'    => $catId,
        ':title'          => $course['title'],
        ':is_featured'    => $course['is_featured'],
        ':price'          => $course['price'],
        ':original_price' => $course['original_price'],
        ':instructor'     => $course['instructor'],
        ':level'          => $course['level'],
        ':duration_hours' => $course['duration_hours'],
        ':total_lessons'  => $course['total_lessons'],
        ':language'       => $course['language'],
        ':description'    => $course['description'],
        ':thumbnail'      => $course['thumbnail'],
        ':benefits'       => $course['benefits'],
        ':requirements'   => $course['requirements'],
        ':start_date'     => $course['start_date'],
        ':schedule'       => $course['schedule'],
        ':study_time'     => $course['study_time'],
        ':contact_phone'  => $course['contact_phone'],
    ]);
    $id = $pdo->lastInsertId();
    $courseIds[] = $id;
    echo "  + Khóa học: {$course['title']} (ID: $id)\n";
}
echo "✅ Thêm " . count($courseIds) . " khóa học xong!\n\n";

// ============================================================
// BƯỚC 5: TẠO BÀI VIẾT DIỄN ĐÀN (POSTS)
// ============================================================
echo "📝 Đang thêm bài viết diễn đàn...\n";
$uid1 = $userIds['student1@gmail.com'];
$uid2 = $userIds['student2@gmail.com'];
$uid3 = $userIds['student3@gmail.com'];
$uid4 = $userIds['student4@gmail.com'];
$uid5 = $userIds['student5@gmail.com'];

$posts = [
    [
        'user_id'     => $uid1,
        'title'       => 'Lộ trình học Web Frontend năm 2026 nên bắt đầu từ đâu?',
        'is_featured' => 1,
        'content'     => '<p>Chào mọi người, mình đang bắt đầu học lập trình Web Frontend. Mình đã xem qua nhiều nguồn trên internet nhưng thấy khá rối, có bạn nào đã qua giai đoạn này có thể chia sẻ <strong>lộ trình học Frontend năm 2026</strong> không?</p><p>Mình đang phân vân giữa việc học:</p><ul><li>HTML/CSS → JavaScript → React</li><li>HTML/CSS → JavaScript → Vue.js</li></ul><p>Theo các bạn thì nên học gì trước? Và mất bao lâu để có thể đi làm? 😊</p>',
    ],
    [
        'user_id'     => $uid2,
        'title'       => 'Bắt đầu học Mobile nên chọn Android hay iOS? Kinh nghiệm thực tế',
        'is_featured' => 1,
        'content'     => '<p>Mình đang muốn học lập trình di động nhưng không biết nên chọn <strong>Android (Kotlin)</strong> hay <strong>iOS (Swift)</strong> để bắt đầu.</p><p>Mình tìm hiểu sơ thì thấy:</p><ul><li>Android: Thị trường Việt Nam chiếm đa số, nhiều việc làm hơn</li><li>iOS: Lương cao hơn, nhưng cần máy Mac để lập trình</li></ul><p>Các bạn đang làm trong ngành có thể tư vấn giúp mình không? Cảm ơn mọi người nhiều ạ!</p>',
    ],
    [
        'user_id'     => $uid3,
        'title'       => '[Chia sẻ] Tài liệu học OOP hiệu quả mà mình đã dùng',
        'is_featured' => 0,
        'content'     => '<p>Sau nhiều tháng vật lộn với <strong>OOP (Lập trình hướng đối tượng)</strong>, mình đã tổng hợp được những tài liệu và cách học hiệu quả nhất. Chia sẻ với mọi người nhé!</p><h3>📚 Tài liệu mình dùng:</h3><ul><li><strong>Head First Object-Oriented Analysis and Design</strong> – Giải thích rất trực quan</li><li><strong>Clean Code</strong> của Uncle Bob – Không phải OOP thuần nhưng rất bổ ích</li><li>Series YouTube "OOP in 100 Seconds" – Ngắn gọn, dễ hiểu</li></ul><h3>💡 Tips học OOP hiệu quả:</h3><ol><li>Đừng chỉ học cú pháp – Phải hiểu TẠI SAO cần OOP</li><li>Lấy ví dụ từ thực tế (Xe hơi, Ngân hàng, Game...)</li><li>Thực hành thiết kế class diagram trước khi code</li></ol><p>Mọi người có thêm gì hay thì comment bên dưới nhé! 🙌</p>',
    ],
    [
        'user_id'     => $uid4,
        'title'       => 'Python hay Java: Ngôn ngữ nào phù hợp cho sinh viên mới ra trường?',
        'is_featured' => 0,
        'content'     => '<p>Mình sắp tốt nghiệp và đang cân nhắc nên đầu tư thời gian vào <strong>Python</strong> hay <strong>Java</strong> để chuẩn bị cho công việc đầu tiên.</p><p>Mình thấy:</p><ul><li>Python đang hot vì AI/ML, Data Science</li><li>Java vẫn rất mạnh trong doanh nghiệp lớn, lương ổn định</li></ul><p>Định hướng của mình là Backend Developer. Mọi người tư vấn giúp mình với nhé! 🙏</p>',
    ],
    [
        'user_id'     => $uid5,
        'title'       => 'Tự học TypeScript sau khi biết JavaScript – Có khó không?',
        'is_featured' => 0,
        'content'     => '<p>Mình đã học JavaScript được khoảng 4 tháng và có thể làm được các dự án cơ bản. Mình thấy nhiều job description yêu cầu <strong>TypeScript</strong>.</p><p>Mình muốn hỏi:</p><ul><li>Biết JS rồi thì học TS có khó không?</li><li>Mất bao lâu để dùng được TS trong dự án thực tế?</li><li>Nên học TS standalone hay kết hợp ngay với React?</li></ul><p>Cảm ơn mọi người đã đọc! 😄</p>',
    ],
];

$postStmt = $pdo->prepare("INSERT INTO posts (user_id, title, is_featured, content) VALUES (:user_id, :title, :is_featured, :content)");
$postIds = [];
foreach ($posts as $post) {
    $postStmt->execute($post);
    $id = $pdo->lastInsertId();
    $postIds[] = $id;
    echo "  + Bài viết: {$post['title']} (ID: $id)\n";
}
echo "✅ Thêm " . count($postIds) . " bài viết xong!\n\n";

// ============================================================
// BƯỚC 6: TẠO BÌNH LUẬN (COMMENTS)
// ============================================================
echo "💬 Đang thêm bình luận...\n";
$commentStmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, parent_id, is_pinned, content) VALUES (:post_id, :user_id, :parent_id, :is_pinned, :content)");

function addComment($stmt, $postId, $userId, $parentId, $isPinned, $content) {
    $stmt->execute([
        ':post_id'   => $postId,
        ':user_id'   => $userId,
        ':parent_id' => $parentId,
        ':is_pinned' => $isPinned,
        ':content'   => $content,
    ]);
    return $stmt->getWrappedStatement() ? null : null;
}

$pdo2 = $pdo; // dùng chung pdo để lấy lastInsertId

// --- Bình luận cho Bài viết 1: Frontend Roadmap ---
$pid1 = $postIds[0];
$commentStmt->execute([':post_id'=>$pid1,':user_id'=>$uid2,':parent_id'=>null,':is_pinned'=>1,':content'=>'<p>Theo mình thì lộ trình chuẩn nhất hiện tại là: <strong>HTML/CSS → JavaScript (thật vững) → React hoặc Vue</strong>. Quan trọng nhất là JavaScript phải thật vững trước khi học Framework, vì Framework chỉ là công cụ thôi. Mình thấy nhiều bạn học React khi JS còn yếu thì rất dễ bị lạc.</p>']);
$c1 = $pdo->lastInsertId();

$commentStmt->execute([':post_id'=>$pid1,':user_id'=>$uid3,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>Mình chọn Vue.js vì cú pháp đơn giản hơn React và phù hợp với người đã biết HTML/CSS. Nhưng nếu bạn muốn cơ hội việc làm nhiều hơn thì React chiếm ưu thế hơn ở Việt Nam đấy nhé!</p>']);
$c2 = $pdo->lastInsertId();

$commentStmt->execute([':post_id'=>$pid1,':user_id'=>$uid4,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>Ngoài framework ra, bạn cũng nên học thêm <strong>Git, GitHub</strong> và hiểu cơ bản về <strong>responsive design</strong> nhé. Nhà tuyển dụng rất coi trọng những kỹ năng này.</p>']);
$c3 = $pdo->lastInsertId();

// Reply cho c1
$commentStmt->execute([':post_id'=>$pid1,':user_id'=>$uid1,':parent_id'=>$c1,':is_pinned'=>0,':content'=>'<p>Cảm ơn bạn nhiều! Vậy theo bạn thì học JS bao lâu thì đủ để chuyển sang React?</p>']);
$commentStmt->execute([':post_id'=>$pid1,':user_id'=>$uid2,':parent_id'=>$c1,':is_pinned'=>0,':content'=>'<p>Mình nghĩ cần ít nhất 2-3 tháng học JS nghiêm túc. Phải hiểu được Closure, Prototype, Async/Await, ES6+ rồi mới nên đụng đến React nhé!</p>']);

// Reply cho c2
$commentStmt->execute([':post_id'=>$pid1,':user_id'=>$uid5,':parent_id'=>$c2,':is_pinned'=>0,':content'=>'<p>+1 cho Vue.js! Cú pháp HTML-like của Vue rất dễ làm quen nếu bạn đã biết HTML/CSS.</p>']);

// --- Bình luận cho Bài viết 2: Android vs iOS ---
$pid2 = $postIds[1];
$commentStmt->execute([':post_id'=>$pid2,':user_id'=>$uid1,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>Mình đang làm Android Developer được 2 năm. Theo mình, ở Việt Nam thì <strong>Android</strong> có nhiều cơ hội việc làm hơn. Số lượng job Android trên TopDev hay ITviec nhiều hơn iOS gần gấp đôi. Nếu chưa có Mac thì Android là lựa chọn hợp lý hơn.</p>']);
$c4 = $pdo->lastInsertId();

$commentStmt->execute([':post_id'=>$pid2,':user_id'=>$uid3,':parent_id'=>null,':is_pinned'=>1,':content'=>'<p>Nếu bạn có điều kiện, có thể cân nhắc học <strong>Flutter (Dart)</strong> – một lần viết code chạy được cả Android và iOS. Nhiều startup đang dùng Flutter để tiết kiệm chi phí phát triển. Lương Flutter Developer cũng đang tăng mạnh!</p>']);
$c5 = $pdo->lastInsertId();

$commentStmt->execute([':post_id'=>$pid2,':user_id'=>$uid4,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>iOS lương cao hơn thật, nhưng để test app bạn PHẢI có máy Mac và có thể cần cả iPhone thật. Tổng chi phí đầu tư ban đầu khá lớn. Nếu ngân sách hạn chế thì Android là lựa chọn tốt hơn.</p>']);

$commentStmt->execute([':post_id'=>$pid2,':user_id'=>$uid2,':parent_id'=>$c4,':is_pinned'=>0,':content'=>'<p>Bạn ơi, vậy học Kotlin hay Java cho Android thì tốt hơn? Mình thấy nhiều người vẫn dùng Java.</p>']);
$commentStmt->execute([':post_id'=>$pid2,':user_id'=>$uid1,':parent_id'=>$c4,':is_pinned'=>0,':content'=>'<p>Học Kotlin đi bạn! Google đã chính thức ưu tiên Kotlin, hầu hết tài liệu và thư viện mới đều hướng dẫn bằng Kotlin. Java Android vẫn hoạt động nhưng đang dần lỗi thời rồi.</p>']);
$commentStmt->execute([':post_id'=>$pid2,':user_id'=>$uid5,':parent_id'=>$c5,':is_pinned'=>0,':content'=>'<p>Flutter thật sự rất tiềm năng! Mình đang học Flutter và thấy cộng đồng phát triển rất mạnh. Dart cũng khá dễ học nếu đã biết OOP.</p>']);

// --- Bình luận cho Bài viết 3: OOP Resources ---
$pid3 = $postIds[2];
$commentStmt->execute([':post_id'=>$pid3,':user_id'=>$uid2,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>Cảm ơn bạn chia sẻ! Mình thêm vào list: <strong>"Object-Oriented Thought Process"</strong> của Matt Weisfeld rất dễ đọc cho người mới. Sách giải thích tại sao cần OOP trước khi đi vào cú pháp, rất hay!</p>']);
$commentStmt->execute([':post_id'=>$pid3,':user_id'=>$uid4,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>Mình thấy cách hiệu quả nhất là <strong>vẽ UML class diagram</strong> trước rồi mới code. Ban đầu hơi tốn thời gian nhưng khi code sẽ nhanh và ít bug hơn nhiều. Bạn có thể dùng draw.io miễn phí.</p>']);
$commentStmt->execute([':post_id'=>$pid3,':user_id'=>$uid5,':parent_id'=>null,':is_pinned'=>1,':content'=>'<p>Tip vàng từ kinh nghiệm của mình: Hãy viết lại các ứng dụng quen thuộc (như hệ thống quản lý thư viện, bán hàng) theo OOP. Khi bạn có thể thiết kế class structure cho vấn đề thực tế, đó là lúc bạn thực sự hiểu OOP rồi!</p>']);

// --- Bình luận cho Bài viết 4: Python vs Java ---
$pid4 = $postIds[3];
$commentStmt->execute([':post_id'=>$pid4,':user_id'=>$uid1,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>Mình làm Backend với Java Spring Boot 3 năm rồi. Nếu bạn muốn đi vào doanh nghiệp lớn (ngân hàng, fintech, outsourcing) thì Java rất phù hợp. Lương ổn định và có nhiều cơ hội career growth.</p>']);
$commentStmt->execute([':post_id'=>$pid4,':user_id'=>$uid3,':parent_id'=>null,':is_pinned'=>1,':content'=>'<p>Cho Backend thì cả hai đều tốt, nhưng mình recommend <strong>Python với FastAPI hoặc Django</strong> nếu bạn muốn startup vibe – dev nhanh hơn, cộng đồng sôi động. Còn <strong>Java Spring Boot</strong> nếu bạn nhắm vào corporate/enterprise. Tùy văn hóa công ty bạn muốn nhé!</p>']);
$commentStmt->execute([':post_id'=>$pid4,':user_id'=>$uid5,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>Một góc nhìn khác: Học Python trước vì cú pháp đơn giản hơn, giúp bạn tập trung vào tư duy lập trình. Sau đó học Java sẽ nhanh hơn nhiều. Đừng quá lo về ngôn ngữ, kỹ năng giải quyết vấn đề mới quan trọng nhất!</p>']);

// --- Bình luận cho Bài viết 5: TypeScript ---
$pid5 = $postIds[4];
$commentStmt->execute([':post_id'=>$pid5,':user_id'=>$uid1,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>Đã biết JavaScript tốt thì học TypeScript cực kỳ nhanh! Mình mất khoảng 2 tuần là bắt đầu dùng được trong dự án. TS chủ yếu thêm Type System lên JS thôi, không có gì magic cả.</p>']);
$c6 = $pdo->lastInsertId();

$commentStmt->execute([':post_id'=>$pid5,':user_id'=>$uid3,':parent_id'=>null,':is_pinned'=>1,':content'=>'<p>Mình suggest học TS kết hợp ngay với React hoặc Node.js. Học "TypeScript thuần" khá nhàm, nhưng khi áp dụng vào dự án thực tế bạn sẽ thấy ngay lợi ích. IDE như VSCode hỗ trợ TypeScript rất tốt, autocomplete và error detection cực kỳ mạnh!</p>']);

$commentStmt->execute([':post_id'=>$pid5,':user_id'=>$uid4,':parent_id'=>null,':is_pinned'=>0,':content'=>'<p>Một lưu ý: Ban đầu bạn sẽ thấy TypeScript hơi "verbose" và mất nhiều thời gian define types. Nhưng khi dự án lớn lên, bạn sẽ thấy TS tiết kiệm cực nhiều thời gian debug. Đây là long-term investment!</p>']);

$commentStmt->execute([':post_id'=>$pid5,':user_id'=>$uid5,':parent_id'=>$c6,':is_pinned'=>0,':content'=>'<p>2 tuần thôi sao! Mình tưởng mất cả tháng. Bạn có recommend khóa học hay channel nào không?</p>']);
$commentStmt->execute([':post_id'=>$pid5,':user_id'=>$uid1,':parent_id'=>$c6,':is_pinned'=>0,':content'=>'<p>Bạn thử xem "TypeScript Full Course" của Matt Pocock trên YouTube nhé – anh ấy giải thích rất dễ hiểu và thực tế. Ngoài ra documentation chính thức của TypeScript cũng rất tốt!</p>']);

echo "✅ Thêm bình luận xong!\n\n";

// ============================================================
// TỔNG KẾT
// ============================================================
$totalCats    = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalUsers   = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPosts   = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$totalComments= $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();

echo "============================================\n";
echo "🎉 SEEDING HOÀN TẤT!\n";
echo "============================================\n";
echo "  📁 Danh mục   : $totalCats\n";
echo "  📚 Khóa học   : $totalCourses\n";
echo "  👤 Người dùng : $totalUsers\n";
echo "  📝 Bài viết   : $totalPosts\n";
echo "  💬 Bình luận  : $totalComments\n";
echo "============================================\n";
echo "✅ Trang web đã sẵn sàng để demo!\n";

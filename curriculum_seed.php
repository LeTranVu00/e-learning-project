<?php
/**
 * CURRICULUM SEEDER – Thêm Chương, Bài giảng, Quiz
 * Chạy: php curriculum_seed.php
 */
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO(
    "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
    $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "✅ Kết nối DB thành công!\n\n";

// Xóa dữ liệu cũ
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$pdo->exec("TRUNCATE TABLE material_completions");
$pdo->exec("TRUNCATE TABLE materials");
$pdo->exec("TRUNCATE TABLE chapters");
$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
echo "🧹 Đã xóa chapters & materials cũ.\n\n";

$chapStmt = $pdo->prepare("INSERT INTO chapters (course_id, title, description, order_num) VALUES (:cid, :title, :desc, :ord)");
$matStmt  = $pdo->prepare("INSERT INTO materials (chapter_id, title, description, type, content, order_num) VALUES (:chid, :title, :desc, :type, :content, :ord)");

function addChapter($stmt, $pdo, $cid, $title, $desc, $ord) {
    $stmt->execute([':cid'=>$cid,':title'=>$title,':desc'=>$desc,':ord'=>$ord]);
    return $pdo->lastInsertId();
}
function addLesson($stmt, $chid, $title, $desc, $type, $content, $ord) {
    $stmt->execute([':chid'=>$chid,':title'=>$title,':desc'=>$desc,':type'=>$type,':content'=>$content,':ord'=>$ord]);
}
function quizJson(array $questions): string {
    return json_encode($questions, JSON_UNESCAPED_UNICODE);
}

// ============================================================
// KHÓA HỌC 1 – ANDROID (ID=1)
// ============================================================
echo "📱 Android...\n";
$ch = addChapter($chapStmt,$pdo,1,'Chương 1: Nhập môn Android & Kotlin','Tổng quan về Android, cài đặt môi trường, học Kotlin cơ bản.',1);
addLesson($matStmt,$ch,'Giới thiệu Android & cài đặt Android Studio','Tổng quan nền tảng Android, tải và cài đặt Android Studio.','link','https://www.youtube.com/watch?v=EknEIzswvC0',1);
addLesson($matStmt,$ch,'Kotlin cơ bản – Biến, kiểu dữ liệu, hàm','Cú pháp Kotlin từ cơ bản nhất.','link','https://www.youtube.com/watch?v=F9UC9DY-vIU',2);
addLesson($matStmt,$ch,'Kotlin – OOP: Class, Object, Inheritance','Lập trình hướng đối tượng trong Kotlin.','link','https://www.youtube.com/watch?v=AcA8t6ZHJk8',3);
addLesson($matStmt,$ch,'[Quiz] Kiểm tra kiến thức Kotlin cơ bản','Bài kiểm tra trắc nghiệm Kotlin.','quiz',quizJson([
    ['question'=>'Trong Kotlin, từ khóa nào dùng để khai báo biến không thể thay đổi (immutable)?','options'=>['var','val','let','const'],'answer'=>1],
    ['question'=>'Hàm main trong Kotlin được khai báo như thế nào?','options'=>['func main(){}','void main(){}','fun main(){}','def main():'],'answer'=>2],
    ['question'=>'Kotlin có nullable type. Để khai báo String có thể null, ta viết:','options'=>['String','String!','String?','Nullable<String>'],'answer'=>2],
    ['question'=>'Extension function trong Kotlin cho phép:','options'=>['Thêm phương thức vào class mà không cần kế thừa','Tạo class mới','Xóa method của class cũ','Không có tác dụng gì'],'answer'=>0],
    ['question'=>'`data class` trong Kotlin tự động generate những method nào?','options'=>['equals(), hashCode(), toString(), copy()','run(), let(), apply()','start(), stop()','init(), destroy()'],'answer'=>0],
]),4);

$ch2 = addChapter($chapStmt,$pdo,1,'Chương 2: Giao diện người dùng – Layouts & Views','XML Layout, View cơ bản, RecyclerView.',2);
addLesson($matStmt,$ch2,'Activity, Intent và vòng đời Activity','Hiểu lifecycle của Activity Android.','link','https://www.youtube.com/watch?v=SyXFqUHxQ5Y',1);
addLesson($matStmt,$ch2,'XML Layout: LinearLayout, ConstraintLayout','Xây dựng giao diện bằng XML.','link','https://www.youtube.com/watch?v=9-1OQXHJ_yY',2);
addLesson($matStmt,$ch2,'RecyclerView – Danh sách động','Hiển thị danh sách hiệu quả với RecyclerView.','link','https://www.youtube.com/watch?v=Mc0XT58A1Z4',3);

$ch3 = addChapter($chapStmt,$pdo,1,'Chương 3: Kết nối API & Firebase','Retrofit, Firebase Realtime DB, Authentication.',3);
addLesson($matStmt,$ch3,'Retrofit – Gọi REST API từ Android','Tích hợp Retrofit để fetch data từ API.','link','https://www.youtube.com/watch?v=k2N3EoZI3eU',1);
addLesson($matStmt,$ch3,'Firebase Authentication – Đăng nhập Google','Tích hợp đăng nhập Google qua Firebase.','link','https://www.youtube.com/watch?v=Z-RE1QuUWPg',2);
addLesson($matStmt,$ch3,'Firebase Realtime Database – CRUD dữ liệu','Lưu trữ và đồng bộ dữ liệu real-time.','link','https://www.youtube.com/watch?v=dRyY6-tDwh0',3);
addLesson($matStmt,$ch3,'[Quiz] Kiểm tra Giao diện & API Android','Bài kiểm tra chương 2 và 3.','quiz',quizJson([
    ['question'=>'RecyclerView khác ListView ở điểm nào?','options'=>['RecyclerView nhẹ hơn và có ViewHolder bắt buộc','ListView mới hơn RecyclerView','Không có sự khác biệt','RecyclerView không hỗ trợ click listener'],'answer'=>0],
    ['question'=>'Intent trong Android dùng để:','options'=>['Khai báo biến','Chuyển màn hình hoặc truyền dữ liệu giữa các Activity','Kết nối database','Vẽ giao diện'],'answer'=>1],
    ['question'=>'ConstraintLayout có ưu điểm gì so với LinearLayout?','options'=>['Không có ưu điểm gì','Cho phép thiết kế giao diện phức tạp với ít view lồng nhau hơn','Chỉ dùng được trên API 30+','Nhanh hơn 10 lần'],'answer'=>1],
    ['question'=>'Retrofit là thư viện dùng để:','options'=>['Xây dựng giao diện','Gọi REST API từ Android','Quản lý database cục bộ','Phát nhạc'],'answer'=>1],
    ['question'=>'Firebase Realtime Database lưu dữ liệu theo định dạng:','options'=>['SQL Tables','XML','JSON Tree','Binary'],'answer'=>2],
]),4);

// ============================================================
// KHÓA HỌC 2 – iOS Swift (ID=2)
// ============================================================
echo "🍎 iOS Swift...\n";
$ch = addChapter($chapStmt,$pdo,2,'Chương 1: Swift Language Fundamentals','Học ngôn ngữ Swift từ đầu.',1);
addLesson($matStmt,$ch,'Giới thiệu Swift & cài đặt Xcode','Bắt đầu với Xcode và Playground.','link','https://www.youtube.com/watch?v=CwA1VWrIgoe',1);
addLesson($matStmt,$ch,'Swift Basics – Variables, Constants, Types','Nền tảng Swift: var, let, kiểu dữ liệu.','link','https://www.youtube.com/watch?v=comQ1-x2a1Q',2);
addLesson($matStmt,$ch,'Optionals và Error Handling trong Swift','Xử lý nil và error an toàn.','link','https://www.youtube.com/watch?v=NiQfBdZIW1g',3);
addLesson($matStmt,$ch,'[Quiz] Swift Fundamentals','Kiểm tra kiến thức Swift cơ bản.','quiz',quizJson([
    ['question'=>'Trong Swift, `let` và `var` khác nhau như thế nào?','options'=>['let là biến, var là hằng','let là hằng (constant), var là biến','Cả hai đều là biến','Không có sự khác biệt'],'answer'=>1],
    ['question'=>'Optional trong Swift được ký hiệu bằng:','options'=>['!','?','*','&'],'answer'=>1],
    ['question'=>'Để unwrap Optional an toàn trong Swift, ta dùng:','options'=>['Force unwrap với !','if let hoặc guard let','try catch','@optional'],'answer'=>1],
    ['question'=>'Swift closure tương đương với gì trong nhiều ngôn ngữ khác?','options'=>['Class','Lambda/Anonymous function','Interface','Enum'],'answer'=>1],
    ['question'=>'Protocol trong Swift tương tự với gì trong Java/C#?','options'=>['Abstract Class','Interface','Enum','Struct'],'answer'=>1],
]),4);

$ch2 = addChapter($chapStmt,$pdo,2,'Chương 2: SwiftUI – Xây dựng giao diện hiện đại','Declarative UI với SwiftUI.',2);
addLesson($matStmt,$ch2,'SwiftUI Basics – View, Text, Image, Stack','Các component cơ bản trong SwiftUI.','link','https://www.youtube.com/watch?v=bqu6BquVi2M',1);
addLesson($matStmt,$ch2,'State, Binding và ObservableObject','Quản lý state trong SwiftUI.','link','https://www.youtube.com/watch?v=MkFqbAeRBFE',2);
addLesson($matStmt,$ch2,'NavigationView và List trong SwiftUI','Điều hướng và danh sách.','link','https://www.youtube.com/watch?v=VLXqnQFkMjo',3);

$ch3 = addChapter($chapStmt,$pdo,2,'Chương 3: Core Data & Networking','Lưu trữ local và gọi API.',3);
addLesson($matStmt,$ch3,'URLSession – Gọi API trong Swift','Fetch dữ liệu từ REST API.','link','https://www.youtube.com/watch?v=sqo844OKESA',1);
addLesson($matStmt,$ch3,'Core Data – Lưu trữ dữ liệu cục bộ','Quản lý persistence với Core Data.','link','https://www.youtube.com/watch?v=O7u9nYWjvKk',2);
addLesson($matStmt,$ch3,'[Quiz] SwiftUI & Networking','Bài kiểm tra chương 2-3.','quiz',quizJson([
    ['question'=>'SwiftUI là gì?','options'=>['Một ngôn ngữ lập trình mới','Framework declarative UI của Apple','Thay thế cho Xcode','Một Design Pattern'],'answer'=>1],
    ['question'=>'@State trong SwiftUI dùng để:','options'=>['Khai báo constant','Quản lý trạng thái cục bộ của View','Gọi API','Lưu dữ liệu vào database'],'answer'=>1],
    ['question'=>'URLSession trong iOS dùng để:','options'=>['Quản lý UI','Thực hiện network requests (HTTP)','Lưu trữ dữ liệu','Phát video'],'answer'=>1],
    ['question'=>'Core Data trong iOS là:','options'=>['REST API framework','ORM/Persistence framework do Apple cung cấp','Thư viện giao diện','Công cụ debug'],'answer'=>1],
    ['question'=>'@Binding trong SwiftUI cho phép:','options'=>['Chia sẻ và đồng bộ state giữa parent và child view','Gọi function bất đồng bộ','Tải ảnh từ URL','Không có tác dụng gì'],'answer'=>0],
]),3);

// ============================================================
// KHÓA HỌC 3 – C/C++ (ID=3)
// ============================================================
echo "⚙️  C/C++...\n";
$ch = addChapter($chapStmt,$pdo,3,'Chương 1: C – Ngôn ngữ nền tảng','Syntax C, Input/Output, điều kiện, vòng lặp.',1);
addLesson($matStmt,$ch,'Hello World & cấu trúc chương trình C','Chương trình C đầu tiên, biên dịch và chạy.','link','https://www.youtube.com/watch?v=KJgsSFOSQv0',1);
addLesson($matStmt,$ch,'Biến, kiểu dữ liệu và toán tử trong C','int, float, char và các toán tử.','link','https://www.youtube.com/watch?v=GjAo5zLKZIo',2);
addLesson($matStmt,$ch,'Câu lệnh điều kiện if-else và switch','Rẽ nhánh logic chương trình.','link','https://www.youtube.com/watch?v=9jHoURYynDs',3);
addLesson($matStmt,$ch,'Vòng lặp for, while, do-while','Lặp lại thao tác trong C.','link','https://www.youtube.com/watch?v=oWQJmEGmFBs',4);
addLesson($matStmt,$ch,'[Quiz] Kiểm tra C cơ bản','Trắc nghiệm về cú pháp và cấu trúc C.','quiz',quizJson([
    ['question'=>'Trong C, hàm để in ra màn hình là:','options'=>['print()','console.log()','printf()','cout'],'answer'=>2],
    ['question'=>'Kiểu dữ liệu nào lưu số thực (decimal) trong C?','options'=>['int','char','bool','float/double'],'answer'=>3],
    ['question'=>'Toán tử `%` trong C có nghĩa là:','options'=>['Phần trăm','Chia lấy dư (modulo)','Chia lấy phần nguyên','Nhân'],'answer'=>1],
    ['question'=>'Để đọc input từ bàn phím trong C, ta dùng:','options'=>['scanf()','input()','cin','readline()'],'answer'=>0],
    ['question'=>'Vòng lặp nào luôn chạy ít nhất 1 lần trong C?','options'=>['for','while','do-while','foreach'],'answer'=>2],
]),5);

$ch2 = addChapter($chapStmt,$pdo,3,'Chương 2: Con trỏ & Bộ nhớ động','Pointer, malloc, free – trái tim của C.',2);
addLesson($matStmt,$ch2,'Con trỏ (Pointer) – Khái niệm và khai báo','Hiểu địa chỉ bộ nhớ và con trỏ.','link','https://www.youtube.com/watch?v=f2i0CnUOniA',1);
addLesson($matStmt,$ch2,'Cấp phát bộ nhớ động – malloc, calloc, free','Quản lý bộ nhớ heap trong C.','link','https://www.youtube.com/watch?v=_8-ht2AKyH4',2);
addLesson($matStmt,$ch2,'Mảng và con trỏ – Mối quan hệ đặc biệt','Array và pointer trong C.','link','https://www.youtube.com/watch?v=zuegQmMdy8M',3);

$ch3 = addChapter($chapStmt,$pdo,3,'Chương 3: C++ – Lập trình hướng đối tượng','Class, Object, Inheritance trong C++.',3);
addLesson($matStmt,$ch3,'C++ vs C – Những điểm khác biệt chính','Giới thiệu C++ từ góc nhìn người đã biết C.','link','https://www.youtube.com/watch?v=18c3MTX0PK0',1);
addLesson($matStmt,$ch3,'Class và Object trong C++','Khai báo và sử dụng class trong C++.','link','https://www.youtube.com/watch?v=wN0x9eZLix4',2);
addLesson($matStmt,$ch3,'Kế thừa và đa hình trong C++','Inheritance, virtual function, polymorphism.','link','https://www.youtube.com/watch?v=X8nYM8wdNRE',3);
addLesson($matStmt,$ch3,'[Quiz] Con trỏ & OOP trong C++','Bài kiểm tra chương 2-3.','quiz',quizJson([
    ['question'=>'Con trỏ (pointer) trong C là gì?','options'=>['Một kiểu dữ liệu lưu số','Biến lưu địa chỉ bộ nhớ của biến khác','Một hàm đặc biệt','Không có khái niệm này trong C'],'answer'=>1],
    ['question'=>'Toán tử `*` khi đứng trước pointer dùng để:','options'=>['Nhân','Khai báo pointer','Dereference – lấy giá trị tại địa chỉ','Lấy địa chỉ'],'answer'=>2],
    ['question'=>'`malloc()` trong C dùng để:','options'=>['Giải phóng bộ nhớ','Cấp phát bộ nhớ động trên heap','Khai báo biến','In ra màn hình'],'answer'=>1],
    ['question'=>'Trong C++, constructor là:','options'=>['Hàm được gọi khi object bị xóa','Hàm có cùng tên với class, được gọi khi tạo object','Hàm static','Hàm trả về void'],'answer'=>1],
    ['question'=>'Virtual function trong C++ cho phép:','options'=>['Chạy nhanh hơn','Đa hình (Polymorphism) – gọi đúng phiên bản hàm của subclass','Ẩn dữ liệu','Tạo nhiều class cùng lúc'],'answer'=>1],
]),4);

// ============================================================
// KHÓA HỌC 4 – C# (ID=4)
// ============================================================
echo "🔵 C#...\n";
$ch = addChapter($chapStmt,$pdo,4,'Chương 1: Nền tảng C# & .NET','Cú pháp C#, kiểu dữ liệu, điều kiện, vòng lặp.',1);
addLesson($matStmt,$ch,'Tổng quan .NET và Visual Studio','Giới thiệu hệ sinh thái .NET.','link','https://www.youtube.com/watch?v=GhQdlIFylQ8',1);
addLesson($matStmt,$ch,'C# Basics – Syntax, Types, Variables','Cú pháp cơ bản C#.','link','https://www.youtube.com/watch?v=gfkTfcpWqAY',2);
addLesson($matStmt,$ch,'OOP trong C# – Class, Interface, Inheritance','4 tính chất OOP trong C#.','link','https://www.youtube.com/watch?v=pzSoC8kMqq0',3);
addLesson($matStmt,$ch,'LINQ – Truy vấn dữ liệu kiểu functional','Language Integrated Query trong C#.','link','https://www.youtube.com/watch?v=5l2qA3Pc83M',4);
addLesson($matStmt,$ch,'[Quiz] C# Cơ bản','Kiểm tra kiến thức C# cơ bản.','quiz',quizJson([
    ['question'=>'C# là ngôn ngữ lập trình được phát triển bởi:','options'=>['Google','Apple','Microsoft','Oracle'],'answer'=>2],
    ['question'=>'Trong C#, từ khóa `var` dùng để:','options'=>['Khai báo biến kiểu variant','Khai báo biến với type inference (tự suy ra kiểu)','Khai báo hằng số','Không hợp lệ trong C#'],'answer'=>1],
    ['question'=>'Interface trong C# khác Abstract Class ở điểm:','options'=>['Interface có thể có code implementation','Interface không thể có property','Interface không có constructor và mọi member đều abstract (mặc định)','Không có sự khác biệt'],'answer'=>2],
    ['question'=>'LINQ trong C# cho phép:','options'=>['Viết SQL thuần trong C#','Truy vấn dữ liệu từ nhiều nguồn khác nhau bằng cú pháp thống nhất','Kết nối database nhanh hơn','Thay thế Entity Framework'],'answer'=>1],
    ['question'=>'async/await trong C# dùng để:','options'=>['Tạo nhiều luồng song song','Lập trình bất đồng bộ mà không block main thread','Xử lý exception','Khai báo hằng số'],'answer'=>1],
]),5);

$ch2 = addChapter($chapStmt,$pdo,4,'Chương 2: ASP.NET Web API & Entity Framework','Backend development với C#.',2);
addLesson($matStmt,$ch2,'Xây dựng RESTful API với ASP.NET Core','Tạo Web API đầu tiên với ASP.NET.','link','https://www.youtube.com/watch?v=fmvcAzHpsk8',1);
addLesson($matStmt,$ch2,'Entity Framework Core – ORM cho .NET','Tương tác database với EF Core.','link','https://www.youtube.com/watch?v=wGQrwKuguKg',2);
addLesson($matStmt,$ch2,'[Quiz] ASP.NET & EF Core','Kiểm tra kiến thức Web API và EF Core.','quiz',quizJson([
    ['question'=>'ASP.NET Core là framework dùng để:','options'=>['Phát triển game','Xây dựng ứng dụng web và API','Lập trình mobile','Phân tích dữ liệu'],'answer'=>1],
    ['question'=>'Entity Framework Core là:','options'=>['Một ngôn ngữ truy vấn','ORM (Object-Relational Mapper) cho .NET','Thư viện UI','Framework test'],'answer'=>1],
    ['question'=>'Trong ASP.NET Core, [HttpGet] attribute dùng để:','options'=>['Đánh dấu controller','Map endpoint xử lý HTTP GET request','Validate dữ liệu','Khai báo dependency'],'answer'=>1],
    ['question'=>'Dependency Injection trong ASP.NET Core cho phép:','options'=>['Inject code vào database','Quản lý và cung cấp các service thông qua constructor','Tạo class tự động','Xóa object khỏi bộ nhớ'],'answer'=>1],
    ['question'=>'Migration trong EF Core dùng để:','options'=>['Di chuyển file','Đồng bộ schema database với model C#','Import dữ liệu CSV','Backup database'],'answer'=>1],
]),3);

// ============================================================
// KHÓA HỌC 5 – JAVA (ID=5)
// ============================================================
echo "☕ Java...\n";
$ch = addChapter($chapStmt,$pdo,5,'Chương 1: Java SE – Nền tảng vững chắc','Cú pháp Java, OOP, Collections, Exception.',1);
addLesson($matStmt,$ch,'Giới thiệu Java & JDK Setup','Cài đặt môi trường và Hello World.','link','https://www.youtube.com/watch?v=eIrMbAQSU34',1);
addLesson($matStmt,$ch,'Java OOP – Class, Object, 4 tính chất','Encapsulation, Inheritance, Polymorphism, Abstraction.','link','https://www.youtube.com/watch?v=pTB0EiLXUC8',2);
addLesson($matStmt,$ch,'Java Collections Framework','List, Map, Set và cách sử dụng.','link','https://www.youtube.com/watch?v=GdAon80-0KA',3);
addLesson($matStmt,$ch,'Exception Handling trong Java','try-catch-finally, custom exception.','link','https://www.youtube.com/watch?v=1XAfapkBQjk',4);
addLesson($matStmt,$ch,'[Quiz] Java SE Fundamentals','Kiểm tra Java cơ bản.','quiz',quizJson([
    ['question'=>'Java là ngôn ngữ:','options'=>['Compiled, không dùng VM','Interpreted, chạy thẳng trên OS','Compiled sang bytecode, chạy trên JVM','Script language'],'answer'=>2],
    ['question'=>'Interface trong Java 8+ có thể có:','options'=>['Chỉ abstract methods','Default methods và static methods','Constructor','Private fields'],'answer'=>1],
    ['question'=>'`ArrayList` trong Java khác `LinkedList` ở:','options'=>['ArrayList dùng linked nodes, LinkedList dùng array','ArrayList dùng dynamic array (O(1) get), LinkedList dùng nodes (O(n) get)','Không có sự khác biệt','ArrayList không hỗ trợ add'],'answer'=>1],
    ['question'=>'Từ khóa `final` trong Java khi áp dụng cho class nghĩa là:','options'=>['Class chạy nhanh hơn','Class không thể bị extend (kế thừa)','Class là singleton','Class không có constructor'],'answer'=>1],
    ['question'=>'Checked Exception trong Java phải được:','options'=>['Bỏ qua','Khai báo với throws hoặc bắt bằng try-catch','Chuyển thành RuntimeException','Không xử lý'],'answer'=>1],
]),5);

$ch2 = addChapter($chapStmt,$pdo,5,'Chương 2: Spring Boot – Backend thực chiến','REST API, JPA, Security.',2);
addLesson($matStmt,$ch2,'Spring Boot – Tạo project đầu tiên','Khởi tạo Spring Boot với Spring Initializr.','link','https://www.youtube.com/watch?v=9SGDpanrc8U',1);
addLesson($matStmt,$ch2,'Spring Data JPA – Repository Pattern','CRUD với Spring Data JPA.','link','https://www.youtube.com/watch?v=8SGI_XS5OPw',2);
addLesson($matStmt,$ch2,'Spring Security – JWT Authentication','Bảo mật API với JWT Token.','link','https://www.youtube.com/watch?v=KxqlJblhzfI',3);
addLesson($matStmt,$ch2,'[Quiz] Spring Boot','Kiểm tra Spring Boot.','quiz',quizJson([
    ['question'=>'Annotation @RestController trong Spring Boot kết hợp:','options'=>['@Service + @Repository','@Controller + @ResponseBody','@Component + @Autowired','@Bean + @Configuration'],'answer'=>1],
    ['question'=>'@Autowired trong Spring dùng để:','options'=>['Tạo database schema','Dependency Injection tự động','Định nghĩa route','Validate input'],'answer'=>1],
    ['question'=>'JPA (Java Persistence API) dùng để:','options'=>['Xây dựng UI','Gọi REST API','Map Java objects với database tables','Xử lý concurrency'],'answer'=>2],
    ['question'=>'JWT (JSON Web Token) trong Spring Security dùng để:','options'=>['Lưu session trên server','Xác thực stateless – token chứa thông tin user','Mã hóa database','Tạo cookie'],'answer'=>1],
    ['question'=>'@Transactional annotation trong Spring đảm bảo:','options'=>['Method chạy nhanh hơn','Các thao tác DB trong method được wrapped trong transaction','Method được cache','Method chỉ chạy 1 lần'],'answer'=>1],
]),4);

// ============================================================
// KHÓA HỌC 6 – JAVASCRIPT (ID=6)
// ============================================================
echo "🟨 JavaScript...\n";
$ch = addChapter($chapStmt,$pdo,6,'Chương 1: JavaScript Cơ bản','Syntax, Variables, Functions, DOM.',1);
addLesson($matStmt,$ch,'Giới thiệu JavaScript & môi trường làm việc','Vai trò của JS trong web, setup VSCode.','link','https://www.youtube.com/watch?v=hdI2bqOjy3c',1);
addLesson($matStmt,$ch,'Variables – var, let, const','Sự khác biệt và khi nào dùng.','link','https://www.youtube.com/watch?v=9WIJQDvt4Us',2);
addLesson($matStmt,$ch,'Functions – Declaration, Expression, Arrow','Các cách khai báo hàm trong JS.','link','https://www.youtube.com/watch?v=gigtS73W5HA',3);
addLesson($matStmt,$ch,'DOM Manipulation – Tương tác HTML','querySelector, addEventListener, innerHTML.','link','https://www.youtube.com/watch?v=5fb2aPlgoys',4);
addLesson($matStmt,$ch,'[Quiz] JavaScript Cơ bản','Kiểm tra kiến thức JS cơ bản.','quiz',quizJson([
    ['question'=>'Sự khác biệt giữa `==` và `===` trong JavaScript?','options'=>['Không có sự khác biệt','== so sánh giá trị, === so sánh giá trị VÀ kiểu dữ liệu','=== chậm hơn ==','== chỉ dùng cho số'],'answer'=>1],
    ['question'=>'`typeof null` trong JavaScript trả về:','options'=>['null','undefined','object','boolean'],'answer'=>2],
    ['question'=>'Arrow function `(a, b) => a + b` là cách viết tắt của:','options'=>['function(a,b) { return a + b; }','function add(a,b) {}','var f = a + b','Không tương đương với function thông thường'],'answer'=>0],
    ['question'=>'Event bubbling trong JavaScript là:','options'=>['Event chỉ kích hoạt trên element được click','Event lan truyền từ element con lên element cha','Event chỉ hoạt động với click','Không có khái niệm này'],'answer'=>1],
    ['question'=>'`localStorage.setItem(key, value)` lưu dữ liệu ở đâu?','options'=>['Server','Session memory (mất khi đóng tab)','Browser storage (giữ nguyên sau khi đóng trình duyệt)','Cookie'],'answer'=>2],
]),5);

$ch2 = addChapter($chapStmt,$pdo,6,'Chương 2: JavaScript Nâng cao – ES6+ & Async','Promises, Async/Await, Modules.',2);
addLesson($matStmt,$ch2,'ES6+ – Destructuring, Spread, Template Literals','Các tính năng modern JavaScript.','link','https://www.youtube.com/watch?v=NCwa_xi0Uuc',1);
addLesson($matStmt,$ch2,'Promise và Fetch API','Gọi API và xử lý bất đồng bộ.','link','https://www.youtube.com/watch?v=PoRJizFvM7s',2);
addLesson($matStmt,$ch2,'Async/Await – Cú pháp bất đồng bộ hiện đại','Viết code async dễ đọc hơn.','link','https://www.youtube.com/watch?v=V_Kr9OSfDeU',3);
addLesson($matStmt,$ch2,'[Quiz] ES6+ & Async JavaScript','Kiểm tra JavaScript nâng cao.','quiz',quizJson([
    ['question'=>'Destructuring assignment `const {a, b} = obj` dùng để:','options'=>['Xóa thuộc tính a và b','Trích xuất giá trị từ object vào biến riêng','Tạo object mới','Copy object'],'answer'=>1],
    ['question'=>'`Promise` trong JavaScript dùng để:','options'=>['Lưu trữ dữ liệu','Xử lý các tác vụ bất đồng bộ','Tạo class','Validate form'],'answer'=>1],
    ['question'=>'`async/await` là cú pháp sugar cho:','options'=>['setTimeout','Promise chains (.then/.catch)','Callback functions','Event listeners'],'answer'=>1],
    ['question'=>'Spread operator `...` trong JS dùng để:','options'=>['Xóa phần tử','Trải phần tử của array/object vào ngữ cảnh khác','So sánh array','Đảo ngược array'],'answer'=>1],
    ['question'=>'`fetch()` trả về:','options'=>['Dữ liệu ngay lập tức','Response object','Promise<Response>','String'],'answer'=>2],
]),4);

// ============================================================
// KHÓA HỌC 7 – OOP (ID=7)
// ============================================================
echo "🧠 OOP...\n";
$ch = addChapter($chapStmt,$pdo,7,'Chương 1: 4 Tính chất cốt lõi của OOP','Encapsulation, Inheritance, Polymorphism, Abstraction.',1);
addLesson($matStmt,$ch,'OOP là gì? Tại sao cần OOP?','Lý do ra đời và lợi ích của OOP.','link','https://www.youtube.com/watch?v=pTB0EiLXUC8',1);
addLesson($matStmt,$ch,'Encapsulation – Đóng gói dữ liệu','Getter, Setter và access modifier.','link','https://www.youtube.com/watch?v=rQlMtztiAoA',2);
addLesson($matStmt,$ch,'Inheritance – Kế thừa','Tái sử dụng code qua kế thừa.','link','https://www.youtube.com/watch?v=9TlHvipP5yA',3);
addLesson($matStmt,$ch,'Polymorphism – Đa hình','Method overriding và overloading.','link','https://www.youtube.com/watch?v=jhDUxynEQRI',4);
addLesson($matStmt,$ch,'Abstraction – Trừu tượng hóa','Abstract class và Interface.','link','https://www.youtube.com/watch?v=HvPlEJ3LHgE',5);
addLesson($matStmt,$ch,'[Quiz] 4 Tính chất OOP','Kiểm tra hiểu biết về OOP.','quiz',quizJson([
    ['question'=>'Encapsulation (Đóng gói) trong OOP có nghĩa là:','options'=>['Xóa dữ liệu không cần thiết','Giấu chi tiết implementation, chỉ expose interface cần thiết','Kế thừa từ class khác','Tạo nhiều phiên bản của hàm'],'answer'=>1],
    ['question'=>'Inheritance cho phép:','options'=>['Chạy nhiều class cùng lúc','Class con kế thừa thuộc tính và phương thức từ class cha','Giấu dữ liệu','Tạo nhiều object cùng lúc'],'answer'=>1],
    ['question'=>'Method overriding (ghi đè) là:','options'=>['Tạo nhiều hàm cùng tên trong cùng 1 class (khác tham số)','Subclass định nghĩa lại hàm của superclass với cùng signature','Xóa method của class cha','Tạo method mới'],'answer'=>1],
    ['question'=>'Abstract class khác Interface ở điểm:','options'=>['Abstract class có thể có code implementation và state','Interface nhanh hơn abstract class','Abstract class không thể có method','Interface có thể có constructor'],'answer'=>0],
    ['question'=>'Polymorphism (Đa hình) cho phép:','options'=>['Một object thuộc nhiều type','Gọi cùng một method nhưng hành vi khác nhau tùy object thực tế','Tạo nhiều class cùng tên','Xóa các method không dùng'],'answer'=>1],
]),6);

$ch2 = addChapter($chapStmt,$pdo,7,'Chương 2: Design Patterns cơ bản','Singleton, Factory, Observer.',2);
addLesson($matStmt,$ch2,'Giới thiệu Design Patterns – Tại sao cần?','GoF patterns và tại sao lập trình viên cần biết.','link','https://www.youtube.com/watch?v=tv-_1er1mWI',1);
addLesson($matStmt,$ch2,'Singleton Pattern','Đảm bảo class chỉ có 1 instance.','link','https://www.youtube.com/watch?v=hUE_j6q0LTQ',2);
addLesson($matStmt,$ch2,'Factory Pattern','Tạo object mà không biết class cụ thể.','link','https://www.youtube.com/watch?v=EcFVTgRHJLM',3);
addLesson($matStmt,$ch2,'[Quiz] Design Patterns','Kiểm tra kiến thức về Design Pattern.','quiz',quizJson([
    ['question'=>'Singleton Pattern đảm bảo:','options'=>['Class có thể tạo vô hạn object','Chỉ có đúng 1 instance của class trong toàn ứng dụng','Class không thể kế thừa','Object được tạo nhanh hơn'],'answer'=>1],
    ['question'=>'Factory Pattern thuộc nhóm nào?','options'=>['Structural Pattern','Behavioral Pattern','Creational Pattern','Architectural Pattern'],'answer'=>2],
    ['question'=>'Observer Pattern phù hợp khi nào?','options'=>['Khi cần tạo 1 object','Khi nhiều object cần được thông báo khi state của 1 object thay đổi','Khi cần giấu dữ liệu','Khi cần tăng performance'],'answer'=>1],
    ['question'=>'SOLID là:','options'=>['Một design pattern','5 nguyên lý thiết kế hướng đối tượng','Một framework','Một ngôn ngữ lập trình'],'answer'=>1],
    ['question'=>'"Ưu tiên composition hơn inheritance" (Composition over Inheritance) có nghĩa là:','options'=>['Không nên dùng inheritance','Nên tạo quan hệ has-a thay vì is-a khi có thể, linh hoạt hơn','Composition luôn nhanh hơn','Không có ý nghĩa thực tế'],'answer'=>1],
]),4);

// ============================================================
// KHÓA HỌC 8 – PHP (ID=8)
// ============================================================
echo "🐘 PHP...\n";
$ch = addChapter($chapStmt,$pdo,8,'Chương 1: PHP Cơ bản','Syntax, biến, hàm, array, form.',1);
addLesson($matStmt,$ch,'PHP là gì? Cài đặt XAMPP','Giới thiệu PHP và môi trường XAMPP.','link','https://www.youtube.com/watch?v=OK_JCtrrv-c',1);
addLesson($matStmt,$ch,'Biến, kiểu dữ liệu và toán tử PHP','Cú pháp PHP cơ bản.','link','https://www.youtube.com/watch?v=a7_WFUlFS94',2);
addLesson($matStmt,$ch,'Mảng (Array) trong PHP','Indexed, Associative và Multidimensional Array.','link','https://www.youtube.com/watch?v=1Lm7i2F6nZY',3);
addLesson($matStmt,$ch,'Xử lý Form HTML với PHP','$_GET, $_POST và validation.','link','https://www.youtube.com/watch?v=n0V37jL3f6c',4);
addLesson($matStmt,$ch,'[Quiz] PHP Cơ bản','Kiểm tra kiến thức PHP cơ bản.','quiz',quizJson([
    ['question'=>'PHP là viết tắt của:','options'=>['Personal Home Page','PHP: Hypertext Preprocessor','Public HTML Page','Programmatic HTML Processing'],'answer'=>1],
    ['question'=>'Trong PHP, biến được khai báo bằng:','options'=>['var name','let name','$name','dim name'],'answer'=>2],
    ['question'=>'Hàm nào để in ra nội dung trong PHP?','options'=>['print_out()','console.log()','echo hoặc print','display()'],'answer'=>2],
    ['question'=>'Superglobal `$_POST` chứa:','options'=>['Dữ liệu từ URL query string','Dữ liệu từ HTTP POST request (thường từ form)','Session data','Cookie data'],'answer'=>1],
    ['question'=>'Hàm `count()` trong PHP dùng để:','options'=>['Đếm số ký tự trong chuỗi','Đếm số phần tử trong array','Tính tổng các số','Đếm số từ'],'answer'=>1],
]),5);

$ch2 = addChapter($chapStmt,$pdo,8,'Chương 2: PHP & MySQL – Xây dựng Website động','PDO, CRUD, Session, Security.',2);
addLesson($matStmt,$ch2,'Kết nối MySQL với PHP PDO','Cài đặt kết nối an toàn với PDO.','link','https://www.youtube.com/watch?v=vXrpFBMaF5E',1);
addLesson($matStmt,$ch2,'CRUD với PDO – Prepared Statements','Create, Read, Update, Delete an toàn.','link','https://www.youtube.com/watch?v=7_Mzjj-BDLk',2);
addLesson($matStmt,$ch2,'PHP Session – Đăng nhập và phân quyền','Xây dựng hệ thống login với Session.','link','https://www.youtube.com/watch?v=YKwOzSHR9Fw',3);
addLesson($matStmt,$ch2,'Bảo mật PHP – SQL Injection, XSS, CSRF','Các mối nguy và cách phòng chống.','link','https://www.youtube.com/watch?v=yMbkSxHVxj4',4);
addLesson($matStmt,$ch2,'[Quiz] PHP & MySQL nâng cao','Kiểm tra PHP/MySQL nâng cao.','quiz',quizJson([
    ['question'=>'PDO Prepared Statement giúp ngăn chặn:','options'=>['XSS (Cross-site Scripting)','SQL Injection','CSRF attack','Brute force'],'answer'=>1],
    ['question'=>'`password_hash()` trong PHP dùng thuật toán mặc định:','options'=>['MD5','SHA1','bcrypt (PASSWORD_BCRYPT)','SHA256'],'answer'=>2],
    ['question'=>'PHP Session lưu dữ liệu ở đâu?','options'=>['Browser localStorage','Cookie trên máy client','Server (file hoặc memory)','Database'],'answer'=>2],
    ['question'=>'CSRF (Cross-Site Request Forgery) được ngăn chặn bằng:','options'=>['Mã hóa password','Token ngẫu nhiên trong form và xác thực server-side','Dùng HTTPS','Validate email'],'answer'=>1],
    ['question'=>'Hàm `htmlspecialchars()` trong PHP dùng để:','options'=>['Mã hóa password','Convert ký tự đặc biệt HTML để ngăn XSS','Validate email','Encode URL'],'answer'=>1],
]),5);

$ch3 = addChapter($chapStmt,$pdo,8,'Chương 3: MVC Pattern & Dự án thực tế','Áp dụng MVC vào website hoàn chỉnh.',3);
addLesson($matStmt,$ch3,'MVC Pattern trong PHP','Model-View-Controller là gì và cách áp dụng.','link','https://www.youtube.com/watch?v=DUg2SWWK18I',1);
addLesson($matStmt,$ch3,'Xây dựng Router đơn giản','URL routing không cần framework.','link','https://www.youtube.com/watch?v=qVB8OBFDves',2);
addLesson($matStmt,$ch3,'Deploy PHP app lên Hosting','Đưa website PHP lên internet.','link','https://www.youtube.com/watch?v=goiRgPp4cic',3);

// ============================================================
// KHÓA HỌC 9 – PYTHON (ID=9)
// ============================================================
echo "🐍 Python...\n";
$ch = addChapter($chapStmt,$pdo,9,'Chương 1: Python Fundamentals','Cú pháp Python cơ bản, kiểu dữ liệu, hàm.',1);
addLesson($matStmt,$ch,'Python là gì? Tại sao nên học?','Giới thiệu Python và cài đặt môi trường.','link','https://www.youtube.com/watch?v=_uQrJ0TkZlc',1);
addLesson($matStmt,$ch,'Variables, Data Types và Operators','Kiểu dữ liệu cơ bản trong Python.','link','https://www.youtube.com/watch?v=khKv-8q7YmY',2);
addLesson($matStmt,$ch,'Strings – Thao tác và các phương thức','Xử lý chuỗi trong Python.','link','https://www.youtube.com/watch?v=k9TUPpGqYTo',3);
addLesson($matStmt,$ch,'Lists, Tuples, Dictionaries & Sets','Cấu trúc dữ liệu Python.','link','https://www.youtube.com/watch?v=W8KRzm-HUcc',4);
addLesson($matStmt,$ch,'Functions và Lambda','Khai báo và sử dụng hàm.','link','https://www.youtube.com/watch?v=9Os0o3wzS_I',5);
addLesson($matStmt,$ch,'[Quiz] Python Fundamentals','Kiểm tra Python cơ bản.','quiz',quizJson([
    ['question'=>'Python sử dụng cơ chế nào để phân khối lệnh thay vì dấu ngoặc nhọn?','options'=>['Dấu ngoặc đơn','Dấu chấm phẩy','Indentation (thụt lề)','Dấu ngoặc vuông'],'answer'=>2],
    ['question'=>'Kiểu dữ liệu nào trong Python là IMMUTABLE (không thể thay đổi)?','options'=>['list','dict','set','tuple'],'answer'=>3],
    ['question'=>'List comprehension `[x**2 for x in range(5)]` tạo ra:','options'=>['[0,1,2,3,4]','[0,1,4,9,16]','[1,4,9,16,25]','Error'],'answer'=>1],
    ['question'=>'`def greet(name="World"):` trong Python cho phép:','options'=>['Gọi hàm không cần truyền name','Bắt buộc truyền name','Tạo hàm không có tham số','Khai báo biến toàn cục'],'answer'=>0],
    ['question'=>'Module `random` trong Python được import bằng:','options'=>['#include random','require random','import random','using random'],'answer'=>2],
]),6);

$ch2 = addChapter($chapStmt,$pdo,9,'Chương 2: Python Thực chiến – File, Web Scraping','OOP, File I/O, Web Scraping.',2);
addLesson($matStmt,$ch2,'OOP trong Python','Class, object, kế thừa trong Python.','link','https://www.youtube.com/watch?v=JeznW_7DlB0',1);
addLesson($matStmt,$ch2,'File I/O – Đọc và ghi file','Làm việc với file text và CSV.','link','https://www.youtube.com/watch?v=Uh2ebFW8OYM',2);
addLesson($matStmt,$ch2,'Web Scraping với BeautifulSoup','Thu thập dữ liệu web tự động.','link','https://www.youtube.com/watch?v=XVv6mJpFOb0',3);
addLesson($matStmt,$ch2,'[Quiz] Python Nâng cao','Kiểm tra Python thực chiến.','quiz',quizJson([
    ['question'=>'`__init__` trong Python class là:','options'=>['Hàm để xóa object','Constructor – chạy khi tạo object mới','Static method','Class method'],'answer'=>1],
    ['question'=>'`with open(file) as f:` trong Python đảm bảo:','options'=>['File được mở nhanh hơn','File tự động đóng sau khi ra khỏi block','File được lock','File không thể ghi'],'answer'=>1],
    ['question'=>'`requests.get(url)` trong Python dùng để:','options'=>['Tạo web server','Gửi HTTP GET request đến URL','Download file','Parse HTML'],'answer'=>1],
    ['question'=>'BeautifulSoup được dùng để:','options'=>['Xây dựng web app','Parse và extract dữ liệu từ HTML/XML','Gửi email','Kết nối database'],'answer'=>1],
    ['question'=>'`enumerate()` trong Python dùng để:','options'=>['Đếm số phần tử','Lặp qua list kèm theo index','Sort list','Lọc list'],'answer'=>1],
]),4);

// ============================================================
// KHÓA HỌC 10 – TYPESCRIPT (ID=10)
// ============================================================
echo "🔷 TypeScript...\n";
$ch = addChapter($chapStmt,$pdo,10,'Chương 1: TypeScript Basics – Type System','Types, Interfaces, Generics.',1);
addLesson($matStmt,$ch,'TypeScript là gì? Tại sao cần TS?','Lợi ích của TypeScript so với JavaScript.','link','https://www.youtube.com/watch?v=d56mG7DezGs',1);
addLesson($matStmt,$ch,'Types & Type Annotations','Khai báo kiểu dữ liệu trong TypeScript.','link','https://www.youtube.com/watch?v=ahCwqrYpIuM',2);
addLesson($matStmt,$ch,'Interfaces & Type Aliases','Định nghĩa hợp đồng dữ liệu.','link','https://www.youtube.com/watch?v=LKVHFHJsiO0',3);
addLesson($matStmt,$ch,'Generics trong TypeScript','Viết code tái sử dụng với Generics.','link','https://www.youtube.com/watch?v=nViEqpgwxHE',4);
addLesson($matStmt,$ch,'[Quiz] TypeScript Basics','Kiểm tra kiến thức TypeScript cơ bản.','quiz',quizJson([
    ['question'=>'TypeScript là:','options'=>['Ngôn ngữ thay thế hoàn toàn JavaScript','Superset của JavaScript với static typing','Ngôn ngữ compile sang WebAssembly','Framework của Microsoft'],'answer'=>1],
    ['question'=>'Trong TypeScript, `any` type có nghĩa:','options'=>['Chỉ chấp nhận string','Vô hiệu hóa type checking cho biến đó','Chỉ chấp nhận number','Tạo union type'],'answer'=>1],
    ['question'=>'Interface trong TypeScript khác Type Alias ở điểm:','options'=>['Interface nhanh hơn','Interface có thể được extend và implement, có thể merge declarations','Type alias không thể dùng cho object','Không có sự khác biệt'],'answer'=>1],
    ['question'=>'Generic `function identity<T>(arg: T): T` có nghĩa:','options'=>['Hàm chỉ nhận string','Hàm nhận và trả về cùng kiểu T (type-safe cho mọi kiểu)','Hàm trả về undefined','Hàm bất đồng bộ'],'answer'=>1],
    ['question'=>'`readonly` modifier trong TypeScript dùng để:','options'=>['Chỉ đọc từ file','Ngăn thay đổi property sau khi khởi tạo','Làm method chạy nhanh hơn','Export property'],'answer'=>1],
]),5);

$ch2 = addChapter($chapStmt,$pdo,10,'Chương 2: TypeScript với React & Node.js','Áp dụng TypeScript vào dự án thực tế.',2);
addLesson($matStmt,$ch2,'TypeScript với React – Typed Components','Props, State typing trong React.','link','https://www.youtube.com/watch?v=FJDVKeh7RJI',1);
addLesson($matStmt,$ch2,'TypeScript với Node.js & Express','Xây dựng API server có kiểu dữ liệu.','link','https://www.youtube.com/watch?v=H91aqUHn8sE',2);
addLesson($matStmt,$ch2,'Advanced Types – Union, Intersection, Utility Types','Kỹ thuật TypeScript nâng cao.','link','https://www.youtube.com/watch?v=Pl5gCRHPNqI',3);
addLesson($matStmt,$ch2,'[Quiz] TypeScript Nâng cao','Kiểm tra TypeScript thực chiến.','quiz',quizJson([
    ['question'=>'`Union Type` trong TypeScript ký hiệu bằng:','options'=>['&','|','+','?'],'answer'=>1],
    ['question'=>'`Partial<T>` Utility Type trong TypeScript làm gì?','options'=>['Tạo partial class','Làm tất cả properties của T trở thành optional','Xóa properties của T','Clone type T'],'answer'=>1],
    ['question'=>'`keyof` operator trong TypeScript dùng để:','options'=>['Lấy số lượng key','Tạo union type của tất cả key của một type','Xóa key','Rename key'],'answer'=>1],
    ['question'=>'Decorator trong TypeScript là:','options'=>['Một kiểu dữ liệu','Function dùng để modify class, method, hoặc property','Import statement','Không tồn tại trong TypeScript'],'answer'=>1],
    ['question'=>'`strict: true` trong tsconfig.json bật:','options'=>['Chạy nhanh hơn','Tất cả strict type checking options (strictNullChecks, noImplicitAny...)','Tắt lỗi','Chỉ cho phép dùng strict mode'],'answer'=>1],
]),4);

// TỔNG KẾT
$totalChapters  = $pdo->query("SELECT COUNT(*) FROM chapters")->fetchColumn();
$totalMaterials = $pdo->query("SELECT COUNT(*) FROM materials")->fetchColumn();
$totalQuizzes   = $pdo->query("SELECT COUNT(*) FROM materials WHERE type='quiz'")->fetchColumn();
$totalLessons   = $pdo->query("SELECT COUNT(*) FROM materials WHERE type='link'")->fetchColumn();

echo "\n============================================\n";
echo "🎉 CURRICULUM SEEDING HOÀN TẤT!\n";
echo "============================================\n";
echo "  📂 Chương (Chapters) : $totalChapters\n";
echo "  🎥 Bài giảng (Videos): $totalLessons\n";
echo "  📝 Bài trắc nghiệm   : $totalQuizzes\n";
echo "  📋 Tổng materials    : $totalMaterials\n";
echo "============================================\n";

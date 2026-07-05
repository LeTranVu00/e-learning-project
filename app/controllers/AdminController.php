<?php
// File: app/controllers/AdminController.php

class AdminController {
    
    // Hàm khởi tạo: Chạy đầu tiên mỗi khi gọi AdminController
    public function __construct() {
        // Cửa ải bảo vệ: Phải đăng nhập VÀ role phải là 'admin'
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = "Cảnh báo: Bạn không có quyền truy cập khu vực Quản trị!";
            header('Location: ?action=home');
            exit();
        }
    }

    // Hiển thị trang Tổng quan (Dashboard)
    public function dashboard() {
        require_once __DIR__ . '/../config/Database.php';
        $db = (new Database())->getConnection();

        // Lấy số liệu thật từ DB
        $total_courses    = $db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        $total_users      = $db->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
        $total_enrollments = $db->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();

        // Tổng doanh thu
        $total_revenue = $db->query("SELECT SUM(c.price) FROM enrollments e JOIN courses c ON e.course_id = c.id")->fetchColumn() ?: 0;

        // Thống kê "Hôm nay"
        $today_revenue = $db->query("SELECT SUM(c.price) FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE DATE(e.enrolled_at) = CURRENT_DATE()")->fetchColumn() ?: 0;
        $today_users = $db->query("SELECT COUNT(*) FROM users WHERE role = 'student' AND DATE(created_at) = CURRENT_DATE()")->fetchColumn() ?: 0;

        // Giao dịch mới nhất (Latest Transactions)
        $latest_transactions = $db->query("
            SELECT u.fullname, u.avatar, c.title, c.price, e.enrolled_at 
            FROM enrollments e 
            JOIN users u ON e.user_id = u.id 
            JOIN courses c ON e.course_id = c.id 
            ORDER BY e.enrolled_at DESC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Hoạt động gần đây (Recent Activities)
        $recent_users = $db->query("SELECT id, fullname, created_at, 'user' as type FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $recent_enrolls = $db->query("
            SELECT e.id, u.fullname, c.title as extra_info, e.enrolled_at as created_at, 'enrollment' as type 
            FROM enrollments e 
            JOIN users u ON e.user_id = u.id 
            JOIN courses c ON e.course_id = c.id 
            ORDER BY e.enrolled_at DESC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        $recent_comments = $db->query("
            SELECT c.id, u.fullname, c.content as extra_info, c.created_at, 'comment' as type 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            ORDER BY c.created_at DESC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $recent_activities = array_merge($recent_users, $recent_enrolls, $recent_comments);
        usort($recent_activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        $recent_activities = array_slice($recent_activities, 0, 7);

        // Dữ liệu biểu đồ Doanh thu & Học viên theo tháng (Năm hiện tại)
        $monthly_stats = $db->query("
            SELECT MONTH(e.enrolled_at) as month, SUM(c.price) as revenue, COUNT(e.id) as enrollments
            FROM enrollments e 
            JOIN courses c ON e.course_id = c.id
            WHERE YEAR(e.enrolled_at) = YEAR(CURRENT_DATE())
            GROUP BY MONTH(e.enrolled_at)
            ORDER BY month ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Khởi tạo mảng 12 tháng với giá trị 0
        $chart_months = ['Th 1', 'Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7', 'Th 8', 'Th 9', 'Th 10', 'Th 11', 'Th 12'];
        $chart_revenue = array_fill(0, 12, 0);
        $chart_enrollments = array_fill(0, 12, 0);

        foreach ($monthly_stats as $stat) {
            $idx = $stat['month'] - 1;
            $chart_revenue[$idx] = (int) $stat['revenue'];
            $chart_enrollments[$idx] = (int) $stat['enrollments'];
        }

        // Dữ liệu biểu đồ Khóa học nổi bật (Top 5)
        $top_courses = $db->query("
            SELECT c.title, COUNT(e.id) as count
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            GROUP BY c.id
            ORDER BY count DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $chart_course_labels = [];
        $chart_course_series = [];
        foreach ($top_courses as $tc) {
            $chart_course_labels[] = $tc['title'];
            $chart_course_series[] = (int) $tc['count'];
        }

        $search = $_GET['search'] ?? '';
        $sort   = $_GET['sort'] ?? 'latest';
        $query  = "SELECT * FROM courses WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (title LIKE ? OR instructor LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($sort === 'oldest') {
            $query .= " ORDER BY id ASC";
        } elseif ($sort === 'price_high') {
            $query .= " ORDER BY price DESC";
        } elseif ($sort === 'price_low') {
            $query .= " ORDER BY price ASC";
        } else {
            $query .= " ORDER BY id DESC"; // latest
        }
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../../views/admin/dashboard.php';
    }

    // Hàm hiển thị Form thêm khóa học mới
    public function createCourse() {
        // Phải gọi kèm header/footer riêng của Admin (nếu có), 
        // hoặc ở đây mình nhúng thẳng vào file giao diện cho nhanh gọn.
        require_once __DIR__ . '/../../views/admin/create_course.php';
    }

    // Hàm xử lý lưu khóa học mới (Có upload ảnh)
    public function storeCourse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title          = $_POST['title'];
            $price          = isset($_POST['price']) ? intval($_POST['price']) : 0;
            $original_price = isset($_POST['original_price']) ? intval($_POST['original_price']) : 0;
            $description    = $_POST['description'];
            // BUG FIX: Khai báo trước khối if upload để không bị undefined khi không có file
            $benefits       = $_POST['benefits'] ?? '';
            $requirements   = $_POST['requirements'] ?? '';
            $instructor     = $_SESSION['user_name'] ?? 'Admin';
            $level          = $_POST['level']          ?? 'Sơ cấp';
            $duration_hours = isset($_POST['duration_hours']) ? intval($_POST['duration_hours']) : 0;
            $total_lessons  = isset($_POST['total_lessons'])  ? intval($_POST['total_lessons'])  : 0;
            $language       = $_POST['language']       ?? 'Tiếng Việt';
            // Card sidebar fields
            $start_date     = !empty($_POST['start_date'])    ? $_POST['start_date']    : null;
            $schedule       = $_POST['schedule']       ?? null;
            $study_time     = $_POST['study_time']     ?? null;
            $contact_phone  = $_POST['contact_phone']  ?? null;
            $thumbnail_path = '';

            // XỬ LÝ UPLOAD ẢNH
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                // Thư mục lưu ảnh
                $upload_dir = __DIR__ . '/../../public/uploads/courses/';
                
                // Lấy đuôi file (ví dụ: .jpg, .png)
                $file_extension = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                // Đổi tên file thành chuỗi ngẫu nhiên + thời gian để không bao giờ bị trùng
                $new_file_name = 'course_' . time() . '_' . uniqid() . '.' . $file_extension;
                
                // Đường dẫn tuyệt đối để move file vào ổ cứng
                $target_file = $upload_dir . $new_file_name;

                // Thực hiện di chuyển file từ bộ nhớ tạm vào thư mục
                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file)) {
                    // Đường dẫn tương đối từ public/index.php
                    $thumbnail_path = 'uploads/courses/' . $new_file_name;
                }
            }

            // LƯU VÀO DATABASE
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Course.php';
            $db = (new Database())->getConnection();
            $courseModel = new Course($db);
            $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

            if ($courseModel->createCourse($title, $description, $thumbnail_path, $benefits, $requirements, $price, $original_price, $instructor, $level, $duration_hours, $total_lessons, $language, $start_date, $schedule, $study_time, $contact_phone, $category_id)) {
                $isSuccess = true;
                $message = "Tuyệt vời! Khóa học đã được tạo thành công.";
            } else {
                $isSuccess = false;
                $message = "Xin lỗi! Có lỗi xảy ra khi tạo khóa học.";
            }

            // Xử lý Request từ AJAX / Fetch
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => $isSuccess, 'message' => $message]);
                exit();
            }

            // Request thông thường
            if ($isSuccess) {
                $_SESSION['success'] = $message;
            } else {
                $_SESSION['error'] = $message;
            }

            // Quay lại trang Quản lý khóa học
            header('Location: ?action=admin_manage_courses');
            exit();
        }
    }

    // 2. Hàm hiển thị trang Quản lý nội dung (Chương & Bài) của 1 khóa học cụ thể
    public function manageContent() {
        if (!isset($_GET['id'])) { header('Location: ?action=admin_dashboard'); exit; }
        $course_id = $_GET['id'];
        
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/Curriculum.php';
        $db = (new Database())->getConnection();
        
        // Lấy thông tin khóa học (để hiển thị tên)
        $stmt = $db->prepare("SELECT id, title FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Lấy cấu trúc bài giảng hiện tại (Giống y hệt luồng Học viên lúc nãy)
        $curriculumModel = new Curriculum($db);
        $curriculum = $curriculumModel->getCourseCurriculum($course_id);
        
        require_once __DIR__ . '/../../views/admin/manage_content.php';
    }

    // 3. Hàm xử lý lưu Chương mới
    public function storeChapter() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            // BUG FIX: Thêm order_num để có thể sắp xếp chương học
            $stmt = $db->prepare("INSERT INTO chapters (course_id, title, description, order_num) VALUES (?, ?, ?, ?)");
            $order_num = $_POST['order_num'] ?? 0;
            $description = $_POST['description'] ?? null;
            $stmt->execute([$_POST['course_id'], $_POST['title'], $description, $order_num]);
            
            $_SESSION['success'] = "Đã thêm Chương học mới!";
            header('Location: ?action=admin_manage_content&id=' . $_POST['course_id']);
            exit();
        }
    }

    // 4. Hàm xử lý lưu Tài liệu/Bài giảng mới
    public function storeMaterial() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Kiểm tra xem POST request có bị huỷ do vượt quá giới hạn dung lượng không
            if (empty($_POST)) {
                $_SESSION['error'] = "File tải lên quá lớn! Vui lòng chọn file nhỏ hơn hoặc cấu hình lại server.";
                header('Location: ?action=admin_manage_courses');
                exit();
            }

            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            
            $chapter_id = $_POST['chapter_id'];
            $course_id = $_POST['course_id'];
            $description = !empty(trim($_POST['description'] ?? '')) ? trim($_POST['description']) : null;
            $stmt = $db->prepare("INSERT INTO materials (chapter_id, title, type, content, description) VALUES (?, ?, ?, ?, ?)");
            $addedCount = 0;

            // Xử lý upload file hàng loạt
            if (isset($_POST['material_type']) && $_POST['material_type'] === 'file' && isset($_FILES['slide_files'])) {
                $upload_dir = __DIR__ . '/../../public/uploads/materials/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $total_files = count($_FILES['slide_files']['name']);
                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['slide_files']['error'][$i] === UPLOAD_ERR_OK) {
                        $original_name = $_FILES['slide_files']['name'][$i];
                        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                        $title = pathinfo($original_name, PATHINFO_FILENAME); // Tên file làm tiêu đề
                        
                        $new_file_name = 'mat_' . time() . '_' . uniqid() . '.' . $file_extension;
                        $target_file = $upload_dir . $new_file_name;

                        if (move_uploaded_file($_FILES['slide_files']['tmp_name'][$i], $target_file)) {
                            $content = 'uploads/materials/' . $new_file_name;
                            $stmt->execute([$chapter_id, $title, 'file', $content, $description]);
                            $addedCount++;
                        }
                    }
                }
            }

            // Xử lý thêm link hàng loạt
            if (isset($_POST['material_type']) && $_POST['material_type'] === 'link' && isset($_POST['link_urls']) && is_array($_POST['link_urls'])) {
                $link_titles = $_POST['link_titles'] ?? [];
                foreach ($_POST['link_urls'] as $index => $url) {
                    if (!empty(trim($url))) {
                        $title = isset($link_titles[$index]) && !empty(trim($link_titles[$index])) ? trim($link_titles[$index]) : 'Liên kết ngoài';
                        $stmt->execute([$chapter_id, $title, 'link', trim($url), $description]);
                        $addedCount++;
                    }
                }
            }
            
            // Xử lý thêm Quiz
            if (isset($_POST['material_type']) && $_POST['material_type'] === 'quiz' && !empty($_POST['quiz_json'])) {
                $title = !empty(trim($_POST['quiz_title'])) ? trim($_POST['quiz_title']) : 'Bài Trắc nghiệm';
                $quiz_json = $_POST['quiz_json']; // Cấu trúc mảng đã stringify
                $stmt->execute([$chapter_id, $title, 'quiz', $quiz_json, $description]);
                $addedCount++;
            }
            
            if ($addedCount > 0) {
                $_SESSION['success'] = "Đã thêm $addedCount Bài giảng/Trắc nghiệm mới vào chương!";
            } else {
                $_SESSION['error'] = "Không có Bài giảng nào được thêm. Vui lòng kiểm tra lại.";
            }

            header('Location: ?action=admin_manage_content&id=' . $course_id);
            exit();
        }
    }

    // Hàm hiển thị trang Tab Quản lý Khóa học
    public function manageCoursesList() {
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/Course.php';
        $db = (new Database())->getConnection();
        
        $courseModel = new Course($db);
        
        // Cấu hình phân trang
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;
        
        // Lấy query string filters
        $search = $_GET['search'] ?? '';
        $sort   = $_GET['sort'] ?? 'latest';
        $date   = $_GET['date'] ?? '';

        $totalCourses = $courseModel->getTotalCoursesCount($search, $date);
        $totalPages = ceil($totalCourses / $limit);

        // Lấy danh sách khóa học có phân trang & lọc
        $courses = $courseModel->getAllCourses($limit, $offset, $search, $sort, $date);
        
        // Lấy danh sách danh mục để đổ vào select box Thêm/Sửa khóa học
        require_once __DIR__ . '/../models/Category.php';
        $catModel = new Category($db);
        $categories = $catModel->getAllCategories();

        // Gọi file giao diện mới
        require_once __DIR__ . '/../../views/admin/manage_courses.php';
    }

    // Đánh dấu khóa học nổi bật
    public function toggleCourseFeatured() {
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Course.php';
            $db = (new Database())->getConnection();
            
            $courseModel = new Course($db);
            $courseModel->toggleFeatured($_GET['id']);
            
            $_SESSION['success'] = "Đã cập nhật trạng thái khóa học nổi bật!";
        }
        $page = isset($_GET['page']) ? '&page=' . $_GET['page'] : '';
        header('Location: ?action=admin_manage_courses' . $page);
        exit();
    }

    // Hàm xử lý CẬP NHẬT khóa học (Đã nâng cấp với fields mới)
    public function updateCourse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id             = $_POST['id'];
            $title          = $_POST['title'];
            $price          = isset($_POST['price']) ? intval($_POST['price']) : 0;
            $original_price = isset($_POST['original_price']) ? intval($_POST['original_price']) : 0;
            $description    = $_POST['description'];
            $benefits       = $_POST['benefits'] ?? '';
            $requirements   = $_POST['requirements'] ?? '';
            $instructor     = $_SESSION['user_name'] ?? 'Admin';
            $level          = $_POST['level']          ?? 'Sơ cấp';
            $duration_hours = isset($_POST['duration_hours']) ? intval($_POST['duration_hours']) : 0;
            $total_lessons  = isset($_POST['total_lessons'])  ? intval($_POST['total_lessons'])  : 0;
            $language       = $_POST['language']       ?? 'Tiếng Việt';
            // Card sidebar fields
            $start_date     = !empty($_POST['start_date'])    ? $_POST['start_date']    : null;
            $schedule       = $_POST['schedule']       ?? null;
            $study_time     = $_POST['study_time']     ?? null;
            $contact_phone  = $_POST['contact_phone']  ?? null;
            $thumbnail_path = $_POST['old_thumbnail'];

            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/courses/';
                $file_extension = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                $new_file_name = 'course_' . time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $new_file_name;

                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file)) {
                    $thumbnail_path = 'uploads/courses/' . $new_file_name;
                }
            }

            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("UPDATE courses SET title = ?, price = ?, original_price = ?, description = ?, benefits = ?, requirements = ?, thumbnail = ?, instructor = ?, level = ?, duration_hours = ?, total_lessons = ?, language = ?, start_date = ?, schedule = ?, study_time = ?, contact_phone = ? WHERE id = ?");
            $stmt->execute([$title, $price, $original_price, $description, $benefits, $requirements, $thumbnail_path, $instructor, $level, $duration_hours, $total_lessons, $language, $start_date, $schedule, $study_time, $contact_phone, $id]);

            $_SESSION['success'] = "Đã cập nhật khóa học thành công!";
            header('Location: ?action=admin_manage_courses');
            exit();
        }
    }

    // Hàm xử lý XÓA khóa học
    public function deleteCourse() {
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            
            $stmt = $db->prepare("DELETE FROM courses WHERE id = ?");
            $success = $stmt->execute([$_GET['id']]);

            // AJAX request check
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Khóa học đã được xóa khỏi hệ thống!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa khóa học.']);
                }
                exit();
            }

            $_SESSION['success'] = "Khóa học đã được xóa khỏi hệ thống!";
            header('Location: ?action=admin_manage_courses');
            exit();
        }
    }

    // ==========================================
    // KHU VỰC QUẢN LÝ CHƯƠNG & BÀI GIẢNG (CRUD)
    // ==========================================

    // Cập nhật Chương
    public function updateChapter() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("UPDATE chapters SET title = ?, description = ? WHERE id = ?");
            $description = $_POST['description'] ?? null;
            $stmt->execute([$_POST['title'], $description, $_POST['id']]);
            
            $_SESSION['success'] = "Đã cập nhật tên Chương!";
            header('Location: ?action=admin_manage_content&id=' . $_POST['course_id']);
            exit();
        }
    }

    // Xóa Chương
    public function deleteChapter() {
        if (isset($_GET['id']) && isset($_GET['course_id'])) {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            // Xóa chương (Lưu ý: Nếu set khóa ngoại ON DELETE CASCADE trong DB thì bài giảng tự mất theo)
            $stmt = $db->prepare("DELETE FROM chapters WHERE id = ?");
            $stmt->execute([$_GET['id']]);

            $_SESSION['success'] = "Đã xóa Chương học!";
            header('Location: ?action=admin_manage_content&id=' . $_GET['course_id']);
            exit();
        }
    }

    // Cập nhật Bài giảng
    public function updateMaterial() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST)) {
                $_SESSION['error'] = "File tải lên quá lớn! Vui lòng chọn file nhỏ hơn hoặc cấu hình lại server.";
                header('Location: ?action=admin_manage_courses');
                exit();
            }

            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            
            $content = $_POST['content'] ?? '';
            
            // Xử lý upload file mới nếu người dùng chọn loại "file" và có file tải lên
            if ($_POST['type'] === 'file' && isset($_FILES['slide_file']) && $_FILES['slide_file']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/materials/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['slide_file']['name'], PATHINFO_EXTENSION);
                $new_file_name = 'mat_' . time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $new_file_name;

                if (move_uploaded_file($_FILES['slide_file']['tmp_name'], $target_file)) {
                    $content = 'uploads/materials/' . $new_file_name;
                }
            }

            $stmt = $db->prepare("UPDATE materials SET title = ?, type = ?, content = ?, description = ? WHERE id = ?");
            $description = $_POST['description'] ?? null;
            $stmt->execute([$_POST['title'], $_POST['type'], $content, $description, $_POST['id']]);
            
            $_SESSION['success'] = "Đã cập nhật Bài giảng!";
            header('Location: ?action=admin_manage_content&id=' . $_POST['course_id']);
            exit();
        }
    }

    // Xóa Bài giảng
    public function deleteMaterial() {
        if (isset($_GET['id']) && isset($_GET['course_id'])) {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("DELETE FROM materials WHERE id = ?");
            $stmt->execute([$_GET['id']]);

            $_SESSION['success'] = "Đã xóa Bài giảng!";
            header('Location: ?action=admin_manage_content&id=' . $_GET['course_id']);
            exit();
        }
    }

    // ==========================================
    // KHU VỰC QUẢN LÝ BÌNH LUẬN
    // ==========================================

    // Hiển thị danh sách bài viết để quản lý bình luận
    public function manageCommentsList() {
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/Forum.php';
        $db = (new Database())->getConnection();
        
        $forumModel = new Forum($db);
        
        $search = $_GET['search'] ?? '';
        $sort   = $_GET['sort'] ?? 'latest';
        $date   = $_GET['date'] ?? '';
        
        // Lấy danh sách posts thay vì comments
        $posts = $forumModel->getAllPosts($search, $sort, null, $date);
        
        require_once __DIR__ . '/../../views/admin/manage_comments.php';
    }

    // Hiển thị iframe chi tiết bài viết và bình luận cho Modal
    public function adminPostComments() {
        if (!isset($_GET['id'])) {
            exit('Không tìm thấy bài viết');
        }
        $post_id = $_GET['id'];
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/Forum.php';
        $db = (new Database())->getConnection();
        $forumModel = new Forum($db);

        $post = $forumModel->getPostById($post_id);
        if (!$post) exit('Bài viết đã bị xóa');

        $comment_sort = 'latest';
        $all_comments = $forumModel->getComments($post_id, $_SESSION['user_id'] ?? null, $comment_sort);
        $commentTree = [];
        // Hàm này $getFlatReplies ở view sẽ tự xử lý mảng
        foreach ($all_comments as $c) {
            $parentId = $c['parent_id'] ?: 0;
            $commentTree[$parentId][] = $c;
        }

        require_once __DIR__ . '/../../views/admin/blank_layout_header.php';
        // Biến này giúp detail.php nhận biết đang ở chế độ admin modal
        $isAdminMode = true;
        // Ẩn nút trở về diễn đàn vì đang ở trong iframe modal
        echo '<style> a[href="?action=forum"] { display: none !important; } </style>';
        // Hiển thị thông báo JS (vì đang dùng _buildCommentHtml cần biến showToast)
        echo '<script>
        window.showToast = function(msg, type) { 
            window.parent.postMessage({action: "toast", msg: msg, type: type}, "*"); 
        };
        const CURRENT_USER = {
            id: ' . ($_SESSION['user_id'] ?? 'null') . ',
            fullname: "' . htmlspecialchars($_SESSION['user_name'] ?? '') . '",
            avatar: "' . htmlspecialchars($_SESSION['user_avatar'] ?? '') . '",
            role: "' . htmlspecialchars($_SESSION['user_role'] ?? 'user') . '"
        };
        </script>';
        require_once __DIR__ . '/../../views/forum/detail.php';
        require_once __DIR__ . '/../../views/admin/blank_layout_footer.php';
    }

    // Cập nhật nội dung bình luận
    public function updateComment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Forum.php';
            $db = (new Database())->getConnection();
            
            $forumModel = new Forum($db);
            $forumModel->updateComment($_POST['id'], $_POST['content']);
            
            $_SESSION['success'] = "Đã cập nhật bình luận thành công!";
            header('Location: ?action=admin_manage_comments');
            exit();
        }
    }

    // Xóa bình luận
    public function deleteComment() {
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Forum.php';
            $db = (new Database())->getConnection();
            
            $forumModel = new Forum($db);
            $forumModel->deleteComment($_GET['id']);

            $_SESSION['success'] = "Bình luận đã được xóa khỏi hệ thống!";
            header('Location: ?action=admin_manage_comments');
            exit();
        }
    }

    // ==========================================
    // KHU VỰC QUẢN LÝ NGƯỜI DÙNG (USERS)
    // ==========================================
    
    public function manageUsersList() {
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../Models/User.php';
        $db = (new Database())->getConnection();
        
        $userModel = new User($db);
        
        $search = $_GET['search'] ?? '';
        $role   = $_GET['role'] ?? 'all';
        $sort   = $_GET['sort'] ?? 'latest';
        
        // Phân trang
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;
        
        $totalUsers = $userModel->getTotalUsersCount($search, $role);
        $totalPages = ceil($totalUsers / $limit);
        
        $users = $userModel->getAllUsers($limit, $offset, $search, $role, $sort);
        
        require_once __DIR__ . '/../Models/Enrollment.php';
        $enrollmentModel = new Enrollment($db);
        foreach ($users as &$user) {
            $user['enrolled_courses'] = $enrollmentModel->getEnrolledCourses($user['id']);
        }
        unset($user); // Fix PHP bug with references

        
        require_once __DIR__ . '/../../views/admin/manage_users.php';
    }

    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../Models/User.php';
            $db = (new Database())->getConnection();
            
            $userModel = new User($db);
            $userModel->updateUser($_POST['id'], $_POST['fullname'], $_POST['role']);
            
            $_SESSION['success'] = "Cập nhật người dùng thành công!";
            header('Location: ?action=admin_manage_users');
            exit();
        }
    }

    public function deleteUser() {
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../Models/User.php';
            $db = (new Database())->getConnection();
            
            // Không cho phép xóa chính mình (nếu cần thì thêm check $_SESSION['user_id'] != $_GET['id'])
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $_GET['id']) {
                $_SESSION['error'] = "Bạn không thể xóa chính mình!";
                header('Location: ?action=admin_manage_users');
                exit();
            }
            
            $userModel = new User($db);
            $userModel->deleteUser($_GET['id']);
            
            $_SESSION['success'] = "Đã xóa người dùng thành công!";
            header('Location: ?action=admin_manage_users');
            exit();
        }
    }

    public function toggleFeaturedPost() {
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../Models/Forum.php';
            $db = (new Database())->getConnection();
            $forumModel = new Forum($db);
            
            if ($forumModel->toggleFeaturedPost($_GET['id'])) {
                $_SESSION['success'] = "Cập nhật trạng thái nổi bật thành công!";
            } else {
                $_SESSION['error'] = "Không thể cập nhật trạng thái nổi bật!";
            }
            
            header('Location: ?action=admin_manage_comments');
            exit();
        }
    }

    // ==========================================
    // KHU VỰC QUẢN LÝ DANH MỤC
    // ==========================================
    public function manageCategories() {
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/Category.php';
        $db = (new Database())->getConnection();
        $catModel = new Category($db);
        $categories = $catModel->getAllCategories();
        require_once __DIR__ . '/../../views/admin/manage_categories.php';
    }

    public function storeCategory() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Category.php';
            $db = (new Database())->getConnection();
            $catModel = new Category($db);
            
            $catModel->createCategory($_POST['name'], $_POST['icon'] ?? 'fa-folder', $_POST['color'] ?? 'bg-gray-100 text-gray-600');
            $_SESSION['success'] = "Đã thêm danh mục mới!";
            header('Location: ?action=admin_manage_categories');
            exit();
        }
    }

    public function updateCategory() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Category.php';
            $db = (new Database())->getConnection();
            $catModel = new Category($db);
            
            $catModel->updateCategory($_POST['id'], $_POST['name'], $_POST['icon'] ?? 'fa-folder', $_POST['color'] ?? 'bg-gray-100 text-gray-600');
            $_SESSION['success'] = "Đã cập nhật danh mục!";
            header('Location: ?action=admin_manage_categories');
            exit();
        }
    }

    public function deleteCategory() {
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Category.php';
            $db = (new Database())->getConnection();
            $catModel = new Category($db);
            
            $catModel->deleteCategory($_GET['id']);
            $_SESSION['success'] = "Đã xóa danh mục!";
            header('Location: ?action=admin_manage_categories');
            exit();
        }
    }

    // ==========================================
    // KHU VỰC QUẢN LÝ KHÓA HỌC TRONG DANH MỤC
    // ==========================================
    public function manageCategoryCourses() {
        if (!isset($_GET['id'])) {
            header('Location: ?action=admin_manage_categories');
            exit();
        }
        
        $category_id = $_GET['id'];
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/Category.php';
        
        $db = (new Database())->getConnection();
        $catModel = new Category($db);
        
        $category = $catModel->getCategoryById($category_id);
        if (!$category) {
            header('Location: ?action=admin_manage_categories');
            exit();
        }
        
        // Lấy danh sách khóa học thuộc danh mục này
        $stmt = $db->prepare("SELECT * FROM courses WHERE category_id = :cat_id ORDER BY id DESC");
        $stmt->bindParam(':cat_id', $category_id);
        $stmt->execute();
        $category_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Lấy danh sách khóa học trống hoặc thuộc danh mục khác (để Thêm vào)
        $stmt2 = $db->prepare("SELECT id, title FROM courses WHERE category_id IS NULL OR category_id != :cat_id ORDER BY id DESC");
        $stmt2->bindParam(':cat_id', $category_id);
        $stmt2->execute();
        $available_courses = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../../views/admin/manage_category_courses.php';
    }

    public function updateCourseCategory() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id']) && isset($_POST['category_id'])) {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            
            $stmt = $db->prepare("UPDATE courses SET category_id = :cat_id WHERE id = :course_id");
            $stmt->execute([
                ':cat_id' => $_POST['category_id'],
                ':course_id' => $_POST['course_id']
            ]);
            
            $_SESSION['success'] = "Đã thêm khóa học vào danh mục thành công!";
            header('Location: ?action=admin_manage_category_courses&id=' . $_POST['category_id']);
            exit();
        }
    }

    public function removeCourseCategory() {
        if (isset($_GET['course_id']) && isset($_GET['category_id'])) {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            
            $stmt = $db->prepare("UPDATE courses SET category_id = NULL WHERE id = :course_id");
            $stmt->execute([':course_id' => $_GET['course_id']]);
            
            $_SESSION['success'] = "Đã gỡ khóa học khỏi danh mục!";
            header('Location: ?action=admin_manage_category_courses&id=' . $_GET['category_id']);
            exit();
        }
    }

    // ==========================================
    // KHU VỰC QUẢN LÝ LIÊN HỆ
    // ==========================================
    public function manageContacts() {
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/Contact.php';
        $db = (new Database())->getConnection();
        $contactModel = new Contact($db);
        
        $contacts = $contactModel->getAllContacts();
        $pending_count = $contactModel->getPendingCount();

        require_once __DIR__ . '/../../views/admin/manage_contacts.php';
    }

    public function resolveContact() {
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Contact.php';
            $db = (new Database())->getConnection();
            $contactModel = new Contact($db);
            
            if ($contactModel->markAsResolved($_GET['id'])) {
                $_SESSION['success'] = "Đã đánh dấu liên hệ là đã xử lý!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra!";
            }
        }
        header("Location: ?action=admin_manage_contacts");
        exit;
    }
}
?>
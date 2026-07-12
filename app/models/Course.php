<?php
// File: app/models/Course.php

class Course {
    private $conn;

    // Nhận kết nối CSDL từ bên ngoài truyền vào
    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm lấy tất cả khóa học (Có hỗ trợ phân trang & lọc)
    public function getAllCourses($limit = null, $offset = null, $search = '', $sort = 'latest', $date = '', $category_id = null) {
        $query = "SELECT c.*, cat.name as category_name,
                  (SELECT COUNT(m.id) FROM materials m JOIN chapters ch ON m.chapter_id = ch.id WHERE ch.course_id = c.id) as real_total_lessons,
                  (SELECT COUNT(e.id) FROM enrollments e WHERE e.course_id = c.id) as real_enrollments
                  FROM courses c LEFT JOIN categories cat ON c.category_id = cat.id WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (c.title LIKE :search OR c.instructor LIKE :search)";
        }
        if (!empty($date)) {
            $query .= " AND DATE(c.created_at) = :date";
        }
        if (!empty($category_id)) {
            $query .= " AND c.category_id = :category_id";
        }
        
        if ($sort === 'oldest') {
            $query .= " ORDER BY c.created_at ASC";
        } elseif ($sort === 'price_high') {
            $query .= " ORDER BY c.price DESC";
        } elseif ($sort === 'price_low') {
            $query .= " ORDER BY c.price ASC";
        } else {
            // latest
            $query .= " ORDER BY c.is_featured DESC, c.created_at DESC";
        }
        
        if ($limit !== null && $offset !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%");
        }
        if (!empty($date)) {
            $stmt->bindValue(':date', $date);
        }
        if (!empty($category_id)) {
            $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        }
        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tổng số khóa học để phân trang
    public function getTotalCoursesCount($search = '', $date = '', $category_id = null) {
        $query = "SELECT COUNT(*) FROM courses WHERE 1=1";
        if (!empty($search)) {
            $query .= " AND (title LIKE :search OR instructor LIKE :search)";
        }
        if (!empty($date)) {
            $query .= " AND DATE(created_at) = :date";
        }
        if (!empty($category_id)) {
            $query .= " AND category_id = :category_id";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%");
        }
        if (!empty($date)) {
            $stmt->bindValue(':date', $date);
        }
        if (!empty($category_id)) {
            $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // Bật/Tắt trạng thái Nổi bật
    public function toggleFeatured($id) {
        // Lấy trạng thái hiện tại
        $query = "SELECT is_featured FROM courses WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $current = $stmt->fetchColumn();

        $newStatus = $current ? 0 : 1;

        $updateQuery = "UPDATE courses SET is_featured = :status WHERE id = :id";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bindParam(':status', $newStatus);
        $updateStmt->bindParam(':id', $id);
        return $updateStmt->execute();
    }

    
    // Lấy chi tiết 1 khóa học dựa vào ID
    public function getCourseById($id) {
        // JOIN với categories để lấy category_name (giống getAllCourses)
        $query = "SELECT c.*, cat.name as category_name 
                  FROM courses c 
                  LEFT JOIN categories cat ON c.category_id = cat.id 
                  WHERE c.id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Gán giá trị thực tế vào tham số :id
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Lấy 1 dòng duy nhất (FETCH_ASSOC)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Hàm thêm khóa học mới vào DB (Đã cập nhật đầy đủ fields)
    public function createCourse($title, $description, $thumbnail, $benefits, $requirements, $price = 0, $original_price = 0, $instructor = '', $level = 'Sơ cấp', $duration_hours = 0, $total_lessons = 0, $language = 'Tiếng Việt', $start_date = null, $schedule = null, $study_time = null, $contact_phone = null, $category_id = null) {
        $query = "INSERT INTO courses (title, category_id, price, original_price, description, thumbnail, benefits, requirements, instructor, level, duration_hours, total_lessons, language, start_date, schedule, study_time, contact_phone) 
                  VALUES (:title, :category_id, :price, :original_price, :description, :thumbnail, :benefits, :requirements, :instructor, :level, :duration_hours, :total_lessons, :language, :start_date, :schedule, :study_time, :contact_phone)";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':title'          => $title,
            ':category_id'    => $category_id,
            ':price'          => $price,
            ':original_price' => $original_price,
            ':description'    => $description,
            ':thumbnail'      => $thumbnail,
            ':benefits'       => $benefits,
            ':requirements'   => $requirements,
            ':instructor'     => $instructor,
            ':level'          => $level,
            ':duration_hours' => $duration_hours,
            ':total_lessons'  => $total_lessons,
            ':language'       => $language,
            ':start_date'     => $start_date,
            ':schedule'       => $schedule,
            ':study_time'     => $study_time,
            ':contact_phone'  => $contact_phone,
        ]);
    }

    // Hàm xóa khóa học
    public function deleteCourse($id) {
        $query = "DELETE FROM courses WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

?>
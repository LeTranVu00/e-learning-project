<?php
// File: app/models/Testimonial.php

class Testimonial {
    // Thuộc tính $db để sau này kết nối Database thật nếu cần
    private $conn;

    public function __construct($db = null) {
        $this->conn = $db;
    }

    // Hàm lấy danh sách cảm nhận học viên (Mock data tạm thời)
    public function getTestimonials() {
        return [
            [
                'name' => 'Nguyễn Văn A', 
                'role' => 'Sinh viên IT', 
                'content' => 'Khóa học cực kỳ chất lượng, giảng viên nhiệt tình. Mình đã tự tin đi thực tập sau khi hoàn thành lộ trình Web Fullstack.',
                'rating' => 5,
                'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Van+A&background=0D8ABC&color=fff'
            ],
            [
                'name' => 'Trần Thị B', 
                'role' => 'Frontend Developer', 
                'content' => 'Giao diện web trực quan, dễ học. Các dự án thực tế trong khóa học giúp mình áp dụng ngay kiến thức vào công việc hiện tại.',
                'rating' => 5,
                'avatar' => 'https://ui-avatars.com/api/?name=Tran+Thi+B&background=F59E0B&color=fff'
            ],
            [
                'name' => 'Lê Hữu C', 
                'role' => 'Freelancer', 
                'content' => 'Nội dung luôn được cập nhật mới nhất theo xu hướng. Mình khuyên các bạn nên tham gia, rất đáng từng đồng tiền bát gạo!',
                'rating' => 4,
                'avatar' => 'https://ui-avatars.com/api/?name=Le+Huu+C&background=10B981&color=fff'
            ],
        ];
    }
}
?>

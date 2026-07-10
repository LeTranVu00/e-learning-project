<?php
// File: app/utils/Uploader.php

class Uploader {
    // 5MB by default
    const DEFAULT_MAX_SIZE = 5 * 1024 * 1024;
    
    // Allowed image and document extensions
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf', 'mp4', 'docx', 'doc', 'zip', 'rar'];
    private $maxSize;

    public function __construct($maxSize = self::DEFAULT_MAX_SIZE, $allowedExtensions = null) {
        $this->maxSize = $maxSize;
        if ($allowedExtensions !== null) {
            $this->allowedExtensions = $allowedExtensions;
        }
    }

    /**
     * Upload a single file
     * @param array $fileArray ($_FILES['fieldname'])
     * @param string $destinationDir (e.g. 'public/uploads/courses/')
     * @param string $prefix
     * @return array ['success' => bool, 'path' => string, 'message' => string]
     */
    public function upload($fileArray, $destinationDir, $prefix = 'file_') {
        if (!isset($fileArray) || $fileArray['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Không có file hoặc lỗi khi tải lên.'];
        }

        // Check file size
        if ($fileArray['size'] > $this->maxSize) {
            return ['success' => false, 'message' => 'Kích thước file vượt quá giới hạn cho phép (' . ($this->maxSize / 1024 / 1024) . 'MB).'];
        }

        // Check file extension safely
        $fileName = $fileArray['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $this->allowedExtensions)) {
            return ['success' => false, 'message' => 'Định dạng file không được hỗ trợ (.' . $fileExtension . ').'];
        }

        // Create directory if not exists
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        // Generate safe unique filename
        $newFileName = $prefix . time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
        $targetFile = rtrim($destinationDir, '/') . '/' . $newFileName;

        if (move_uploaded_file($fileArray['tmp_name'], $targetFile)) {
            return ['success' => true, 'path' => $newFileName, 'message' => 'Tải lên thành công'];
        }

        return ['success' => false, 'message' => 'Không thể di chuyển file vào thư mục lưu trữ.'];
    }

    /**
     * Upload multiple files
     * @param array $fileArray ($_FILES['fieldname'])
     * @param int $index The index in the array of multiple files
     * @param string $destinationDir
     * @param string $prefix
     * @return array
     */
    public function uploadMultiple($fileArray, $index, $destinationDir, $prefix = 'file_') {
        if (!isset($fileArray['error'][$index]) || $fileArray['error'][$index] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Lỗi tải lên file.'];
        }

        if ($fileArray['size'][$index] > $this->maxSize) {
            return ['success' => false, 'message' => 'File quá lớn.'];
        }

        $fileName = $fileArray['name'][$index];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $this->allowedExtensions)) {
            return ['success' => false, 'message' => 'Định dạng không hỗ trợ (.'.$fileExtension.').'];
        }

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        $newFileName = $prefix . time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
        $targetFile = rtrim($destinationDir, '/') . '/' . $newFileName;

        if (move_uploaded_file($fileArray['tmp_name'][$index], $targetFile)) {
            return ['success' => true, 'path' => $newFileName, 'message' => 'Thành công'];
        }

        return ['success' => false, 'message' => 'Lưu file thất bại.'];
    }
}
?>

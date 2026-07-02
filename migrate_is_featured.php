<?php
require_once __DIR__ . '/app/config/Database.php';
$db = (new Database())->getConnection();
try {
    $db->exec("ALTER TABLE posts ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER title;");
    echo "Column added successfully.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>

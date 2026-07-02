<?php
require_once __DIR__ . '/app/config/Database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("DESCRIBE courses");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols, JSON_PRETTY_PRINT);
?>

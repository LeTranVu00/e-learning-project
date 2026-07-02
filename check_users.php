<?php
require_once __DIR__ . '/app/config/Database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT id, fullname, email, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($users, JSON_PRETTY_PRINT);
?>

<?php
require_once __DIR__ . '/../inc/header.php';
require_role(['admin','teacher']);
$id = intval($_GET['id'] ?? 0);
if ($id) {
    // fetch user id
    $stmt = $conn->prepare("SELECT user_id FROM students WHERE student_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($r && $r['user_id']) {
        $stmt2 = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt2->bind_param('i', $r['user_id']);
        $stmt2->execute();
        $stmt2->close();
    }
}
header("Location: list.php");
exit;

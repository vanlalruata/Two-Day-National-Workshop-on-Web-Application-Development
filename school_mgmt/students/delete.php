<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}
$id = intval($_GET['id'] ?? 0);
if ($id) {
    // Delete student: users, students cascade will remove user and student if FK set correctly
    // But to be safe: find user_id and delete user which cascades
    $stmt = $conn->prepare("SELECT user_id FROM students WHERE student_id=?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($r && $r['user_id']) {
        $uid = intval($r['user_id']);
        $stmt2 = $conn->prepare("DELETE FROM users WHERE user_id=?");
        $stmt2->bind_param('i',$uid);
        $stmt2->execute();
        $stmt2->close();
    }
}
header("Location: list.php");
exit;

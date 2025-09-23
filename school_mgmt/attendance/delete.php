<?php
// attendance/delete.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$class_id = intval($_GET['class_id'] ?? 0);
$date = $_GET['date'] ?? '';

if ($class_id && $date) {
    $stmt = $conn->prepare("DELETE FROM attendance WHERE class_id = ? AND date = ?");
    $stmt->bind_param('is', $class_id, $date);
    $stmt->execute();
    $stmt->close();
}

header("Location: list.php");
exit;

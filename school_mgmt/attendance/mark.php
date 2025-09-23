<?php
// attendance/mark.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: add.php");
    exit;
}

$class_id = intval($_POST['class_id'] ?? 0);
$date = $_POST['date'] ?? '';
$status_arr = $_POST['status'] ?? [];

if (!$class_id || !$date || !is_array($status_arr)) {
    echo "<div class='alert alert-danger'>Invalid data.</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, class_id, date, status) VALUES (?, ?, ?, ?)");
    foreach ($status_arr as $student_id => $status) {
        $sid = intval($student_id);
        $st = $status === 'Absent' ? 'Absent' : 'Present';
        $stmt->bind_param('iiss', $sid, $class_id, $date, $st);
        $stmt->execute();
    }
    $stmt->close();
    $conn->commit();
    $_SESSION['flash_success'] = "Attendance saved.";
    header("Location: list.php");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    echo "<div class='alert alert-danger'>Error saving attendance: " . htmlspecialchars($e->getMessage()) . "</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

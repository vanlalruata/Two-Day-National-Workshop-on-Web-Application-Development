<?php
// results/delete.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id) {
    $stmt = $conn->prepare("DELETE FROM results WHERE result_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
header("Location: list.php");
exit;

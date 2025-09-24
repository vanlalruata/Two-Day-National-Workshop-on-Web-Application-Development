<?php
// inc/functions.php
if (session_status() === PHP_SESSION_NONE) session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: /school_mgmt/index.php");
        exit();
    }
}

function require_role($roles = []) {
    if (!is_logged_in()) {
        header("Location: /school_mgmt/index.php");
        exit();
    }
    if (!is_array($roles)) $roles = [$roles];
    if (!in_array($_SESSION['role'], $roles) && $_SESSION['role'] !== 'admin') {
        // admins allowed always
        http_response_code(403);
        echo "<div class='max-w-3xl mx-auto mt-12 p-6 bg-red-50 border border-red-200 rounded'>Forbidden — You don't have permission.</div>";
        exit;
    }
}
?>

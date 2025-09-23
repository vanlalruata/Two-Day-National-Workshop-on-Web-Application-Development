<?php
// inc/functions.php
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: /school_mgmt/auth/login.php");
        exit();
    }
}

function require_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        // allow admin to do everything
        if ($_SESSION['role'] !== 'admin') {
            header("HTTP/1.1 403 Forbidden");
            echo "Forbidden - You don't have permission to access this page.";
            exit();
        }
    }
}
?>

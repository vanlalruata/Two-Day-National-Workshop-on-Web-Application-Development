<?php
function checkAuth($required_role = null) {
    if(!isset($_SESSION['user_id'])) {
        header("Location: " . SITE_URL . "index.php");
        exit();
    }
    
    if($required_role && $_SESSION['user_type'] != $required_role) {
        header("Location: " . SITE_URL . "pages/dashboard/" . $_SESSION['user_type'] . "_dashboard.php");
        exit();
    }
    
    // Check session timeout
    if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        header("Location: " . SITE_URL . "index.php?timeout=1");
        exit();
    }
    
    $_SESSION['last_activity'] = time();
}
?>
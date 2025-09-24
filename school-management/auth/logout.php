<?php
// auth/logout.php
session_start();
session_unset();
session_destroy();
header("Location: /school_mgmt/index.php");
exit;

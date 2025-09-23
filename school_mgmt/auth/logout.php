<?php
session_start();
session_unset();
session_destroy();
header("Location: /school_mgmt/auth/login.php");
exit();

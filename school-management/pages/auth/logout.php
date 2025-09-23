<?php
session_start();
require_once '../../config/config.php';
require_once '../../functions/auth_functions.php';

$auth = new Auth(null);
$auth->logout();

header("Location: ../../index.php?logout=1");
exit();
?>
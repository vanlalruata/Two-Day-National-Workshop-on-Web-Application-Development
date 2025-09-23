<?php
session_start();
require_once 'config/database.php';
require_once 'functions/auth_functions.php';

if(isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard/" . $_SESSION['user_type'] . "_dashboard.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

if($_POST) {
    $auth = new Auth($db);
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if($auth->login($username, $password)) {
        header("Location: pages/dashboard/" . $_SESSION['user_type'] . "_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System - Login</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.png" alt="School Logo" class="mb-3" style="height: 60px;">
                            <h4 class="text-primary">School Management System</h4>
                            <p class="text-muted">Please sign in to continue</p>
                        </div>
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Demo Credentials:<br>
                                Admin: admin / password<br>
                                Teacher: teacher1 / password<br>
                                Student: student1 / password
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
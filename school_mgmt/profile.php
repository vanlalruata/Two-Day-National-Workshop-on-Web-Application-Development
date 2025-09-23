<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Fetch user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_pic = $user['profile_pic']; // keep old if not changed

    // File upload handling
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . time() . "_" . basename($_FILES["profile_pic"]["name"]);
        
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $target_file;
        }
    }

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET email = ?, password = ?, profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $email, $hashed, $profile_pic, $user_id);
    } else {
        $sql = "UPDATE users SET email = ?, profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $email, $profile_pic, $user_id);
    }

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
        $_SESSION['username'] = $user['username']; // refresh session
    } else {
        $error = "Error updating profile!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile Settings - School Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">SchoolMgmt</a>
    <div class="d-flex">
      <a href="auth/logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h3>Profile Settings</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" class="form-control" value="<?= $user['username'] ?>" disabled>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="<?= $user['email'] ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label>New Password (leave blank if not changing)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Profile Picture</label><br>
            <?php if ($user['profile_pic']): ?>
                <img src="<?= $user['profile_pic'] ?>" width="80" class="mb-2 rounded">
            <?php endif; ?>
            <input type="file" name="profile_pic" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>

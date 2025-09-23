<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
require_role('admin');

$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $subject_specialization = trim($_POST['subject_specialization']);
    $default_password = password_hash('teacher123', PASSWORD_DEFAULT);

    if (!$username || !$email) $errors[] = "Please fill all required fields.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (username,password,role,email) VALUES (?,?, 'teacher', ?)");
        $stmt->bind_param('sss', $username, $default_password, $email);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $stmt->close();
            $stmt2 = $conn->prepare("INSERT INTO teachers (user_id, subject_specialization) VALUES (?, ?)");
            $stmt2->bind_param('is', $user_id, $subject_specialization);
            if ($stmt2->execute()) $success = "Teacher added.";
            else $errors[] = "Could not add teacher.";
            $stmt2->close();
        } else {
            $errors[] = "Could not create user (duplicate?).";
        }
    }
}
?>

<div class="row">
  <div class="col-md-6">
    <h4>Add Teacher</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>
    <form method="post">
      <div class="mb-3"><label>Name</label><input name="username" class="form-control" required></div>
      <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
      <div class="mb-3"><label>Specialization</label><input name="subject_specialization" class="form-control"></div>
      <button class="btn btn-primary">Add</button>
      <a href="list.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

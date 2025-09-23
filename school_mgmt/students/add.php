<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $admission_no = trim($_POST['admission_no']);
    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?? 'Male';
    $address = $_POST['address'] ?? null;
    $default_password = password_hash('student123', PASSWORD_DEFAULT);

    // basic validation
    if (!$username || !$email || !$admission_no) $errors[] = "Please fill required fields.";

    // create user
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (username,password,role,email) VALUES (?,?, 'student', ?)");
        $stmt->bind_param('sss', $username, $default_password, $email);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $stmt->close();
            $stmt2 = $conn->prepare("INSERT INTO students (user_id, admission_no, dob, gender, address) VALUES (?,?,?,?,?)");
            $stmt2->bind_param('issss', $user_id, $admission_no, $dob, $gender, $address);
            if ($stmt2->execute()) {
                $success = "Student added.";
            } else {
                $errors[] = "Could not create student record.";
            }
            $stmt2->close();
        } else {
            $errors[] = "Could not create user (username/email may already exist).";
        }
    }
}
?>
<div class="row">
  <div class="col-md-8">
    <h4>Add Student</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    <form method="post">
      <div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="username" required></div>
      <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
      <div class="mb-3"><label class="form-label">Admission No</label><input class="form-control" name="admission_no" required></div>
      <div class="mb-3"><label class="form-label">DOB</label><input type="date" class="form-control" name="dob"></div>
      <div class="mb-3"><label class="form-label">Gender</label>
        <select name="gender" class="form-control">
          <option>Male</option><option>Female</option><option>Other</option>
        </select>
      </div>
      <div class="mb-3"><label class="form-label">Address</label><textarea class="form-control" name="address"></textarea></div>
      <button class="btn btn-primary">Add</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>
  </div>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

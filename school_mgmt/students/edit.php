<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: list.php"); exit; }

$errors = []; $success = '';

$stmt = $conn->prepare("SELECT st.*, u.username, u.email FROM students st JOIN users u ON st.user_id=u.user_id WHERE st.student_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();
$stmt->close();
if (!$student) { echo "Student not found"; include_once __DIR__ . '/../inc/footer.php'; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $admission_no = trim($_POST['admission_no']);
    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?? 'Male';
    $address = $_POST['address'] ?? null;
    // update user
    $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE user_id=?");
    $stmt->bind_param('ssi', $username, $email, $student['user_id']);
    if ($stmt->execute()) {
        $stmt->close();
        $stmt2 = $conn->prepare("UPDATE students SET admission_no=?, dob=?, gender=?, address=? WHERE student_id=?");
        $stmt2->bind_param('ssssi', $admission_no, $dob, $gender, $address, $id);
        if ($stmt2->execute()) { $success = "Updated."; }
        else $errors[] = "Could not update student.";
        $stmt2->close();
    } else { $errors[] = "Could not update user."; }
}
?>
<div class="row">
  <div class="col-md-8">
    <h4>Edit Student</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    <form method="post">
      <div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="username" value="<?= htmlspecialchars($student['username']) ?>" required></div>
      <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($student['email']) ?>" required></div>
      <div class="mb-3"><label class="form-label">Admission No</label><input class="form-control" name="admission_no" value="<?= htmlspecialchars($student['admission_no']) ?>" required></div>
      <div class="mb-3"><label class="form-label">DOB</label><input type="date" class="form-control" name="dob" value="<?= htmlspecialchars($student['dob']) ?>"></div>
      <div class="mb-3"><label class="form-label">Gender</label>
        <select name="gender" class="form-control">
          <option <?= $student['gender']=='Male'?'selected':'' ?>>Male</option>
          <option <?= $student['gender']=='Female'?'selected':'' ?>>Female</option>
          <option <?= $student['gender']=='Other'?'selected':'' ?>>Other</option>
        </select>
      </div>
      <div class="mb-3"><label class="form-label">Address</label><textarea class="form-control" name="address"><?= htmlspecialchars($student['address']) ?></textarea></div>
      <button class="btn btn-primary">Update</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>
  </div>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

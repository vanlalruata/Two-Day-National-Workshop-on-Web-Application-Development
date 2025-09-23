<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: list.php"); exit; }

$stmt = $conn->prepare("SELECT t.*, u.username, u.email FROM teachers t 
                        JOIN users u ON t.user_id=u.user_id 
                        WHERE t.teacher_id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$teacher) { echo "Teacher not found."; include_once __DIR__.'/../inc/footer.php'; exit; }

$errors=[]; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $username=trim($_POST['username']);
    $email=trim($_POST['email']);
    $subject_specialization=trim($_POST['subject_specialization']);
    // update user
    $stmt=$conn->prepare("UPDATE users SET username=?, email=? WHERE user_id=?");
    $stmt->bind_param('ssi',$username,$email,$teacher['user_id']);
    if($stmt->execute()) {
        $stmt->close();
        $stmt2=$conn->prepare("UPDATE teachers SET subject_specialization=? WHERE teacher_id=?");
        $stmt2->bind_param('si',$subject_specialization,$id);
        if($stmt2->execute()) $success="Updated.";
        else $errors[]="Could not update teacher.";
        $stmt2->close();
    } else $errors[]="Could not update user.";
}
?>

<div class="row">
  <div class="col-md-6">
    <h4>Edit Teacher</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>
    <form method="post">
      <div class="mb-3"><label>Name</label><input name="username" class="form-control" value="<?= htmlspecialchars($teacher['username']) ?>" required></div>
      <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($teacher['email']) ?>" required></div>
      <div class="mb-3"><label>Specialization</label><input name="subject_specialization" class="form-control" value="<?= htmlspecialchars($teacher['subject_specialization']) ?>"></div>
      <button class="btn btn-primary">Update</button>
      <a href="list.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

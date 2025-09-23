<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: list.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM classes WHERE class_id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$class) { echo "Class not found."; include_once __DIR__ . '/../inc/footer.php'; exit; }

$success=''; $errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $class_name = trim($_POST['class_name']);
    $section = trim($_POST['section']);
    if (!$class_name) $errors[]="Class name required.";
    if (empty($errors)) {
        $stmt=$conn->prepare("UPDATE classes SET class_name=?, section=? WHERE class_id=?");
        $stmt->bind_param('ssi',$class_name,$section,$id);
        if($stmt->execute()) $success="Updated.";
        else $errors[]="Could not update class.";
        $stmt->close();
    }
}
?>

<div class="row">
  <div class="col-md-6">
    <h4>Edit Class</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>
    <form method="post">
      <div class="mb-3"><label>Class Name</label>
        <input name="class_name" class="form-control" value="<?= htmlspecialchars($class['class_name']) ?>" required>
      </div>
      <div class="mb-3"><label>Section</label>
        <input name="section" class="form-control" value="<?= htmlspecialchars($class['section']) ?>">
      </div>
      <button class="btn btn-primary">Update</button>
      <a href="list.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

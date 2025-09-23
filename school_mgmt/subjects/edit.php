<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: list.php"); exit; }

// Fetch subject
$stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$subject) { echo "Subject not found."; include_once __DIR__ . '/../inc/footer.php'; exit; }

// Fetch classes
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name ASC");

$success=''; $errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $subject_name = trim($_POST['subject_name']);
    $class_id = intval($_POST['class_id']);
    if (!$subject_name) $errors[] = "Subject name is required.";
    if (empty($errors)) {
        $stmt=$conn->prepare("UPDATE subjects SET subject_name=?, class_id=? WHERE subject_id=?");
        $stmt->bind_param('sii',$subject_name,$class_id,$id);
        if ($stmt->execute()) $success="Updated.";
        else $errors[]="Could not update subject.";
        $stmt->close();
    }
}
?>

<div class="row">
  <div class="col-md-6">
    <h4>Edit Subject</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>

    <form method="post">
      <div class="mb-3"><label>Subject Name</label>
        <input name="subject_name" class="form-control" value="<?= htmlspecialchars($subject['subject_name']) ?>" required>
      </div>
      <div class="mb-3"><label>Class</label>
        <select name="class_id" class="form-control" required>
          <?php while($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['class_id'] ?>" <?= $c['class_id']==$subject['class_id']?'selected':'' ?>>
              <?= htmlspecialchars($c['class_name']) ?> <?= $c['section'] ? " - ".htmlspecialchars($c['section']) : "" ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <button class="btn btn-primary">Update</button>
      <a href="list.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

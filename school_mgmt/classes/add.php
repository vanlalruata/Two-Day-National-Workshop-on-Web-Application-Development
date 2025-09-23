<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
require_role('admin');

$success = ''; $errors=[];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name']);
    $section = trim($_POST['section']);

    if (!$class_name) $errors[] = "Class name required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO classes (class_name, section) VALUES (?, ?)");
        $stmt->bind_param('ss', $class_name, $section);
        if ($stmt->execute()) $success="Class added.";
        else $errors[]="Could not add class.";
        $stmt->close();
    }
}
?>

<div class="row">
  <div class="col-md-6">
    <h4>Add Class</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>
    <form method="post">
      <div class="mb-3"><label>Class Name</label><input name="class_name" class="form-control" required></div>
      <div class="mb-3"><label>Section</label><input name="section" class="form-control"></div>
      <button class="btn btn-primary">Add</button>
      <a href="list.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

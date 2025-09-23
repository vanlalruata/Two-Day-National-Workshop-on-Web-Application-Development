<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
require_role('admin');

// Fetch classes for dropdown
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name ASC");

$success = ''; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = trim($_POST['subject_name']);
    $class_id = intval($_POST['class_id']);

    if (!$subject_name) $errors[] = "Subject name is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name, class_id) VALUES (?, ?)");
        $stmt->bind_param('si', $subject_name, $class_id);
        if ($stmt->execute()) $success = "Subject added.";
        else $errors[] = "Could not add subject.";
        $stmt->close();
    }
}
?>

<div class="row">
  <div class="col-md-6">
    <h4>Add Subject</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>

    <form method="post">
      <div class="mb-3"><label>Subject Name</label><input name="subject_name" class="form-control" required></div>
      <div class="mb-3"><label>Class</label>
        <select name="class_id" class="form-control" required>
          <option value="">-- Select Class --</option>
          <?php while($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['class_id'] ?>"><?= htmlspecialchars($c['class_name']) ?> <?= $c['section'] ? " - ".htmlspecialchars($c['section']) : "" ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <button class="btn btn-primary">Add</button>
      <a href="list.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

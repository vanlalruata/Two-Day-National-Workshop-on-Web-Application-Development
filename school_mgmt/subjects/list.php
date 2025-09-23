<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
require_role('admin'); // Only admin

$sql = "SELECT s.subject_id, s.subject_name, c.class_name, c.section
        FROM subjects s
        LEFT JOIN classes c ON s.class_id = c.class_id
        ORDER BY s.subject_id DESC";
$res = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Subjects</h4>
  <div>
    <a href="add.php" class="btn btn-success btn-sm">+ Add Subject</a>
    <a href="/school_mgmt/dashboard.php" class="btn btn-secondary btn-sm">Back</a>
  </div>
</div>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>ID</th><th>Subject Name</th><th>Class</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $row['subject_id'] ?></td>
        <td><?= htmlspecialchars($row['subject_name']) ?></td>
        <td><?= htmlspecialchars($row['class_name']) ?> <?= $row['section'] ? " - ".htmlspecialchars($row['section']) : "" ?></td>
        <td>
          <a href="edit.php?id=<?= $row['subject_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
          <a href="delete.php?id=<?= $row['subject_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this subject?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

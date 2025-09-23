<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
require_role('admin'); // only admin

$res = $conn->query("SELECT * FROM classes ORDER BY class_id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Classes</h4>
  <div>
    <a href="add.php" class="btn btn-success btn-sm">+ Add Class</a>
    <a href="/school_mgmt/dashboard.php" class="btn btn-secondary btn-sm">Back</a>
  </div>
</div>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr><th>ID</th><th>Class Name</th><th>Section</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $row['class_id'] ?></td>
        <td><?= htmlspecialchars($row['class_name']) ?></td>
        <td><?= htmlspecialchars($row['section']) ?></td>
        <td>
          <a href="edit.php?id=<?= $row['class_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
          <a href="delete.php?id=<?= $row['class_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this class?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

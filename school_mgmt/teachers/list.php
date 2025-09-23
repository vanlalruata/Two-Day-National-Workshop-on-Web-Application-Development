<?php
require_once __DIR__ . '/../inc/header.php';
require_login();
require_role('admin'); // Only Admin can manage teachers

$sql = "SELECT t.teacher_id, u.username, u.email, t.subject_specialization 
        FROM teachers t 
        JOIN users u ON t.user_id = u.user_id 
        ORDER BY t.teacher_id DESC";
$res = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Teachers</h4>
  <div>
    <a href="add.php" class="btn btn-success btn-sm">+ Add Teacher</a>
    <a href="/school_mgmt/dashboard.php" class="btn btn-secondary btn-sm">Back</a>
  </div>
</div>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>ID</th><th>Name</th><th>Email</th><th>Specialization</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $r['teacher_id'] ?></td>
        <td><?= htmlspecialchars($r['username']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= htmlspecialchars($r['subject_specialization']) ?></td>
        <td>
          <a href="edit.php?id=<?= $r['teacher_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
          <a href="delete.php?id=<?= $r['teacher_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete teacher?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

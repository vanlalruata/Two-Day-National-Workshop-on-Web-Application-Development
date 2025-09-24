<?php
// exams/list.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$sql = "SELECT ex.exam_id, ex.exam_date, c.class_id, c.class_name, c.section, sb.subject_id, sb.subject_name
        FROM exams ex
        LEFT JOIN classes c ON ex.class_id = c.class_id
        LEFT JOIN subjects sb ON ex.subject_id = sb.subject_id
        ORDER BY ex.exam_date DESC, c.class_name ASC";
$res = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Exams</h4>
  <div>
    <a class="btn btn-success btn-sm" href="add.php">+ Add Exam</a>
    <a class="btn btn-secondary btn-sm" href="/school_mgmt/dashboard.php">Back</a>
  </div>
</div>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>ID</th><th>Date</th><th>Class</th><th>Subject</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($res && $res->num_rows): ?>
      <?php while($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $row['exam_id'] ?></td>
          <td><?= htmlspecialchars($row['exam_date']) ?></td>
          <td><?= htmlspecialchars($row['class_name']) ?><?= $row['section'] ? ' - '.htmlspecialchars($row['section']) : '' ?></td>
          <td><?= htmlspecialchars($row['subject_name']) ?></td>
          <td>
            <a class="btn btn-info btn-sm" href="edit.php?id=<?= $row['exam_id'] ?>">Edit</a>
            <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $row['exam_id'] ?>" onclick="return confirm('Delete this exam?')">Delete</a>
            <a class="btn btn-sm btn-primary" href="/school_mgmt/results/add.php?exam_id=<?= $row['exam_id'] ?>">Add Results</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="5" class="text-center">No exams found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

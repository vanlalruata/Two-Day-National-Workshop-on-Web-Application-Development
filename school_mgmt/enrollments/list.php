<?php
// enrollments/list.php
require_once __DIR__ . '/../inc/header.php';
require_login();

// Allow admin and teacher to view/manage enrollments
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$sql = "SELECT e.enrollment_id, e.year, 
               s.student_id, u.username AS student_name, s.admission_no,
               c.class_id, c.class_name, c.section
        FROM enrollments e
        JOIN students s ON e.student_id = s.student_id
        JOIN users u ON s.user_id = u.user_id
        JOIN classes c ON e.class_id = c.class_id
        ORDER BY e.enrollment_id DESC";
$res = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Enrollments</h4>
  <div>
    <a class="btn btn-success btn-sm" href="add.php">+ Add Enrollment</a>
    <a class="btn btn-secondary btn-sm" href="/school_mgmt/dashboard.php">Back</a>
  </div>
</div>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Student</th>
      <th>Admission No</th>
      <th>Class</th>
      <th>Year</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $row['enrollment_id'] ?></td>
        <td><?= htmlspecialchars($row['student_name']) ?></td>
        <td><?= htmlspecialchars($row['admission_no']) ?></td>
        <td><?= htmlspecialchars($row['class_name']) ?><?= $row['section'] ? ' - '.htmlspecialchars($row['section']) : '' ?></td>
        <td><?= htmlspecialchars($row['year']) ?></td>
        <td>
          <a href="edit.php?id=<?= $row['enrollment_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
          <a href="delete.php?id=<?= $row['enrollment_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete enrollment?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    <?php if ($res->num_rows === 0): ?>
      <tr><td colspan="6" class="text-center">No enrollments found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

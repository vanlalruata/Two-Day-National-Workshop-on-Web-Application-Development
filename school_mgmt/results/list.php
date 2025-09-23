<?php
// results/list.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

// Optional filter by exam_id or class
$exam_id = intval($_GET['exam_id'] ?? 0);
$where = "";
if ($exam_id) {
    $where = "WHERE r.exam_id = " . $exam_id;
}

$sql = "SELECT r.result_id, r.marks_obtained, r.grade,
               ex.exam_id, ex.exam_date, 
               c.class_id, c.class_name, c.section,
               sb.subject_id, sb.subject_name,
               st.student_id, u.username AS student_name, st.admission_no
        FROM results r
        JOIN exams ex ON r.exam_id = ex.exam_id
        LEFT JOIN classes c ON ex.class_id = c.class_id
        LEFT JOIN subjects sb ON ex.subject_id = sb.subject_id
        JOIN students st ON r.student_id = st.student_id
        JOIN users u ON st.user_id = u.user_id
        $where
        ORDER BY ex.exam_date DESC, c.class_name, u.username";

$res = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Results</h4>
  <div>
    <a class="btn btn-secondary btn-sm" href="/school_mgmt/exams/list.php">Back to Exams</a>
    <a class="btn btn-success btn-sm" href="add.php">+ Add Result</a>
  </div>
</div>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>#</th><th>Exam Date</th><th>Class</th><th>Subject</th><th>Student</th><th>Admission No</th><th>Marks</th><th>Grade</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($res && $res->num_rows): ?>
      <?php while($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $row['result_id'] ?></td>
          <td><?= htmlspecialchars($row['exam_date']) ?></td>
          <td><?= htmlspecialchars($row['class_name']) ?><?= $row['section'] ? ' - '.htmlspecialchars($row['section']) : '' ?></td>
          <td><?= htmlspecialchars($row['subject_name']) ?></td>
          <td><?= htmlspecialchars($row['student_name']) ?></td>
          <td><?= htmlspecialchars($row['admission_no']) ?></td>
          <td><?= htmlspecialchars($row['marks_obtained']) ?></td>
          <td><?= htmlspecialchars($row['grade']) ?></td>
          <td>
            <a class="btn btn-sm btn-warning" href="edit.php?id=<?= $row['result_id'] ?>">Edit</a>
            <a class="btn btn-sm btn-danger" href="delete.php?id=<?= $row['result_id'] ?>" onclick="return confirm('Delete this result?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="9" class="text-center">No results found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

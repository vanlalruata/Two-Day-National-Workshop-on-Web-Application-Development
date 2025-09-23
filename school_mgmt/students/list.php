<?php
require_once __DIR__ . '/../inc/header.php';
require_login();

// Only admin or teacher can view/manage students
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

// fetch students with class & user
$sql = "SELECT st.student_id, u.username, u.email, st.admission_no, st.dob, st.gender, st.address
        FROM students st
        JOIN users u ON st.user_id = u.user_id
        ORDER BY st.student_id DESC";
$res = $conn->query($sql);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Students</h4>
  <div>
    <a class="btn btn-sm btn-success" href="add.php">+ Add Student</a>
    <a class="btn btn-sm btn-secondary" href="/school_mgmt/dashboard.php">Back</a>
  </div>
</div>

<table class="table table-bordered table-striped">
<thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Email</th><th>Admission No</th><th>DOB</th><th>Gender</th><th>Actions</th></tr></thead>
<tbody>
<?php while($r = $res->fetch_assoc()): ?>
  <tr>
    <td><?= $r['student_id'] ?></td>
    <td><?= htmlspecialchars($r['username']) ?></td>
    <td><?= htmlspecialchars($r['email']) ?></td>
    <td><?= htmlspecialchars($r['admission_no']) ?></td>
    <td><?= htmlspecialchars($r['dob']) ?></td>
    <td><?= htmlspecialchars($r['gender']) ?></td>
    <td>
      <a href="edit.php?id=<?= $r['student_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
      <a href="delete.php?id=<?= $r['student_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this student?')">Delete</a>
    </td>
  </tr>
<?php endwhile; ?>
</tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

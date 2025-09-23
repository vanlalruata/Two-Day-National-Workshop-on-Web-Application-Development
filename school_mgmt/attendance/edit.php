<?php
// attendance/edit.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$class_id = intval($_GET['class_id'] ?? 0);
$date = $_GET['date'] ?? '';
if (!$class_id || !$date) { header("Location: list.php"); exit; }

// fetch attendance rows
$stmt = $conn->prepare("SELECT a.attendance_id, a.student_id, a.status, u.username, st.admission_no
                        FROM attendance a
                        JOIN students st ON a.student_id = st.student_id
                        JOIN users u ON st.user_id = u.user_id
                        WHERE a.class_id = ? AND a.date = ?
                        ORDER BY u.username");
$stmt->bind_param('is', $class_id, $date);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<div class='alert alert-warning'>No attendance records found for this class and date.</div>";
    echo "<a class='btn btn-secondary' href='list.php'>Back</a>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$errors = []; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // update statuses
    $statuses = $_POST['status'] ?? [];
    $conn->begin_transaction();
    try {
        $up = $conn->prepare("UPDATE attendance SET status=? WHERE attendance_id=?");
        foreach ($statuses as $att_id => $stat) {
            $aid = intval($att_id);
            $s = $stat === 'Absent' ? 'Absent' : 'Present';
            $up->bind_param('si', $s, $aid);
            $up->execute();
        }
        $up->close();
        $conn->commit();
        $success = "Attendance updated.";
        // reload results
        $stmt->execute();
        $res = $stmt->get_result();
    } catch (Exception $e) {
        $conn->rollback();
        $errors[] = "Error updating: " . $e->getMessage();
    }
}
?>

<h4>Edit Attendance for <?= htmlspecialchars($date) ?></h4>
<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>

<form method="post">
  <table class="table table-bordered">
    <thead><tr><th>#</th><th>Student</th><th>Admission No</th><th>Status</th></tr></thead>
    <tbody>
      <?php $i=0; while($row = $res->fetch_assoc()): $i++; ?>
        <tr>
          <td><?= $i ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['admission_no']) ?></td>
          <td>
            <select name="status[<?= $row['attendance_id'] ?>]" class="form-control">
              <option value="Present" <?= $row['status']=='Present' ? 'selected' : '' ?>>Present</option>
              <option value="Absent" <?= $row['status']=='Absent' ? 'selected' : '' ?>>Absent</option>
            </select>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <button class="btn btn-primary">Save Changes</button>
  <a class="btn btn-secondary" href="list.php">Back</a>
</form>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

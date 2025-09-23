<?php
// attendance/add.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

// fetch classes
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name");

$errors = []; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = intval($_POST['class_id'] ?? 0);
    $date = $_POST['date'] ?? date('Y-m-d');

    if (!$class_id) $errors[] = "Select a class.";
    if (!$date) $errors[] = "Pick a date.";

    // After selecting class & date, show student list for marking (handled below)
    if (empty($errors)) {
        // Check if attendance already exists for this class/date
        $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM attendance WHERE class_id=? AND date=?");
        $stmt->bind_param('is', $class_id, $date);
        $stmt->execute();
        $c = $stmt->get_result()->fetch_assoc()['c'];
        $stmt->close();
        if ($c > 0) {
            $errors[] = "Attendance for this class and date already exists. You may edit it instead.";
        } else {
            // fetch students of this class via enrollments
            $stmt = $conn->prepare("SELECT st.student_id, u.username, st.admission_no FROM enrollments e JOIN students st ON e.student_id = st.student_id JOIN users u ON st.user_id = u.user_id WHERE e.class_id = ? AND e.year = ? ORDER BY u.username");
            // default to current year if enrollment uses year; use provided date's year
            $year = intval(date('Y', strtotime($date)));
            $stmt->bind_param('ii', $class_id, $year);
            $stmt->execute();
            $students = $stmt->get_result();
            $stmt->close();

            // if no enrollments, optionally include all students without enrollments (fallback)
        }
    }
}
?>

<div class="row">
  <div class="col-md-8">
    <h4>Mark Attendance</h4>

    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label>Class</label>
        <select name="class_id" class="form-control" required>
          <option value="">-- Select Class --</option>
          <?php while($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['class_id'] ?>" <?= (isset($_POST['class_id']) && $_POST['class_id']==$c['class_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['class_name']) ?><?= $c['section'] ? ' - '.htmlspecialchars($c['section']) : '' ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label>Date</label>
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>" required>
      </div>

      <button class="btn btn-primary">Load Students</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>

    <?php if (!empty($students) && $students->num_rows > 0): ?>
      <hr>
      <form method="post" action="mark.php">
        <input type="hidden" name="class_id" value="<?= intval($_POST['class_id']) ?>">
        <input type="hidden" name="date" value="<?= htmlspecialchars($_POST['date']) ?>">
        <table class="table table-bordered">
          <thead><tr><th>#</th><th>Student</th><th>Admission No</th><th>Status</th></tr></thead>
          <tbody>
            <?php $i=0; while($st = $students->fetch_assoc()): $i++; ?>
              <tr>
                <td><?= $i ?></td>
                <td><?= htmlspecialchars($st['username']) ?></td>
                <td><?= htmlspecialchars($st['admission_no']) ?></td>
                <td>
                  <select name="status[<?= $st['student_id'] ?>]" class="form-control">
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                  </select>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <button class="btn btn-success">Save Attendance</button>
      </form>
    <?php elseif (isset($students) && $students->num_rows == 0): ?>
      <div class="alert alert-warning">No students enrolled for the selected class/year.</div>
    <?php endif; ?>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

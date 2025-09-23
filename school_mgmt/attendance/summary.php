<?php
// attendance/summary.php
require_once __DIR__ . '/../inc/header.php';
require_login();

// Only admin & teacher can view
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

// Filter inputs
$class_id = intval($_GET['class_id'] ?? 0);
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

// fetch classes for filter dropdown
$classes = $conn->query("SELECT class_id, class_name, section FROM classes ORDER BY class_name ASC");

// Build WHERE conditions
$where = [];
$params = [];
$types = "";

if ($class_id) { $where[] = "a.class_id = ?"; $params[] = $class_id; $types .= "i"; }
if ($from) { $where[] = "a.date >= ?"; $params[] = $from; $types .= "s"; }
if ($to) { $where[] = "a.date <= ?"; $params[] = $to; $types .= "s"; }

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Query attendance summary
$sql = "SELECT st.student_id, u.username, st.admission_no,
               COUNT(a.attendance_id) AS total_days,
               SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) AS present_days
        FROM attendance a
        JOIN students st ON a.student_id = st.student_id
        JOIN users u ON st.user_id = u.user_id
        $where_sql
        GROUP BY st.student_id, u.username, st.admission_no
        ORDER BY u.username ASC";

$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();
$stmt->close();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Attendance Summary</h4>
  <a class="btn btn-secondary btn-sm" href="/school_mgmt/dashboard.php">Back</a>
</div>

<form method="get" class="row g-2 mb-3">
  <div class="col-auto">
    <select name="class_id" class="form-select">
      <option value="">-- All Classes --</option>
      <?php while($c = $classes->fetch_assoc()): ?>
        <option value="<?= $c['class_id'] ?>" <?= $class_id==$c['class_id']?'selected':'' ?>>
          <?= htmlspecialchars($c['class_name']) ?><?= $c['section'] ? " - ".htmlspecialchars($c['section']) : "" ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="col-auto">
    <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="form-control">
  </div>
  <div class="col-auto">
    <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="form-control">
  </div>
  <div class="col-auto">
    <button class="btn btn-primary">Filter</button>
    <a href="summary.php" class="btn btn-outline-secondary">Reset</a>
  </div>
</form>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>#</th><th>Student</th><th>Admission No</th><th>Total Days</th><th>Present</th><th>Absent</th><th>Percentage</th>
    </tr>
  </thead>
  <tbody>
    <?php
      $i=0;
      if ($res->num_rows):
        while($row = $res->fetch_assoc()):
          $i++;
          $total = $row['total_days'];
          $present = $row['present_days'];
          $absent = $total - $present;
          $perc = $total>0 ? round(($present/$total)*100,1) : 0;
    ?>
      <tr>
        <td><?= $i ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['admission_no']) ?></td>
        <td><?= $total ?></td>
        <td><?= $present ?></td>
        <td><?= $absent ?></td>
        <td><?= $perc ?>%</td>
      </tr>
    <?php endwhile; else: ?>
      <tr><td colspan="7" class="text-center">No attendance data.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

<?php
// attendance/list.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

// fetch aggregated attendance dates grouped by class/date
$sql = "SELECT a.class_id, c.class_name, c.section, a.date, COUNT(a.attendance_id) AS total_rows
        FROM attendance a
        JOIN classes c ON a.class_id = c.class_id
        GROUP BY a.class_id, a.date
        ORDER BY a.date DESC, c.class_name ASC";
$res = $conn->query($sql);

// optional: filter by class/date via GET
$filter_class = intval($_GET['class_id'] ?? 0);
$filter_date = $_GET['date'] ?? '';

?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Attendance Records</h4>
  <div>
    <a href="add.php" class="btn btn-success btn-sm">+ Mark Attendance</a>
    <a href="/school_mgmt/dashboard.php" class="btn btn-secondary btn-sm">Back</a>
  </div>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <select name="class_id" class="form-select">
      <option value="">-- All Classes --</option>
      <?php
        $cls = $conn->query("SELECT * FROM classes ORDER BY class_name");
        while($c = $cls->fetch_assoc()):
      ?>
        <option value="<?= $c['class_id'] ?>" <?= $filter_class && $filter_class==$c['class_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['class_name']) ?><?= $c['section'] ? ' - '.htmlspecialchars($c['section']) : '' ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="col-auto">
    <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>" class="form-control">
  </div>
  <div class="col-auto">
    <button class="btn btn-primary">Filter</button>
    <a class="btn btn-outline-secondary" href="list.php">Reset</a>
  </div>
</form>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>Date</th>
      <th>Class</th>
      <th>Entries</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
      // re-run a filtered query to list grouped rows
      $where = [];
      if ($filter_class) $where[] = "a.class_id = " . intval($filter_class);
      if ($filter_date) $where[] = "a.date = '" . $conn->real_escape_string($filter_date) . "'";
      $where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";
      $sql2 = "SELECT a.class_id, c.class_name, c.section, a.date, COUNT(a.attendance_id) AS total_rows
               FROM attendance a
               JOIN classes c ON a.class_id = c.class_id
               $where_sql
               GROUP BY a.class_id, a.date
               ORDER BY a.date DESC, c.class_name ASC";
      $res2 = $conn->query($sql2);
      if ($res2 && $res2->num_rows):
        while($row = $res2->fetch_assoc()):
    ?>
      <tr>
        <td><?= htmlspecialchars($row['date']) ?></td>
        <td><?= htmlspecialchars($row['class_name']) ?><?= $row['section'] ? ' - '.htmlspecialchars($row['section']) : '' ?></td>
        <td><?= $row['total_rows'] ?></td>
        <td>
          <a class="btn btn-sm btn-info" href="edit.php?class_id=<?= $row['class_id'] ?>&date=<?= $row['date'] ?>">View / Edit</a>
          <a class="btn btn-sm btn-danger" href="delete.php?class_id=<?= $row['class_id'] ?>&date=<?= $row['date'] ?>" onclick="return confirm('Delete all attendance records for this class and date?')">Delete</a>
        </td>
      </tr>
    <?php
        endwhile;
      else:
    ?>
      <tr><td colspan="4" class="text-center">No attendance records found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

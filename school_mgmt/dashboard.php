<?php
require_once __DIR__ . '/inc/header.php';
require_login();

// quick stats (counts)
$counts = [];
$tables = ['students','teachers','classes','subjects'];
foreach ($tables as $t) {
  $res = $conn->query("SELECT COUNT(*) AS c FROM $t");
  $counts[$t] = $res->fetch_assoc()['c'] ?? 0;
}
?>
<div class="row">
  <div class="col-md-12">
    <h3>Dashboard</h3>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)</p>
  </div>
</div>

<div class="row">
  <div class="col-md-3"><div class="card p-3"><h5>Students</h5><p><?= $counts['students'] ?></p></div></div>
  <div class="col-md-3"><div class="card p-3"><h5>Teachers</h5><p><?= $counts['teachers'] ?></p></div></div>
  <div class="col-md-3"><div class="card p-3"><h5>Classes</h5><p><?= $counts['classes'] ?></p></div></div>
  <div class="col-md-3"><div class="card p-3"><h5>Subjects</h5><p><?= $counts['subjects'] ?></p></div></div>
</div>

<?php include_once __DIR__ . '/inc/footer.php'; ?>

<?php
require_once __DIR__ . '/inc/header.php';
require_login();

$role = $_SESSION['role'];

// quick stats (counts)
$counts = [];
$tables = ['students','teachers','classes','subjects'];
foreach ($tables as $t) {
  $res = $conn->query("SELECT COUNT(*) AS c FROM $t");
  $counts[$t] = $res ? (int)$res->fetch_assoc()['c'] : 0;
}
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <div class="col-span-2">
    <div class="bg-white p-6 rounded shadow">
      <h1 class="text-2xl font-semibold mb-2">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
      <p class="text-sm text-gray-600 mb-4">Role: <span class="inline-block px-2 py-1 bg-gray-100 rounded"><?= htmlspecialchars($role) ?></span></p>

      <?php if ($role === 'admin'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <a href="/school_mgmt/users/list.php" class="block p-4 rounded border hover:shadow">
            <div class="text-sm text-gray-500">User Management</div><div class="text-lg font-semibold mt-1">Manage Users</div>
          </a>
          <a href="/school_mgmt/students/list.php" class="block p-4 rounded border hover:shadow">
            <div class="text-sm text-gray-500">Students</div><div class="text-lg font-semibold mt-1">Manage Students</div>
          </a>
          <a href="/school_mgmt/teachers/list.php" class="block p-4 rounded border hover:shadow">
            <div class="text-sm text-gray-500">Teachers</div><div class="text-lg font-semibold mt-1">Manage Teachers</div>
          </a>
        </div>
      <?php elseif ($role === 'teacher'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <a href="/school_mgmt/students/list.php" class="block p-4 rounded border hover:shadow">
            <div class="text-sm text-gray-500">Students</div><div class="text-lg font-semibold mt-1">View Students</div>
          </a>
          <a href="/school_mgmt/attendance/list.php" class="block p-4 rounded border hover:shadow">
            <div class="text-sm text-gray-500">Attendance</div><div class="text-lg font-semibold mt-1">Mark Attendance</div>
          </a>
        </div>
      <?php else: /* student */ ?>
        <div class="bg-gray-50 p-4 rounded">
          <p class="text-gray-700">Student Dashboard — upcoming features: view attendance, grades, timetable.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <aside class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-semibold mb-3">Summary</h3>
    <ul class="space-y-2 text-sm">
      <li>Students: <span class="font-medium"><?= $counts['students'] ?></span></li>
      <li>Teachers: <span class="font-medium"><?= $counts['teachers'] ?></span></li>
      <li>Classes: <span class="font-medium"><?= $counts['classes'] ?></span></li>
      <li>Subjects: <span class="font-medium"><?= $counts['subjects'] ?></span></li>
    </ul>
  </aside>
</div>

<?php include_once __DIR__ . '/inc/footer.php'; ?>

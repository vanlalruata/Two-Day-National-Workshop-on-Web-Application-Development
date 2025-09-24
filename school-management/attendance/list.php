<?php
require_once __DIR__ . '/../inc/header.php';
require_role(['admin','teacher']);

// Fetch attendance records
$sql = "SELECT a.attendance_id, a.date, a.status,
               u.username AS student_name, st.admission_no,
               c.class_name, c.section
        FROM attendance a
        JOIN students st ON a.student_id = st.student_id
        JOIN users u ON st.user_id = u.user_id
        JOIN classes c ON a.class_id = c.class_id
        ORDER BY a.date DESC, u.username";
$res = $conn->query($sql);
?>
<div class="bg-white p-6 rounded shadow max-w-6xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">Attendance Records</h3>
    <a href="add.php" class="btn bg-primary-500 text-white">+ Mark Attendance</a>
  </div>

  <table class="w-full table-auto border">
    <thead class="bg-gray-50">
      <tr>
        <th class="p-3">#</th>
        <th class="p-3">Date</th>
        <th class="p-3">Student</th>
        <th class="p-3">Admission No</th>
        <th class="p-3">Class</th>
        <th class="p-3">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row=$res->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="p-3"><?= $row['attendance_id'] ?></td>
          <td class="p-3"><?= htmlspecialchars($row['date']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['student_name']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['admission_no']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['class_name']).' '.htmlspecialchars($row['section']) ?></td>
          <td class="p-3">
            <span class="px-2 py-1 rounded <?= $row['status']=='Present'?'bg-green-100 text-green-700':'bg-red-100 text-red-700' ?>">
              <?= $row['status'] ?>
            </span>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

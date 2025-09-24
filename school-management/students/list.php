<?php
require_once __DIR__ . '/../inc/header.php';
require_role(['admin','teacher']);

$sql = "SELECT st.student_id, u.username, u.email, st.admission_no, st.dob, st.gender
        FROM students st JOIN users u ON st.user_id = u.user_id ORDER BY st.student_id DESC";
$res = $conn->query($sql);
?>
<div class="bg-white p-6 rounded shadow max-w-6xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">Students</h3>
    <div class="flex gap-3">
      <a href="add.php" class="btn bg-primary-500 text-white">+ Add Student</a>
      <a href="/school_mgmt/dashboard.php" class="text-gray-600">Back</a>
    </div>
  </div>

  <table class="w-full table-auto border">
    <thead class="bg-gray-50">
      <tr>
        <th class="p-3">#</th><th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Admission No</th><th class="p-3">DOB</th><th class="p-3">Gender</th><th class="p-3">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($r = $res->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="p-3"><?= $r['student_id'] ?></td>
          <td class="p-3"><?= htmlspecialchars($r['username']) ?></td>
          <td class="p-3"><?= htmlspecialchars($r['email']) ?></td>
          <td class="p-3"><?= htmlspecialchars($r['admission_no']) ?></td>
          <td class="p-3"><?= htmlspecialchars($r['dob']) ?></td>
          <td class="p-3"><?= htmlspecialchars($r['gender']) ?></td>
          <td class="p-3">
            <a class="text-blue-600" href="edit.php?id=<?= $r['student_id'] ?>">Edit</a> |
            <a class="text-red-600" href="delete.php?id=<?= $r['student_id'] ?>" onclick="return confirm('Delete student?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$sql = "SELECT e.enrollment_id, e.year, 
               u.username AS student_name, st.admission_no, 
               c.class_name, c.section
        FROM enrollments e
        JOIN students st ON e.student_id=st.student_id
        JOIN users u ON st.user_id=u.user_id
        JOIN classes c ON e.class_id=c.class_id
        ORDER BY e.year DESC, student_name";
$res = $conn->query($sql);
?>
<div class="bg-white p-6 rounded shadow max-w-6xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">Enrollments</h3>
    <a href="add.php" class="btn bg-primary-500 text-white">+ Enroll Student</a>
  </div>

  <table class="w-full table-auto border">
    <thead class="bg-gray-50">
      <tr>
        <th class="p-3">#</th>
        <th class="p-3">Student</th>
        <th class="p-3">Admission No</th>
        <th class="p-3">Class</th>
        <th class="p-3">Year</th>
        <th class="p-3">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row=$res->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="p-3"><?= $row['enrollment_id'] ?></td>
          <td class="p-3"><?= htmlspecialchars($row['student_name']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['admission_no']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['class_name']).' '.htmlspecialchars($row['section']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['year']) ?></td>
          <td class="p-3">
            <a class="text-blue-600" href="edit.php?id=<?= $row['enrollment_id'] ?>">Edit</a> |
            <a class="text-red-600" href="delete.php?id=<?= $row['enrollment_id'] ?>" onclick="return confirm('Delete enrollment?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

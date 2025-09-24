<?php
require_once __DIR__ . '/../inc/header.php';
require_role(['admin','teacher']);

$sql = "SELECT r.result_id, r.marks_obtained, r.grade,
               e.exam_date, sb.subject_name,
               u.username AS student_name, st.admission_no,
               c.class_name, c.section
        FROM results r
        JOIN exams e ON r.exam_id=e.exam_id
        JOIN subjects sb ON e.subject_id=sb.subject_id
        JOIN students st ON r.student_id=st.student_id
        JOIN users u ON st.user_id=u.user_id
        JOIN classes c ON e.class_id=c.class_id
        ORDER BY e.exam_date DESC, student_name";
$res = $conn->query($sql);
?>
<div class="bg-white p-6 rounded shadow max-w-7xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">Results</h3>
    <a href="add.php" class="btn bg-primary-500 text-white">+ Add Result</a>
  </div>

  <table class="w-full table-auto border">
    <thead class="bg-gray-50">
      <tr>
        <th class="p-3">#</th>
        <th class="p-3">Student</th>
        <th class="p-3">Admission No</th>
        <th class="p-3">Class</th>
        <th class="p-3">Subject</th>
        <th class="p-3">Exam Date</th>
        <th class="p-3">Marks</th>
        <th class="p-3">Grade</th>
        <th class="p-3">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row=$res->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="p-3"><?= $row['result_id'] ?></td>
          <td class="p-3"><?= htmlspecialchars($row['student_name']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['admission_no']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['class_name']).' '.htmlspecialchars($row['section']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['subject_name']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['exam_date']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['marks_obtained']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['grade']) ?></td>
          <td class="p-3">
            <a class="text-blue-600" href="edit.php?id=<?= $row['result_id'] ?>">Edit</a> |
            <a class="text-red-600" href="delete.php?id=<?= $row['result_id'] ?>" onclick="return confirm('Delete result?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

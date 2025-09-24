<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$sql = "SELECT e.exam_id, e.exam_date, 
               c.class_name, c.section, 
               sb.subject_name
        FROM exams e
        JOIN classes c ON e.class_id=c.class_id
        LEFT JOIN subjects sb ON e.subject_id=sb.subject_id
        ORDER BY e.exam_date DESC";
$res = $conn->query($sql);
?>
<div class="bg-white p-6 rounded shadow max-w-5xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">Exams</h3>
    <a href="add.php" class="btn bg-primary-500 text-white">+ Add Exam</a>
  </div>

  <table class="w-full table-auto border">
    <thead class="bg-gray-50">
      <tr>
        <th class="p-3">#</th>
        <th class="p-3">Class</th>
        <th class="p-3">Subject</th>
        <th class="p-3">Exam Date</th>
        <th class="p-3">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row=$res->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="p-3"><?= $row['exam_id'] ?></td>
          <td class="p-3"><?= htmlspecialchars($row['class_name']).' '.htmlspecialchars($row['section']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['subject_name']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['exam_date']) ?></td>
          <td class="p-3">
            <a class="text-blue-600" href="edit.php?id=<?= $row['exam_id'] ?>">Edit</a> |
            <a class="text-red-600" href="delete.php?id=<?= $row['exam_id'] ?>" onclick="return confirm('Delete exam?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

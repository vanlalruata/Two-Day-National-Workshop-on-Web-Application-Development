<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$res = $conn->query("SELECT t.teacher_id, u.username, u.email, t.subject_specialization 
                     FROM teachers t 
                     JOIN users u ON t.user_id = u.user_id 
                     ORDER BY t.teacher_id DESC");
?>
<div class="bg-white p-6 rounded shadow max-w-6xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">Teachers</h3>
    <a href="add.php" class="btn bg-primary-500 text-white">+ Add Teacher</a>
  </div>

  <table class="w-full table-auto border">
    <thead class="bg-gray-50">
      <tr>
        <th class="p-3">#</th><th class="p-3">Name</th><th class="p-3">Email</th>
        <th class="p-3">Specialization</th><th class="p-3">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row=$res->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="p-3"><?= $row['teacher_id'] ?></td>
          <td class="p-3"><?= htmlspecialchars($row['username']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['email']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['subject_specialization']) ?></td>
          <td class="p-3">
            <a class="text-blue-600" href="edit.php?id=<?= $row['teacher_id'] ?>">Edit</a> |
            <a class="text-red-600" href="delete.php?id=<?= $row['teacher_id'] ?>" onclick="return confirm('Delete teacher?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

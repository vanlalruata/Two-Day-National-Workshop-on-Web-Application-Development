<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$res = $conn->query("SELECT * FROM classes ORDER BY class_id DESC");
?>
<div class="bg-white p-6 rounded shadow max-w-4xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">Classes</h3>
    <a href="add.php" class="btn bg-primary-500 text-white">+ Add Class</a>
  </div>

  <table class="w-full table-auto border">
    <thead class="bg-gray-50">
      <tr>
        <th class="p-3">#</th>
        <th class="p-3">Class Name</th>
        <th class="p-3">Section</th>
        <th class="p-3">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row=$res->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="p-3"><?= $row['class_id'] ?></td>
          <td class="p-3"><?= htmlspecialchars($row['class_name']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['section']) ?></td>
          <td class="p-3">
            <a class="text-blue-600" href="edit.php?id=<?= $row['class_id'] ?>">Edit</a> |
            <a class="text-red-600" href="delete.php?id=<?= $row['class_id'] ?>" onclick="return confirm('Delete class?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

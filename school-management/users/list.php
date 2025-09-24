<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$res = $conn->query("SELECT user_id, username, email, role, created_at FROM users ORDER BY user_id DESC");
?>
<div class="bg-white p-6 rounded shadow max-w-5xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">User Management</h3>
    <a href="add.php" class="btn bg-primary-500 text-white">+ Add User</a>
  </div>

  <table class="w-full table-auto border">
    <thead class="bg-gray-50">
      <tr class="text-left">
        <th class="p-3">#</th>
        <th class="p-3">Username</th>
        <th class="p-3">Email</th>
        <th class="p-3">Role</th>
        <th class="p-3">Created</th>
        <th class="p-3">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $res->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="p-3"><?= $row['user_id'] ?></td>
          <td class="p-3"><?= htmlspecialchars($row['username']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['email']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['role']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['created_at']) ?></td>
          <td class="p-3">
            <a class="text-blue-600" href="edit.php?id=<?= $row['user_id'] ?>">Edit</a> |
            <a class="text-red-600" href="delete.php?id=<?= $row['user_id'] ?>" onclick="return confirm('Delete user?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

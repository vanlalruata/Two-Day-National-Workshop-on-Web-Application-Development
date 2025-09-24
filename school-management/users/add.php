<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$errors = []; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'student';
    $password = $_POST['password'] ?? '';

    if (!$username || !$email || !$password) $errors[] = "Fill required fields.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $username, $hash, $role, $email);
        if ($stmt->execute()) { $success = "User created."; } else { $errors[] = "Could not create user (duplicate?)."; }
        $stmt->close();
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Add User</h3>
  <?php foreach ($errors as $e): ?><div class="mb-2 p-2 bg-red-50 border text-red-700 rounded"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if ($success): ?><div class="mb-2 p-2 bg-green-50 border text-green-700 rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Username</label>
      <input name="username" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input name="email" type="email" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Role</label>
      <select name="role" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
        <option value="admin">Admin</option>
        <option value="teacher">Teacher</option>
        <option value="student" selected>Student</option>
        <option value="parent">Parent</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Password</label>
      <input name="password" type="password" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>

    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Create</button>
      <a class="text-gray-500" href="list.php">Cancel</a>
    </div>
  </form>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

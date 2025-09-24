<?php
require_once __DIR__ . '/inc/header.php';
require_login();

$user_id = $_SESSION['user_id'];
$success = $error = '';

// fetch user
$stmt = $conn->prepare("SELECT username, email, phone FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $newpass = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($newpass) {
        if ($newpass !== $confirm) $error = "Passwords do not match.";
        else {
            $hash = password_hash($newpass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET email=?, phone=?, password=? WHERE user_id=?");
            $stmt->bind_param('sssi', $email, $phone, $hash, $user_id);
            if ($stmt->execute()) $success = "Profile and password updated.";
            else $error = "Update failed.";
            $stmt->close();
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET email=?, phone=? WHERE user_id=?");
        $stmt->bind_param('ssi', $email, $phone, $user_id);
        if ($stmt->execute()) $success = "Profile updated.";
        else $error = "Update failed.";
        $stmt->close();
    }
    // refresh user
    $stmt = $conn->prepare("SELECT username, email, phone FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
  <h2 class="text-xl font-semibold mb-4">Profile Settings</h2>
  <?php if ($success): ?><div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Username</label>
      <input class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2 bg-gray-50" value="<?= htmlspecialchars($user['username']) ?>" disabled>
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input name="email" value="<?= htmlspecialchars($user['email']) ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">Phone</label>
      <input name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
    </div>

    <hr class="my-4">

    <div>
      <label class="block text-sm font-medium">New Password</label>
      <input type="password" name="new_password" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">Confirm Password</label>
      <input type="password" name="confirm_password" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
    </div>

    <div class="flex items-center gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Save</button>
      <a href="/school_mgmt/dashboard.php" class="text-gray-500">Cancel</a>
    </div>
  </form>
</div>

<?php include_once __DIR__ . '/inc/footer.php'; ?>

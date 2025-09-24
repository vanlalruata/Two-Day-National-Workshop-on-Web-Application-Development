<?php
// index.php (login)
session_start();
require_once __DIR__ . '/config/db.php';

// if already logged in => redirect
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $u = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($u && password_verify($password, $u['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $u['user_id'];
            $_SESSION['username'] = $u['username'];
            $_SESSION['role'] = $u['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in both fields.";
    }
}

include_once __DIR__ . '/inc/header.php';
?>

<div class="max-w-md mx-auto mt-12">
  <div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-semibold mb-4">Sign in to SchoolMgmt</h2>
    <?php if ($error): ?>
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Username</label>
        <input name="username" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm px-3 py-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm px-3 py-2" required>
      </div>
      <div class="flex items-center justify-between">
        <button class="bg-primary-500 text-white px-4 py-2 rounded hover:bg-primary-700">Login</button>
        <a href="#" class="text-sm text-gray-500">Forgot?</a>
      </div>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/inc/footer.php'; ?>

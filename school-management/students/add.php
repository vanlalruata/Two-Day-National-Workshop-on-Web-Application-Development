<?php
require_once __DIR__ . '/../inc/header.php';
require_role(['admin','teacher']);

$errors=[]; $success='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $admission_no = trim($_POST['admission_no'] ?? '');
    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?? 'Other';
    $default_password = password_hash('student123', PASSWORD_DEFAULT);

    if (!$name || !$email || !$admission_no) $errors[] = "Please fill required fields.";

    if (empty($errors)) {
        // create user
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, email) VALUES (?, ?, 'student', ?)");
        $stmt->bind_param('sss', $name, $default_password, $email);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id; $stmt->close();
            $stmt2 = $conn->prepare("INSERT INTO students (user_id, admission_no, dob, gender) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param('isss', $user_id, $admission_no, $dob, $gender);
            if ($stmt2->execute()) $success = "Student added."; else $errors[] = "Could not create student record.";
            $stmt2->close();
        } else {
            $errors[] = "Could not create user (duplicate username/email).";
        }
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Add Student</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 rounded text-red-700"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if ($success): ?><div class="mb-2 p-2 bg-green-50 rounded text-green-700"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Full Name</label>
      <input name="name" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input name="email" type="email" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Admission No</label>
      <input name="admission_no" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">DOB</label>
        <input type="date" name="dob" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Gender</label>
        <select name="gender" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
          <option>Male</option><option>Female</option><option selected>Other</option>
        </select>
      </div>
    </div>

    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Add Student</button>
      <a class="text-gray-500" href="list.php">Cancel</a>
    </div>
  </form>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

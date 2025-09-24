<?php
require_once __DIR__ . '/../inc/header.php';
require_role(['admin','teacher']);
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: list.php"); exit; }

$stmt = $conn->prepare("SELECT st.*, u.username, u.email FROM students st JOIN users u ON st.user_id = u.user_id WHERE st.student_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$student) { echo "<div>Student not found.</div>"; include_once __DIR__ . '/../inc/footer.php'; exit; }

$errors=[]; $success='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $admission_no = trim($_POST['admission_no'] ?? '');
    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?? 'Other';

    $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE user_id=?");
    $stmt->bind_param('ssi', $name, $email, $student['user_id']);
    if ($stmt->execute()) {
        $stmt->close();
        $stmt2 = $conn->prepare("UPDATE students SET admission_no=?, dob=?, gender=? WHERE student_id=?");
        $stmt2->bind_param('sssi', $admission_no, $dob, $gender, $id);
        if ($stmt2->execute()) $success = "Updated.";
        else $errors[] = "Could not update student.";
        $stmt2->close();
    } else {
        $errors[] = "Could not update user.";
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Edit Student</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 rounded text-red-700"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if ($success): ?><div class="mb-2 p-2 bg-green-50 rounded text-green-700"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Full Name</label>
      <input name="name" value="<?= htmlspecialchars($student['username']) ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input name="email" value="<?= htmlspecialchars($student['email']) ?>" type="email" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Admission No</label>
      <input name="admission_no" value="<?= htmlspecialchars($student['admission_no']) ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">DOB</label>
        <input type="date" name="dob" value="<?= htmlspecialchars($student['dob']) ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Gender</label>
        <select name="gender" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
          <option <?= $student['gender']=='Male' ? 'selected' : '' ?>>Male</option>
          <option <?= $student['gender']=='Female' ? 'selected' : '' ?>>Female</option>
          <option <?= $student['gender']=='Other' ? 'selected' : '' ?>>Other</option>
        </select>
      </div>
    </div>

    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Save</button>
      <a class="text-gray-500" href="list.php">Cancel</a>
    </div>
  </form>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

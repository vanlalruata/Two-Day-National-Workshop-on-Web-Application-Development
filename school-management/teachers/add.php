<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$errors=[]; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $specialization = trim($_POST['subject_specialization'] ?? '');
    $password = $_POST['password'] ?? 'teacher123'; // default fallback

    if (!$name || !$email) $errors[] = "Please fill required fields.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt=$conn->prepare("INSERT INTO users (username,password,role,email) VALUES (?,?, 'teacher', ?)");
        $stmt->bind_param('sss',$name,$hash,$email);
        if($stmt->execute()){
            $user_id = $stmt->insert_id; $stmt->close();
            $stmt2=$conn->prepare("INSERT INTO teachers (user_id, subject_specialization) VALUES (?, ?)");
            $stmt2->bind_param('is',$user_id,$specialization);
            if($stmt2->execute()) $success="Teacher created.";
            else $errors[]="Could not insert teacher record.";
            $stmt2->close();
        } else { $errors[]="Could not create user (duplicate?)."; }
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Add Teacher</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 text-red-700 border rounded"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if($success): ?><div class="mb-2 p-2 bg-green-50 text-green-700 border rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Full Name</label>
      <input name="name" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Specialization</label>
      <input name="subject_specialization" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">Password</label>
      <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" placeholder="Default: teacher123">
    </div>
    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Add Teacher</button>
      <a href="list.php" class="text-gray-500">Cancel</a>
    </div>
  </form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

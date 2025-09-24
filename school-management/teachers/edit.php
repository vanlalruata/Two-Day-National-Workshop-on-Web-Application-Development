<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if(!$id){ header("Location: list.php"); exit; }

$stmt=$conn->prepare("SELECT t.*, u.username,u.email FROM teachers t JOIN users u ON t.user_id=u.user_id WHERE t.teacher_id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$teacher=$stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$teacher){ echo "<div>Teacher not found</div>"; include_once __DIR__ . '/../inc/footer.php'; exit; }

$errors=[]; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $specialization = trim($_POST['subject_specialization'] ?? '');

    $stmt=$conn->prepare("UPDATE users SET username=?, email=? WHERE user_id=?");
    $stmt->bind_param('ssi',$name,$email,$teacher['user_id']);
    if($stmt->execute()){
        $stmt->close();
        $stmt2=$conn->prepare("UPDATE teachers SET subject_specialization=? WHERE teacher_id=?");
        $stmt2->bind_param('si',$specialization,$id);
        if($stmt2->execute()) $success="Teacher updated.";
        else $errors[]="Could not update teacher record.";
        $stmt2->close();
    } else $errors[]="Could not update user.";
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Edit Teacher</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 text-red-700 border rounded"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if($success): ?><div class="mb-2 p-2 bg-green-50 text-green-700 border rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Full Name</label>
      <input name="name" value="<?= htmlspecialchars($teacher['username']) ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Specialization</label>
      <input name="subject_specialization" value="<?= htmlspecialchars($teacher['subject_specialization']) ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
    </div>
    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Save</button>
      <a href="list.php" class="text-gray-500">Cancel</a>
    </div>
  </form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

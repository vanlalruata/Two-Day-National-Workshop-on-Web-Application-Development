<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$classes = $conn->query("SELECT * FROM classes ORDER BY class_name");

$errors=[]; $success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $subject_name = trim($_POST['subject_name']??'');
    $class_id = intval($_POST['class_id']??0);
    if(!$subject_name) $errors[]="Subject name is required.";
    if(empty($errors)){
        $stmt=$conn->prepare("INSERT INTO subjects (subject_name,class_id) VALUES (?,?)");
        if($class_id){
            $stmt->bind_param('si',$subject_name,$class_id);
        } else {
            $null=null;
            $stmt->bind_param('si',$subject_name,$null);
        }
        if($stmt->execute()) $success="Subject added.";
        else $errors[]="Error adding subject.";
        $stmt->close();
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Add Subject</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 text-red-700 border rounded"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if($success): ?><div class="mb-2 p-2 bg-green-50 text-green-700 border rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Subject Name</label>
      <input name="subject_name" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Class (optional)</label>
      <select name="class_id" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
        <option value="">-- None --</option>
        <?php while($c=$classes->fetch_assoc()): ?>
          <option value="<?= $c['class_id'] ?>"><?= htmlspecialchars($c['class_name']) ?> <?= htmlspecialchars($c['section']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Save</button>
      <a href="list.php" class="text-gray-500">Cancel</a>
    </div>
  </form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$errors=[]; $success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $class_name=trim($_POST['class_name']??'');
    $section=trim($_POST['section']??'');

    if(!$class_name) $errors[]="Class name is required.";
    if(empty($errors)){
        $stmt=$conn->prepare("INSERT INTO classes (class_name, section) VALUES (?, ?)");
        $stmt->bind_param('ss',$class_name,$section);
        if($stmt->execute()) $success="Class added.";
        else $errors[]="Error adding class.";
        $stmt->close();
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Add Class</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 border text-red-700 rounded"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if($success): ?><div class="mb-2 p-2 bg-green-50 border text-green-700 rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Class Name</label>
      <input name="class_name" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Section</label>
      <input name="section" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2">
    </div>
    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Save</button>
      <a href="list.php" class="text-gray-500">Cancel</a>
    </div>
  </form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

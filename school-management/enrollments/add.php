<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

// get students & classes
$students = $conn->query("SELECT st.student_id, u.username, st.admission_no 
                          FROM students st JOIN users u ON st.user_id=u.user_id 
                          ORDER BY u.username");
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name");

$errors=[]; $success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $student_id=intval($_POST['student_id']??0);
    $class_id=intval($_POST['class_id']??0);
    $year=intval($_POST['year']??date('Y'));

    if(!$student_id||!$class_id||!$year) $errors[]="All fields required.";
    if(empty($errors)){
        $stmt=$conn->prepare("INSERT INTO enrollments (student_id,class_id,year) VALUES (?,?,?)");
        $stmt->bind_param('iii',$student_id,$class_id,$year);
        if($stmt->execute()) $success="Enrollment added.";
        else $errors[]="Error adding enrollment (maybe duplicate?).";
        $stmt->close();
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Enroll Student</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 border text-red-700 rounded"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if($success): ?><div class="mb-2 p-2 bg-green-50 border text-green-700 rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Student</label>
      <select name="student_id" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
        <option value="">-- Select --</option>
        <?php while($s=$students->fetch_assoc()): ?>
          <option value="<?= $s['student_id'] ?>"><?= htmlspecialchars($s['username']) ?> (<?= htmlspecialchars($s['admission_no']) ?>)</option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Class</label>
      <select name="class_id" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
        <option value="">-- Select --</option>
        <?php while($c=$classes->fetch_assoc()): ?>
          <option value="<?= $c['class_id'] ?>"><?= htmlspecialchars($c['class_name']) ?> <?= htmlspecialchars($c['section']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Year</label>
      <input type="number" name="year" value="<?= date('Y') ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Enroll</button>
      <a href="list.php" class="text-gray-500">Cancel</a>
    </div>
  </form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

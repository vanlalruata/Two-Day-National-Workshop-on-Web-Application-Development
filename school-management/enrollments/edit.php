<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$id=intval($_GET['id']??0);
if(!$id){ header("Location:list.php"); exit; }

$stmt=$conn->prepare("SELECT * FROM enrollments WHERE enrollment_id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$en=$stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$en){ echo "<div>Enrollment not found</div>"; include_once __DIR__.'/../inc/footer.php'; exit; }

$students = $conn->query("SELECT st.student_id, u.username, st.admission_no FROM students st JOIN users u ON st.user_id=u.user_id ORDER BY u.username");
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name");

$errors=[]; $success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $student_id=intval($_POST['student_id']??0);
    $class_id=intval($_POST['class_id']??0);
    $year=intval($_POST['year']??date('Y'));
    if(!$student_id||!$class_id||!$year) $errors[]="All fields required.";
    if(empty($errors)){
        $stmt=$conn->prepare("UPDATE enrollments SET student_id=?, class_id=?, year=? WHERE enrollment_id=?");
        $stmt->bind_param('iiii',$student_id,$class_id,$year,$id);
        if($stmt->execute()) $success="Enrollment updated.";
        else $errors[]="Error updating enrollment.";
        $stmt->close();
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Edit Enrollment</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 border text-red-700 rounded"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if($success): ?><div class="mb-2 p-2 bg-green-50 border text-green-700 rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Student</label>
      <select name="student_id" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
        <?php while($s=$students->fetch_assoc()): ?>
          <option value="<?= $s['student_id'] ?>" <?= $en['student_id']==$s['student_id']?'selected':'' ?>>
            <?= htmlspecialchars($s['username']) ?> (<?= htmlspecialchars($s['admission_no']) ?>)
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Class</label>
      <select name="class_id" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
        <?php while($c=$classes->fetch_assoc()): ?>
          <option value="<?= $c['class_id'] ?>" <?= $en['class_id']==$c['class_id']?'selected':'' ?>>
            <?= htmlspecialchars($c['class_name']) ?> <?= htmlspecialchars($c['section']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Year</label>
      <input type="number" name="year" value="<?= htmlspecialchars($en['year']) ?>" class="mt-1 block w-full rounded-md border-gray-200 px-3 py-2" required>
    </div>
    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Update</button>
      <a href="list.php" class="text-gray-500">Cancel</a>
    </div>
  </form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

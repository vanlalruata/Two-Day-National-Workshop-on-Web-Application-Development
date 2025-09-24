<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$classes=$conn->query("SELECT * FROM classes ORDER BY class_name");
$subjects=$conn->query("SELECT sb.subject_id, sb.subject_name, c.class_name, c.section
                        FROM subjects sb LEFT JOIN classes c ON sb.class_id=c.class_id
                        ORDER BY sb.subject_name");

$errors=[]; $success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $class_id=intval($_POST['class_id']??0);
    $subject_id=intval($_POST['subject_id']??0);
    $exam_date=$_POST['exam_date']??'';

    if(!$class_id||!$subject_id||!$exam_date) $errors[]="All fields required.";
    if(empty($errors)){
        $stmt=$conn->prepare("INSERT INTO exams (class_id,subject_id,exam_date) VALUES (?,?,?)");
        $stmt->bind_param('iis',$class_id,$subject_id,$exam_date);
        if($stmt->execute()) $success="Exam created.";
        else $errors[]="Error creating exam.";
        $stmt->close();
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Add Exam</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 text-red-700 border rounded"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if($success): ?><div class="mb-2 p-2 bg-green-50 text-green-700 border rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Class</label>
      <select name="class_id" class="mt-1 block w-full rounded border-gray-200 px-3 py-2" required>
        <option value="">-- Select --</option>
        <?php while($c=$classes->fetch_assoc()): ?>
          <option value="<?= $c['class_id'] ?>"><?= htmlspecialchars($c['class_name']).' '.htmlspecialchars($c['section']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Subject</label>
      <select name="subject_id" class="mt-1 block w-full rounded border-gray-200 px-3 py-2" required>
        <option value="">-- Select --</option>
        <?php while($s=$subjects->fetch_assoc()): ?>
          <option value="<?= $s['subject_id'] ?>"><?= htmlspecialchars($s['subject_name']) ?> (<?= htmlspecialchars($s['class_name'].' '.$s['section']) ?>)</option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Exam Date</label>
      <input type="date" name="exam_date" class="mt-1 block w-full rounded border-gray-200 px-3 py-2" required>
    </div>
    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Save</button>
      <a href="list.php" class="text-gray-500">Cancel</a>
    </div>
  </form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

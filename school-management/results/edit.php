<?php
require_once __DIR__ . '/../inc/header.php';
require_role(['admin','teacher']);

$id=intval($_GET['id']??0);
if(!$id){ header("Location:list.php"); exit; }

$stmt=$conn->prepare("SELECT * FROM results WHERE result_id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$result=$stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$result){ echo "<div>Result not found</div>"; include_once __DIR__.'/../inc/footer.php'; exit; }

$exams=$conn->query("SELECT e.exam_id, e.exam_date, sb.subject_name, c.class_name, c.section
                     FROM exams e
                     JOIN subjects sb ON e.subject_id=sb.subject_id
                     JOIN classes c ON e.class_id=c.class_id
                     ORDER BY e.exam_date DESC");
$students=$conn->query("SELECT st.student_id, u.username, st.admission_no
                        FROM students st JOIN users u ON st.user_id=u.user_id
                        ORDER BY u.username");

$errors=[]; $success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $exam_id=intval($_POST['exam_id']??0);
    $student_id=intval($_POST['student_id']??0);
    $marks=floatval($_POST['marks_obtained']??0);
    $grade=trim($_POST['grade']??'');
    if(!$exam_id||!$student_id) $errors[]="Exam and student required.";
    if(empty($errors)){
        $stmt=$conn->prepare("UPDATE results SET exam_id=?, student_id=?, marks_obtained=?, grade=? WHERE result_id=?");
        $stmt->bind_param('iidsi',$exam_id,$student_id,$marks,$grade,$id);
        if($stmt->execute()) $success="Result updated.";
        else $errors[]="Error updating result.";
        $stmt->close();
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Edit Result</h3>
  <?php foreach($errors as $e): ?><div class="mb-2 p-2 bg-red-50 border text-red-700 rounded"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  <?php if($success): ?><div class="mb-2 p-2 bg-green-50 border text-green-700 rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Exam</label>
      <select name="exam_id" class="mt-1 block w-full rounded border-gray-200 px-3 py-2" required>
        <?php while($e=$exams->fetch_assoc()): ?>
          <option value="<?= $e['exam_id'] ?>" <?= $result['exam_id']==$e['exam_id']?'selected':'' ?>>
            <?= htmlspecialchars($e['subject_name']).' ('.$e['class_name'].' '.$e['section'].' - '.$e['exam_date'].')' ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Student</label>
      <select name="student_id" class="mt-1 block w-full rounded border-gray-200 px-3 py-2" required>
        <?php while($s=$students->fetch_assoc()): ?>
          <option value="<?= $s['student_id'] ?>" <?= $result['student_id']==$s['student_id']?'selected':'' ?>>
            <?= htmlspecialchars($s['username']).' ('.$s['admission_no'].')' ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Marks Obtained</label>
      <input type="number" step="0.01" name="marks_obtained" value="<?= htmlspecialchars($result['marks_obtained']) ?>" class="mt-1 block w-full rounded border-gray-200 px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">Grade</label>
      <input name="grade" value="<?= htmlspecialchars($result['grade']) ?>" class="mt-1 block w-full rounded border-gray-200 px-3 py-2">
    </div>
    <div class="flex gap-3">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Update</button>
      <a href="list.php" class="text-gray-500">Cancel</a>
    </div>
  </form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

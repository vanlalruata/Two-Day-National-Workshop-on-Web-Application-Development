<?php
// exams/edit.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: list.php"); exit; }

// fetch exam
$stmt = $conn->prepare("SELECT * FROM exams WHERE exam_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$exam) { echo "Exam not found."; include_once __DIR__ . '/../inc/footer.php'; exit; }

// fetch classes & subjects
$classes = $conn->query("SELECT class_id, class_name, section FROM classes ORDER BY class_name");
$subjects = $conn->query("SELECT subject_id, subject_name, class_id FROM subjects ORDER BY subject_name");

$errors = []; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = intval($_POST['class_id'] ?? 0);
    $subject_id = intval($_POST['subject_id'] ?? 0);
    $exam_date = $_POST['exam_date'] ?? '';

    if (!$class_id) $errors[] = "Select class.";
    if (!$subject_id) $errors[] = "Select subject.";
    if (!$exam_date) $errors[] = "Select date.";

    if (empty($errors)) {
        $stmt2 = $conn->prepare("UPDATE exams SET class_id=?, subject_id=?, exam_date=? WHERE exam_id=?");
        $stmt2->bind_param('iisi', $class_id, $subject_id, $exam_date, $id);
        if ($stmt2->execute()) {
            $success = "Exam updated.";
            // refresh exam
            $stmt2->close();
            $stmtR = $conn->prepare("SELECT * FROM exams WHERE exam_id=?");
            $stmtR->bind_param('i',$id);
            $stmtR->execute();
            $exam = $stmtR->get_result()->fetch_assoc();
            $stmtR->close();
        } else {
            $errors[] = "Could not update exam.";
        }
    }
}
?>

<div class="row">
  <div class="col-md-6">
    <h4>Edit Exam</h4>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label>Class</label>
        <select name="class_id" id="class_select" class="form-control" required>
          <option value="">-- Select Class --</option>
          <?php while($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['class_id'] ?>" <?= ($exam['class_id']==$c['class_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['class_name']) ?><?= $c['section'] ? ' - '.htmlspecialchars($c['section']) : '' ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label>Subject</label>
        <select name="subject_id" id="subject_select" class="form-control" required>
          <option value="">-- Select Subject --</option>
          <?php
            $subjects->data_seek(0);
            while($s = $subjects->fetch_assoc()): ?>
              <option value="<?= $s['subject_id'] ?>" data-class="<?= $s['class_id'] ?>" <?= ($exam['subject_id']==$s['subject_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['subject_name']) ?>
              </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label>Exam Date</label>
        <input type="date" name="exam_date" class="form-control" value="<?= htmlspecialchars($exam['exam_date']) ?>" required>
      </div>

      <button class="btn btn-primary">Update Exam</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>
  </div>
</div>

<script>
  // same client-side subject filter as add.php
  (function(){
    const classSelect = document.getElementById('class_select');
    const subjectSelect = document.getElementById('subject_select');
    const options = Array.from(subjectSelect.options).map(o => ({value:o.value, text:o.text, cls:o.dataset.class}));

    function filterSubjects(){
      const cls = classSelect.value;
      subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
      options.forEach(op => {
        if (!cls || op.cls === cls) {
          const opt = document.createElement('option');
          opt.value = op.value; opt.text = op.text; opt.dataset.class = op.cls;
          subjectSelect.appendChild(opt);
        }
      });
      // restore selected if present
      const selected = '<?= intval($exam['subject_id']) ?>';
      if (selected) subjectSelect.value = selected;
    }
    classSelect.addEventListener('change', filterSubjects);
    window.addEventListener('DOMContentLoaded', filterSubjects);
  })();
</script>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

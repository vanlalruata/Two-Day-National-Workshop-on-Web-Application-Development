<?php
// exams/add.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

// fetch classes and subjects
$classes = $conn->query("SELECT class_id, class_name, section FROM classes ORDER BY class_name");
$subjects = $conn->query("SELECT subject_id, subject_name, class_id FROM subjects ORDER BY subject_name");

$errors = []; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = intval($_POST['class_id'] ?? 0);
    $subject_id = intval($_POST['subject_id'] ?? 0);
    $exam_date = $_POST['exam_date'] ?? '';

    if (!$class_id) $errors[] = "Select class.";
    if (!$subject_id) $errors[] = "Select subject.";
    if (!$exam_date) $errors[] = "Select exam date.";

    if (empty($errors)) {
        // prevent duplicate exam for same class-subject-date
        $stmt = $conn->prepare("SELECT exam_id FROM exams WHERE class_id=? AND subject_id=? AND exam_date=? LIMIT 1");
        $stmt->bind_param('iis', $class_id, $subject_id, $exam_date);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($exists) {
            $errors[] = "An exam already exists for this class, subject and date.";
        } else {
            $stmt2 = $conn->prepare("INSERT INTO exams (class_id, subject_id, exam_date) VALUES (?,?,?)");
            $stmt2->bind_param('iis', $class_id, $subject_id, $exam_date);
            if ($stmt2->execute()) {
                $success = "Exam created.";
            } else {
                $errors[] = "Could not create exam.";
            }
            $stmt2->close();
        }
    }
}
?>

<div class="row">
  <div class="col-md-6">
    <h4>Add Exam</h4>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Class</label>
        <select name="class_id" id="class_select" class="form-control" required>
          <option value="">-- Select Class --</option>
          <?php while($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['class_id'] ?>" <?= (isset($_POST['class_id']) && $_POST['class_id']==$c['class_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['class_name']) ?><?= $c['section'] ? ' - '.htmlspecialchars($c['section']) : '' ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Subject</label>
        <select name="subject_id" id="subject_select" class="form-control" required>
          <option value="">-- Select Subject --</option>
          <?php
            // subjects will be filtered client-side by class via data-class attribute
            $subjects->data_seek(0);
            while($s = $subjects->fetch_assoc()): ?>
              <option value="<?= $s['subject_id'] ?>" data-class="<?= $s['class_id'] ?>" <?= (isset($_POST['subject_id']) && $_POST['subject_id']==$s['subject_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['subject_name']) ?>
              </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Exam Date</label>
        <input type="date" name="exam_date" class="form-control" value="<?= htmlspecialchars($_POST['exam_date'] ?? '') ?>" required>
      </div>

      <button class="btn btn-primary">Create Exam</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>
  </div>
</div>

<script>
  // client-side filter for subjects by class
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
    }
    classSelect.addEventListener('change', filterSubjects);
    // initial filter (preserve POST selection if any)
    window.addEventListener('DOMContentLoaded', () => {
      const selectedClass = '<?= intval($_POST['class_id'] ?? 0) ?>';
      if (selectedClass) classSelect.value = selectedClass;
      const selectedSubject = '<?= intval($_POST['subject_id'] ?? 0) ?>';
      filterSubjects();
      if (selectedSubject) subjectSelect.value = selectedSubject;
    });
  })();
</script>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

<?php
// results/add.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$errors = []; $success = '';
$exam_id = intval($_GET['exam_id'] ?? ($_POST['exam_id'] ?? 0));

// If exam_id provided, fetch exam + enrolled students for that class & year
$exam = null; $students = null;
if ($exam_id) {
    $stmt = $conn->prepare("SELECT ex.*, c.class_name, c.section, sb.subject_name
                            FROM exams ex
                            LEFT JOIN classes c ON ex.class_id = c.class_id
                            LEFT JOIN subjects sb ON ex.subject_id = sb.subject_id
                            WHERE ex.exam_id = ?");
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $exam = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($exam) {
        $year = intval(date('Y', strtotime($exam['exam_date'])));
        // fetch students enrolled in that class for the exam year
        $stmt2 = $conn->prepare("SELECT st.student_id, u.username, st.admission_no
                                 FROM enrollments e
                                 JOIN students st ON e.student_id = st.student_id
                                 JOIN users u ON st.user_id = u.user_id
                                 WHERE e.class_id = ? AND e.year = ?
                                 ORDER BY u.username");
        $stmt2->bind_param('ii', $exam['class_id'], $year);
        $stmt2->execute();
        $students = $stmt2->get_result();
        $stmt2->close();
    } else {
        $errors[] = "Exam not found.";
    }
}

// grade calculator helper
function calc_grade($marks) {
    if ($marks >= 90) return 'A';
    if ($marks >= 75) return 'B';
    if ($marks >= 60) return 'C';
    if ($marks >= 50) return 'D';
    return 'F';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_id = intval($_POST['exam_id'] ?? 0);
    $student_id = intval($_POST['student_id'] ?? 0);
    $marks = floatval($_POST['marks_obtained'] ?? 0);

    if (!$exam_id) $errors[] = "Exam is required.";
    if (!$student_id) $errors[] = "Student is required.";

    if (empty($errors)) {
        // prevent duplicate result for same exam-student
        $stmt = $conn->prepare("SELECT result_id FROM results WHERE exam_id=? AND student_id=? LIMIT 1");
        $stmt->bind_param('ii', $exam_id, $student_id);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($exists) {
            $errors[] = "Result for this student and exam already exists. Use edit instead.";
        } else {
            $grade = calc_grade($marks);
            $stmt2 = $conn->prepare("INSERT INTO results (exam_id, student_id, marks_obtained, grade) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param('iids', $exam_id, $student_id, $marks, $grade);
            if ($stmt2->execute()) {
                $success = "Result recorded.";
            } else {
                $errors[] = "Could not save result.";
            }
            $stmt2->close();
        }
    }
}
?>

<div class="row">
  <div class="col-md-8">
    <h4>Add Result</h4>

    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="post">
      <input type="hidden" name="exam_id" value="<?= htmlspecialchars($exam_id) ?>">

      <div class="mb-3">
        <label class="form-label">Exam</label>
        <?php if ($exam): ?>
          <div class="form-control"><?= htmlspecialchars($exam['subject_name']) ?> — <?= htmlspecialchars($exam['class_name']) ?><?= $exam['section'] ? ' - '.htmlspecialchars($exam['section']) : '' ?> (<?= htmlspecialchars($exam['exam_date']) ?>)</div>
        <?php else: ?>
          <select name="exam_id" class="form-control" required onchange="if(this.value) window.location='add.php?exam_id='+this.value;">
            <option value="">-- Select Exam (or provide exam_id in URL) --</option>
            <?php
              $exlist = $conn->query("SELECT ex.exam_id, ex.exam_date, c.class_name, c.section, sb.subject_name FROM exams ex LEFT JOIN classes c ON ex.class_id=c.class_id LEFT JOIN subjects sb ON ex.subject_id=sb.subject_id ORDER BY ex.exam_date DESC");
              while($e = $exlist->fetch_assoc()):
            ?>
              <option value="<?= $e['exam_id'] ?>"><?= htmlspecialchars($e['subject_name']) ?> — <?= htmlspecialchars($e['class_name']) ?><?= $e['section'] ? ' - '.htmlspecialchars($e['section']) : '' ?> (<?= $e['exam_date'] ?>)</option>
            <?php endwhile; ?>
          </select>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Student</label>
        <?php if ($students): ?>
          <select name="student_id" class="form-control" required>
            <option value="">-- Select Student --</option>
            <?php while($st = $students->fetch_assoc()): ?>
              <option value="<?= $st['student_id'] ?>"><?= htmlspecialchars($st['username']) ?> (<?= htmlspecialchars($st['admission_no']) ?>)</option>
            <?php endwhile; ?>
          </select>
        <?php else: ?>
          <select name="student_id" class="form-control" required>
            <option value="">-- Select Student --</option>
            <?php
              // fallback: list all students if no exam selected or no enrollment mapping
              $all = $conn->query("SELECT st.student_id, u.username, st.admission_no FROM students st JOIN users u ON st.user_id=u.user_id ORDER BY u.username");
              while($a = $all->fetch_assoc()):
            ?>
              <option value="<?= $a['student_id'] ?>"><?= htmlspecialchars($a['username']) ?> (<?= htmlspecialchars($a['admission_no']) ?>)</option>
            <?php endwhile; ?>
          </select>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Marks Obtained</label>
        <input type="number" step="0.01" name="marks_obtained" class="form-control" required>
      </div>

      <button class="btn btn-primary">Save Result</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

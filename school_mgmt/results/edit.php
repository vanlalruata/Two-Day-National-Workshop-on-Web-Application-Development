<?php
// results/edit.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: list.php"); exit; }

// fetch result + related info
$stmt = $conn->prepare("SELECT r.*, ex.exam_date, ex.class_id, ex.subject_id, c.class_name, c.section, sb.subject_name, st.student_id, u.username AS student_name, st.admission_no
                        FROM results r
                        JOIN exams ex ON r.exam_id = ex.exam_id
                        LEFT JOIN classes c ON ex.class_id = c.class_id
                        LEFT JOIN subjects sb ON ex.subject_id = sb.subject_id
                        JOIN students st ON r.student_id = st.student_id
                        JOIN users u ON st.user_id = u.user_id
                        WHERE r.result_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$row) { echo "Result not found."; include_once __DIR__ . '/../inc/footer.php'; exit; }

$errors = []; $success = '';
function calc_grade($marks) {
    if ($marks >= 90) return 'A';
    if ($marks >= 75) return 'B';
    if ($marks >= 60) return 'C';
    if ($marks >= 50) return 'D';
    return 'F';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marks = floatval($_POST['marks_obtained'] ?? 0);
    $grade = calc_grade($marks);

    $stmt2 = $conn->prepare("UPDATE results SET marks_obtained = ?, grade = ? WHERE result_id = ?");
    $stmt2->bind_param('dsi', $marks, $grade, $id);
    if ($stmt2->execute()) {
        $success = "Updated.";
        // refresh row
        $stmt2->close();
        $stmtR = $conn->prepare("SELECT * FROM results WHERE result_id = ?");
        $stmtR->bind_param('i', $id);
        $stmtR->execute();
        $row = $stmtR->get_result()->fetch_assoc();
        $stmtR->close();
    } else {
        $errors[] = "Could not update result.";
    }
}
?>

<div class="row">
  <div class="col-md-8">
    <h4>Edit Result</h4>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Exam</label>
      <div class="form-control"><?= htmlspecialchars($row['subject_name']) ?> — <?= htmlspecialchars($row['class_name']) ?><?= $row['section'] ? ' - '.htmlspecialchars($row['section']) : '' ?> (<?= htmlspecialchars($row['exam_date']) ?>)</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Student</label>
      <div class="form-control"><?= htmlspecialchars($row['student_name']) ?> (<?= htmlspecialchars($row['admission_no']) ?>)</div>
    </div>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Marks Obtained</label>
        <input type="number" step="0.01" name="marks_obtained" class="form-control" value="<?= htmlspecialchars($row['marks_obtained']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Grade</label>
        <input class="form-control" value="<?= htmlspecialchars($row['grade']) ?>" disabled>
      </div>

      <button class="btn btn-primary">Save</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

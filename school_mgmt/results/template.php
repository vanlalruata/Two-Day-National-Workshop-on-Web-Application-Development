<?php
// results/template.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$exam_id = intval($_GET['exam_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$exam_id) {
    $exam_id = intval($_POST['exam_id'] ?? 0);
}

if (!$exam_id) {
    // show simple selector
    $exams = $conn->query("SELECT ex.exam_id, ex.exam_date, c.class_name, c.section, sb.subject_name 
                           FROM exams ex 
                           LEFT JOIN classes c ON ex.class_id=c.class_id
                           LEFT JOIN subjects sb ON ex.subject_id=sb.subject_id
                           ORDER BY ex.exam_date DESC");
    include_once __DIR__ . '/../inc/footer.php';
    ?>
    <div class="row">
      <div class="col-md-6">
        <h4>Download Results Template</h4>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Exam</label>
            <select name="exam_id" class="form-control" required>
              <option value="">-- Select Exam --</option>
              <?php while($e = $exams->fetch_assoc()): ?>
                <option value="<?= $e['exam_id'] ?>"><?= htmlspecialchars($e['subject_name']) ?> — <?= htmlspecialchars($e['class_name']) ?> <?= $e['section'] ? '- '.htmlspecialchars($e['section']) : '' ?> (<?= $e['exam_date'] ?>)</option>
              <?php endwhile; ?>
            </select>
          </div>
          <button class="btn btn-primary">Download Template</button>
          <a class="btn btn-secondary" href="list.php">Back</a>
        </form>
      </div>
    </div>
    <?php
    exit;
}

// fetch exam details
$stmt = $conn->prepare("SELECT ex.*, c.class_name, c.section FROM exams ex LEFT JOIN classes c ON ex.class_id = c.class_id WHERE ex.exam_id = ? LIMIT 1");
$stmt->bind_param('i', $exam_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$exam) {
    echo "<div class='alert alert-danger'>Exam not found.</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

// fetch enrolled students for the exam year
$year = intval(date('Y', strtotime($exam['exam_date'])));
$stmt = $conn->prepare("SELECT st.student_id, u.username, st.admission_no FROM enrollments e JOIN students st ON e.student_id = st.student_id JOIN users u ON st.user_id = u.user_id WHERE e.class_id = ? AND e.year = ? ORDER BY u.username");
$stmt->bind_param('ii', $exam['class_id'], $year);
$stmt->execute();
$students = $stmt->get_result();
$stmt->close();

// fallback: if no enrollments, list all students
if ($students->num_rows === 0) {
    $students = $conn->query("SELECT st.student_id, u.username, st.admission_no FROM students st JOIN users u ON st.user_id = u.user_id ORDER BY u.username");
}

// produce CSV
$filename = "results_template_exam_{$exam_id}.csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');

$output = fopen('php://output', 'w');
fputcsv($output, ['student_id','admission_no','marks']);

while ($r = $students->fetch_assoc()) {
    $sid = $r['student_id'];
    $adm = $r['admission_no'] ?? '';
    // leave marks blank
    fputcsv($output, [$sid, $adm, '']);
}
fclose($output);
exit;

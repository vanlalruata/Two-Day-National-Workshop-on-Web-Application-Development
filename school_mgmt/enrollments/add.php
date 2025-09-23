<?php
// enrollments/add.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

$errors = [];
$success = '';

// fetch students and classes for dropdowns
$students_q = $conn->query("SELECT s.student_id, u.username, s.admission_no FROM students s JOIN users u ON s.user_id = u.user_id ORDER BY u.username ASC");
$classes_q = $conn->query("SELECT class_id, class_name, section FROM classes ORDER BY class_name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id'] ?? 0);
    $class_id = intval($_POST['class_id'] ?? 0);
    $year = intval($_POST['year'] ?? date('Y'));

    if (!$student_id) $errors[] = "Select a student.";
    if (!$class_id) $errors[] = "Select a class.";
    if (!$year || $year < 1900) $errors[] = "Provide a valid year.";

    if (empty($errors)) {
        // Prevent duplicate enrollment for same student-class-year
        $stmt = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE student_id=? AND class_id=? AND year=? LIMIT 1");
        $stmt->bind_param('iii', $student_id, $class_id, $year);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($exists) {
            $errors[] = "This student is already enrolled in the selected class for the year $year.";
        } else {
            $stmt2 = $conn->prepare("INSERT INTO enrollments (student_id, class_id, year) VALUES (?, ?, ?)");
            $stmt2->bind_param('iii', $student_id, $class_id, $year);
            if ($stmt2->execute()) {
                $success = "Enrollment added.";
            } else {
                $errors[] = "Could not add enrollment.";
            }
            $stmt2->close();
        }
    }
}
?>

<div class="row">
  <div class="col-md-6">
    <h4>Add Enrollment</h4>

    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Student</label>
        <select name="student_id" class="form-control" required>
          <option value="">-- Select Student --</option>
          <?php
            // reset pointer if used earlier
            $students_q->data_seek(0);
            while ($st = $students_q->fetch_assoc()):
          ?>
            <option value="<?= $st['student_id'] ?>" <?= (isset($_POST['student_id']) && $_POST['student_id']==$st['student_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($st['username']) ?> (<?= htmlspecialchars($st['admission_no']) ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Class</label>
        <select name="class_id" class="form-control" required>
          <option value="">-- Select Class --</option>
          <?php
            $classes_q->data_seek(0);
            while ($c = $classes_q->fetch_assoc()):
          ?>
            <option value="<?= $c['class_id'] ?>" <?= (isset($_POST['class_id']) && $_POST['class_id']==$c['class_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['class_name']) ?><?= $c['section'] ? ' - '.htmlspecialchars($c['section']) : '' ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Year</label>
        <input type="number" name="year" class="form-control" value="<?= htmlspecialchars($_POST['year'] ?? date('Y')) ?>" required>
      </div>

      <button class="btn btn-primary">Add Enrollment</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

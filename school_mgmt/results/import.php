<?php
// results/import.php
require_once __DIR__ . '/../inc/header.php';
require_login();
if (!in_array($_SESSION['role'], ['admin','teacher'])) {
    echo "<div class='alert alert-danger'>Forbidden</div>";
    include_once __DIR__ . '/../inc/footer.php';
    exit;
}

function calc_grade($marks) {
    if ($marks >= 90) return 'A';
    if ($marks >= 75) return 'B';
    if ($marks >= 60) return 'C';
    if ($marks >= 50) return 'D';
    return 'F';
}

$exams = $conn->query("SELECT ex.exam_id, ex.exam_date, c.class_name, c.section, sb.subject_name 
                       FROM exams ex 
                       LEFT JOIN classes c ON ex.class_id=c.class_id
                       LEFT JOIN subjects sb ON ex.subject_id=sb.subject_id
                       ORDER BY ex.exam_date DESC");

$errors = [];
$report = [
    'processed' => 0,
    'inserted' => 0,
    'skipped_existing' => 0,
    'failed' => 0,
    'details' => []  // per-row details
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_id = intval($_POST['exam_id'] ?? 0);
    if (!$exam_id) $errors[] = "Please select an exam.";

    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Please upload a valid CSV file.";
    }

    if (empty($errors)) {
        // open uploaded file
        $tmp = $_FILES['csv_file']['tmp_name'];
        if (($handle = fopen($tmp, 'r')) !== false) {
            // Read header
            $header = fgetcsv($handle);
            if ($header === false) {
                $errors[] = "CSV file appears empty.";
            } else {
                // normalize headers: trim + lowercase
                $hdr = array_map(function($h){ return strtolower(trim($h)); }, $header);
                // detect format: either contains 'student_id' OR 'admission_no', and 'marks' OR 'marks_obtained'
                $has_student_id = in_array('student_id', $hdr);
                $has_adm = in_array('admission_no', $hdr) || in_array('admissionno', $hdr) || in_array('admission no', $hdr);
                $has_marks = in_array('marks', $hdr) || in_array('marks_obtained', $hdr) || in_array('score', $hdr);

                if ((!$has_student_id && !$has_adm) || !$has_marks) {
                    $errors[] = "CSV header missing required columns. Expected headers: student_id or admission_no; and marks (or marks_obtained).";
                } else {
                    // map column indexes
                    $colIndex = [];
                    foreach ($hdr as $i => $h) {
                        if ($h === 'student_id') $colIndex['student_id'] = $i;
                        if ($h === 'admission_no' || $h === 'admissionno' || $h === 'admission no') $colIndex['admission_no'] = $i;
                        if ($h === 'marks' || $h === 'marks_obtained' || $h === 'score') $colIndex['marks'] = $i;
                    }

                    // Prepare statements outside loop
                    $stmt_check_result = $conn->prepare("SELECT result_id FROM results WHERE exam_id=? AND student_id=? LIMIT 1");
                    $stmt_get_student_by_adm = $conn->prepare("SELECT student_id FROM students WHERE admission_no = ? LIMIT 1");
                    $stmt_insert = $conn->prepare("INSERT INTO results (exam_id, student_id, marks_obtained, grade) VALUES (?, ?, ?, ?)");

                    // Begin transaction
                    $conn->begin_transaction();
                    $lineNo = 1; // header was line 1
                    while (($row = fgetcsv($handle)) !== false) {
                        $lineNo++;
                        $report['processed']++;
                        // read fields
                        $student_id = null;
                        if (isset($colIndex['student_id'])) {
                            $student_id = intval($row[$colIndex['student_id']]);
                        } elseif (isset($colIndex['admission_no'])) {
                            $adm = trim($row[$colIndex['admission_no']]);
                            // find student_id by admission_no
                            $stmt_get_student_by_adm->bind_param('s', $adm);
                            $stmt_get_student_by_adm->execute();
                            $res_st = $stmt_get_student_by_adm->get_result()->fetch_assoc();
                            if ($res_st) $student_id = intval($res_st['student_id']);
                            else $student_id = 0;
                        }

                        $marks = isset($colIndex['marks']) ? trim($row[$colIndex['marks']]) : null;
                        $marks = $marks === '' ? null : (float) $marks;

                        // validate
                        if (!$student_id || $marks === null || !is_numeric($marks)) {
                            $report['failed']++;
                            $report['details'][] = [
                                'line' => $lineNo,
                                'status' => 'failed',
                                'reason' => !$student_id ? 'Student not found' : 'Invalid marks',
                                'row' => $row
                            ];
                            continue;
                        }

                        // check existing result
                        $stmt_check_result->bind_param('ii', $exam_id, $student_id);
                        $stmt_check_result->execute();
                        $exists = $stmt_check_result->get_result()->fetch_assoc();
                        if ($exists) {
                            $report['skipped_existing']++;
                            $report['details'][] = [
                                'line' => $lineNo,
                                'status' => 'skipped',
                                'reason' => 'result exists',
                                'student_id' => $student_id
                            ];
                            continue;
                        }

                        // insert
                        $grade = calc_grade($marks);
                        $stmt_insert->bind_param('iids', $exam_id, $student_id, $marks, $grade);
                        $ok = $stmt_insert->execute();
                        if ($ok) {
                            $report['inserted']++;
                            $report['details'][] = [
                                'line' => $lineNo,
                                'status' => 'inserted',
                                'student_id' => $student_id,
                                'marks' => $marks,
                                'grade' => $grade
                            ];
                        } else {
                            $report['failed']++;
                            $report['details'][] = [
                                'line' => $lineNo,
                                'status' => 'failed',
                                'reason' => 'db error: '.$stmt_insert->error,
                                'row' => $row
                            ];
                        }
                    } // end while rows

                    // commit
                    $conn->commit();

                    // close statements
                    $stmt_check_result->close();
                    $stmt_get_student_by_adm->close();
                    $stmt_insert->close();

                } // end header ok
            } // end header read
            fclose($handle);
        } else {
            $errors[] = "Could not open uploaded CSV.";
        }
    } // end no errors
}
?>

<div class="row">
  <div class="col-md-8">
    <h4>Import Results (CSV)</h4>

    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>

    <?php if (!empty($report['processed'])): ?>
      <div class="alert alert-info">
        Processed: <?= $report['processed'] ?> — Inserted: <?= $report['inserted'] ?> — Skipped (existing): <?= $report['skipped_existing'] ?> — Failed: <?= $report['failed'] ?>
      </div>

      <details>
        <summary>Show per-row details (<?= count($report['details']) ?>)</summary>
        <table class="table table-sm table-bordered mt-2">
          <thead><tr><th>Line</th><th>Status</th><th>Info</th></tr></thead>
          <tbody>
            <?php foreach($report['details'] as $d): ?>
              <tr>
                <td><?= htmlspecialchars($d['line']) ?></td>
                <td><?= htmlspecialchars($d['status']) ?></td>
                <td>
                  <?php
                    if (isset($d['reason'])) echo htmlspecialchars($d['reason']).'<br/>';
                    if (isset($d['student_id'])) echo 'Student ID: '.htmlspecialchars($d['student_id']).'<br/>';
                    if (isset($d['marks'])) echo 'Marks: '.htmlspecialchars($d['marks']).' Grade: '.htmlspecialchars($d['grade']).'<br/>';
                    if (isset($d['row'])) echo 'Row: '.htmlspecialchars(implode(',', $d['row']));
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </details>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Exam</label>
        <select name="exam_id" class="form-control" required>
          <option value="">-- Select Exam --</option>
          <?php while($e = $exams->fetch_assoc()): ?>
            <option value="<?= $e['exam_id'] ?>" <?= (isset($exam_id) && $exam_id==$e['exam_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($e['subject_name']) ?> — <?= htmlspecialchars($e['class_name']) ?><?= $e['section'] ? ' - '.htmlspecialchars($e['section']) : '' ?> (<?= $e['exam_date'] ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">CSV file</label>
        <input type="file" name="csv_file" accept=".csv,text/csv" class="form-control" required>
        <small class="form-text text-muted">CSV header must contain <strong>student_id OR admission_no</strong> and <strong>marks</strong>.</small>
      </div>

      <button class="btn btn-primary">Upload & Import</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>

    <hr>
    <h6>Example CSV formats</h6>
    <pre>
# Format A (student_id)
student_id,marks
101,78.5
102,91
...

# Format B (admission_no)
admission_no,marks
ADM2023-001,78.5
ADM2023-002,91
    </pre>

  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

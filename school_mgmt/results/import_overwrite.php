<?php
// results/import_overwrite.php
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
$report = ['processed'=>0,'inserted'=>0,'updated'=>0,'skipped'=>0,'failed'=>0,'details'=>[]];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_id = intval($_POST['exam_id'] ?? 0);
    $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] == '1';

    if (!$exam_id) $errors[] = "Please select an exam.";
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) $errors[] = "Please upload a valid CSV.";

    if (empty($errors)) {
        $tmp = $_FILES['csv_file']['tmp_name'];
        if (($handle = fopen($tmp, 'r')) !== false) {
            $header = fgetcsv($handle);
            if ($header === false) $errors[] = "CSV appears empty.";
            else {
                $hdr = array_map(function($h){ return strtolower(trim($h)); }, $header);
                $has_student_id = in_array('student_id', $hdr);
                $has_adm = in_array('admission_no', $hdr) || in_array('admissionno', $hdr);
                $has_marks = in_array('marks', $hdr) || in_array('marks_obtained', $hdr);

                if ((!$has_student_id && !$has_adm) || !$has_marks) {
                    $errors[] = "CSV header missing required columns.";
                } else {
                    $colIndex = [];
                    foreach ($hdr as $i => $h) {
                        if ($h === 'student_id') $colIndex['student_id'] = $i;
                        if ($h === 'admission_no' || $h === 'admissionno') $colIndex['admission_no'] = $i;
                        if ($h === 'marks' || $h === 'marks_obtained') $colIndex['marks'] = $i;
                    }

                    $stmt_check = $conn->prepare("SELECT result_id FROM results WHERE exam_id=? AND student_id=? LIMIT 1");
                    $stmt_get_by_adm = $conn->prepare("SELECT student_id FROM students WHERE admission_no = ? LIMIT 1");
                    $stmt_insert = $conn->prepare("INSERT INTO results (exam_id, student_id, marks_obtained, grade) VALUES (?, ?, ?, ?)");
                    $stmt_update = $conn->prepare("UPDATE results SET marks_obtained = ?, grade = ? WHERE result_id = ?");

                    $conn->begin_transaction();
                    $line = 1;
                    while (($row = fgetcsv($handle)) !== false) {
                        $line++;
                        $report['processed']++;
                        $student_id = null;
                        if (isset($colIndex['student_id'])) $student_id = intval($row[$colIndex['student_id']]);
                        elseif (isset($colIndex['admission_no'])) {
                            $adm = trim($row[$colIndex['admission_no']]);
                            $stmt_get_by_adm->bind_param('s',$adm);
                            $stmt_get_by_adm->execute();
                            $resst = $stmt_get_by_adm->get_result()->fetch_assoc();
                            $stmt_get_by_adm->close();
                            if ($resst) $student_id = intval($resst['student_id']);
                            else $student_id = 0;
                            // re-prepare statement for reuse
                            $stmt_get_by_adm = $conn->prepare("SELECT student_id FROM students WHERE admission_no = ? LIMIT 1");
                        }
                        $marks = isset($colIndex['marks']) ? trim($row[$colIndex['marks']]) : null;
                        $marks = $marks === '' ? null : (float)$marks;

                        if (!$student_id || $marks === null || !is_numeric($marks)) {
                            $report['failed']++; $report['details'][]=['line'=>$line,'status'=>'failed','reason'=>(!$student_id?'student not found':'invalid marks')]; continue;
                        }

                        $stmt_check->bind_param('ii', $exam_id, $student_id);
                        $stmt_check->execute();
                        $exists = $stmt_check->get_result()->fetch_assoc();
                        if ($exists) {
                            if ($overwrite) {
                                $grade = calc_grade($marks);
                                $stmt_update->bind_param('dsi', $marks, $grade, $exists['result_id']);
                                if ($stmt_update->execute()) { $report['updated']++; $report['details'][]=['line'=>$line,'status'=>'updated','student_id'=>$student_id,'marks'=>$marks,'grade'=>$grade]; }
                                else { $report['failed']++; $report['details'][]=['line'=>$line,'status'=>'failed','reason'=>'db update error']; }
                            } else {
                                $report['skipped']++; $report['details'][]=['line'=>$line,'status'=>'skipped','reason'=>'exists'];
                            }
                        } else {
                            $grade = calc_grade($marks);
                            $stmt_insert->bind_param('iids', $exam_id, $student_id, $marks, $grade);
                            if ($stmt_insert->execute()) { $report['inserted']++; $report['details'][]=['line'=>$line,'status'=>'inserted','student_id'=>$student_id,'marks'=>$marks,'grade'=>$grade]; }
                            else { $report['failed']++; $report['details'][]=['line'=>$line,'status'=>'failed','reason'=>'db insert error']; }
                        }
                    } // end loop
                    $conn->commit();
                    $stmt_check->close(); $stmt_get_by_adm->close(); $stmt_insert->close(); $stmt_update->close();
                }
            }
            fclose($handle);
        } else $errors[] = "Cannot open uploaded CSV.";
    }
}
?>

<div class="row">
  <div class="col-md-8">
    <h4>Import Results (Overwrite option)</h4>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>

    <?php if ($report['processed']): ?>
      <div class="alert alert-info">
        Processed: <?= $report['processed'] ?> — Inserted: <?= $report['inserted'] ?> — Updated: <?= $report['updated'] ?> — Skipped: <?= $report['skipped'] ?> — Failed: <?= $report['failed'] ?>
      </div>
      <details><summary>Show details (<?= count($report['details']) ?>)</summary>
        <table class="table table-sm mt-2"><thead><tr><th>Line</th><th>Status</th><th>Info</th></tr></thead><tbody>
        <?php foreach($report['details'] as $d): ?>
          <tr><td><?= htmlspecialchars($d['line']) ?></td><td><?= htmlspecialchars($d['status']) ?></td><td><?= htmlspecialchars(json_encode(array_diff_key($d,array_flip(['row'])))) ?></td></tr>
        <?php endforeach; ?>
        </tbody></table>
      </details>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label>Exam</label>
        <select name="exam_id" class="form-control" required>
          <option value="">-- Select Exam --</option>
          <?php while($e = $exams->fetch_assoc()): ?>
            <option value="<?= $e['exam_id'] ?>"><?= htmlspecialchars($e['subject_name']) ?> — <?= htmlspecialchars($e['class_name']) ?><?= $e['section'] ? ' - '.htmlspecialchars($e['section']) : '' ?> (<?= $e['exam_date'] ?>)</option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label>CSV file</label>
        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="overwrite" name="overwrite" value="1">
        <label class="form-check-label" for="overwrite">Overwrite existing results if present</label>
      </div>

      <button class="btn btn-primary">Upload</button>
      <a class="btn btn-secondary" href="list.php">Back</a>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>

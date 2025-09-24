<?php
require_once __DIR__ . '/../inc/header.php';
require_role(['admin','teacher']);

// Fetch classes for dropdown
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name");

// Step 1: Select class & date
$class_id = intval($_GET['class_id'] ?? 0);
$date = $_GET['date'] ?? date('Y-m-d');

// Step 2: Fetch students of class if selected
$students = [];
if ($class_id) {
    $sql="SELECT st.student_id, u.username, st.admission_no
          FROM enrollments e
          JOIN students st ON e.student_id=st.student_id
          JOIN users u ON st.user_id=u.user_id
          WHERE e.class_id=? AND e.year=YEAR(?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('is',$class_id,$date);
    $stmt->execute();
    $students=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle form submission
$success=$error='';
if ($_SERVER['REQUEST_METHOD']==='POST' && $class_id && $date) {
    foreach($_POST['status'] as $student_id=>$status){
        $stmt=$conn->prepare("INSERT INTO attendance (student_id,class_id,date,status) 
                              VALUES (?,?,?,?)
                              ON DUPLICATE KEY UPDATE status=VALUES(status)");
        $stmt->bind_param('iiss',$student_id,$class_id,$date,$status);
        $stmt->execute();
        $stmt->close();
    }
    $success="Attendance saved for $date.";
}
?>

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
  <h3 class="text-lg font-semibold mb-4">Mark Attendance</h3>

  <?php if($success): ?><div class="mb-3 p-3 bg-green-50 text-green-700 border rounded"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if($error): ?><div class="mb-3 p-3 bg-red-50 text-red-700 border rounded"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="get" class="flex gap-4 mb-4">
    <div>
      <label class="block text-sm font-medium">Class</label>
      <select name="class_id" class="mt-1 block w-full rounded border-gray-200 px-3 py-2" required>
        <option value="">-- Select --</option>
        <?php while($c=$classes->fetch_assoc()): ?>
          <option value="<?= $c['class_id'] ?>" <?= $class_id==$c['class_id']?'selected':'' ?>>
            <?= htmlspecialchars($c['class_name']) ?> <?= htmlspecialchars($c['section']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Date</label>
      <input type="date" name="date" value="<?= $date ?>" class="mt-1 block w-full rounded border-gray-200 px-3 py-2" required>
    </div>
    <div class="flex items-end">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Load</button>
    </div>
  </form>

  <?php if($class_id && $students): ?>
    <form method="post" class="space-y-3">
      <input type="hidden" name="class_id" value="<?= $class_id ?>">
      <input type="hidden" name="date" value="<?= $date ?>">

      <table class="w-full table-auto border">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-3">Student</th>
            <th class="p-3">Admission No</th>
            <th class="p-3">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($students as $s): ?>
            <tr class="border-t">
              <td class="p-3"><?= htmlspecialchars($s['username']) ?></td>
              <td class="p-3"><?= htmlspecialchars($s['admission_no']) ?></td>
              <td class="p-3">
                <select name="status[<?= $s['student_id'] ?>]" class="rounded border-gray-200 px-2 py-1">
                  <option value="Present">Present</option>
                  <option value="Absent">Absent</option>
                </select>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="mt-3">
        <button class="bg-primary-500 text-white px-4 py-2 rounded">Save Attendance</button>
      </div>
    </form>
  <?php elseif($class_id): ?>
    <div class="p-3 bg-yellow-50 border text-yellow-700 rounded">No students enrolled in this class for <?= date('Y') ?>.</div>
  <?php endif; ?>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

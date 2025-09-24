<?php
require_once __DIR__ . '/../inc/header.php';
require_role(['admin','teacher']);

$class_id=intval($_GET['class_id']??0);
$from=$_GET['from']??'';
$to=$_GET['to']??'';

$classes=$conn->query("SELECT * FROM classes ORDER BY class_name");

$where=[]; $params=[]; $types='';
if($class_id){ $where[]="a.class_id=?"; $params[]=$class_id; $types.='i'; }
if($from){ $where[]="a.date>=?"; $params[]=$from; $types.='s'; }
if($to){ $where[]="a.date<=?"; $params[]=$to; $types.='s'; }
$where_sql=$where?'WHERE '.implode(' AND ',$where):'';

$sql="SELECT st.student_id,u.username,st.admission_no,
             COUNT(a.attendance_id) AS total_days,
             SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) AS present_days
      FROM attendance a
      JOIN students st ON a.student_id=st.student_id
      JOIN users u ON st.user_id=u.user_id
      $where_sql
      GROUP BY st.student_id,u.username,st.admission_no
      ORDER BY u.username";
$stmt=$conn->prepare($sql);
if($params) $stmt->bind_param($types,...$params);
$stmt->execute();
$res=$stmt->get_result();
$stmt->close();
?>
<div class="bg-white p-6 rounded shadow max-w-6xl mx-auto">
  <h3 class="text-lg font-semibold mb-4">Attendance Summary</h3>

  <form method="get" class="flex flex-wrap gap-4 mb-4">
    <div>
      <label class="block text-sm font-medium">Class</label>
      <select name="class_id" class="mt-1 block rounded border-gray-200 px-3 py-2">
        <option value="">-- All --</option>
        <?php while($c=$classes->fetch_assoc()): ?>
          <option value="<?= $c['class_id'] ?>" <?= $class_id==$c['class_id']?'selected':'' ?>>
            <?= htmlspecialchars($c['class_name']).' '.htmlspecialchars($c['section']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">From</label>
      <input type="date" name="from" value="<?= $from ?>" class="mt-1 block rounded border-gray-200 px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">To</label>
      <input type="date" name="to" value="<?= $to ?>" class="mt-1 block rounded border-gray-200 px-3 py-2">
    </div>
    <div class="flex items-end">
      <button class="bg-primary-500 text-white px-4 py-2 rounded">Filter</button>
    </div>
  </form>

  <table class="w-full table-auto border">
    <thead class="bg-gray-50">
      <tr>
        <th class="p-3">Student</th>
        <th class="p-3">Admission No</th>
        <th class="p-3">Total Days</th>
        <th class="p-3">Present</th>
        <th class="p-3">Absent</th>
        <th class="p-3">Percentage</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row=$res->fetch_assoc()): 
        $absent=$row['total_days']-$row['present_days'];
        $perc=$row['total_days']?round(($row['present_days']/$row['total_days'])*100,1):0;
      ?>
        <tr class="border-t">
          <td class="p-3"><?= htmlspecialchars($row['username']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['admission_no']) ?></td>
          <td class="p-3"><?= $row['total_days'] ?></td>
          <td class="p-3"><?= $row['present_days'] ?></td>
          <td class="p-3"><?= $absent ?></td>
          <td class="p-3"><?= $perc ?>%</td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>

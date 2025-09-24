<?php
// edit.php - update or toggle status
require_once __DIR__ . '/config/db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

$errors = [];
// handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $due = $_POST['due_date'] ? $_POST['due_date'] : null;
    $status = $_POST['status'] ?? 'pending';

    if ($title === '') $errors[] = "Title is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, priority=?, due_date=?, status=? WHERE id=?");
        $stmt->bind_param('sssssi', $title, $description, $priority, $due, $status, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: index.php');
        exit;
    }
}

// load task
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id=? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$task) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/inc/header.php';
?>
<div class="card">
  <div class="header-row">
    <h2>Edit Task</h2>
    <a href="index.php" class="small">Back to list</a>
  </div>

  <?php if ($errors): ?>
    <div class="card" style="background:#fff6f6;color:#9b1c1c">
      <?php foreach ($errors as $e) echo '<div class="small">'.htmlspecialchars($e).'</div>'; ?>
    </div>
  <?php endif; ?>

  <form method="post" class="card" style="padding:14px;">
    <div style="margin-bottom:8px">
      <label class="small">Title</label>
      <input name="title" class="input" required value="<?= htmlspecialchars($task['title']) ?>">
    </div>

    <div style="margin-bottom:8px">
      <label class="small">Description</label>
      <textarea name="description" class="input"><?= htmlspecialchars($task['description']) ?></textarea>
    </div>

    <div class="form-row" style="margin-bottom:8px">
      <div style="flex:1">
        <label class="small">Priority</label>
        <select name="priority" class="input">
          <option value="high" <?= $task['priority']==='high' ? 'selected' : '' ?>>High</option>
          <option value="medium" <?= $task['priority']==='medium' ? 'selected' : '' ?>>Medium</option>
          <option value="low" <?= $task['priority']==='low' ? 'selected' : '' ?>>Low</option>
        </select>
      </div>

      <div style="max-width:220px">
        <label class="small">Due date</label>
        <input type="date" name="due_date" class="input" value="<?= $task['due_date'] ? htmlspecialchars($task['due_date']) : '' ?>">
      </div>

      <div style="max-width:160px">
        <label class="small">Status</label>
        <select name="status" class="input">
          <option value="pending" <?= $task['status']==='pending' ? 'selected' : '' ?>>Pending</option>
          <option value="done" <?= $task['status']==='done' ? 'selected' : '' ?>>Done</option>
        </select>
      </div>
    </div>

    <div class="form-actions">
      <button class="btn">Save Changes</button>
      <a href="index.php" class="btn ghost">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>

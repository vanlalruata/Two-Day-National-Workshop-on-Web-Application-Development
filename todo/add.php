<?php
// add.php - create task
require_once __DIR__ . '/config/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $due = $_POST['due_date'] ? $_POST['due_date'] : null;

    if ($title === '') $errors[] = "Title is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, priority, due_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $title, $description, $priority, $due);
        $stmt->execute();
        $stmt->close();
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/inc/header.php';
?>
<div class="card">
  <div class="header-row">
    <h2>New Task</h2>
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
      <input name="title" class="input" required value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
    </div>

    <div style="margin-bottom:8px">
      <label class="small">Description</label>
      <textarea name="description" class="input"><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
    </div>

    <div class="form-row" style="margin-bottom:8px">
      <div style="flex:1">
        <label class="small">Priority</label>
        <select name="priority" class="input">
          <option value="high" <?= (isset($priority) && $priority==='high') ? 'selected' : '' ?>>High</option>
          <option value="medium" <?= (!isset($priority) || $priority==='medium') ? 'selected' : '' ?>>Medium</option>
          <option value="low" <?= (isset($priority) && $priority==='low') ? 'selected' : '' ?>>Low</option>
        </select>
      </div>
      <div style="max-width:220px">
        <label class="small">Due date</label>
        <input type="date" name="due_date" class="input" value="<?= isset($due) ? htmlspecialchars($due) : '' ?>">
      </div>
    </div>

    <div class="form-actions">
      <button class="btn">Create Task</button>
      <a href="index.php" class="btn ghost">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>

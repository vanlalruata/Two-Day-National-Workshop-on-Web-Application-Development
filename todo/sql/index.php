<?php
// index.php - list tasks
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/inc/header.php';

// fetch tasks ordered by priority then newest
$sql = "SELECT * FROM tasks ORDER BY FIELD(priority,'high','medium','low'), created_at DESC";
$res = $conn->query($sql);

?>
<div class="card">
  <div class="header-row">
    <h2>All Tasks</h2>
    <div class="small">Quick list of tasks — create, edit, delete, and mark done.</div>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Title</th>
        <th class="small">Priority</th>
        <th class="small">Due</th>
        <th class="small">Status</th>
        <th style="width:150px"></th>
      </tr>
    </thead>
    <tbody>
      <?php if ($res && $res->num_rows): ?>
        <?php while ($r = $res->fetch_assoc()): ?>
          <tr>
            <td>
              <div style="font-weight:600"><?= htmlspecialchars($r['title']) ?></div>
              <?php if (trim($r['description'])): ?>
                <div class="small"><?= nl2br(htmlspecialchars($r['description'])) ?></div>
              <?php endif; ?>
            </td>
            <td class="small">
              <?php
                if ($r['priority'] === 'high') echo '<span class="pr-high">High</span>';
                elseif ($r['priority'] === 'medium') echo '<span class="pr-med">Medium</span>';
                else echo '<span class="pr-low">Low</span>';
              ?>
            </td>
            <td class="small"><?= $r['due_date'] ? htmlspecialchars($r['due_date']) : '-' ?></td>
            <td class="small">
              <?php if ($r['status'] === 'done'): ?>
                <span class="badge done">Done</span>
              <?php else: ?>
                <span class="badge pending">Pending</span>
              <?php endif; ?>
            </td>
            <td>
              <a class="btn ghost" href="edit.php?id=<?= $r['id'] ?>">Edit</a>
              <a class="btn" href="delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Delete this task?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="small">No tasks yet. <a href="add.php" class="btn">Add a task</a></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>

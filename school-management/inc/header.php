<?php
// inc/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>School Management</title>
  <!-- <link rel="stylesheet" href="/school_mgmt/assets/css/styles.css"> -->
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>  
</head>
<body class="bg-gray-50 text-gray-800">
  <header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <div class="text-2xl font-semibold text-primary-700">SchoolMgmt</div>
        <?php if (is_logged_in()): ?>
          <nav class="hidden md:flex gap-2">
            <a href="/school_mgmt/dashboard.php" class="px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
            <a href="/school_mgmt/students/list.php" class="px-3 py-2 rounded hover:bg-gray-100">Students</a>
            <a href="/school_mgmt/teachers/list.php" class="px-3 py-2 rounded hover:bg-gray-100">Teachers</a>
            <a href="/school_mgmt/classes/list.php" class="px-3 py-2 rounded hover:bg-gray-100">Classes</a>
            <a href="/school_mgmt/subjects/list.php" class="px-3 py-2 rounded hover:bg-gray-100">Subjects</a>
          </nav>
        <?php endif; ?>
      </div>

      <div class="flex items-center gap-4">
        <?php if (is_logged_in()): ?>
          <div class="text-sm text-gray-600">Hi, <span class="font-medium"><?= htmlspecialchars($_SESSION['username']) ?></span> — <span class="text-xs px-2 py-1 rounded bg-gray-100"><?= htmlspecialchars($_SESSION['role']) ?></span></div>
          <a href="/school_mgmt/profile.php" class="btn bg-white border">Profile</a>
          <a href="/school_mgmt/auth/logout.php" class="btn bg-red-600 text-white hover:bg-red-700">Logout</a>
        <?php else: ?>
          <a href="/school_mgmt/index.php" class="btn bg-primary-500 text-white">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-6">

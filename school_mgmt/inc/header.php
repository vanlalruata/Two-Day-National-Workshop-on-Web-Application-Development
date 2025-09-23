<?php
// inc/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>SchoolMgmt</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/school_mgmt/dashboard.php">SchoolMgmt</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="/school_mgmt/students/list.php">Students</a></li>
          <li class="nav-item"><a class="nav-link" href="/school_mgmt/teachers/list.php">Teachers</a></li>
          <li class="nav-item"><a class="nav-link" href="/school_mgmt/classes/list.php">Classes</a></li>
          <li class="nav-item"><a class="nav-link" href="/school_mgmt/subjects/list.php">Subjects</a></li>
          <li class="nav-item"><a class="nav-link" href="/school_mgmt/enrollments/list.php">Enrollments</a></li>
          <li class="nav-item"><a class="nav-link" href="/school_mgmt/attendance/list.php">Attendance</a></li>
          <li class="nav-item"><a class="nav-link" href="/school_mgmt/exams/list.php">Exams</a></li>
          <li class="nav-item"><a class="nav-link" href="/school_mgmt/results/list.php">Results</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex">
        <?php if (is_logged_in()): ?>
          <span class="navbar-text me-2 text-white"> <?= $_SESSION['username'] ?> (<?= $_SESSION['role'] ?>) </span>
          <a class="btn btn-outline-light btn-sm" href="/school_mgmt/auth/logout.php">Logout</a>
        <?php else: ?>
          <a class="btn btn-outline-light btn-sm" href="/school_mgmt/auth/login.php">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<div class="container">

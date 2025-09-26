<?php
// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .admin-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }

        .sidebar-header {
            padding: 20px;
            background: #34495e;
            text-align: center;
        }

        .sidebar-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            border-bottom: 1px solid #34495e;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #34495e;
            color: white;
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .admin-header {
            background: white;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .quick-link {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .quick-link:hover {
            transform: translateY(-5px);
        }

        .quick-link i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #3498db;
        }

        /* Form Styles for Admin */
        .admin-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-admin {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .btn-admin:hover {
            background: #2980b9;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .data-table tr:hover {
            background: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-edit {
            background: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-cogs"></i> Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a></li>
                <li><a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i> Profile
                </a></li>
                <li><a href="skills.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'skills.php' ? 'active' : ''; ?>">
                    <i class="fas fa-code"></i> Skills
                </a></li>
                <li><a href="projects.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'projects.php' ? 'active' : ''; ?>">
                    <i class="fas fa-project-diagram"></i> Projects
                </a></li>
                <li><a href="experiences.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'experiences.php' ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i> Experience
                </a></li>
                <li><a href="education.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'education.php' ? 'active' : ''; ?>">
                    <i class="fas fa-graduation-cap"></i> Education
                </a></li>
                <li><a href="messages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Messages
                    <?php
                    // Show unread message count
                    $unread_count = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
                    if($unread_count > 0): ?>
                        <span style="background: #e74c3c; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.8rem;">
                            <?php echo $unread_count; ?>
                        </span>
                    <?php endif; ?>
                </a></li>
                <li><a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
                <div class="user-info">
                    <span>Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
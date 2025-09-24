<?php
include '../includes/config.php';
$pageTitle = "Admin Dashboard";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get counts for dashboard
$profile_count = $pdo->query("SELECT COUNT(*) FROM profile")->fetchColumn();
$skills_count = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
$projects_count = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$messages_count = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
?>

<?php include 'includes/admin_header.php'; ?>

<div class="admin-container">
    <h1>Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="stat-info">
                <h3>Profile</h3>
                <span class="stat-number"><?php echo $profile_count; ?></span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-code"></i>
            </div>
            <div class="stat-info">
                <h3>Skills</h3>
                <span class="stat-number"><?php echo $skills_count; ?></span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-info">
                <h3>Projects</h3>
                <span class="stat-number"><?php echo $projects_count; ?></span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-info">
                <h3>New Messages</h3>
                <span class="stat-number"><?php echo $messages_count; ?></span>
            </div>
        </div>
    </div>
    
    <div class="quick-links">
        <h2>Quick Links</h2>
        <div class="links-grid">
            <a href="profile.php" class="quick-link">
                <i class="fas fa-user-edit"></i>
                <span>Edit Profile</span>
            </a>
            <a href="skills.php" class="quick-link">
                <i class="fas fa-cogs"></i>
                <span>Manage Skills</span>
            </a>
            <a href="projects.php" class="quick-link">
                <i class="fas fa-project-diagram"></i>
                <span>Manage Projects</span>
            </a>
            <a href="messages.php" class="quick-link">
                <i class="fas fa-inbox"></i>
                <span>View Messages</span>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
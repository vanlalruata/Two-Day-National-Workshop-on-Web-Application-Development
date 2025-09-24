<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'My Portfolio'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">Portfolio</a>
            </div>
            <div class="nav-menu">
                <a href="index.php#home">Home</a>
                <a href="index.php#about">About</a>
                <a href="index.php#skills">Skills</a>
                <a href="index.php#projects">Projects</a>
                <a href="index.php#experience">Experience</a>
                <a href="index.php#education">Education</a>
                <a href="index.php#contact">Contact</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="admin/dashboard.php">Admin</a>
                <?php endif; ?>
            </div>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
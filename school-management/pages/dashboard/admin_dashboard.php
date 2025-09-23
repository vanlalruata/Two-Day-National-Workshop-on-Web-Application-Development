<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';

checkAuth('admin');
$database = new Database();
$db = $database->getConnection();

// Get dashboard statistics
$stats = [
    'total_students' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
    'total_teachers' => $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn(),
    'total_classes' => $db->query("SELECT COUNT(*) FROM classes")->fetchColumn(),
    'total_subjects' => $db->query("SELECT COUNT(*) FROM subjects")->fetchColumn()
];

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Admin Dashboard</h2>
                <p class="text-muted">Welcome back, <?php echo $_SESSION['username']; ?>!</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4><?php echo $stats['total_students']; ?></h4>
                                <p class="mb-0">Total Students</p>
                            </div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="../students/student_list.php" class="text-white">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4><?php echo $stats['total_teachers']; ?></h4>
                                <p class="mb-0">Total Teachers</p>
                            </div>
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="../teachers/teacher_list.php" class="text-white">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4><?php echo $stats['total_classes']; ?></h4>
                                <p class="mb-0">Total Classes</p>
                            </div>
                            <i class="fas fa-school fa-2x"></i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="../classes/class_list.php" class="text-white">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4><?php echo $stats['total_subjects']; ?></h4>
                                <p class="mb-0">Total Subjects</p>
                            </div>
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="../subjects/subject_list.php" class="text-white">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="../students/add_student.php" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-user-plus"></i> Add New Student
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="../teachers/add_teacher.php" class="btn btn-outline-success btn-block">
                                    <i class="fas fa-user-tie"></i> Add New Teacher
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="../classes/add_class.php" class="btn btn-outline-info btn-block">
                                    <i class="fas fa-plus-circle"></i> Add New Class
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="../attendance/mark_attendance.php" class="btn btn-outline-warning btn-block">
                                    <i class="fas fa-clipboard-check"></i> Mark Attendance
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
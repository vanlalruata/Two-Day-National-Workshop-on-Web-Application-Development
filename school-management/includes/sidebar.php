<!-- Sidebar -->
<nav class="sidebar bg-dark" id="sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == $user_type . '_dashboard.php' ? 'active' : ''; ?>" 
                   href="../dashboard/<?php echo $user_type; ?>_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            
            <?php if($user_type == 'admin'): ?>
                <!-- Admin Menu Items -->
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'student') !== false ? 'active' : ''; ?>" 
                       href="../students/student_list.php">
                        <i class="fas fa-users"></i>
                        Students
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'teacher') !== false ? 'active' : ''; ?>" 
                       href="../teachers/teacher_list.php">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Teachers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'class') !== false ? 'active' : ''; ?>" 
                       href="../classes/class_list.php">
                        <i class="fas fa-school"></i>
                        Classes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'subject') !== false ? 'active' : ''; ?>" 
                       href="../subjects/subject_list.php">
                        <i class="fas fa-book"></i>
                        Subjects
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'attendance') !== false ? 'active' : ''; ?>" 
                       href="../attendance/attendance_report.php">
                        <i class="fas fa-clipboard-check"></i>
                        Attendance Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'grade') !== false ? 'active' : ''; ?>" 
                       href="../grades/grade_report.php">
                        <i class="fas fa-chart-bar"></i>
                        Grade Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : ''; ?>" 
                       href="../settings/system_settings.php">
                        <i class="fas fa-cog"></i>
                        System Settings
                    </a>
                </li>
                
            <?php elseif($user_type == 'teacher'): ?>
                <!-- Teacher Menu Items -->
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'attendance') !== false ? 'active' : ''; ?>" 
                       href="../attendance/mark_attendance.php">
                        <i class="fas fa-clipboard-check"></i>
                        Mark Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'grade') !== false ? 'active' : ''; ?>" 
                       href="../grades/enter_grades.php">
                        <i class="fas fa-edit"></i>
                        Enter Grades
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'student') !== false ? 'active' : ''; ?>" 
                       href="../students/student_list.php">
                        <i class="fas fa-users"></i>
                        My Students
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'attendance') !== false && strpos($_SERVER['PHP_SELF'], 'view') !== false ? 'active' : ''; ?>" 
                       href="../attendance/view_attendance.php">
                        <i class="fas fa-list"></i>
                        View Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'grade') !== false && strpos($_SERVER['PHP_SELF'], 'view') !== false ? 'active' : ''; ?>" 
                       href="../grades/view_grades.php">
                        <i class="fas fa-chart-bar"></i>
                        View Grades
                    </a>
                </li>
                
            <?php else: ?>
                <!-- Student Menu Items -->
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'grade') !== false ? 'active' : ''; ?>" 
                       href="../grades/view_grades.php">
                        <i class="fas fa-chart-bar"></i>
                        My Grades
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'attendance') !== false ? 'active' : ''; ?>" 
                       href="../attendance/view_attendance.php">
                        <i class="fas fa-calendar-check"></i>
                        My Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'subject') !== false ? 'active' : ''; ?>" 
                       href="../subjects/my_subjects.php">
                        <i class="fas fa-book"></i>
                        My Subjects
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'class') !== false ? 'active' : ''; ?>" 
                       href="../classes/my_class.php">
                        <i class="fas fa-users"></i>
                        My Class
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item mt-auto">
                <a class="nav-link" href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<script>
document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('mobile-show');
});
</script>
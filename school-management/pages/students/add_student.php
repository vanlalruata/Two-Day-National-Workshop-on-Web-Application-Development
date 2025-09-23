<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';

checkAuth('admin');
$database = new Database();
$db = $database->getConnection();

// Get classes for dropdown
$classes = $db->query("SELECT * FROM classes ORDER BY class_name, section")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$success = false;

if($_POST) {
    // Validate input
    $student_number = trim($_POST['student_number']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $class_id = $_POST['class_id'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Validation
    if(empty($student_number)) $errors[] = "Student number is required";
    if(empty($first_name)) $errors[] = "First name is required";
    if(empty($last_name)) $errors[] = "Last name is required";
    if(empty($email)) $errors[] = "Email is required";
    if(empty($username)) $errors[] = "Username is required";
    if(empty($password)) $errors[] = "Password is required";
    if(strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    
    // Check if student number already exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE student_number = ?");
    $stmt->execute([$student_number]);
    if($stmt->fetchColumn() > 0) {
        $errors[] = "Student number already exists";
    }
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if($stmt->fetchColumn() > 0) {
        $errors[] = "Email already exists";
    }
    
    // Check if username already exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if($stmt->fetchColumn() > 0) {
        $errors[] = "Username already exists";
    }
    
    if(empty($errors)) {
        try {
            $db->beginTransaction();
            
            // Create user account
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, 'student')");
            $stmt->execute([$username, $email, $hashed_password]);
            $user_id = $db->lastInsertId();
            
            // Create student record
            $stmt = $db->prepare("INSERT INTO students (user_id, student_number, first_name, last_name, date_of_birth, gender, phone, address, class_id, enrollment_date) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())");
            $stmt->execute([$user_id, $student_number, $first_name, $last_name, $date_of_birth, $gender, $phone, $address, $class_id]);
            $student_id = $db->lastInsertId();
            
            // Create enrollment record
            $stmt = $db->prepare("INSERT INTO enrollments (student_id, class_id, enrollment_date, status, academic_year) 
                                 VALUES (?, ?, CURDATE(), 'Active', ?)");
            $academic_year = date('Y') . '-' . (date('Y') + 1);
            $stmt->execute([$student_id, $class_id, $academic_year]);
            
            $db->commit();
            $success = true;
            $_SESSION['success'] = "Student added successfully!";
            header("Location: student_list.php");
            exit();
            
        } catch(PDOException $e) {
            $db->rollBack();
            $errors[] = "Error creating student: " . $e->getMessage();
        }
    }
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Add New Student</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard/admin_dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="student_list.php">Students</a></li>
                        <li class="breadcrumb-item active">Add Student</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php foreach($errors as $error): ?>
                    <p class="mb-0"><?php echo $error; ?></p>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student Number *</label>
                            <input type="text" class="form-control" name="student_number" 
                                   value="<?php echo htmlspecialchars($_POST['student_number'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class *</label>
                            <select class="form-control" name="class_id" required>
                                <option value="">Select Class</option>
                                <?php foreach($classes as $class): ?>
                                    <option value="<?php echo $class['class_id']; ?>"
                                            <?php echo (($_POST['class_id'] ?? '') == $class['class_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class['class_name'] . ' - ' . $class['section']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" 
                                   value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name" 
                                   value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" required>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" 
                                   value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-control" name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo (($_POST['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (($_POST['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo (($_POST['gender'] ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name $params[] = $class_filter;
}

if($status_filter) {
    $where_conditions[] = "e.status = ?";
    $params[] = $status_filter;
}

$where_clause = implode(" AND ", $where_conditions);

// Get total records
$count_query = "SELECT COUNT(*) FROM students s 
                LEFT JOIN classes c ON s.class_id = c.class_id 
                LEFT JOIN enrollments e ON s.student_id = e.student_id 
                WHERE $where_clause";
$stmt = $db->prepare($count_query);
$stmt->execute($params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// Get students
$query = "SELECT s.*, c.class_name, c.section, e.status as enrollment_status,
          (SELECT COUNT(*) FROM grades WHERE student_id = s.student_id) as total_grades
          FROM students s
          LEFT JOIN classes c ON s.class_id = c.class_id 
          LEFT JOIN enrollments e ON s.student_id = e.student_id 
          WHERE $where_clause
          ORDER BY s.first_name, s.last_name
          LIMIT $offset, $records_per_page";

$stmt = $db->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get classes for filter
$classes = $db->query("SELECT * FROM classes ORDER BY class_name, section")->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Student Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard/admin_dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Students</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="add_student.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Student
                </a>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by name or student number" 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select class="form-control" name="class">
                            <option value="">All Classes</option>
                            <?php foreach($classes as $class): ?>
                                <option value="<?php echo $class['class_id']; ?>" 
                                        <?php echo $class_filter == $class['class_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class['class_name'] . ' - ' . $class['section']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status">
                            <option value="">All</option>
                            <option value="Active" <?php echo $status_filter == 'Active' ? 'selected' : ''; ?>>Active</option>
                            <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Dropped" <?php echo $status_filter == 'Dropped' ? 'selected' : ''; ?>>Dropped</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="student_list.php" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Student List</h5>
                <span class="badge bg-secondary"><?php echo $total_records; ?> students</span>
            </div>
            <div class="card-body">
                <?php if(empty($students)): ?>
                    <div class="alert alert-info">No students found matching your criteria.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student Number</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Gender</th>
                                    <th>Phone</th>
                                    <th>Grades</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if($student['profile_picture']): ?>
                                                    <img src="../../uploads/profiles/<?php echo $student['profile_picture']; ?>" 
                                                         alt="Profile" class="rounded-circle me-2" 
                                                         style="width: 30px; height: 30px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center me-2" 
                                                         style="width: 30px; height: 30px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($student['email']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($student['class_name']): ?>
                                                <?php echo htmlspecialchars($student['class_name'] . ' - ' . $student['section']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $student['total_grades']; ?> grades</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $student['enrollment_status'] == 'Active' ? 'success' : ($student['enrollment_status'] == 'Completed' ? 'primary' : 'danger'); ?>">
                                                <?php echo htmlspecialchars($student['enrollment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="student_profile.php?id=<?php echo $student['student_id']; ?>" 
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" 
                                                class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit">

<button type="button" class="btn btn-outline-danger delete-btn" 
                                                     onclick="deleteStudent(<?php echo $student['student_id']; ?>)" 
                                                     title="Delete"><i class="fas fa-trash">    

                                                     <?php endforeach; ?>

                                                                     <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>&class=<?php echo $class_filter; ?>&status=<?php echo $status_filter; ?>">
                                    Previous
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&class=<?php echo $class_filter; ?>&status=<?php echo $status_filter; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>&class=<?php echo $class_filter; ?>&status=<?php echo $status_filter; ?>">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this student? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const confirmDelete = document.getElementById('confirmDelete');
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('onclick').match(/\d+/)[0];
            confirmDelete.href = 'delete_student.php?id=' + studentId;
            deleteModal.show();
        });
    });
});
</script>
<?php include '../../includes/footer.php'; ?>


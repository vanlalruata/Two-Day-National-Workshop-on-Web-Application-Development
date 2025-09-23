<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';

checkAuth();
$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Get user details based on user type
if($user_type == 'admin') {
    $query = "SELECT a.*, u.username, u.email FROM admins a 
              JOIN users u ON a.user_id = u.user_id 
              WHERE a.user_id = ?";
} elseif($user_type == 'teacher') {
    $query = "SELECT t.*, u.username, u.email FROM teachers t 
              JOIN users u ON t.user_id = u.user_id 
              WHERE t.user_id = ?";
} else {
    $query = "SELECT s.*, u.username, u.email, c.class_name FROM students s 
              JOIN users u ON s.user_id = u.user_id 
              LEFT JOIN classes c ON s.class_id = c.class_id 
              WHERE s.user_id = ?";
}

$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if($_POST) {
    $errors = [];
    $success = false;
    
    // Validate and update profile
    if($user_type == 'admin') {
        $full_name = $_POST['full_name'];
        $position = $_POST['position'];
        $phone = $_POST['phone'];
        
        if(empty($full_name)) {
            $errors[] = "Full name is required";
        }
        
        if(empty($errors)) {
            $update_query = "UPDATE admins SET full_name = ?, position = ?, phone = ? WHERE user_id = ?";
            $stmt = $db->prepare($update_query);
            $stmt->execute([$full_name, $position, $phone, $user_id]);
            $success = true;
        }
    } elseif($user_type == 'teacher') {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $qualification = $_POST['qualification'];
        $specialization = $_POST['specialization'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        
        if(empty($first_name) || empty($last_name)) {
            $errors[] = "First name and last name are required";
        }
        
        if(empty($errors)) {
            $update_query = "UPDATE teachers SET first_name = ?, last_name = ?, qualification = ?, 
                           specialization = ?, phone = ?, address = ? WHERE user_id = ?";
            $stmt = $db->prepare($update_query);
            $stmt->execute([$first_name, $last_name, $qualification, $specialization, $phone, $address, $user_id]);
            $success = true;
        }
    } else {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        
        if(empty($first_name) || empty($last_name)) {
            $errors[] = "First name and last name are required";
        }
        
        if(empty($errors)) {
            $update_query = "UPDATE students SET first_name = ?, last_name = ?, date_of_birth = ?, 
                           gender = ?, phone = ?, address = ? WHERE user_id = ?";
            $stmt = $db->prepare($update_query);
            $stmt->execute([$first_name, $last_name, $date_of_birth, $gender, $phone, $address, $user_id]);
            $success = true;
        }
    }
    
    // Handle profile picture upload
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if(in_array($_FILES['profile_picture']['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
            $upload_dir = "../../uploads/profiles/";
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $filename = $user_type . "_" . $user_id . "." . $file_extension;
            $filepath = $upload_dir . $filename;
            
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filepath)) {
                // Update profile picture in database
                if($user_type == 'admin') {
                    $stmt = $db->prepare("UPDATE admins SET profile_picture = ? WHERE user_id = ?");
                } elseif($user_type == 'teacher') {
                    $stmt = $db->prepare("UPDATE teachers SET profile_picture = ? WHERE user_id = ?");
                } else {
                    $stmt = $db->prepare("UPDATE students SET profile_picture = ? WHERE user_id = ?");
                }
                $stmt->execute([$filename, $user_id]);
            }
        }
    }
    
    if($success) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    }
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>My Profile</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard/<?php echo $user_type; ?>_dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php foreach($errors as $error): ?>
                    <p class="mb-0"><?php echo $error; ?></p>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <?php if(!empty($user['profile_picture'])): ?>
                                <img src="../../uploads/profiles/<?php echo $user['profile_picture']; ?>" 
                                     alt="Profile Picture" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-4x text-white"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h5><?php echo htmlspecialchars($user['full_name'] ?? $user['first_name'] . ' ' . $user['last_name']); ?></h5>
                        <p class="text-muted"><?php echo ucfirst($user_type); ?></p>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Edit Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                </div>
                            </div>
                            
                            <?php if($user_type == 'admin'): ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" name="full_name" 
                                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Position</label>
                                        <input type="text" class="form-control" name="position" 
                                               value="<?php echo htmlspecialchars($user['position']); ?>">
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" class="form-control" name="first_name" 
                                               value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" 
                                               value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($user_type == 'teacher'): ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Qualification</label>
                                        <input type="text" class="form-control" name="qualification" 
                                               value="<?php echo htmlspecialchars($user['qualification']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Specialization</label>
                                        <input type="text" class="form-control" name="specialization" 
                                               value="<?php echo htmlspecialchars($user['specialization']); ?>">
                                    </div>
                                </div>
                            <?php elseif($user_type == 'student'): ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" name="date_of_birth" 
                                               value="<?php echo htmlspecialchars($user['date_of_birth']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-control" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?php echo $user['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo $user['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                            <option value="Other" <?php echo $user['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control" name="profile_picture" accept="image/*">
                                    <small class="text-muted">Max size: 2MB (JPG, JPEG, PNG)</small>
                                </div>
                            </div>
                            
                            <?php if($user_type != 'admin'): ?>
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>
                            <?php endif; ?>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                <a href="change_password.php" class="btn btn-outline-secondary">Change Password</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
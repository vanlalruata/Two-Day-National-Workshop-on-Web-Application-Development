<?php
// System Configuration
define('SITE_NAME', 'School Management System');
define('SITE_URL', 'http://localhost/school-management/');
define('UPLOAD_PATH', 'uploads/');
define('PROFILE_PATH', 'uploads/profiles/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour

// Pagination
define('RECORDS_PER_PAGE', 10);

// File Upload Settings
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
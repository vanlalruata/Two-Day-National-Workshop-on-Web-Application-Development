<?php
class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT user_id, username, password, user_type FROM users 
                  WHERE username = ? AND is_active = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['last_activity'] = time();
            
            // Update last login
            $update_query = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
            $stmt = $this->conn->prepare($update_query);
            $stmt->execute([$user['user_id']]);
            
            return true;
        }
        
        return false;
    }

    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function hasRole($role) {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] == $role;
    }
}
?>
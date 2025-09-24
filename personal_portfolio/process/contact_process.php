<?php
include '../includes/config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Basic validation
    if(empty($name) || empty($email) || empty($message)) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: ../index.php#contact");
        exit;
    }
    
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: ../index.php#contact");
        exit;
    }
    
    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        
        $_SESSION['success'] = "Thank you for your message! I'll get back to you soon.";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Sorry, there was an error sending your message. Please try again.";
    }
    
    header("Location: ../index.php#contact");
    exit;
}
?>
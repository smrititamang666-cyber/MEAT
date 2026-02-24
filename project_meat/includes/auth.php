<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Login function
function login_user($email_or_phone, $password) {
    global $conn;

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
    $stmt->bind_param("ss", $email_or_phone, $email_or_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
        return "User not found!";
    }

    $user = $result->fetch_assoc();

    // Verify password
    if(password_verify($password, $user['password'])){
        // Store session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if($user['role'] == 'admin'){
            header("Location: ../admin/dashboard.php");
        } elseif($user['role'] == 'seller'){
            header("Location: ../seller/dashboard.php");
        } else {
            header("Location: ../customer/dashboard.php");
        }
        exit();
    } else {
        return "Wrong password!";
    }
}

// Logout function
function logout_user() {
    session_unset();      // Remove all session variables
    session_destroy();    // Destroy the session
    header("Location: login.php"); // Redirect to login page
    exit();
}

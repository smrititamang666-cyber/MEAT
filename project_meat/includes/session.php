<?php
// Start session if not already started
if(!session_id()){
    session_start();
}

// Function to check if user is logged in
function is_logged_in(){
    return isset($_SESSION['user_id']);
}

// Function to check user role
function is_admin(){
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

function is_seller(){
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'seller');
}

function is_customer(){
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'customer');
}

// Function to force login
function require_login(){
    if(!is_logged_in()){
        header("Location: ../public/login.php");
        exit();
    }
}

// Function to force role access
function require_role($role){
    if(!is_logged_in() || $_SESSION['role'] !== $role){
        header("Location: ../public/login.php");
        exit();
    }
}
?>
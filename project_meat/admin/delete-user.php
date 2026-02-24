<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('admin');

if(isset($_GET['id'])){
    $user_id = (int)$_GET['id'];

    // Prevent admin from deleting themselves
    if($user_id === (int)$_SESSION['user_id']){
        header("Location: manage-user.php?error=Cannot+delete+your+own+account");
        exit();
    }

    // Delete the user using prepared statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if($stmt->execute()){
        header("Location: manage-user.php?msg=User+deleted+successfully");
    } else {
        header("Location: manage-user.php?error=Failed+to+delete+user");
    }
    exit();
}

header("Location: manage-user.php");
exit();
?>

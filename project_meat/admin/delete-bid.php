<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('admin');

if(isset($_GET['id'])){
    $bid_id = (int)$_GET['id'];

    // Delete the bid using prepared statement
    $stmt = $conn->prepare("DELETE FROM bids WHERE id = ?");
    $stmt->bind_param("i", $bid_id);
    if($stmt->execute()){
        header("Location: manage-bids.php?msg=Bid+deleted+successfully");
    } else {
        header("Location: manage-bids.php?error=Failed+to+delete+bid");
    }
    exit();
}

header("Location: manage-bids.php");
exit();
?>

<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('admin');

if(isset($_GET['bid_id'])){
    $bid_id = (int)$_GET['bid_id'];

    // Close the bid using prepared statement
    $stmt = $conn->prepare("UPDATE bids SET status = 'Closed' WHERE id = ?");
    $stmt->bind_param("i", $bid_id);
    if($stmt->execute()){
        header("Location: manage-bids.php?msg=Bid+closed+successfully");
    } else {
        header("Location: manage-bids.php?error=Failed+to+close+bid");
    }
    exit();
}

header("Location: manage-bids.php");
exit();
?>

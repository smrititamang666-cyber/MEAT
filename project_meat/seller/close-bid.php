<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('seller');

if(isset($_GET['bid_id'])){
    $bid_id = (int)$_GET['bid_id'];
    $seller_id = (int)$_SESSION['user_id'];

    // Get seller's shop
    $shop_stmt = $conn->prepare("SELECT id FROM shops WHERE user_id = ? LIMIT 1");
    $shop_stmt->bind_param("i", $seller_id);
    $shop_stmt->execute();
    $shop = $shop_stmt->get_result()->fetch_assoc();

    if (!$shop) {
        echo "Shop not found. <a href='dashboard.php'>Go Back</a>";
        exit();
    }
    $shop_id = (int)$shop['id'];

    // Verify seller owns the bid via shop_id
    $check_stmt = $conn->prepare("SELECT shop_id FROM bids WHERE id = ?");
    $check_stmt->bind_param("i", $bid_id);
    $check_stmt->execute();
    $check = $check_stmt->get_result()->fetch_assoc();

    if($check && $check['shop_id'] == $shop_id){
        // Close the bid using prepared statement
        $update_stmt = $conn->prepare("UPDATE bids SET status = 'Closed' WHERE id = ?");
        $update_stmt->bind_param("i", $bid_id);
        $update_stmt->execute();
        echo "Bid closed successfully! <a href='view-bids.php'>Go Back</a>";
        exit();
    } else {
        echo "You are not authorized to close this bid. <a href='view-bids.php'>Go Back</a>";
        exit();
    }
}
header("Location: view-bids.php");
exit();
?>

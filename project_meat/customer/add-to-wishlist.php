<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('customer');

$customer_id = (int)$_SESSION['user_id'];

// Handle remove from wishlist
if(isset($_GET['remove'])){
    $wishlist_id = (int)$_GET['remove'];
    $del_stmt = $conn->prepare("DELETE FROM wishlist WHERE id = ? AND customer_id = ?");
    $del_stmt->bind_param("ii", $wishlist_id, $customer_id);
    $del_stmt->execute();
    header("Location: wishlist.php");
    exit();
}

// Handle add to wishlist
if(isset($_GET['product_id'])){
    $product_id = (int)$_GET['product_id'];

    // Check if already in wishlist using prepared statement
    $check_stmt = $conn->prepare("SELECT id FROM wishlist WHERE customer_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $customer_id, $product_id);
    $check_stmt->execute();
    $check = $check_stmt->get_result();

    if($check->num_rows == 0){
        // Insert into wishlist using prepared statement
        $insert_stmt = $conn->prepare("INSERT INTO wishlist (customer_id, product_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $customer_id, $product_id);
        $insert_stmt->execute();
    }

    header("Location: wishlist.php");
    exit();
}

// If no valid parameter, redirect back
header("Location: browse-products.php");
exit();
?>

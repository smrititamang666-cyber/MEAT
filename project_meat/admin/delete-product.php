<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('admin');

if(isset($_GET['id'])){
    $product_id = (int)$_GET['id'];

    // Delete the product using prepared statement
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    if($stmt->execute()){
        header("Location: manage-products.php?msg=Product+deleted+successfully");
    } else {
        header("Location: manage-products.php?error=Failed+to+delete+product");
    }
    exit();
}

header("Location: manage-products.php");
exit();
?>

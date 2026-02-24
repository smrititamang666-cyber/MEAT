<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('customer');

$error = "";
$success = "";
$product = null;

if(isset($_GET['product_id'])){
    $product_id = (int)$_GET['product_id'];
    $customer_id = (int)$_SESSION['user_id'];

    // Get product info using prepared statement
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        $error = "Product not found.";
    } else {
        $shop_id = (int)$product['shop_id'];
        $price = (float)$product['price'];
        $quantity = isset($_GET['qty']) ? max(1, (int)$_GET['qty']) : 1;
        $total = $price * $quantity;

        // Insert order using prepared statement
        $order_stmt = $conn->prepare("INSERT INTO orders (customer_id, product_id, shop_id, quantity, total_price) VALUES (?, ?, ?, ?, ?)");
        $order_stmt->bind_param("iiiid", $customer_id, $product_id, $shop_id, $quantity, $total);

        if ($order_stmt->execute()) {
            $success = "Order placed successfully!";
        } else {
            $error = "Failed to place order: " . $order_stmt->error;
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>Place Order</h2>
    </div>

    <?php if($success): ?>
        <div class="order-confirmation" style="text-align:center; padding: 40px; background: var(--card-bg); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); max-width: 500px; margin: 0 auto;">
            <div style="font-size:4rem;">✅</div>
            <h3 style="color:#2e7d32;"><?php echo htmlspecialchars($success); ?></h3>
            <p>Your order for <strong><?php echo htmlspecialchars($product['name'] ?? ''); ?></strong> has been placed.</p>
            <div style="margin-top: 20px;">
                <a href="my-orders.php" class="btn btn-primary">View My Orders</a>
            </div>
        </div>
    <?php elseif($error): ?>
        <div style="text-align:center; padding: 40px;">
            <div style="font-size:4rem;">❌</div>
            <h3 style="color:#c62828;"><?php echo htmlspecialchars($error); ?></h3>
            <a href="browse-products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div style="text-align:center; padding: 40px;">
            <div style="font-size:4rem;">⚠️</div>
            <h3>No product selected</h3>
            <a href="browse-products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

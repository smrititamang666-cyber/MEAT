<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('customer');

$customer_id = (int)$_SESSION['user_id'];
$product = null;
$error = "";
$success = "";

if(isset($_GET['product_id'])){
    $product_id = (int)$_GET['product_id'];

    // Check if customer has successfully bought this product using prepared statement
    $order_stmt = $conn->prepare("SELECT id FROM orders WHERE customer_id = ? AND product_id = ? AND status = 'Paid'");
    $order_stmt->bind_param("ii", $customer_id, $product_id);
    $order_stmt->execute();
    $order = $order_stmt->get_result();

    if($order->num_rows == 0){
        $error = "You can only rate products you have purchased.";
    } else {
        // Get product info using prepared statement
        $prod_stmt = $conn->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $prod_stmt->bind_param("i", $product_id);
        $prod_stmt->execute();
        $product = $prod_stmt->get_result()->fetch_assoc();
    }
}

if(isset($_POST['submit']) && isset($_GET['product_id'])){
    $product_id = (int)$_GET['product_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment'] ?? '');

    // Check rating range
    if($rating < 1 || $rating > 5){
        $error = "Rating must be between 1 and 5.";
    } else {
        // Insert rating using prepared statement
        $rate_stmt = $conn->prepare("INSERT INTO ratings (customer_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        $rate_stmt->bind_param("iiis", $customer_id, $product_id, $rating, $comment);
        if($rate_stmt->execute()){
            $success = "Thank you for your rating!";
        } else {
            $error = "Failed to submit rating. You may have already rated this product.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>Rate Product</h2>
        <p>Share your experience with this product</p>
    </div>

    <?php if($error): ?>
        <div class="empty-state">
            <div class="empty-state-icon">⚠️</div>
            <h3>Cannot Rate</h3>
            <p><?php echo htmlspecialchars($error); ?></p>
            <a href="my-orders.php" class="btn btn-primary">Back to Orders</a>
        </div>
    <?php elseif($success): ?>
        <div class="empty-state">
            <div class="empty-state-icon">⭐</div>
            <h3>Rating Submitted!</h3>
            <p>Thank you for sharing your feedback.</p>
            <a href="my-orders.php" class="btn btn-primary">Back to Orders</a>
        </div>
    <?php elseif($product): ?>
        <div class="styled-form">
            <h3 style="margin-bottom: var(--spacing-md); color: var(--primary-color);">
                <?php echo htmlspecialchars($product['name']); ?>
            </h3>
            <form method="POST">
                <div class="form-group">
                    <label for="rating">Rating (1 to 5)</label>
                    <select name="rating" id="rating" required>
                        <option value="">Select Rating</option>
                        <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                        <option value="4">⭐⭐⭐⭐ - Very Good</option>
                        <option value="3">⭐⭐⭐ - Good</option>
                        <option value="2">⭐⭐ - Fair</option>
                        <option value="1">⭐ - Poor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comment">Your Review</label>
                    <textarea name="comment" id="comment" rows="4" placeholder="Tell us about your experience..."></textarea>
                </div>

                <button type="submit" name="submit" class="btn-submit">Submit Rating</button>
            </form>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">❓</div>
            <h3>No Product Selected</h3>
            <p>Please select a product to rate from your orders.</p>
            <a href="my-orders.php" class="btn btn-primary">View Orders</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

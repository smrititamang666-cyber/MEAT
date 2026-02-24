<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('seller');

$error = $success = "";

// Fetch seller's shop
$seller_id = (int)$_SESSION['user_id'];

$shop_stmt = $conn->prepare("SELECT * FROM shops WHERE user_id = ? LIMIT 1");
$shop_stmt->bind_param("i", $seller_id);
$shop_stmt->execute();
$shop = $shop_stmt->get_result()->fetch_assoc();

if (!$shop) {
    die("You must create a shop before starting a bid. <a href='shop-profile.php'>Create Shop</a>");
}
$shop_id = (int)$shop['id'];

// Fetch seller's products using shop_id
$prod_stmt = $conn->prepare("SELECT * FROM products WHERE shop_id = ?");
$prod_stmt->bind_param("i", $shop_id);
$prod_stmt->execute();
$products = $prod_stmt->get_result();

if(isset($_POST['start_bid'])){
    $product_id = (int)$_POST['product_id'];
    $starting_price = (float)$_POST['starting_price'];

    // Insert bid with correct columns
    $bid_stmt = $conn->prepare("INSERT INTO bids (product_id, shop_id, seller_id, bid_amount) VALUES (?, ?, ?, ?)");
    $bid_stmt->bind_param("iiid", $product_id, $shop_id, $seller_id, $starting_price);
    if ($bid_stmt->execute()) {
        $success = "Bidding started successfully!";
    } else {
        $error = "Failed to start bid: " . $bid_stmt->error;
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>Start a Bid</h2>
    </div>

    <?php if($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if($success): ?>
        <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <?php if($products->num_rows > 0): ?>
    <form method="POST">
        <div class="form-group">
            <label>Select Product:</label>
            <select name="product_id" required>
                <?php while($row = $products->fetch_assoc()): ?>
                    <option value="<?php echo (int)$row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Starting Price (Rs):</label>
            <input type="number" step="0.01" name="starting_price" min="0" required>
        </div>

        <button type="submit" name="start_bid" class="btn btn-primary">Start Bid</button>
    </form>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📦</div>
            <h3>No Products Found</h3>
            <p>You need to add products before starting a bid.</p>
            <a href="add-product.php" class="btn btn-primary">Add Product</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

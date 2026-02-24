<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('customer');

$customer_id = (int)$_SESSION['user_id'];

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("
    SELECT w.id AS wishlist_id, p.*, c.name AS category_name, s.shop_name
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    JOIN categories c ON p.category_id = c.id
    JOIN shops s ON p.shop_id = s.id
    WHERE w.customer_id = ?
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>My Wishlist</h2>
        <p>Products you've saved for later</p>
    </div>

    <?php if($result->num_rows > 0): ?>
        <div class="products-grid">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <?php if(!empty($row['image'])): ?>
                    <img src="../assets/images/products/<?php echo htmlspecialchars($row['image']); ?>" 
                         alt="<?php echo htmlspecialchars($row['name']); ?>" 
                         style="width:100%; height:180px; object-fit:cover; border-radius: var(--radius-md);">
                <?php else: ?>
                    <div style="width:100%; height:180px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; font-size:3rem; border-radius: var(--radius-md);">🥩</div>
                <?php endif; ?>
                <div class="product-info">
                    <span class="category-tag"><?php echo htmlspecialchars($row['category_name']); ?></span>
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p>Shop: <?php echo htmlspecialchars($row['shop_name']); ?></p>
                    <div class="price">Rs <?php echo number_format($row['price'], 2); ?></div>
                    <div style="display:flex; gap:10px; margin-top:10px;">
                        <a href="place-order.php?product_id=<?php echo (int)$row['id']; ?>" class="btn btn-primary">Order Now</a>
                        <a href="add-to-wishlist.php?remove=<?php echo (int)$row['wishlist_id']; ?>" 
                           class="btn btn-secondary" 
                           onclick="return confirm('Remove from wishlist?');">Remove</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">❤️</div>
            <h3>Your Wishlist is Empty</h3>
            <p>Browse products and add them to your wishlist.</p>
            <a href="browse-products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('customer');

$user_id = (int)$_SESSION['user_id'];

// Optional category filter
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Fetch all active products with category and shop info
if ($category_filter > 0) {
    $stmt = $conn->prepare("
        SELECT p.*, c.name AS category_name, c.icon AS category_icon, s.shop_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN shops s ON p.shop_id = s.id
        WHERE p.status = 'active' AND p.category_id = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->bind_param("i", $category_filter);
} else {
    $stmt = $conn->prepare("
        SELECT p.*, c.name AS category_name, c.icon AS category_icon, s.shop_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN shops s ON p.shop_id = s.id
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC
    ");
}
$stmt->execute();
$products = $stmt->get_result();

// Fetch categories for filter
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<section class="hero" style="padding: 40px 20px;">
    <div class="hero-content">
        <h1>Browse Products</h1>
        <p>Explore our premium meat selection</p>
    </div>
</section>

<div class="container">

    <!-- Category Filter -->
    <div class="category-filter" style="margin-bottom: var(--spacing-lg); display:flex; gap:10px; flex-wrap:wrap;">
        <a href="browse-products.php" class="btn <?php echo $category_filter == 0 ? 'btn-primary' : 'btn-outline'; ?>">All</a>
        <?php if($categories): while($cat = $categories->fetch_assoc()): ?>
            <a href="browse-products.php?category=<?php echo (int)$cat['id']; ?>"
               class="btn <?php echo $category_filter == $cat['id'] ? 'btn-primary' : 'btn-outline'; ?>">
                <?php echo htmlspecialchars($cat['icon'] . ' ' . $cat['name']); ?>
            </a>
        <?php endwhile; endif; ?>
    </div>

    <?php if($products && $products->num_rows > 0): ?>
        <div class="products-grid">
            <?php while($row = $products->fetch_assoc()): ?>
                <div class="product-card">
                    <?php if(!empty($row['image'])): ?>
                        <img src="../assets/images/products/<?php echo htmlspecialchars($row['image']); ?>"
                             alt="<?php echo htmlspecialchars($row['name']); ?>"
                             style="width:100%; height:180px; object-fit:cover; border-radius: var(--radius-md);">
                    <?php else: ?>
                        <div style="width:100%; height:180px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; font-size:3rem; border-radius: var(--radius-md);">
                            <?php echo htmlspecialchars($row['category_icon'] ?? '🥩'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="product-info">
                        <span class="category-tag"><?php echo htmlspecialchars($row['category_name']); ?></span>
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p style="color:var(--text-light); font-size:0.9rem;">
                            <?php echo htmlspecialchars($row['shop_name']); ?>
                        </p>
                        <?php if(!empty($row['size'])): ?>
                            <p style="font-size:0.85rem; color:var(--text-light);">📏 <?php echo htmlspecialchars($row['size']); ?></p>
                        <?php endif; ?>
                        <div class="price">Rs <?php echo number_format($row['price'], 2); ?></div>
                        <div style="display:flex; gap:8px; margin-top:10px; flex-wrap:wrap;">
                            <a href="place-order.php?product_id=<?php echo (int)$row['id']; ?>"
                               class="btn btn-primary">Order Now</a>
                            <a href="add-to-wishlist.php?product_id=<?php echo (int)$row['id']; ?>"
                               class="btn btn-secondary-styled">♡ Wishlist</a>
                            <?php if($row['is_bidding']): ?>
                                <a href="bidding.php" class="btn btn-outline">🏷️ Bid</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🥩</div>
            <h3>No Products Found</h3>
            <p>No products are available in this category yet.</p>
            <a href="browse-products.php" class="btn btn-primary">View All Products</a>
        </div>
    <?php endif; ?>
</div>

<style>
    .btn-outline {
        display: inline-block;
        padding: var(--spacing-xs) var(--spacing-sm);
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.85rem;
        transition: var(--transition-normal);
    }
    .btn-outline:hover {
        background: var(--primary-color);
        color: #fff;
    }
    .btn-secondary-styled {
        display: inline-block;
        padding: var(--spacing-xs) var(--spacing-sm);
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.85rem;
        transition: var(--transition-normal);
    }
    .btn-secondary-styled:hover {
        background: var(--primary-color);
        color: #fff;
    }
</style>

<?php include "../includes/footer.php"; ?>

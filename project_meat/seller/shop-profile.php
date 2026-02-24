<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('seller');

$seller_id = (int)$_SESSION['user_id'];

/* 1️⃣ Fetch shop */
$stmt = $conn->prepare("SELECT * FROM shops WHERE user_id = ?");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$shop = $stmt->get_result()->fetch_assoc();
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="shop-profile-container">

<?php if (!$shop): ?>
    <!-- Empty State / Create Shop CTA -->
    <div class="create-shop-cta">
        <div class="empty-state-icon">🏪</div>
        <h2>You Don't Have a Shop Yet</h2>
        <p>Start selling your products today! Create your shop by adding your first product.</p>
        <a href="add-product.php" class="btn-create-shop">Create Your Shop</a>
    </div>

<?php else: ?>

    <!-- Shop Hero Section -->
    <div class="shop-hero">
        <div class="shop-hero-content">
            <div class="shop-avatar">
                <?= strtoupper(substr($shop['shop_name'], 0, 1)) ?>
            </div>
            <div class="shop-info">
                <h1><?= htmlspecialchars($shop['shop_name']) ?></h1>
                <p class="shop-description"><?= htmlspecialchars($shop['description']) ?></p>
                <div class="shop-meta">
                    <div class="shop-meta-item">
                        <span>📧</span>
                        <?= htmlspecialchars($shop['email']) ?>
                    </div>
                    <div class="shop-meta-item">
                        <span>⭐</span>
                        Rating: <?= $shop['rating'] ?? 'N/A' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop Details Card -->
    <div class="shop-details-card">
        <div class="detail-item">
            <label>Shop Name</label>
            <span><?= htmlspecialchars($shop['shop_name']) ?></span>
        </div>
        <div class="detail-item">
            <label>Email</label>
            <span><?= htmlspecialchars($shop['email']) ?></span>
        </div>
        <div class="detail-item">
            <label>Rating</label>
            <span class="rating-stars">
                <?php 
                $rating = $shop['rating'] ?? 0;
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $rating ? '★' : '<span class="empty">★</span>';
                }
                ?>
                (<?= $rating ? $rating : 'N/A' ?>)
            </span>
        </div>
    </div>

    <!-- Products Section -->
    <div class="products-section">
        <div class="section-title">
            <h2>My Products</h2>
            <?php
            /* 2️⃣ Fetch products ONLY if shop exists */
            $product_stmt = $conn->prepare("
                SELECT p.*, c.name AS category_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.shop_id = ?
            ");
            $product_stmt->bind_param("i", $shop['id']);
            $product_stmt->execute();
            $products = $product_stmt->get_result();
            ?>
            <span class="count-badge"><?= $products->num_rows ?> Products</span>
        </div>

        <?php if ($products->num_rows === 0): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <h3>No Products Yet</h3>
                <p>Start adding products to your shop to begin selling.</p>
                <a href="add-product.php" class="btn-create-shop">Add Your First Product</a>
            </div>
        <?php else: ?>

            <div class="products-grid">
                <?php while ($row = $products->fetch_assoc()): ?>
                    <div class="product-card-enhanced">
                        <div class="product-image-wrapper">
                            <img src="../assets/images/products/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                            <span class="product-category-tag"><?= htmlspecialchars($row['category_name']) ?></span>
                        </div>
                        
                        <div class="product-details">
                            <h3 class="product-title"><?= htmlspecialchars($row['name']) ?></h3>
                            
                            <div class="product-specs">
                                <span>📏 Size: <?= htmlspecialchars($row['size']) ?></span>
                                <span>✨ Quality: <?= htmlspecialchars($row['quality']) ?></span>
                            </div>
                            
                            <div class="product-price">
                                Rs <?= number_format($row['price'], 2) ?>
                            </div>
                            
                            <div class="product-actions">
                                <a href="start-bid.php" class="btn btn-primary">Start Bid</a>
                                <a href="add-product.php" class="btn btn-secondary-styled">Add Product</a>
                            </div>

                            <!-- Ratings Section -->
                            <?php
                            $rating_stmt = $conn->prepare("
                                SELECT r.rating, u.name, r.created_at
                                FROM ratings r
                                JOIN users u ON r.customer_id = u.id
                                WHERE r.product_id = ?
                                ORDER BY r.created_at DESC
                                LIMIT 3
                            ");
                            $rating_stmt->bind_param("i", $row['id']);
                            $rating_stmt->execute();
                            $ratings = $rating_stmt->get_result();
                            ?>

                            <?php if ($ratings->num_rows > 0): ?>
                                <div class="ratings-section">
                                    <h4>⭐ Customer Reviews</h4>
                                    <?php while ($r = $ratings->fetch_assoc()): ?>
                                        <div class="rating-item">
                                            <div class="rating-header">
                                                <span class="rating-user"><?= htmlspecialchars($r['name']) ?></span>
                                                <span class="rating-date"><?= date('M d, Y', strtotime($r['created_at'])) ?></span>
                                            </div>
                                            <div class="rating-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?= $i <= $r['rating'] ? '★' : '<span class="empty">★</span>'; ?>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="no-ratings">
                                    <p>No ratings yet. Be the first to review!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php endif; ?>
    </div>

<?php endif; ?>

</div>

<?php include "../includes/footer.php"; ?>

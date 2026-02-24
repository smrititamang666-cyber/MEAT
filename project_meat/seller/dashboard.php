<?php
require_once "../includes/config.php";
require_once "../includes/session.php";

// Only allow sellers
require_role('seller');

// Get seller info
$user_id = (int)$_SESSION['user_id'];

// Get shop info
$shop = null;
$shop_stmt = $conn->prepare("SELECT * FROM shops WHERE user_id = ?");
$shop_stmt->bind_param("i", $user_id);
$shop_stmt->execute();
$shop_result = $shop_stmt->get_result();
if ($shop_result && $shop_result->num_rows > 0) {
    $shop = $shop_result->fetch_assoc();
}

// Get counts for dashboard
$products_count = 0;
$active_bids = 0;
$total_sales = 0;

try {
    if($shop) {
        $shop_id = (int)$shop['id'];
        
        // Use prepared statements for security
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM products WHERE shop_id = ?");
        $stmt->bind_param("i", $shop_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result) $products_count = $result->fetch_assoc()['cnt'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM bids WHERE shop_id = ? AND status = 'Open'");
        $stmt->bind_param("i", $shop_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result) $active_bids = $result->fetch_assoc()['cnt'];
        
        $stmt = $conn->prepare("SELECT COALESCE(SUM(total_price), 0) as total FROM orders WHERE shop_id = ?");
        $stmt->bind_param("i", $shop_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result) $total_sales = $result->fetch_assoc()['total'];
    }
} catch (Exception $e) {
    // Tables might not exist
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<!-- Hero Section -->
<section class="hero" style="padding: 40px 20px;">
    <div class="hero-content">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
        <p>Manage your shop and products</p>
    </div>
</section>

<div class="container">
    <?php if(!$shop): ?>
        <!-- No Shop Created -->
        <div class="no-shop-card">
            <div class="no-shop-icon">🏪</div>
            <h2>Create Your Shop</h2>
            <p>You haven't created a shop yet. Start selling your products today!</p>
            <a href="shop-profile.php" class="btn btn-primary">Create Shop</a>
        </div>
    <?php else: ?>
        <!-- Shop Info Card -->
        <div class="shop-info-card">
            <div class="shop-info-header">
                <div class="shop-avatar">
                    <?php echo strtoupper(substr($shop['shop_name'], 0, 1)); ?>
                </div>
                <div class="shop-details">
                    <h2><?php echo htmlspecialchars($shop['shop_name']); ?></h2>
                    <p><?php echo htmlspecialchars($shop['description'] ?? 'Premium meat shop'); ?></p>
                    <span class="shop-status">Active</span>
                </div>
                <a href="shop-profile.php" class="btn btn-secondary">Edit Shop</a>
            </div>
        </div>
        
        <!-- Dashboard Cards -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="dashboard-icon">📦</div>
                <h3>Products</h3>
                <p class="card-count"><?php echo $products_count; ?></p>
                <a href="add-product.php" class="btn btn-primary">Add Product</a>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-icon">🏷️</div>
                <h3>Active Bids</h3>
                <p class="card-count"><?php echo $active_bids; ?></p>
                <a href="view-bids.php" class="btn btn-primary">View Bids</a>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-icon">💰</div>
                <h3>Total Sales</h3>
                <p class="card-count">Rs <?php echo number_format($total_sales, 0); ?></p>
                <a href="view-bids.php" class="btn btn-primary">View Bids</a>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-icon">📊</div>
                <h3>Start Bid</h3>
                <p>Create new bids</p>
                <a href="start-bid.php" class="btn btn-primary">New Bid</a>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="section-header" style="margin-top: 40px;">
            <h2>Quick Actions</h2>
        </div>
        
        <div class="quick-actions">
            <a href="add-product.php" class="action-card">
                <span class="action-icon">➕</span>
                <span class="action-text">Add Product</span>
            </a>
            <a href="start-bid.php" class="action-card">
                <span class="action-icon">🏷️</span>
                <span class="action-text">Start Bid</span>
            </a>
            <a href="view-bids.php" class="action-card">
                <span class="action-icon">📋</span>
                <span class="action-text">View Bids</span>
            </a>
            <a href="shop-profile.php" class="action-card">
                <span class="action-icon">⚙️</span>
                <span class="action-text">Shop Settings</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
    .no-shop-card {
        background: var(--card-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        text-align: center;
        box-shadow: var(--shadow-md);
        max-width: 500px;
        margin: 0 auto;
    }
    
    .no-shop-icon {
        font-size: 4rem;
        margin-bottom: var(--spacing-md);
    }
    
    .no-shop-card h2 {
        color: var(--primary-color);
        margin-bottom: var(--spacing-sm);
    }
    
    .no-shop-card p {
        color: var(--text-light);
        margin-bottom: var(--spacing-md);
    }
    
    .shop-info-card {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
    }
    
    .shop-info-header {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }
    
    .shop-avatar {
        width: 80px;
        height: 80px;
        border-radius: var(--radius-lg);
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-white);
        font-size: 2rem;
        font-weight: bold;
    }
    
    .shop-details {
        flex: 1;
    }
    
    .shop-details h2 {
        color: var(--text-dark);
        margin-bottom: var(--spacing-xs);
    }
    
    .shop-details p {
        color: var(--text-light);
        margin-bottom: var(--spacing-xs);
    }
    
    .shop-status {
        display: inline-block;
        background: #e8f5e9;
        color: #2e7d32;
        padding: 4px 12px;
        border-radius: var(--radius-sm);
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-xl);
    }
    
    .dashboard-card {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-normal);
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }
    
    .dashboard-icon {
        font-size: 3rem;
        margin-bottom: var(--spacing-sm);
    }
    
    .dashboard-card h3 {
        color: var(--text-dark);
        margin-bottom: var(--spacing-xs);
    }
    
    .dashboard-card p {
        color: var(--text-light);
        margin-bottom: var(--spacing-sm);
    }
    
    .card-count {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color) !important;
    }
    
    .quick-actions {
        display: flex;
        gap: var(--spacing-md);
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .action-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: var(--spacing-md);
        background: var(--card-bg);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        transition: var(--transition-normal);
        min-width: 150px;
    }
    
    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        background: var(--primary-color);
    }
    
    .action-card:hover .action-text {
        color: var(--text-white);
    }
    
    .action-icon {
        font-size: 2rem;
        margin-bottom: var(--spacing-xs);
    }
    
    .action-text {
        color: var(--text-dark);
        font-weight: 500;
    }
</style>

<?php include "../includes/footer.php"; ?>

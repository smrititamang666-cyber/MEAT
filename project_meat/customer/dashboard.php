<?php
require_once "../includes/config.php";
require_once "../includes/session.php";

// Only allow customers
require_role('customer');

// Get user info
$user_id = (int)$_SESSION['user_id'];

// Get counts for dashboard
$wishlist_count = 0;
$orders_count = 0;
$active_bids = 0;

try {
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM wishlist WHERE customer_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result) $wishlist_count = $result->fetch_assoc()['cnt'];

    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM orders WHERE customer_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result) $orders_count = $result->fetch_assoc()['cnt'];

    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM bids WHERE customer_id = ? AND status = 'Open'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result) $active_bids = $result->fetch_assoc()['cnt'];
} catch (Exception $e) {
    // Tables might not exist
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<!-- Hero Section -->
<section class="hero" style="padding: 40px 20px;">
    <div class="hero-content">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
        <p>Manage your orders, wishlist, and bidding activities</p>
    </div>
</section>

<div class="container">
    <!-- Dashboard Cards -->
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="dashboard-icon">🛒</div>
            <h3>Browse Products</h3>
            <p>Explore our premium meat selection</p>
            <a href="browse-products.php" class="btn btn-primary">View Products</a>
        </div>
        
        <div class="dashboard-card">
            <div class="dashboard-icon">❤️</div>
            <h3>My Wishlist</h3>
            <p class="card-count"><?php echo $wishlist_count; ?> items</p>
            <a href="wishlist.php" class="btn btn-primary">View Wishlist</a>
        </div>
        
        <div class="dashboard-card">
            <div class="dashboard-icon">📦</div>
            <h3>My Orders</h3>
            <p class="card-count"><?php echo $orders_count; ?> orders</p>
            <a href="my-orders.php" class="btn btn-primary">View Orders</a>
        </div>
        
        <div class="dashboard-card">
            <div class="dashboard-icon">🏷️</div>
            <h3>Bidding Area</h3>
            <p class="card-count"><?php echo $active_bids; ?> active bids</p>
            <a href="bidding.php" class="btn btn-primary">View Bids</a>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="section-header" style="margin-top: 40px;">
        <h2>Quick Actions</h2>
    </div>
    
    <div class="quick-actions">
        <a href="browse-products.php" class="action-card">
            <span class="action-icon">🔍</span>
            <span class="action-text">Browse Products</span>
        </a>
        <a href="wishlist.php" class="action-card">
            <span class="action-icon">💝</span>
            <span class="action-text">My Wishlist</span>
        </a>
        <a href="my-orders.php" class="action-card">
            <span class="action-icon">🛍️</span>
            <span class="action-text">My Orders</span>
        </a>
        <a href="bidding.php" class="action-card">
            <span class="action-icon">💰</span>
            <span class="action-text">Active Bids</span>
        </a>
    </div>
</div>

<style>
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
        font-size: 1.5rem;
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

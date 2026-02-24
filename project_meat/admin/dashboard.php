<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('admin');

// Get counts for dashboard
$users_count = 0;
$sellers_count = 0;
$products_count = 0;
$active_bids = 0;

try {
    $result = $conn->query("SELECT COUNT(*) as cnt FROM users");
    if($result) $users_count = $result->fetch_assoc()['cnt'];
    
    $result = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'seller'");
    if($result) $sellers_count = $result->fetch_assoc()['cnt'];
    
    $result = $conn->query("SELECT COUNT(*) as cnt FROM products");
    if($result) $products_count = $result->fetch_assoc()['cnt'];
    
    $result = $conn->query("SELECT COUNT(*) as cnt FROM bids WHERE status = 'Open'");
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
        <h1>Admin Dashboard</h1>
        <p>Manage your marketplace</p>
    </div>
</section>

<div class="container">
    <!-- Dashboard Stats -->
    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo $users_count; ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🏪</div>
            <div class="stat-info">
                <h3><?php echo $sellers_count; ?></h3>
                <p>Sellers</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <h3><?php echo $products_count; ?></h3>
                <p>Products</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🏷️</div>
            <div class="stat-info">
                <h3><?php echo $active_bids; ?></h3>
                <p>Active Bids</p>
            </div>
        </div>
    </div>
    
    <!-- Management Cards -->
    <div class="section-header">
        <h2>Management</h2>
    </div>
    
    <div class="management-grid">
        <div class="management-card">
            <div class="management-icon">👥</div>
            <h3>Manage Users</h3>
            <p>View and manage customer accounts</p>
            <a href="manage-user.php" class="btn btn-primary">Manage Users</a>
        </div>
        
        <div class="management-card">
            <div class="management-icon">🏪</div>
            <h3>Manage Sellers</h3>
            <p>View and manage seller shops</p>
            <a href="manage-sellers.php" class="btn btn-primary">Manage Sellers</a>
        </div>
        
        <div class="management-card">
            <div class="management-icon">📦</div>
            <h3>Manage Products</h3>
            <p>View and manage all products</p>
            <a href="manage-products.php" class="btn btn-primary">Manage Products</a>
        </div>
        
        <div class="management-card">
            <div class="management-icon">🏷️</div>
            <h3>Manage Bids</h3>
            <p>View and manage bidding activities</p>
            <a href="manage-bids.php" class="btn btn-primary">Manage Bids</a>
        </div>
    </div>
</div>

<style>
    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-xl);
    }
    
    .stat-card {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
        box-shadow: var(--shadow-sm);
        transition: var(--transition-normal);
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    
    .stat-icon {
        font-size: 2.5rem;
    }
    
    .stat-info h3 {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: var(--spacing-xs);
    }
    
    .stat-info p {
        color: var(--text-light);
        font-size: 0.9rem;
    }
    
    .management-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-md);
    }
    
    .management-card {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-normal);
    }
    
    .management-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }
    
    .management-icon {
        font-size: 3rem;
        margin-bottom: var(--spacing-sm);
    }
    
    .management-card h3 {
        color: var(--text-dark);
        margin-bottom: var(--spacing-xs);
    }
    
    .management-card p {
        color: var(--text-light);
        margin-bottom: var(--spacing-md);
    }
</style>

<?php include "../includes/footer.php"; ?>

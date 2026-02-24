<?php
require_once "../includes/config.php";
require_once "../includes/session.php";

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);

// Fetch categories from database (with fallback to static if table doesn't exist)
$categories = [];
$category_icons = [
    'red_meat' => '🥩',
    'white_meat' => '🍗',
    'fish' => '🐟',
    'shellfish' => '🦐'
];

try {
    // Try to fetch categories from database
    $result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
} catch (Exception $e) {
    // Table doesn't exist or other error, use static categories
}

// If no categories from database, use static fallback
if (empty($categories)) {
    $categories = [
        ['id' => 1, 'name' => 'Exquisite Red Meat', 'description' => 'Premium beef, lamb & pork', 'slug' => 'red_meat'],
        ['id' => 2, 'name' => 'Exquisite White Meat', 'description' => 'Chicken, turkey & more', 'slug' => 'white_meat'],
        ['id' => 3, 'name' => 'Exquisite Fish', 'description' => 'Fresh salmon, tuna & cod', 'slug' => 'fish'],
        ['id' => 4, 'name' => 'Shellfish', 'description' => 'Lobster, crab & shrimp', 'slug' => 'shellfish']
    ];
}

// Fetch featured products with category name
$featured_products = [];
try {
    $result = $conn->query("
        SELECT p.*, c.name AS category_name, c.icon AS category_icon
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC
        LIMIT 6
    ");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $featured_products[] = $row;
        }
    }
} catch (Exception $e) {
    // Products table might not exist
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exquisite Meat Marketplace</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include "../includes/navbar.php"; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Welcome to Exquisite Meat Marketplace</h1>
        <p>Discover the finest selection of premium meats, fresh seafood, and gourmet specialties delivered to your doorstep.</p>
        <?php if(!$logged_in): ?>
        <div class="hero-buttons">
            <a href="register.php" class="btn btn-primary">Get Started</a>
            <a href="login.php" class="btn btn-secondary">Login</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<div class="container">
    <!-- User Welcome Bar (Logged in users) -->
    <?php if($logged_in): ?>
        <div class="user-bar">
            <div class="welcome-text">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                </div>
                <div>
                    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
                    <p>Ready to explore our premium selections?</p>
                </div>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    <?php else: ?>
        <!-- CTA for non-logged in users -->
        <div class="cta-section">
            <h2>Join Our Premium Meat Club</h2>
            <p>Sign up today to access exclusive deals, bid on premium cuts, and enjoy fresh deliveries.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-primary">Register Now</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="section-header">
            <h2>Exquisite Categories</h2>
            <p>Browse our curated selection of premium meats and seafood</p>
        </div>
        
        <div class="categories">
            <?php foreach($categories as $index => $category): ?>
                <?php 
                    $slug = isset($category['slug']) ? $category['slug'] : strtolower(str_replace(' ', '_', $category['name']));
                    $icon = isset($category_icons[$slug]) ? $category_icons[$slug] : '🥩';
                    $description = isset($category['description']) ? $category['description'] : 'Premium quality selection';
                ?>
                <div class="category-box <?php echo $slug; ?>">
                    <span class="category-icon"><?php echo $icon; ?></span>
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p><?php echo htmlspecialchars($description); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="section-header">
            <h2>Why Choose Us</h2>
            <p>Experience the difference with our premium quality commitment</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🏆</div>
                <h3>Premium Quality</h3>
                <p>Sourced from the finest farms and suppliers, guaranteeing exceptional taste and freshness.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3>Fast Delivery</h3>
                <p>Fresh products delivered to your doorstep within hours of ordering.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Secure Bidding</h3>
                <p>Transparent bidding system with secure payments and quality guarantees.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3>Customer Reviews</h3>
                <p>Rate and review products to help others make informed decisions.</p>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <?php if(!empty($featured_products)): ?>
    <section class="featured-section">
        <div class="section-header">
            <h2>Featured Products</h2>
            <p>Discover our most popular premium selections</p>
        </div>
        
        <div class="featured-products">
            <?php foreach($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php
                            // Display icon from category or fallback emoji
                            $cat_name = strtolower($product['category_name'] ?? '');
                            if(!empty($product['category_icon'])) echo $product['category_icon'];
                            elseif(strpos($cat_name, 'red') !== false) echo '🥩';
                            elseif(strpos($cat_name, 'white') !== false) echo '🍗';
                            elseif(strpos($cat_name, 'fish') !== false || strpos($cat_name, 'shell') !== false) echo '🐟';
                            else echo '🥩';
                        ?>
                    </div>
                    <div class="product-info">
                        <span class="category-tag"><?php echo htmlspecialchars($product['category_name'] ?? 'Meat'); ?></span>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="price">
                            Rs <?php echo number_format($product['price'], 2); ?>
                        </div>
                        <p><?php echo htmlspecialchars(substr($product['description'] ?? 'Premium quality meat', 0, 80)); ?>...</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Call to Action -->
    <section class="cta-section">
        <h2>Ready to Order?</h2>
        <p>Join thousands of satisfied customers who enjoy our premium meat selection.</p>
        <div class="cta-buttons">
            <?php if($logged_in): ?>
                <a href="../customer/browse-products.php" class="btn btn-primary">Browse Products</a>
                <a href="../customer/dashboard.php" class="btn btn-secondary">My Dashboard</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary">Create Account</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include "../includes/footer.php"; ?>

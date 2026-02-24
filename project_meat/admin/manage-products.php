<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('admin');

$products = $conn->query("
    SELECT p.*, s.shop_name, c.name AS category_name
    FROM products p
    LEFT JOIN shops s ON p.shop_id = s.id
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>Manage Products</h2>
        <p>View and manage all marketplace products</p>
    </div>

    <?php if($products && $products->num_rows > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Shop</th>
                    <th>Price (Rs)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['shop_name'] ?? 'N/A'); ?></td>
                    <td>Rs <?php echo number_format($row['price'], 2); ?></td>
                    <td class="action-links">
                        <a href="delete-product.php?id=<?php echo (int)$row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this product?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📦</div>
            <h3>No Products Found</h3>
            <p>There are no products in the marketplace yet.</p>
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

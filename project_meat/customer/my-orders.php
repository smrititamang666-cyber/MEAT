<?php
require_once "../includes/config.php";
require_once "../includes/session.php";

// Only allow customers
require_role('customer');

// Get user info
$user_id = (int)$_SESSION['user_id'];

// Get user's orders with product and shop info using prepared statement
$orders = [];
try {
    $stmt = $conn->prepare("
        SELECT o.*, p.name AS product_name, p.price AS unit_price, 
               p.image AS product_image, s.shop_name,
               c.name AS category_name
        FROM orders o
        LEFT JOIN products p ON o.product_id = p.id
        LEFT JOIN shops s ON o.shop_id = s.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE o.customer_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
} catch (Exception $e) {
    // Table might not exist or query error
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>My Orders</h2>
        <p>View your order history</p>
    </div>

    <?php if(empty($orders)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📦</div>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet. Start browsing our premium selection!</p>
            <a href="browse-products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Shop</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td>#<?php echo (int)$order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['product_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['shop_name'] ?? 'N/A'); ?></td>
                    <td><?php echo (int)($order['quantity'] ?? 1); ?></td>
                    <td>Rs <?php echo number_format($order['total_price'] ?? 0, 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower($order['status'] ?? 'pending'); ?>">
                            <?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?>
                        </span>
                    </td>
                    <td><?php echo isset($order['created_at']) ? date('M d, Y', strtotime($order['created_at'])) : 'N/A'; ?></td>
                    <td class="action-links">
                        <?php if(($order['status'] ?? '') == 'Paid'): ?>
                            <a href="rate-product.php?product_id=<?php echo (int)$order['product_id']; ?>" class="btn-edit">Rate</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

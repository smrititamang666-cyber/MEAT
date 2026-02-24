<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('admin');

// Fetch all bids with product, seller, and customer info
$bids = $conn->query("
    SELECT b.*, p.name AS product_name, 
           s.shop_name,
           u.name AS bidder_name
    FROM bids b
    JOIN products p ON b.product_id = p.id
    JOIN shops s ON p.shop_id = s.id
    LEFT JOIN users u ON b.customer_id = u.id
    ORDER BY b.created_at DESC
");
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>Manage Bids</h2>
        <p>View and manage all bidding activities</p>
    </div>

    <?php if($bids && $bids->num_rows > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Shop</th>
                    <th>Current Bid (Rs)</th>
                    <th>Highest Bidder</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $bids->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['shop_name']); ?></td>
                    <td>Rs <?php echo number_format($row['bid_amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['bidder_name'] ?? 'No bids yet'); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower(htmlspecialchars($row['status'])); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td class="action-links">
                        <?php if($row['status'] == 'Open'): ?>
                            <a href="close-bid.php?bid_id=<?php echo (int)$row['id']; ?>" class="btn-close-bid" onclick="return confirm('Close this bid?');">Close</a>
                        <?php else: ?>
                            <span style="color:var(--text-light);">Closed</span>
                        <?php endif; ?>
                        <a href="delete-bid.php?id=<?php echo (int)$row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this bid?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏷️</div>
            <h3>No Bids Found</h3>
            <p>There are no bidding activities yet.</p>
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

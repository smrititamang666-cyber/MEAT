<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('seller');

$seller_id = (int)$_SESSION['user_id'];

// Get seller's shop first
$shop_stmt = $conn->prepare("SELECT id FROM shops WHERE user_id = ? LIMIT 1");
$shop_stmt->bind_param("i", $seller_id);
$shop_stmt->execute();
$shop = $shop_stmt->get_result()->fetch_assoc();

$bids = null;
if ($shop) {
    $shop_id = (int)$shop['id'];
    // Fetch all bids for seller's shop using shop_id
    $bid_stmt = $conn->prepare("
        SELECT b.id AS bid_id, p.name AS product_name, b.bid_amount, u.name AS highest_bidder, b.status
        FROM bids b
        JOIN products p ON b.product_id = p.id
        LEFT JOIN users u ON b.customer_id = u.id
        WHERE b.shop_id = ?
        ORDER BY b.created_at DESC
    ");
    $bid_stmt->bind_param("i", $shop_id);
    $bid_stmt->execute();
    $bids = $bid_stmt->get_result();
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>Manage Bids</h2>
        <p>View and manage your active bids</p>
    </div>

    <?php if($bids && $bids->num_rows > 0): ?>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Current Highest Bid (Rs)</th>
                <th>Highest Bidder</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $bids->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td>Rs <?php echo number_format($row['bid_amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['highest_bidder'] ?? 'No bids yet'); ?></td>
            <td>
                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                </span>
            </td>
            <td class="action-links">
                <?php if($row['status'] == 'Open'): ?>
                    <a href="close-bid.php?bid_id=<?php echo (int)$row['bid_id']; ?>" class="btn-close-bid" onclick="return confirm('Close this bid?');">Close Bid</a>
                <?php else: ?>
                    Closed
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏷️</div>
            <h3>No Bids Found</h3>
            <p>You haven't started any bids yet.</p>
            <a href="start-bid.php" class="btn btn-primary">Start a Bid</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

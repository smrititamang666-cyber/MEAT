<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('customer');

$msg = "";

// Place a bid
if(isset($_POST['place_bid'])){
    $bid_id = (int)$_POST['bid_id'];
    $bid_amount = (float)$_POST['bid_amount'];
    $customer_id = (int)$_SESSION['user_id'];

    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT bid_amount FROM bids WHERE id = ?");
    $stmt->bind_param("i", $bid_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current = $result->fetch_assoc();

    if($current && $bid_amount > $current['bid_amount']){
        // Update bid with prepared statement
        $update_stmt = $conn->prepare("UPDATE bids SET bid_amount = ?, customer_id = ? WHERE id = ?");
        $update_stmt->bind_param("dii", $bid_amount, $customer_id, $bid_id);
        $update_stmt->execute();
        $msg = "Bid placed successfully!";
    } else {
        $msg = "Your bid must be higher than the current bid.";
    }
}

// Fetch all open bids using prepared statement
$bid_stmt = $conn->prepare("
    SELECT b.id AS bid_id, p.name AS product_name, s.shop_name, b.bid_amount
    FROM bids b
    JOIN products p ON b.product_id = p.id
    JOIN shops s ON b.shop_id = s.id
    WHERE b.status = 'Open'
");
$bid_stmt->execute();
$bids = $bid_stmt->get_result();
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>Bidding Area</h2>
        <p>Place your bids on premium meat products</p>
    </div>

    <?php if($msg): ?>
        <p style="color:<?php echo strpos($msg, 'successfully') !== false ? 'green' : 'red'; ?>; margin-bottom:15px;">
            <?php echo htmlspecialchars($msg); ?>
        </p>
    <?php endif; ?>

    <?php if($bids && $bids->num_rows > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Shop</th>
                    <th>Current Bid (Rs)</th>
                    <th>Place Your Bid</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $bids->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['shop_name']); ?></td>
                <td>Rs <?php echo number_format($row['bid_amount'], 2); ?></td>
                <td>
                    <form method="POST" style="display:flex; gap:8px; align-items:center;">
                        <input type="hidden" name="bid_id" value="<?php echo (int)$row['bid_id']; ?>">
                        <input type="number" step="0.01" name="bid_amount" 
                               min="<?php echo $row['bid_amount'] + 0.01; ?>"
                               placeholder="Enter amount" required
                               style="width:130px; padding:6px; border:1px solid #ccc; border-radius:4px;">
                        <button type="submit" name="place_bid" class="btn btn-primary" style="padding:6px 12px;">Bid</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏷️</div>
            <h3>No Open Bids</h3>
            <p>There are no active bids at the moment. Check back later!</p>
            <a href="browse-products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

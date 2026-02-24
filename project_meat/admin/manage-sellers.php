<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('admin');

// Fetch only sellers with their shop info
$sellers = $conn->query("
    SELECT u.id, u.name, u.email, u.phone, u.status, u.created_at,
           s.shop_name, s.rating
    FROM users u
    LEFT JOIN shops s ON s.user_id = u.id
    WHERE u.role = 'seller'
    ORDER BY u.id DESC
");
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>Manage Sellers</h2>
        <p>View and manage all seller accounts and their shops</p>
    </div>

    <?php if($sellers && $sellers->num_rows > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Shop Name</th>
                    <th>Rating</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $sellers->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['shop_name'] ?? 'No shop yet'); ?></td>
                    <td><?php echo $row['rating'] !== null ? number_format($row['rating'], 1) : 'N/A'; ?></td>
                    <td class="action-links">
                        <a href="delete-user.php?id=<?php echo (int)$row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this seller?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏪</div>
            <h3>No Sellers Found</h3>
            <p>There are no registered sellers yet.</p>
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

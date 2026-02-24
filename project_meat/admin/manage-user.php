<?php
require_once "../includes/config.php";
require_once "../includes/session.php";
require_role('admin');

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container">
    <div class="section-header">
        <h2>All Users</h2>
        <p>Manage registered users</p>
    </div>

    <?php if($users && $users->num_rows > 0): ?>
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo (int)$row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
                <span class="status-badge status-<?php echo strtolower(htmlspecialchars($row['role'])); ?>">
                    <?php echo htmlspecialchars($row['role']); ?>
                </span>
            </td>
            <td class="action-links">
                <a href="delete-user.php?id=<?php echo (int)$row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h3>No Users Found</h3>
            <p>There are no registered users yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>

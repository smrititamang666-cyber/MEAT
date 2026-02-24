<?php
// Determine base path for links
$current_file = basename($_SERVER['PHP_SELF']);
$is_root = ($current_file == 'index.php' || $current_file == 'login.php' || $current_file == 'register.php' || $current_file == 'logout.php');
$base = $is_root ? '' : '../';
?>
<nav>
    <div class="logo">
        <a href="<?php echo $base; ?>index.php"><span>Exquisite</span> Meat</a>
    </div>
    <ul>
        <li><a href="<?php echo $base; ?>index.php">Home</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php 
            $role = $_SESSION['role'] ?? '';
            $dashboard_link = '#';
            if($role == 'admin') $dashboard_link = $base . 'admin/dashboard.php';
            elseif($role == 'seller') $dashboard_link = $base . 'seller/dashboard.php';
            else $dashboard_link = $base . 'customer/dashboard.php';
            ?>
            <li><a href="<?php echo $dashboard_link; ?>">Dashboard</a></li>
            <li><a href="<?php echo $base; ?>public/logout.php" class="nav-logout">Logout (<?php echo htmlspecialchars($_SESSION['name']); ?>)</a></li>
        <?php else: ?>
            <li><a href="<?php echo $base; ?>public/login.php">Login</a></li>
            <li><a href="<?php echo $base; ?>public/register.php" class="nav-register">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

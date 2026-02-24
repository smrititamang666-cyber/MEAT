<?php
// Determine base path for links (same logic as navbar)
$current_file = basename($_SERVER['PHP_SELF']);
$is_root = ($current_file == 'index.php' || $current_file == 'login.php' || $current_file == 'register.php' || $current_file == 'logout.php');
$footer_base = $is_root ? '' : '../public/';
?>
<hr>
<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>Exquisite Meat Marketplace</h3>
            <p>Your premium source for quality meats and seafood.</p>
        </div>
        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo $footer_base; ?>index.php">Home</a></li>
                <li><a href="<?php echo $footer_base; ?>login.php">Login</a></li>
                <li><a href="<?php echo $footer_base; ?>register.php">Register</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Contact Us</h4>
            <p>Email: info@exquisitemeat.com</p>
            <p>Phone: +977-1-xxxxxxx</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Exquisite Meat Marketplace. All Rights Reserved.</p>
    </div>
</footer>
</body>
</html>

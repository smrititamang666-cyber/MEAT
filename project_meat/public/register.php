<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";

// Initialize variables
$error = $success = "";

// Handle form submission
if(isset($_POST['register'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate role - only allow 'seller' or 'customer', never 'admin'
    $allowed_roles = ['seller', 'customer'];
    $role = in_array($_POST['role'], $allowed_roles) ? $_POST['role'] : 'customer';

    // Validate passwords
    if($password !== $confirm_password){
        $error = "Passwords do not match!";
    } elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters!";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email or phone exists using prepared statement
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $check_stmt->bind_param("ss", $email, $phone);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if($check_result->num_rows > 0){
            $error = "Email or phone already exists!";
        } else {
            // Insert user using prepared statement
            $insert_stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $role);
            if($insert_stmt->execute()){
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error: " . $insert_stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Exquisite Meat Marketplace</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .auth-container {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
        }
        
        .auth-card {
            background: var(--card-bg);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            padding: var(--spacing-xl);
            width: 100%;
            max-width: 500px;
            animation: slideInUp 0.6s ease-out;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }
        
        .auth-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: var(--spacing-xs);
        }
        
        .auth-header p {
            color: var(--text-light);
        }
        
        .form-group {
            margin-bottom: var(--spacing-md);
        }
        
        .form-group label {
            display: block;
            margin-bottom: var(--spacing-xs);
            color: var(--text-dark);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: var(--spacing-sm);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition-normal);
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
        }
        
        .role-selector {
            display: flex;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-md);
        }
        
        .role-option {
            flex: 1;
            position: relative;
        }
        
        .role-option input {
            position: absolute;
            opacity: 0;
        }
        
        .role-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: var(--spacing-md);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition-normal);
        }
        
        .role-option label:hover {
            border-color: var(--primary-light);
        }
        
        .role-option input:checked + label {
            border-color: var(--primary-color);
            background: rgba(139, 0, 0, 0.05);
        }
        
        .role-icon {
            font-size: 2rem;
            margin-bottom: var(--spacing-xs);
        }
        
        .role-name {
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .btn-submit {
            width: 100%;
            padding: var(--spacing-sm);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: var(--text-white);
            border: none;
            border-radius: var(--radius-md);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-normal);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .auth-error {
            background: #ffebee;
            color: #c62828;
            padding: var(--spacing-sm);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
            text-align: center;
        }
        
        .auth-success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: var(--spacing-sm);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
            text-align: center;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: var(--spacing-md);
            color: var(--text-light);
        }
        
        .auth-footer a {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .auth-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .back-home {
            text-align: center;
            margin-top: var(--spacing-md);
        }
        
        .back-home a {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .back-home a:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
<?php include "../includes/navbar.php"; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Create Account</h1>
            <p>Join our premium meat marketplace</p>
        </div>
        
        <?php if($error): ?>
            <div class="auth-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="auth-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number (Nepali)</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password (min 6 characters)" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            
            <div class="form-group">
                <label>I want to register as:</label>
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" id="role_customer" name="role" value="customer" required>
                        <label for="role_customer">
                            <span class="role-icon">🛒</span>
                            <span class="role-name">Customer</span>
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="role_seller" name="role" value="seller">
                        <label for="role_seller">
                            <span class="role-icon">🏪</span>
                            <span class="role-name">Seller</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="register" class="btn-submit">Create Account</button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
        
        <div class="back-home">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>

<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";

$error = "";
if(isset($_POST['login'])){
    $email_or_phone = $_POST['email_or_phone'];
    $password = $_POST['password'];

    $error = login_user($email_or_phone, $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Exquisite Meat Marketplace</title>
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
            max-width: 450px;
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
            <h1>Welcome Back</h1>
            <p>Login to access your account</p>
        </div>
        
        <?php if($error): ?>
            <div class="auth-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email_or_phone">Email or Phone</label>
                <input type="text" id="email_or_phone" name="email_or_phone" placeholder="Enter your email or phone" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" name="login" class="btn-submit">Login</button>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
        
        <div class="back-home">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>

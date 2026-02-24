<?php
/**
 * Database Setup Script for Exquisite Meat Marketplace
 * Run this file to create the database and all tables
 */

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'meat';

echo "<h1>Exquisite Meat Marketplace - Database Setup</h1>";

try {
    // Connect without database first to create it
    $conn = new mysqli($db_host, $db_user, $db_pass);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>✓ Connected to MySQL server</p>";
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        echo "<p>✓ Database '$db_name' created or already exists</p>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db($db_name);
    echo "<p>✓ Selected database '$db_name'</p>";
    
    // Read and execute SQL file
    $sql_file = file_get_contents(__DIR__ . '/database.sql');
    
    // Split SQL statements
    $statements = array_filter(array_map('trim', explode(';', $sql_file)));
    
    $table_count = 0;
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0 || strpos($statement, '/*') === 0) {
            continue;
        }
        
        if ($conn->query($statement)) {
            // Check if it's a CREATE TABLE statement
            if (stripos($statement, 'CREATE TABLE') !== false) {
                $table_count++;
            }
        } else {
            // Ignore duplicate entry errors for INSERT statements
            if (strpos($conn->error, 'Duplicate') === false) {
                echo "<p style='color: orange;'>Warning: " . $conn->error . "</p>";
            }
        }
    }
    
    echo "<p>✓ Created $table_count tables</p>";
    echo "<p>✓ Inserted default data (categories, sample users, sample shop)</p>";
    
    echo "<h2 style='color: green;'>Setup Complete!</h2>";
    echo "<p><strong>Default Login Credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Admin: admin@exquisitemeat.com / password</li>";
    echo "<li>Seller: seller@exquisitemeat.com / password</li>";
    echo "<li>Customer: customer@exquisitemeat.com / password</li>";
    echo "</ul>";
    echo "<p><a href='public/index.php'>Go to Homepage</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error!</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    exit(1);
}
?>

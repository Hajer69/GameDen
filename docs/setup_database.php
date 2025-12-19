<?php


$host = 'localhost';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Database name
$dbname = 'gamedendb';

echo "<h2>GameDen Database Setup</h2>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "✅ Database '$dbname' created<br>";
} else {
    echo "❌ Error: " . $conn->error . "<br>";
}

// Select database
$conn->select_db($dbname);

// Create contact_messages table
$sql = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    newsletter ENUM('yes','no') DEFAULT 'no',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✅ Table 'contact_messages' created<br>";
} else {
    echo "❌ Error: " . $conn->error . "<br>";
}

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✅ Table 'users' created<br>";
} else {
    echo "❌ Error: " . $conn->error . "<br>";
}

// Add sample user
$sql = "INSERT IGNORE INTO users (username, email, password) 
        VALUES ('testuser', 'test@example.com', '" . password_hash('test123', PASSWORD_DEFAULT) . "')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Sample user added<br>";
}

echo "<div style='background: #d4edda; padding: 15px; margin-top: 20px; border-radius: 5px;'>
        <h3>✅ Setup Complete!</h3>
        <p>Database '$dbname' is ready.</p>
        <p>
            <a href='admin_panel.php' style='padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>
                Admin Panel
            </a>
            <a href='contact.html' style='padding: 8px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;'>
                Contact Form
            </a>
        </p>
      </div>";

$conn->close();
?>

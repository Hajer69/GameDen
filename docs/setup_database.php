<?php

// Database connection parameters
$host = 'localhost';
$user = 'root';
$pass = 'root';
$port = 8889;

// Create connection without database selected
$conn = new mysqli($host, $user, $pass, '', $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h3>Database Setup for GameDen</h3>";

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS gameden_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
if ($conn->query($sql) === TRUE) {
    echo "<p>✅ Database 'gameden_db' created successfully</p>";
} else {
    echo "<p>❌ Error creating database: " . $conn->error . "</p>";
}

// Select the database
$conn->select_db('gameden_db');

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
    echo "<p>✅ Table 'contact_messages' created successfully</p>";
} else {
    echo "<p>❌ Error creating table: " . $conn->error . "</p>";
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
    echo "<p>✅ Table 'users' created successfully</p>";
} else {
    echo "<p>❌ Error creating table: " . $conn->error . "</p>";
}

// Insert sample users
$sql = "INSERT IGNORE INTO users (username, email, password) VALUES 
    ('admin', 'admin@gameden.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "'),
    ('player1', 'player1@gameden.com', '" . password_hash('player123', PASSWORD_DEFAULT) . "'),
    ('gamer2', 'gamer2@gameden.com', '" . password_hash('gamer456', PASSWORD_DEFAULT) . "')";

if ($conn->query($sql) === TRUE) {
    echo "<p>✅ Sample users inserted</p>";
} else {
    echo "<p>❌ Error inserting users: " . $conn->error . "</p>";
}

$conn->close();

echo "<div class='alert alert-success mt-3'>";
echo "<h4>Setup Completed!</h4>";
echo "<p>Database and tables are ready.</p>";
echo "<p><a href='admin_panel.php' class='btn btn-primary'>Go to Admin Panel</a></p>";
echo "</div>";
?>
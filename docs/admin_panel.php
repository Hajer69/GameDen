<?php

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gamedendb';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize count_row
$count_row = ['total' => 0];

// Process DELETE request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM contact_messages WHERE id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        $delete_message = "Message deleted successfully";
    } else {
        $delete_error = "Error deleting message: " . $conn->error;
    }
    // Redirect to remove delete_id from URL
    header("Location: admin_panel.php");
    exit();
}

// Process UPDATE request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);
    $newsletter = $_POST['newsletter'];
    
    $sql = "UPDATE contact_messages SET newsletter = '$newsletter' WHERE id = $update_id";
    if ($conn->query($sql) === TRUE) {
        $update_message = "Message updated successfully";
    } else {
        $update_error = "Error updating message: " . $conn->error;
    }
}

// Count total messages
$count_sql = "SELECT COUNT(*) as total FROM contact_messages";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    $count_sql = "SELECT COUNT(*) as total FROM contact_messages 
                 WHERE name LIKE '%$search_term%' 
                 OR email LIKE '%$search_term%' 
                 OR message LIKE '%$search_term%'";
}
$count_result = $conn->query($count_sql);
if ($count_result) {
    $count_row = $count_result->fetch_assoc();
} else {
    $count_row = ['total' => 0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - GameDen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .header { background: linear-gradient(135deg, #203079 0%, #7C4DFF 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .search-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .table-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-custom { background-color: #7C4DFF; color: white; border: none; }
        .btn-custom:hover { background-color: #6b3ccc; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>GameDen Admin Panel</h1>
                    <p class="mb-0">Manage contact messages and user data</p>
                </div>
                <div>
                    <a href="contact.html" class="btn btn-light me-2">Contact Form</a>
                    <a href="index.html" class="btn btn-outline-light">Home Page</a>
                </div>
            </div>
        </div>

        <!-- Display messages -->
        <?php if (isset($delete_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $delete_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($update_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $update_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Search Functionality -->
        <div class="search-box">
            <h4>🔍 Search Messages</h4>
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, email, or message..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-custom w-100">Search</button>
                </div>
                <div class="col-md-2">
                    <a href="admin_panel.php" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>

        <!-- Messages Table -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Contact Messages</h4>
                <span class="badge bg-primary">
                    <?php echo $count_row['total'] . " messages"; ?>
                </span>
            </div>

            <?php
            // Build search condition
            $search_condition = "";
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search_term = $conn->real_escape_string($_GET['search']);
                $search_condition = " WHERE name LIKE '%$search_term%' 
                                     OR email LIKE '%$search_term%' 
                                     OR message LIKE '%$search_term%' 
                                     OR subject LIKE '%$search_term%'";
            }

            // Fetch messages from database
            $sql = "SELECT * FROM contact_messages $search_condition ORDER BY created_at DESC";
            $result = $conn->query($sql);
            
            // Check if query succeeded
            if ($result === FALSE) {
                echo '<div class="alert alert-danger">';
                echo '<h5>Database Query Error</h5>';
                echo '<p>Error: ' . $conn->error . '</p>';
                echo '<p>SQL: ' . htmlspecialchars($sql) . '</p>';
                echo '</div>';
            } elseif ($result->num_rows > 0) { 
            ?>
            
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Newsletter</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($row['subject']); ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?php 
                                        $message = htmlspecialchars($row['message']);
                                        echo strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
                                    ?></small>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="update_id" value="<?php echo $row['id']; ?>">
                                        <select name="newsletter" class="form-select form-select-sm" 
                                                onchange="this.form.submit()" style="width: auto;">
                                            <option value="yes" <?php echo $row['newsletter'] == 'yes' ? 'selected' : ''; ?>>Yes</option>
                                            <option value="no" <?php echo $row['newsletter'] == 'no' ? 'selected' : ''; ?>>No</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <small><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                                </td>
                                <td>
                                    <!-- DELETE button -->
                                    <a href="admin_panel.php?delete_id=<?php echo $row['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this message?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            
            <?php 
            } else { 
            ?>
            
                <div class="alert alert-info">
                    No messages found. 
                    <?php if (isset($_GET['search'])): ?>
                        Try a different search term.
                    <?php else: ?>
                        <a href="contact.html">Send a test message</a>
                    <?php endif; ?>
                </div>
            
            <?php 
            } 
            ?>
        </div>

        <!-- Database Info -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Database Information</h5>
                <p class="card-text">
                    <strong>Database:</strong> gamedendb<br>
                    <strong>Tables:</strong> contact_messages, users<br>
                    <strong>Total Messages:</strong> <?php echo $count_row['total']; ?><br>
                    <strong>Connection:</strong> <?php echo $conn->host_info; ?>
                </p>
                <a href="setup_database.php" class="btn btn-outline-primary btn-sm">Reset Database</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
<?php $conn->close(); ?>

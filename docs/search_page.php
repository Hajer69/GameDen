<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gamedendb';


$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Messages - GameDen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .search-container { max-width: 800px; margin: 50px auto; }
        .search-box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .results-box { background: white; padding: 20px; border-radius: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="search-container">
        <div class="search-box">
            <h1 class="text-center mb-4">🔍 Search Contact Messages</h1>
            
            <form method="GET" class="mb-4">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" class="form-control" 
                           placeholder="Enter search terms (name, email, or message content)..." 
                           value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
                <div class="form-text mt-2">Search across all message fields</div>
            </form>

            <?php if (isset($_GET['q']) && !empty($_GET['q'])): ?>
                <?php
                $search_term = $conn->real_escape_string($_GET['q']);
                $sql = "SELECT * FROM contact_messages 
                        WHERE name LIKE '%$search_term%' 
                        OR email LIKE '%$search_term%' 
                        OR message LIKE '%$search_term%' 
                        OR subject LIKE '%$search_term%'
                        ORDER BY created_at DESC";
                
                $result = $conn->query($sql);
                ?>
                
                <div class="results-box">
                    <h4>Search Results for "<?php echo htmlspecialchars($_GET['q']); ?>"</h4>
                    
                    <?php if ($result->num_rows > 0): ?>
                        <p class="text-muted">Found <?php echo $result->num_rows; ?> result(s)</p>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo htmlspecialchars($row['subject']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $message = htmlspecialchars($row['message']);
                                            echo strlen($message) > 100 ? substr($message, 0, 100) . '...' : $message;
                                            ?>
                                        </td>
                                        <td>
                                            <small><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            No results found for your search. Try different keywords.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="admin_panel.php" class="btn btn-outline-primary me-2">Advanced Admin Search</a>
                <a href="contact.html" class="btn btn-outline-secondary">Back to Contact Form</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>

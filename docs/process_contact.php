<?php
// ============================================
// DATABASE CONNECTION FOR MAMP
// ============================================
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'gameden_db';
$port = 8889; // Default MAMP MySQL port

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ============================================
// CREATE TABLE IF NOT EXISTS
// ============================================
$createTableSQL = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    newsletter ENUM('yes', 'no') DEFAULT 'no',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($createTableSQL)) {
    die("Error creating table: " . $conn->error);
}

// ============================================
// PART C: CLASS DEFINITION
// ============================================
class ContactRecord {
    public $id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $newsletter;
    public $created_at;
    
    public function __construct($id, $name, $email, $subject, $message, $newsletter, $created_at) {
        $this->id = $id;
        $this->name = htmlspecialchars($name);
        $this->email = htmlspecialchars($email);
        $this->subject = htmlspecialchars($subject);
        $this->message = htmlspecialchars($message);
        $this->newsletter = $newsletter;
        $this->created_at = $created_at;
    }
    
    public function displayAsTableRow() {
        $messagePreview = (strlen($this->message) > 50) ? substr($this->message, 0, 50) . "..." : $this->message;
        
        return "<tr>
                <td>{$this->id}</td>
                <td>{$this->name}</td>
                <td>{$this->email}</td>
                <td>{$this->subject}</td>
                <td>{$messagePreview}</td>
                <td>{$this->newsletter}</td>
                <td>{$this->created_at}</td>
            </tr>";
    }
}

// ============================================
// HANDLE FORM SUBMISSION
// ============================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $newsletter = $_POST['newsletter'] ?? 'no';
    
    // Prepare SQL statement (prevent SQL injection)
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, newsletter) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $subject, $message, $newsletter);
    
    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission - GameDen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Your existing styles here */
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold text-primary-custom" href="index.html">
                GameDen
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.html">Back to Contact</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                    
                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success">
                            <h4>✓ Message Received Successfully!</h4>
                            <p>Your message has been saved to our database.</p>
                        </div>
                    <?php elseif (isset($error)): ?>
                        <div class="alert alert-danger">
                            <h4>✗ Database Error</h4>
                            <p><?php echo $error; ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Display submitted data -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Your Submitted Information</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                            <p><strong>Subject:</strong> <?php echo htmlspecialchars($subject); ?></p>
                            <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($message)); ?></p>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <div class="alert alert-warning">
                        <h4>No Form Data Received</h4>
                        <p>Please submit the contact form first.</p>
                        <a href="contact.html" class="btn btn-primary">Go to Contact Form</a>
                    </div>
                <?php endif; ?>
                
                <!-- ============================================
                PART C: DISPLAY ALL RECORDS FROM DATABASE
                ============================================ -->
                <?php
                // Retrieve all records from database
                $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0):
                    // Create array of objects
                    $contactRecords = array();
                    
                    while($row = $result->fetch_assoc()) {
                        $record = new ContactRecord(
                            $row['id'],
                            $row['name'],
                            $row['email'],
                            $row['subject'],
                            $row['message'],
                            $row['newsletter'],
                            $row['created_at']
                        );
                        $contactRecords[] = $record;
                    }
                    
                    // Function to display table
                    function displayContactTable($records) {
                        echo '<div class="mt-5">';
                        echo '<h3 class="mb-4">All Contact Messages in Database</h3>';
                        echo '<table class="table table-striped">';
                        echo '<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Newsletter</th><th>Date</th></tr></thead>';
                        echo '<tbody>';
                        
                        foreach($records as $record) {
                            echo $record->displayAsTableRow();
                        }
                        
                        echo '</tbody></table></div>';
                    }
                    
                    // Call the function
                    displayContactTable($contactRecords);
                    
                else:
                    echo '<div class="alert alert-info">No messages in database yet.</div>';
                endif;
                
                $conn->close();
                ?>
                
                <!-- Navigation buttons -->
                <div class="mt-4 text-center">
                    <a href="contact.html" class="btn btn-primary me-2">Submit Another Message</a>
                    <a href="index.html" class="btn btn-outline-secondary">Back to Home</a>
                </div>
                
            </div>
        </div>
    </div>
    
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 GameDen</p>
        </div>
    </footer>
</body>
</html>

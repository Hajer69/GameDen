<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'gameden_db';
$port = 8889;

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Class definition for contact records
class ContactRecord {
    public $id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $newsletter;
    public $created_at;
    
    // Constructor
    public function __construct($id, $name, $email, $subject, $message, $newsletter, $created_at) {
        $this->id = $id;
        $this->name = htmlspecialchars($name);
        $this->email = htmlspecialchars($email);
        $this->subject = htmlspecialchars($subject);
        $this->message = htmlspecialchars($message);
        $this->newsletter = $newsletter;
        $this->created_at = $created_at;
    }
    
    // Method to display record as table row
    public function displayAsTableRow() {
        $message_preview = (strlen($this->message) > 50) ? substr($this->message, 0, 50) . "..." : $this->message;
        
        $newsletter_badge = ($this->newsletter == 'yes') ? 
            '<span class="badge bg-success">Yes</span>' : 
            '<span class="badge bg-secondary">No</span>';
        
        return "<tr>
                <td>{$this->id}</td>
                <td>{$this->name}</td>
                <td>{$this->email}</td>
                <td><span class='badge bg-info'>{$this->subject}</span></td>
                <td>{$message_preview}</td>
                <td>{$newsletter_badge}</td>
                <td>{$this->created_at}</td>
            </tr>";
    }
}

// Handle form submission
$form_submitted = false;
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_submitted = true;
    
    // Get form data with validation
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 'yes' : 'no';
    
    // Input validation
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, newsletter) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $subject, $message, $newsletter);
        
        if ($stmt->execute()) {
            $success = true;
            $form_data = [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'newsletter' => $newsletter
            ];
        } else {
            $db_error = $stmt->error;
        }
        $stmt->close();
    } else {
        $validation_errors = $errors;
    }
}

// Retrieve data from MySQL database
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);

//  Create array of objects
$contactRecords = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $record = new ContactRecord(
            $row['id'],
            $row['name'],
            $row['email'],
            $row['subject'],
            $row['message'],
            $row['newsletter'],
            date('Y-m-d H:i', strtotime($row['created_at']))
        );
        $contactRecords[] = $record;
    }
}

// Function to display data in XHTML table
function displayContactTable($recordsArray) {
    echo '<div class="table-responsive mt-4">';
    echo '<table class="table table-striped table-bordered table-hover">';
    echo '<thead class="table-dark">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Name</th>';
    echo '<th>Email</th>';
    echo '<th>Subject</th>';
    echo '<th>Message Preview</th>';
    echo '<th>Newsletter</th>';
    echo '<th>Date Submitted</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // Iterate over array and display each record
    foreach($recordsArray as $record) {
        echo $record->displayAsTableRow();
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Results - GameDen</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin-top: 30px;
        }
        .page-header {
            background: linear-gradient(135deg, #203079 0%, #7C4DFF 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 40px;
            text-align: center;
        }
        .result-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .card-header {
            background-color: #203079;
            color: white;
            padding: 20px;
            font-weight: bold;
        }
        .requirements-list {
            list-style: none;
            padding-left: 0;
        }
        .requirements-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .badge {
            font-size: 0.9em;
            padding: 5px 10px;
        }
        .btn-action {
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="display-4">GameDen Contact System</h1>
            <p class="lead">Form Processing and Database Management System</p>
        </div>

        <!-- Form Submission Results -->
        <?php if ($form_submitted): ?>
            <div class="result-card">
                <div class="card-header">
                    <h3 class="mb-0">
                        <?php if (isset($success)): ?>
                            <span class="badge bg-success me-2">✓</span> Form Submission Successful
                        <?php else: ?>
                            <span class="badge bg-warning me-2">!</span> Submission Issues Detected
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Thank You!</h4>
                            <p>Your message has been successfully saved to our database.</p>
                            <hr>
                            <p class="mb-0">We will respond to your inquiry as soon as possible.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Submitted Information</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%;">Field</th>
                                            <th>Value</th>
                                        </tr>
                                        <tr>
                                            <td><strong>Name</strong></td>
                                            <td><?php echo htmlspecialchars($form_data['name']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email</strong></td>
                                            <td><?php echo htmlspecialchars($form_data['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Subject</strong></td>
                                            <td><span class="badge bg-info"><?php echo htmlspecialchars($form_data['subject']); ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Newsletter</strong></td>
                                            <td>
                                                <?php if ($form_data['newsletter'] == 'yes'): ?>
                                                    <span class="badge bg-success">Subscribed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Not Subscribed</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Your Message</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text"><?php echo nl2br(htmlspecialchars($form_data['message'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif (isset($validation_errors)): ?>
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Validation Errors Found</h4>
                            <ul>
                                <?php foreach($validation_errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <hr>
                            <p class="mb-0">Please go back and correct these errors.</p>
                        </div>
                    <?php elseif (isset($db_error)): ?>
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Database Error</h4>
                            <p><?php echo $db_error; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="result-card">
                <div class="card-header bg-warning">
                    <h3 class="mb-0">No Form Data Received</h3>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-warning">
                        <h4 class="alert-heading">Direct Access Detected</h4>
                        <p>This page is designed to process contact form submissions.</p>
                        <hr>
                        <p class="mb-0">Please submit the contact form first.</p>
                    </div>
                    <a href="contact.html" class="btn btn-primary btn-action">Go to Contact Form</a>
                </div>
            </div>
        <?php endif; ?>

        <!-- All Contact Messages from Database -->
        <div class="result-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">All Contact Messages in Database</h3>
                    <span class="badge bg-primary">
                        <?php echo count($contactRecords); ?> Total Records
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($contactRecords)): ?>
                    <!-- Display contact table using the function -->
                    <?php displayContactTable($contactRecords); ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4 class="alert-heading">No Messages Yet</h4>
                        <p>The database is currently empty. Be the first to send a message!</p>
                        <hr>
                        <a href="contact.html" class="btn btn-outline-primary">Send First Message</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- System Requirements Check -->
        <div class="result-card">
            <div class="card-header">
                <h3 class="mb-0">Project Requirements Status</h3>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="requirements-list">
                            <li>
                                <strong>Form Processing with PHP</strong>
                                <span class="badge bg-success float-end">Implemented</span>
                            </li>
                            <li>
                                <strong>Well-formatted XHTML Display</strong>
                                <span class="badge bg-success float-end">Implemented</span>
                            </li>
                            <li>
                                <strong>Class for Single Record</strong>
                                <span class="badge bg-success float-end">Implemented</span>
                            </li>
                            <li>
                                <strong>Array of Objects</strong>
                                <span class="badge bg-success float-end">Implemented</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="requirements-list">
                            <li>
                                <strong>Function for Table Display</strong>
                                <span class="badge bg-success float-end">Implemented</span>
                            </li>
                            <li>
                                <strong>MySQL Database Interaction</strong>
                                <span class="badge bg-success float-end">Implemented</span>
                            </li>
                            <li>
                                <strong>SQL Queries (SELECT)</strong>
                                <span class="badge bg-success float-end">Implemented</span>
                            </li>
                            <li>
                                <strong>Search Functionality</strong>
                                <a href="admin_panel.php" class="badge bg-primary float-end">View Admin Panel</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4">
            <a href="admin_panel.php" class="btn btn-success btn-action">
                <i class="bi bi-gear"></i> Admin Panel (Search/Update/Delete)
            </a>
            <a href="search_page.php" class="btn btn-info btn-action">
                <i class="bi bi-search"></i> Search Messages
            </a>
            <a href="contact.html" class="btn btn-primary btn-action">
                <i class="bi bi-envelope"></i> Submit Another Message
            </a>
            <a href="index.html" class="btn btn-secondary btn-action">
                <i class="bi bi-house"></i> Back to Home
            </a>
        </div>

        <!-- Footer -->
        <footer class="mt-5 text-center text-muted">
            <p>GameDen Contact Management System | PHP & MySQL Integration</p>
            <p class="small">Database: <?php echo $dbname; ?> | Records: <?php echo count($contactRecords); ?></p>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Auto-dismiss alerts after 5 seconds -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>

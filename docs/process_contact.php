<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission - GameDen</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #203079;
            --primary-purple: #7C4DFF;
            --accent-orange: #FFB300;
        }
        .text-primary-custom {
            color: var(--primary-blue) !important;
        }
        .btn-purple {
            background-color: var(--primary-purple) !important;
            border-color: var(--primary-purple) !important;
            color: white !important;
        }
        .table-custom {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .table-custom th {
            background-color: var(--primary-purple);
            color: white;
            padding: 12px;
            text-align: left;
        }
        .table-custom td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .table-custom tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar (Same as contact.html) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold text-primary-custom" href="index.html">
                GameDen
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.html">Back to Contact</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <?php
                // ============================================
                // PART A: Handle form submission
                // ============================================
                
                // Check if form was submitted
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    
                    // ============================================
                    // PART C: Class definition (Single record)
                    // ============================================
                    class ContactRecord {
                        // Properties (like table columns)
                        public $id;
                        public $name;
                        public $email;
                        public $subject;
                        public $message;
                        public $newsletter;
                        public $timestamp;
                        
                        // Constructor
                        public function __construct($name, $email, $subject, $message, $newsletter) {
                            $this->id = uniqid(); // Generate unique ID
                            $this->name = htmlspecialchars($name);
                            $this->email = htmlspecialchars($email);
                            $this->subject = htmlspecialchars($subject);
                            $this->message = htmlspecialchars($message);
                            $this->newsletter = ($newsletter == 'yes') ? 'Yes' : 'No';
                            $this->timestamp = date("Y-m-d H:i:s");
                        }
                        
                        // Method to display as table row
                        public function displayAsRow() {
                            return "<tr>
                                    <td>{$this->id}</td>
                                    <td>{$this->name}</td>
                                    <td>{$this->email}</td>
                                    <td>{$this->subject}</td>
                                    <td>" . substr($this->message, 0, 50) . "...</td>
                                    <td>{$this->newsletter}</td>
                                    <td>{$this->timestamp}</td>
                                </tr>";
                        }
                    }
                    
                    // ============================================
                    // Get form data
                    // ============================================
                    $name = $_POST['name'] ?? 'Not provided';
                    $email = $_POST['email'] ?? 'Not provided';
                    $subject = $_POST['subject'] ?? 'Not selected';
                    $message = $_POST['message'] ?? 'No message';
                    $newsletter = $_POST['newsletter'] ?? 'no';
                    
                    // ============================================
                    // Create new contact record object
                    // ============================================
                    $newRecord = new ContactRecord($name, $email, $subject, $message, $newsletter);
                    
                    // ============================================
                    // PART C: Array of objects (All records in table)
                    // ============================================
                    $contactRecords = array();
                    
                    // Add the new record to array
                    $contactRecords[] = $newRecord;
                    
                    // For demonstration, add some sample records
                    // In real application, these would come from database
                    $contactRecords[] = new ContactRecord(
                        "John Doe", 
                        "john@example.com", 
                        "question", 
                        "How to play multiplayer?", 
                        "yes"
                    );
                    
                    $contactRecords[] = new ContactRecord(
                        "Jane Smith", 
                        "jane@example.com", 
                        "suggestion", 
                        "Please add more puzzle games", 
                        "no"
                    );
                    
                    $contactRecords[] = new ContactRecord(
                        "Alex Johnson", 
                        "alex@example.com", 
                        "problem", 
                        "Game crashes on level 5", 
                        "yes"
                    );
                    
                    // ============================================
                    // Display success message
                    // ============================================
                    echo '<div class="alert alert-success" role="alert">';
                    echo '<h4 class="alert-heading">Message Received Successfully!</h4>';
                    echo '<p>Thank you <strong>' . htmlspecialchars($name) . '</strong> for contacting GameDen.</p>';
                    echo '<p>We will respond to your <strong>' . htmlspecialchars($subject) . '</strong> inquiry shortly.</p>';
                    echo '<hr>';
                    echo '<p class="mb-0">Your message has been recorded and saved.</p>';
                    echo '</div>';
                    
                    // ============================================
                    // PART C: Function to display data in XHTML table
                    // ============================================
                    function displayContactTable($recordsArray) {
                        echo '<div class="mt-5">';
                        echo '<h3 class="text-primary-custom mb-4">Recent Contact Messages</h3>';
                        
                        echo '<table class="table table-custom table-striped table-bordered">';
                        echo '<thead class="thead-dark">';
                        echo '<tr>';
                        echo '<th>ID</th>';
                        echo '<th>Name</th>';
                        echo '<th>Email</th>';
                        echo '<th>Subject</th>';
                        echo '<th>Message Preview</th>';
                        echo '<th>Newsletter</th>';
                        echo '<th>Timestamp</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        
                        // Iterate over array and display each record
                        foreach($recordsArray as $record) {
                            echo $record->displayAsRow();
                        }
                        
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    }
                    
                    // ============================================
                    // Call the function to display table
                    // ============================================
                    displayContactTable($contactRecords);
                    
                    // ============================================
                    // PART B: Display form data in well-formatted XHTML
                    // ============================================
                    echo '<div class="card mt-4">';
                    echo '<div class="card-header bg-primary text-white">';
                    echo '<h4>Your Submitted Information</h4>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<div class="row">';
                    
                    echo '<div class="col-md-6">';
                    echo '<h5>Personal Information</h5>';
                    echo '<ul class="list-group">';
                    echo '<li class="list-group-item"><strong>Name:</strong> ' . htmlspecialchars($name) . '</li>';
                    echo '<li class="list-group-item"><strong>Email:</strong> ' . htmlspecialchars($email) . '</li>';
                    echo '<li class="list-group-item"><strong>Subject:</strong> ' . htmlspecialchars($subject) . '</li>';
                    echo '</ul>';
                    echo '</div>';
                    
                    echo '<div class="col-md-6">';
                    echo '<h5>Message Details</h5>';
                    echo '<div class="card">';
                    echo '<div class="card-body">';
                    echo '<p>' . nl2br(htmlspecialchars($message)) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '<p class="mt-3"><strong>Newsletter Subscription:</strong> ' . (($newsletter == 'yes') ? 'Subscribed ✓' : 'Not Subscribed') . '</p>';
                    echo '</div>';
                    
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    
                } else {
                    // If someone accesses this page directly without form submission
                    echo '<div class="alert alert-warning" role="alert">';
                    echo '<h4 class="alert-heading">No Form Data Received</h4>';
                    echo '<p>Please submit the contact form first.</p>';
                    echo '<hr>';
                    echo '<a href="contact.html" class="btn btn-purple">Go to Contact Form</a>';
                    echo '</div>';
                }
                ?>
                
                <!-- Action buttons -->
                <div class="mt-4 text-center">
                    <a href="contact.html" class="btn btn-purple me-2">Submit Another Message</a>
                    <a href="index.html" class="btn btn-outline-secondary">Back to Home</a>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 GameDen. All rights reserved. | Contact Form Processing System</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
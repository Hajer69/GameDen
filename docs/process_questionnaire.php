<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gamedendb';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Class definition for questionnaire records
class QuestionnaireRecord {
    public $favorite_game;
    public $rating;
    public $improvements;
    public $comments;
    public $submitted_at;

    public function __construct($favorite_game, $rating, $improvements, $comments, $submitted_at) {
        $this->favorite_game = htmlspecialchars($favorite_game);
        $this->rating = htmlspecialchars($rating);
        $this->improvements = htmlspecialchars($improvements);
        $this->comments = htmlspecialchars($comments);
        $this->submitted_at = $submitted_at;
    }

    public function displayAsTableRow() {
        $preview = (strlen($this->comments) > 50) ? substr($this->comments, 0, 50) . "..." : $this->comments;
        return "<tr>
            <td>{$this->favorite_game}</td>
            <td><span class='badge bg-success'>{$this->rating}</span></td>
            <td>{$this->improvements}</td>
            <td>{$preview}</td>
            <td>{$this->submitted_at}</td>
        </tr>";
    }
}

$form_submitted = false;
$success = false;
$form_data = [];
$validation_errors = [];

$favorite_map = [
    'sudoku' => 'Sudoku Challenge',
    'rps' => 'Rock Paper Scissors',
    'tictactoe' => 'Tic Tac Toe',
    'quiz' => 'Brain Quiz'
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_submitted = true;

    //these are for JS validation only; PHP will still validate them lightly
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $age = (int)($_POST['age'] ?? 0);

    $favorite_game = trim($_POST['favorite_game'] ?? '');
    $rating = trim($_POST['rating'] ?? '');
    $improvements_arr = $_POST['improvements'] ?? []; 
    $comments = trim($_POST['comments'] ?? '');

    // Server-side validation backup
    if ($username === '' || !preg_match('/^[A-Za-z0-9_]{4,}$/', $username)) {
        $validation_errors[] = "Username must be at least 4 characters (letters/numbers/underscore).";
    }
    if ($password === '' || !preg_match('/^[A-Za-z0-9@]{8,}$/', $password)) {
        $validation_errors[] = "Password must be at least 8 characters (letters/numbers/@).";
    }
    if ($age < 5) {
        $validation_errors[] = "Age must be 5 or above.";
    }

    if ($favorite_game === '') {
        $validation_errors[] = "Favorite game is required.";
    }
    if ($rating === '') {
        $validation_errors[] = "Rating is required.";
    }
    if (!is_array($improvements_arr) || count($improvements_arr) < 2) {
        $validation_errors[] = "Please select at least two improvement options.";
    }

    $favorite_label = $favorite_map[$favorite_game] ?? $favorite_game;
    $improvements = is_array($improvements_arr) ? implode(", ", $improvements_arr) : "";

    if (empty($validation_errors)) {
        $success = true;
        $form_data = [
            'username' => $username,
            'age' => $age,
            'favorite_game' => $favorite_label,
            'rating' => $rating,
            'improvements' => $improvements,
            'comments' => $comments
        ];

        if (!isset($_SESSION['questionnaire_records'])) {
            $_SESSION['questionnaire_records'] = [];
        }

        $_SESSION['questionnaire_records'][] = new QuestionnaireRecord(
            $form_data['favorite_game'],
            $form_data['rating'],
            $form_data['improvements'],
            $form_data['comments'],
            date('Y-m-d H:i')
        );
    }
}

$records = $_SESSION['questionnaire_records'] ?? [];

function displayQuestionnaireTable($records) {
    echo '<div class="table-responsive mt-4">';
    echo '<table class="table table-striped table-bordered table-hover">';
    echo '<thead class="table-dark">';
    echo '<tr>
            <th>Favorite Game</th>
            <th>Rating</th>
            <th>Improvements</th>
            <th>Comments Preview</th>
            <th>Submitted At</th>
          </tr>';
    echo '</thead><tbody>';

    foreach ($records as $record) {
        echo $record->displayAsTableRow();
    }

    echo '</tbody></table></div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Questionnaire Results - GameDen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">

    <div class="p-4 rounded text-white text-center mb-4"
         style="background: linear-gradient(135deg, #203079 0%, #7C4DFF 100%);">
        <h1 class="mb-0">GameDen Questionnaire</h1>
        <p class="mb-0">Form Processing Results</p>
    </div>

    <?php if ($form_submitted): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <strong><?php echo $success ? "✓ Feedback Submitted Successfully" : "Submission Errors"; ?></strong>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Thank you! Your feedback has been received.
                    </div>

                    <h5>Submitted Information</h5>
                    <table class="table table-bordered">
                        <tr><th>Username</th><td><?php echo htmlspecialchars($form_data['username']); ?></td></tr>
                        <tr><th>Age</th><td><?php echo htmlspecialchars((string)$form_data['age']); ?></td></tr>
                        <tr><th>Favorite Game</th><td><?php echo htmlspecialchars($form_data['favorite_game']); ?></td></tr>
                        <tr><th>Rating</th><td><?php echo htmlspecialchars($form_data['rating']); ?></td></tr>
                        <tr><th>Improvements</th><td><?php echo htmlspecialchars($form_data['improvements']); ?></td></tr>
                        <tr><th>Comments</th><td><?php echo nl2br(htmlspecialchars($form_data['comments'])); ?></td></tr>
                    </table>

                <?php else: ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($validation_errors as $err): ?>
                                <li><?php echo htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <a href="questionnaire.html" class="btn btn-secondary">Go Back</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>All Questionnaire Submissions (Session)</strong>
            <span class="badge bg-primary"><?php echo count($records); ?></span>
        </div>
        <div class="card-body">
            <?php
            if (!empty($records)) {
                displayQuestionnaireTable($records);
            } else {
                echo '<div class="alert alert-info mb-0">No submissions yet.</div>';
            }
            ?>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="questionnaire.html" class="btn btn-primary">Submit Another</a>
        <a href="index.html" class="btn btn-secondary">Back Home</a>
    </div>

</div>
</body>
</html>
<?php $conn->close(); ?>

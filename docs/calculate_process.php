<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gamedendb';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$conn->query("CREATE TABLE IF NOT EXISTS brain_calculator_runs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    playerName VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    experience VARCHAR(20) NOT NULL,
    time_minutes INT NOT NULL,
    focus INT NOT NULL,
    challengeScore DECIMAL(6,1) NOT NULL,
    difficultyText VARCHAR(40) NOT NULL,
    gameText VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

class BrainRun {
    public $id,$playerName,$age,$experience,$time,$focus,$score,$difficulty,$created_at;
    public function __construct($id,$playerName,$age,$experience,$time,$focus,$score,$difficulty,$created_at) {
        $this->id=$id;
        $this->playerName=htmlspecialchars($playerName);
        $this->age=(int)$age;
        $this->experience=htmlspecialchars($experience);
        $this->time=(int)$time;
        $this->focus=(int)$focus;
        $this->score=number_format((float)$score,1);
        $this->difficulty=htmlspecialchars($difficulty);
        $this->created_at=$created_at;
    }
    public function displayAsTableRow() {
        return "<tr>
            <td>{$this->id}</td>
            <td>{$this->playerName}</td>
            <td>{$this->age}</td>
            <td>{$this->experience}</td>
            <td>{$this->time}</td>
            <td>{$this->focus}</td>
            <td>{$this->score}</td>
            <td><span class='badge bg-warning text-dark'>{$this->difficulty}</span></td>
            <td>{$this->created_at}</td>
        </tr>";
    }
}

$form_submitted = false;
$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_submitted = true;

    $playerName = trim($_POST['playerName'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $experience = trim($_POST['experience'] ?? '');
    $time = (int)($_POST['time'] ?? 0);
    $focus = (int)($_POST['focus'] ?? 0);

    if ($playerName === '') $errors[] = "Name is required.";
    if ($age < 4) $errors[] = "Age must be 4 or above.";
    if ($time < 5) $errors[] = "Time must be 5 minutes or more.";
    if ($experience === '') $errors[] = "Experience is required.";
    if ($focus < 1 || $focus > 5) $errors[] = "Focus must be between 1 and 5.";

    if (empty($errors)) {
        $baseFromExperience = 0;
        if ($experience === "beginner") $baseFromExperience = 30;
        else if ($experience === "intermediate") $baseFromExperience = 60;
        else if ($experience === "expert") $baseFromExperience = 90;

        $cappedTime = min($time, 60);
        $focusFactor = $focus * 5;

        $agePenalty = 0;
        if ($age < 12) $agePenalty = 10;
        else if ($age >= 60) $agePenalty = 15;

        $timeContribution = $cappedTime * 2;
        $ageAdjustment = $agePenalty / 2;

        $challengeScore = $baseFromExperience + $timeContribution + $focusFactor - $ageAdjustment;

        $difficultyText = "";
        $gameText = "";

        if ($age < 12) {
            $difficultyText = "Easy";
            $gameText = "Tic Tac Toe, simple pattern games, and short memory games.";
        } else if ($age >= 60 && $time >= 20) {
            $difficultyText = "Brain-Healthy";
            $gameText = "Relaxed Sudoku, gentle memory games, and focus puzzles.";
        } else if ($experience === "expert" && $time >= 20 && $challengeScore >= 140) {
            $difficultyText = "Hard";
            $gameText = "Hard Sudoku, logic grids, and advanced strategy puzzles.";
        } else if ($challengeScore >= 110) {
            $difficultyText = "Medium-Hard";
            $gameText = "Standard Sudoku, timed memory games, and strategy puzzles.";
        } else if ($challengeScore >= 80) {
            $difficultyText = "Medium";
            $gameText = "4x4 Sudoku, memory match games, and pattern recognition.";
        } else {
            $difficultyText = "Easy";
            $gameText = "Tic Tac Toe, simple riddles, and quick brain teasers.";
        }

        $stmt = $conn->prepare("INSERT INTO brain_calculator_runs
            (playerName, age, experience, time_minutes, focus, challengeScore, difficultyText, gameText)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            $errors[] = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("sisiiiss", $playerName, $age, $experience, $time, $focus, $challengeScore, $difficultyText, $gameText);

            if ($stmt->execute()) {
                $success = true;

                // ✅ Redirect BACK to calculate.html WITH results so it displays there
                header("Location: calculate.html?name=" . urlencode($playerName) .
                    "&difficulty=" . urlencode($difficultyText) .
                    "&game=" . urlencode($gameText) .
                    "&score=" . urlencode(number_format($challengeScore, 1)));
                exit;

            } else {
                $errors[] = "Database insert failed: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$runs = [];
$res = $conn->query("SELECT * FROM brain_calculator_runs ORDER BY created_at DESC");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $runs[] = new BrainRun(
            $row['id'], $row['playerName'], $row['age'], $row['experience'],
            $row['time_minutes'], $row['focus'], $row['challengeScore'],
            $row['difficultyText'], date('Y-m-d H:i', strtotime($row['created_at']))
        );
    }
}

function displayRunsTable($runs) {
    echo '<div class="table-responsive mt-3">';
    echo '<table class="table table-striped table-bordered table-hover">';
    echo '<thead class="table-dark"><tr>
        <th>ID</th><th>Name</th><th>Age</th><th>Experience</th><th>Time</th><th>Focus</th><th>Score</th><th>Difficulty</th><th>Date</th>
    </tr></thead><tbody>';
    foreach ($runs as $r) echo $r->displayAsTableRow();
    echo '</tbody></table></div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Brain Calculator Results - GameDen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
  <div class="p-4 rounded text-white" style="background: linear-gradient(135deg,#203079 0%,#7C4DFF 100%);">
    <h1 class="mb-0">Brain Calculator Results</h1>
    <p class="mb-0">Server-side calculation + database logging</p>
  </div>

  <?php if ($form_submitted && !$success): ?>
    <div class="card shadow-sm mt-4">
      <div class="card-body">
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
            </ul>
          </div>
        <?php endif; ?>
        <a class="btn btn-secondary" href="calculate.html">Back to Calculator</a>
      </div>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between">
      <strong>All Calculator Runs</strong>
      <span class="badge bg-primary"><?php echo count($runs); ?></span>
    </div>
    <div class="card-body">
      <?php
        if (!empty($runs)) displayRunsTable($runs);
        else echo '<div class="alert alert-info mb-0">No runs yet.</div>';
      ?>
    </div>
  </div>
</div>
</body>
</html>
<?php $conn->close(); ?>

<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gamedendb';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$form_submitted = false;
$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_submitted = true;

    $fullName = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['new_username'] ?? '');
    $email = trim($_POST['new_email'] ?? '');
    $password = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $favorite = trim($_POST['favorite_game'] ?? '');

    if ($fullName === '') $errors[] = "Full name is required.";
    if ($username === '') $errors[] = "Username is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if ($password === '' || strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if ($favorite === '') $errors[] = "Please choose your favorite game.";

    // Duplicate check
    if (empty($errors)) {
        $sqlCheck = "SELECT username FROM users WHERE username=? OR email=? LIMIT 1";
        $check = $conn->prepare($sqlCheck);
        if (!$check) {
            die("SQL Prepare failed: " . htmlspecialchars($conn->error) . "<br>Query: " . htmlspecialchars($sqlCheck));
        }
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $res = $check->get_result();
        if ($res && $res->num_rows > 0) {
            $errors[] = "Username or Email already exists.";
        }
        $check->close();
    }

    // Insert
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $sqlInsert = "INSERT INTO users (username, password, email, fullName, favoriteGame)
                     VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlInsert);

        if (!$stmt) {
            // This will tell you EXACTLY which column name is wrong/missing
            die("SQL Prepare failed: " . htmlspecialchars($conn->error) . "<br>Query: " . htmlspecialchars($sqlInsert));
        }

        $stmt->bind_param("sssss", $username, $hashed, $email, $fullName, $favorite);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Database insert failed: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup Results - GameDen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
  <div class="p-4 rounded text-white" style="background: linear-gradient(135deg,#203079 0%,#7C4DFF 100%);">
    <h1 class="mb-0">Signup Results</h1>
    <p class="mb-0">GameDen Registration</p>
  </div>

  <?php if ($form_submitted): ?>
    <div class="card shadow-sm mt-4">
      <div class="card-body">
        <?php if ($success): ?>
          <div class="alert alert-success">✅ Account created successfully!</div>
          <a class="btn btn-primary" href="login.html">Go to Login</a>
        <?php else: ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <a class="btn btn-secondary" href="signup.html">Back to Signup</a>
        <?php endif; ?>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning mt-4">Direct access detected. Please use the signup form.</div>
    <a class="btn btn-secondary" href="signup.html">Go to Signup</a>
  <?php endif; ?>
</div>
</body>
</html>
<?php $conn->close(); ?>
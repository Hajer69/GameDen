<?php
session_start();

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

    $login = trim($_POST['username'] ?? '');   // username OR email
    $password = $_POST['password'] ?? '';
    $remember = (($_POST['remember'] ?? '') === 'yes');

    if ($login === '') $errors[] = "Username/Email is required.";
    if ($password === '') $errors[] = "Password is required.";

    if (empty($errors)) {

        // ✅ No `id` column here
        $sql = "SELECT username, email, password, fullName, favoriteGame FROM users
                WHERE username=? OR email=? LIMIT 1";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("SQL Prepare failed: " . htmlspecialchars($conn->error) . "<br>Query: " . htmlspecialchars($sql));
        }

        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $stored = $row['password'];

            // Support hashed OR plaintext demo passwords
            $ok = password_verify($password, $stored) || hash_equals((string)$stored, (string)$password);

            if ($ok) {
                $success = true;

                $_SESSION['username'] = $row['username'];
                $_SESSION['fullName'] = $row['fullName'] ?? $row['username'];
                $_SESSION['favoriteGame'] = $row['favoriteGame'] ?? '';

                if ($remember) {
                    setcookie("gameden_user", $row['username'], time() + (7 * 24 * 60 * 60), "/");
                }

            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No user found with that username/email.";
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
  <title>Login Results - GameDen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
  <div class="p-4 rounded text-white" style="background: linear-gradient(135deg,#203079 0%,#7C4DFF 100%);">
    <h1 class="mb-0">Login Results</h1>
    <p class="mb-0">GameDen Authentication</p>
  </div>

  <?php if ($form_submitted): ?>
    <div class="card shadow-sm mt-4">
      <div class="card-body">
        <?php if ($success): ?>
          <div class="alert alert-success mb-3">
            ✅ Welcome, <strong><?php echo htmlspecialchars($_SESSION['fullName']); ?></strong>!
          </div>
          <?php if (!empty($_SESSION['favoriteGame'])): ?>
            <p>Favorite Game: <span class="badge bg-info"><?php echo htmlspecialchars($_SESSION['favoriteGame']); ?></span></p>
          <?php endif; ?>
          <a class="btn btn-primary" href="index.html">Go Home</a>
        <?php else: ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <a class="btn btn-secondary" href="login.html">Back to Login</a>
        <?php endif; ?>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning mt-4">Direct access detected. Please login from the form.</div>
    <a class="btn btn-secondary" href="login.html">Go to Login</a>
  <?php endif; ?>

</div>
</body>
</html>
<?php $conn->close(); ?>

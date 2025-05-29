<?php
session_start();
include 'db_connection.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  // Check student table first
  $query = "SELECT studentID, password, name FROM student WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($userID, $hashedPassword, $studentName);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
      unset($_SESSION['adminID']);
      $_SESSION['studentID'] = $userID;
      $_SESSION['studentName'] = $studentName;
      header("Location: bookings1.php");
      exit;
    }
  }
  $stmt->close();

  // If not a student, check admin table
  $query = "SELECT adminID, password FROM admin WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($adminID, $hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
      unset($_SESSION['studentID']);
      $_SESSION['adminID'] = $adminID;
      header("Location: bookings2.php");
      exit;
    }
  }
  $stmt->close();

  $error = "Incorrect email or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BooCAS - Log In</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <div class="logo">
      <img src="booCAS_logo.png" alt="booCAS Logo" class="logo-icon">
    </div>
    <nav>
      <ul>
        <li><a href="index.php">Back to Home</a></li>
      </ul>
    </nav>
  </header>

  <main class="login-signup">
    <div class="signup-panel">
      <h2>Log In</h2>

      <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form method="POST" action="">
        <label for="email">Email</label>
        <div class="input-wrapper">
          <input type="email" name="email" id="email" placeholder="Enter your email" required>
        </div>

        <label for="password">Password</label>
        <div class="input-wrapper">
          <input type="password" name="password" id="password" placeholder="Enter your password" required>
          <span class="toggle-password" onclick="togglePasswordVisibility()">
            <!-- Simple 2D SVG eye icon -->
            <svg width="20" height="20" viewBox="0 0 20 20" style="vertical-align: middle;">
              <ellipse cx="10" cy="10" rx="8" ry="5" fill="none" stroke="#888" stroke-width="2"/>
              <circle cx="10" cy="10" r="2" fill="#888"/>
            </svg>
          </span>
        </div>

        <button type="submit">Log In</button>
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
      </form>
    </div>
  </main>

  <footer class="upv-footer">
    <div class="footer-row">
      <div class="footer-logos">
        <img src="upv-logo.png" alt="UPV Logo" class="footer-logo">
        <img src="dpsm-logo.png" alt="DPSM Logo" class="footer-logo">
      </div>
      <div class="footer-contact">
        <p><strong>UPV Division of Physical Sciences and Mathematics</strong><br>
        UPV CAS Building, 5023 Miagao, Iloilo<br>
        Tel. No.: (033) 315-9625 Local: 239<br>
        Email: <a href="mailto:psm.upvisayas@up.edu.ph">psm.upvisayas@up.edu.ph</a></p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2024 Division of Physical Sciences and Mathematics. All rights reserved.</p>
    </div>
  </footer>

  <script>
    function togglePasswordVisibility() {
      const pwd = document.getElementById('password');
      pwd.type = pwd.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>

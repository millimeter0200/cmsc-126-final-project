<?php
session_start();
include 'db_connection.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $firstName = $_POST['first_name'];
  $middleInitial = $_POST['middle_initial'];
  $lastName = $_POST['last_name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Concatenate first name, middle initial, and last name for full name
  $name = trim($firstName . ' ' . ($middleInitial ? $middleInitial . '. ' : '') . $lastName);
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Check if email already exists
  $stmt = $conn->prepare("SELECT studentID FROM student WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $error = "An account with this email already exists.";
  } else {
    $role = $_POST['role'];

    if ($role === 'student') {
      $insert = $conn->prepare("INSERT INTO student (name, email, password) VALUES (?, ?, ?)");
      $insert->bind_param("sss", $name, $email, $hashedPassword);
    } elseif ($role === 'admin') {
      $insert = $conn->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)");
      $insert->bind_param("sss", $name, $email, $hashedPassword);
    } else {
      $error = "Invalid role selected.";
    }

    if (empty($error)) {
      if ($insert && $insert->execute()) {
        $success = "Account created successfully. You can now <a href='login.php'>log in</a>.";
      } else {
        $error = "Signup failed. Please try again.";
      }
      if (isset($insert)) {
        $insert->close();
      }
    }
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BooCAS - Sign Up</title>
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
      <h2>Create an account</h2>

      <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
      <?php elseif ($success): ?>
        <p style="color: green;"><?= $success ?></p>
      <?php endif; ?>

      <form method="POST" action="">
        <label for="first_name">First Name</label>
        <input type="text" name="first_name" required placeholder="Juan">

        <label for="middle_initial">Middle Initial</label>
        <input type="text" name="middle_initial" maxlength="1" placeholder="D">

        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" required placeholder="Dela Cruz">

        <label for="email">Email</label>
        <input type="email" name="email" required placeholder="email@up.edu.ph">

        <label for="password">Password</label>
        <input type="password" name="password" required placeholder="Enter a strong password">

        <div class="dropdown-role">
          <label for="role">Select Role</label>
          <select name="role" id="role" required>
            <option value="" disabled selected>-- Choose your role --</option>
            <option value="student">Student</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <button type="submit">Create Account</button>
        <p>Already have an account? <a href="login.php">Log in here</a></p>
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
</body>
</html>

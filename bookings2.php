<?php
session_start();

if (!isset($_SESSION['studentID'])) {
  header("Location: login.php");
  exit;
}

include 'db_connection.php';

// Get pending bookings
$pendingSQL = "SELECT r.*, rm.name AS room_name, s.name AS student_name
               FROM reservation r
               JOIN room rm ON r.roomID = rm.roomID
               JOIN student s ON r.studentID = s.studentID
               WHERE r.status = 'Pending'
               ORDER BY r.reservation_date ASC";
$pendingResult = $conn->query($pendingSQL);

// Get all booking history
$historySQL = "SELECT r.*, rm.name AS room_name
               FROM reservation r
               JOIN room rm ON r.roomID = rm.roomID
               WHERE r.status IN ('Approved', 'Rejected')
               ORDER BY r.reservation_date DESC";
$historyResult = $conn->query($historySQL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BooCAS - Admin Bookings</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <div class="logo">
      <img src="booCAS_logo.png" alt="booCAS Logo" class="logo-icon">
    </div>
    <nav>
      <ul>
        <li><a id="bookings-link" href="#">My Bookings</a></li>
        <li><a href="calendar.php">Status</a></li>
        <li><a href="login.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="admin-dashboard">
      <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Manage room booking requests and view booking history.</p>
      </div>

      <div class="admin-panels">
        <section class="booking-requests">
          <h2>Pending Booking Requests</h2>
          <?php while ($row = $pendingResult->fetch_assoc()): ?>
            <div class="booking-item">
              <div class="booking-details">
                <p class="room-name"><?= $row['room_name'] ?></p>
                <p><?= date("F j, Y", strtotime($row['reservation_date'])) ?> 
                   <?= date("h:i A", strtotime($row['start_time'])) ?> - 
                   <?= date("h:i A", strtotime($row['end_time'])) ?></p>
                <p><strong>Student:</strong> <?= $row['student_name'] ?></p>
                <p><strong>Reason:</strong> <?= $row['purpose'] ?></p>
              </div>
              <div class="booking-actions">
                <button class="confirmed" onclick="approveBooking(<?= $row['reservationID'] ?>)">Approve</button>
                <button class="pending" onclick="declineBooking(<?= $row['reservationID'] ?>)">Decline</button>
              </div>
            </div>
          <?php endwhile; ?>
        </section>

        <section class="booking-history">
          <h2>Booking History</h2>
          <?php while ($row = $historyResult->fetch_assoc()): ?>
            <div class="booking-item">
              <div class="booking-details">
                <p class="room-name"><?= $row['room_name'] ?></p>
                <p><?= date("F j, Y", strtotime($row['reservation_date'])) ?> 
                   <?= date("h:i A", strtotime($row['start_time'])) ?> - 
                   <?= date("h:i A", strtotime($row['end_time'])) ?></p>
              </div>
              <button class="<?= strtolower($row['status']) ?>">
                <?= strtoupper($row['status']) ?>
              </button>
            </div>
          <?php endwhile; ?>
        </section>
      </div>
    </section>
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
  <script src="script.js"></script>
</body>
</html>

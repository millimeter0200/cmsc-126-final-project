<?php
include 'db_connection.php';

// Get pending bookings
$pendingSQL = "SELECT r.*, rm.name AS room_name, s.first_name, s.last_name
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
        <li><a href="role-selection.html">Home</a></li>
        <li><a href="bookings2.php">Bookings</a></li>
        <li><a href="calendar.html">Schedule & Events</a></li>
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
                <p><strong>Student:</strong> <?= $row['first_name'] . ' ' . $row['last_name'] ?></p>
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

  <footer>
    <p>FOOTNOTE</p>
  </footer>
  <script src="script.js"></script>
</body>
</html>

<?php
include 'db_connection.php';
$studentID = 1; // Replace with session-based ID later

$sql = "SELECT r.*, rm.name AS room_name
        FROM reservation r
        JOIN room rm ON r.roomID = rm.roomID
        WHERE r.studentID = $studentID
        ORDER BY r.reservation_date DESC, r.start_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BooCAS - Student Bookings</title>
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
        <li><a href="bookings1.php">Bookings</a></li>
        <li><a href="calendar.html">Schedule & Status</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="bookings-grid">
      <section class="bookings">
        <h2>My Bookings</h2>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="booking-item">
            <div class="booking-details">
              <p class="room-name"><?= htmlspecialchars($row['room_name']) ?></p>
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

      <section class="booking-history">
        <h2>Booking History</h2>
        <!-- You can replicate the loop and filter by old/completed status here -->
      </section>

      <section class="reserve">
        <h2>Reserve a Room</h2>
        <form id="reserveForm">
          <label for="roomTypeReserve">Room Type</label>
          <select id="roomTypeReserve">
            <option value="">Select Room Type</option>
            <option value="classroom">Classroom</option>
            <option value="laboratory">Laboratory</option>
          </select>

          <label for="roomNumber">Room Number</label>
          <select id="roomNumber">
            <option value="">Select Room Number</option>
            <option value="101">101</option>
            <option value="PL2">PL2</option>
            <option value="CL3">CL3</option>
            <option value="106">106</option>
          </select>

          <label for="dateReserve">Date</label>
          <input type="text" id="dateReserve" placeholder="dd/mm/yyyy">

          <label for="timeSlot">Time Slot</label>
          <select id="timeSlot">
            <option value="">Select Time Slot</option>
            <option value="08:00 AM - 04:00 PM">08:00 AM - 04:00 PM</option>
            <option value="07:00 AM - 12:30 PM">07:00 AM - 12:30 PM</option>
            <option value="11:00 AM - 02:30 PM">11:00 AM - 02:30 PM</option>
            <option value="01:00 PM - 02:00 PM">01:00 PM - 02:00 PM</option>
          </select>

          <label for="notes">Reason/Additional Notes</label>
          <input type="text" id="notes">

          <button type="button" onclick="submitReservation()">Submit Reservation</button>
        </form>
      </section>
    </div>
  </main>

  <footer>
    <p>FOOTNOTE</p>
  </footer>
  <script src="script.js"></script>
</body>
</html>

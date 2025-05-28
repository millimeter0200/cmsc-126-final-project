<?php
session_start();

if (!isset($_SESSION['studentID'])) {
  header("Location: login.php");
  exit;
}

include 'db_connection.php';
$studentID = $_SESSION['studentID']; // Use session-based ID

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body>
  <header>
    <div class="logo">
      <img src="booCAS_logo.png" alt="booCAS Logo" class="logo-icon">
    </div>
    <nav>
      <ul>
        <li><a href="bookings1.php">My Bookings</a></li>
        <li><a href="calendar.php">Status</a></li>
        <li><a href="login.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="bookings-grid">
      <!-- Only the reservation form in this section -->
      <section class="reserve">
        <h2>Reserve a Room</h2>
        <form id="reserveForm">
          <label for="roomTypeReserve">Room Type</label>
          <select id="roomTypeReserve" onchange="filterRooms()" required>
            <option value="">-- Select Room Type --</option>
            <option value="classroom">Classroom</option>
            <option value="laboratory">Laboratory</option>
          </select>

          <label for="roomNumber">Room Number</label>
          <select id="roomNumber" name="roomID" required>
            <option value="">-- Select Room --</option>
          </select>

          <label for="startDateTime">Start</label>
          <input type="text" id="startDateTime" placeholder="Select start date & time">

          <label for="endDateTime">End</label>
          <input type="text" id="endDateTime" placeholder="Select end date & time">

          <label for="subjectActivity">Subject/Activity</label>
          <input type="text" id="subjectActivity" name="subjectActivity" required>

          <label for="purpose">Purpose</label>
          <input type="text" id="purpose" name="purpose" required>

          <label for="divisionOffice">Division/Office/Organization</label>
          <input type="text" id="divisionOffice" name="divisionOffice" required>

          <button type="button" onclick="submitReservation()">Submit Reservation</button>
        </form>
      </section>

      <!-- Move the booking loop here -->
      <section class="booking-history">
        <h2>Booking History</h2>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="booking-item">
            <div class="booking-details">
              <p class="room-name"><?= htmlspecialchars($row['room_name']) ?></p>
              <?php
              $start = strtotime($row['reservation_date'] . ' ' . $row['start_time']);
              $end = strtotime($row['reservation_date'] . ' ' . $row['end_time']);
              ?>
              <p>
                <?= date("F j, Y h:i A", $start) ?> - <?= date("F j, Y h:i A", $end) ?>
              </p>
            </div>
            <button class="<?= strtolower($row['status']) ?>">
              <?= strtoupper($row['status']) ?>
            </button>
          </div>
        <?php endwhile; ?>
      </section>
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
  <script src="./script.js" defer></script>
</body>
</html>
<script>
  function submitReservation() {
    const form = document.getElementById('reserveForm');
    const formData = new FormData(form);

    const payload = {
      roomType: formData.get('roomTypeReserve'),
      roomNumber: formData.get('roomID'),
      startDateTime: formData.get('startDateTime'),
      endDateTime: formData.get('endDateTime'),
      subjectActivity: formData.get('subjectActivity'),
      purpose: formData.get('purpose'),
      divisionOffice: formData.get('divisionOffice')
    };

    fetch('reserve_handler.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("Reservation submitted successfully!");
        location.reload();
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch(err => {
      alert("Request failed: " + err.message);
    });
  }
</script>

<?php
session_start();
if (!isset($_SESSION['studentID'])) { header("Location: login.php"); exit; }
?>

<?php
include 'db_connection.php';
$studentID = $_SESSION['studentID']; // Use session-based ID

$sql = "SELECT r.*, rm.name AS room_name, s.name AS student_name
        FROM reservation r
        JOIN room rm ON r.roomID = rm.roomID
        JOIN student s ON r.studentID = s.studentID
        WHERE r.studentID = $studentID
        ORDER BY r.reservation_date DESC, r.start_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BooCAS - Student Bookings</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/themes/material_blue.css">
  <link rel="stylesheet" href="styles.css">
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
        <li><a href="calendar.php">Calendar</a></li>
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
          <select id="roomTypeReserve" name="roomType" onchange="filterRooms()" required>
            <option value="">-- Select Room Type --</option>
            <option value="classroom">Classroom</option>
            <option value="laboratory">Laboratory</option>
          </select>

          <label for="roomNumber">Room Number</label>
          <select id="roomNumber" name="roomNumber" required>
            <option value="">-- Select Room --</option>
          </select>

          <label for="startDateTime" onkeydown="return false;">Start</label>
          <!-- Use readonly to prevent manual typing and ensure Flatpickr works as expected -->
          <input type="text" id="startDateTime" name="startDateTime" placeholder="Select start date & time" readonly>

          <label for="endDateTime" onkeydown="return false;">End</label>
          <!-- Use readonly to prevent manual typing and ensure Flatpickr works as expected -->
          <input type="text" id="endDateTime" name="endDateTime" placeholder="Select end date & time" readonly>

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
        <h2>
          <?php
            // Get the student's first name from the session or from the first row of results
            $studentFirstName = '';
            if (!empty($allRows)) {
              // Use the first row's student_name if available
              $fullName = $allRows[0]['student_name'] ?? '';
              $studentFirstName = explode(' ', trim($fullName))[0];
            } elseif (isset($_SESSION['studentName'])) {
              // Fallback to session if available
              $studentFirstName = explode(' ', trim($_SESSION['studentName']))[0];
            } else {
              $studentFirstName = 'My';
            }
            echo htmlspecialchars($studentFirstName) . "'s History";
          ?>
        </h2>
        <?php
          $count = 0;
          $allRows = [];
          while ($row = $result->fetch_assoc()) {
            $allRows[] = $row;
          }
          // Separate this student's pending and non-pending reservations
          $pendingRows = [];
          $otherRows = [];
          foreach ($allRows as $row) {
            if (strtolower($row['status']) === 'pending') {
              $pendingRows[] = $row;
            } else {
              $otherRows[] = $row;
            }
          }
          // Merge pending first, then others
          $sortedRows = array_merge($pendingRows, $otherRows);
          foreach ($sortedRows as $i => $row):
            $isHidden = $i >= 5;
        ?>
          <div class="booking-item<?= $isHidden ? ' hidden-booking' : '' ?>"
               onclick="showReservationDetails(this)"
               data-room="<?= htmlspecialchars($row['room_name']) ?>"
               data-date="<?= date("F j, Y", strtotime($row['reservation_date'])) ?>"
               data-time="<?= date("h:i A", strtotime($row['start_time'])) ?> - <?= date("h:i A", strtotime($row['end_time'])) ?>"
               data-student="<?= htmlspecialchars($_SESSION['studentName'] ?? '') ?>"
               data-subject="<?= htmlspecialchars($row['subjectActivity']) ?>"
               data-division="<?= htmlspecialchars($row['divisionOffice']) ?>"
               data-purpose="<?= htmlspecialchars($row['purpose']) ?>"
               data-status="<?= htmlspecialchars($row['status']) ?>">
            <div class="booking-details">
              <p class="room-name"><?= htmlspecialchars($row['room_name']) ?></p>
              <p><?= date("F j, Y", strtotime($row['reservation_date'])) ?> 
                 <?= date("h:i A", strtotime($row['start_time'])) ?> - 
                 <?= date("h:i A", strtotime($row['end_time'])) ?></p>
              <div class="status-btn-center">
                <button class="status-btn <?= strtolower($row['status']) ?>"
                  <?php if (isset($row['reservationID'])): ?>
                    onclick="event.stopPropagation(); toggleStatusDropdown(this)"
                  <?php endif; ?>
                >
                  <?= strtoupper($row['status']) ?>
                </button>
                <?php if (strtolower($row['status']) === 'rejected' && !empty($row['decline_reason'])): ?>
                  <p><strong>Decline Reason:</strong> <?= htmlspecialchars($row['decline_reason']) ?></p>
                <?php endif; ?>
                <?php if (isset($row['reservationID'])): ?>
                  <div class="status-dropdown" style="display:none; position: absolute; z-index: 10; right: 0;">
                    <button onclick="event.stopPropagation(); updateStatus(<?= $row['reservationID'] ?>, 'Approved', this)">Approved</button>
                    <button onclick="event.stopPropagation(); updateStatus(<?= $row['reservationID'] ?>, 'Rejected', this)">Rejected</button>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (count($sortedRows) > 5): ?>
          <button id="toggleHistoryBtn" type="button" style="margin: 16px auto 0; display: block;" onclick="toggleBookingHistory()">Show All</button>
        <?php endif; ?>
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

  <!-- Modal for reservation details -->
  <div class="modal" id="detailsModal" style="display:none;">
    <div class="modal-content">
      <span class="close-modal" onclick="closeDetailsModal()">&times;</span>
      <h3>Reservation Details</h3>
      <div id="modalDetails"></div>
    </div>
  </div>

  <script>
  function toggleBookingHistory() {
    const hidden = document.querySelectorAll('.hidden-booking');
    const btn = document.getElementById('toggleHistoryBtn');
    const isHidden = hidden[0] && hidden[0].style.display !== 'block';
    hidden.forEach(div => {
      div.style.display = isHidden ? 'block' : 'none';
    });
    btn.textContent = isHidden ? 'Show Less' : 'Show All';
  }
  // Hide hidden bookings by default
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.hidden-booking').forEach(div => div.style.display = 'none');
  });
  </script>
</body>
</html>
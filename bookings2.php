<?php
session_start();
if (!isset($_SESSION['adminID'])) { header("Location: login.php"); exit; }
?>

<?php
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
$historySQL = "SELECT r.*, rm.name AS room_name, s.name AS student_name
               FROM reservation r
               JOIN room rm ON r.roomID = rm.roomID
               JOIN student s ON r.studentID = s.studentID
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
        <li><a id="bookings-link" href="bookings2.php">Reservations</a></li>
        <li><a href="calendar.php">Calendar</a></li>
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
          <h2>Pending Reservations</h2>
          <?php while ($row = $pendingResult->fetch_assoc()): ?>
            <div class="booking-item booking-request-card"
              onclick="showReservationDetails(this)"
              data-room="<?= htmlspecialchars($row['room_name']) ?>"
              data-date="<?= date("F j, Y", strtotime($row['reservation_date'])) ?>"
              data-time="<?= date("h:i A", strtotime($row['start_time'])) ?> - <?= date("h:i A", strtotime($row['end_time'])) ?>"                data-student="<?= htmlspecialchars($row['student_name'] ?? 'N/A') ?>"
              data-subject="<?= htmlspecialchars($row['subjectActivity'] ?? 'N/A') ?>"
              data-division="<?= htmlspecialchars($row['divisionOffice'] ?? 'N/A') ?>"
              data-purpose="<?= htmlspecialchars($row['purpose'] ?? 'N/A') ?>"
              data-status="<?= htmlspecialchars($row['status']) ?>">
            <div class="booking-details">
                <p class="room-name"><?= $row['room_name'] ?></p>
                <p><?= date("F j, Y", strtotime($row['reservation_date'])) ?> 
                   <?= date("h:i A", strtotime($row['start_time'])) ?> - 
                   <?= date("h:i A", strtotime($row['end_time'])) ?></p>
                <p><strong>Student:</strong> <?= $row['student_name'] ?></p>
                <p><strong>Purpose:</strong> <?= $row['purpose'] ?></p>
              </div>
              <div class="booking-actions">
                <button class="confirmed" onclick="event.stopPropagation(); approveBooking(<?= $row['reservationID'] ?>)">Approve</button>
                <button 
                  class="decline-btn<?php if (strtolower($row['status']) === 'rejected') echo ' rejected'; ?>" 
                  onclick="event.stopPropagation(); declineBooking(<?= $row['reservationID'] ?>)">
                  Decline
                </button>
              </div>
            </div>
          <?php endwhile; ?>
        </section>

        <section class="booking-history">
          <h2>Booking History</h2>
          <?php
            $historyRows = [];
            while ($row = $historyResult->fetch_assoc()) {
              $historyRows[] = $row;
            }
            foreach ($historyRows as $i => $row):
              $isHidden = $i >= 5;
          ?>
            <div class="booking-item<?= $isHidden ? ' hidden-booking' : '' ?>"
                 onclick="showReservationDetails(this)"
                 data-room="<?= htmlspecialchars($row['room_name']) ?>"
                 data-date="<?= date("F j, Y", strtotime($row['reservation_date'])) ?>"
                 data-time="<?= date("h:i A", strtotime($row['start_time'])) ?> - <?= date("h:i A", strtotime($row['end_time'])) ?>"
                 data-student="<?= htmlspecialchars($row['student_name'] ?? 'N/A') ?>"
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
                    onclick="event.stopPropagation(); toggleStatusDropdown(this)">
                    <?= strtoupper($row['status']) ?>
                  </button>
                  <?php if (strtolower($row['status']) === 'rejected' && !empty($row['decline_reason'])): ?>
                    <p class="decline-reason"><strong>Decline Reason:</strong> <?= htmlspecialchars($row['decline_reason']) ?></p>
                  <?php endif; ?>
                  <div class="status-dropdown" style="display:none; position: absolute; left: 50%; transform: translateX(-50%); z-index: 10; top: 110%;">
                    <button onclick="event.stopPropagation(); updateStatus(<?= $row['reservationID'] ?>, 'Approved', this)">Approved</button>
                    <button onclick="event.stopPropagation(); updateStatus(<?= $row['reservationID'] ?>, 'Rejected', this)">Rejected</button>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (count($historyRows) > 5): ?>
            <button id="toggleHistoryBtn" type="button" style="margin: 16px auto 0; display: block;" onclick="toggleBookingHistory()">Show All</button>
          <?php endif; ?>
        </section>
      </div>
    </section>


    <!-- Reservation Details Modal -->
    <div class="modal" id="detailsModal" style="display:none;">
      <div class="modal-content">
        <span class="close-modal" onclick="closeDetailsModal()">&times;</span>
        <h3>Reservation Details</h3>
        <div id="modalDetails"></div>
      </div>
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
    document.getElementById('detailsModal').addEventListener('click', function(e) {
      if (e.target === this) closeDetailsModal();
    });

    function declineBooking(reservationID) {
      const reason = prompt("Please enter the reason for declining this reservation:");
      if (reason === null || reason.trim() === "") {
        alert("Decline reason is required.");
        return;
      }
      // Send AJAX request to update status and reason
      fetch('decline_reservation.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `reservationID=${reservationID}&reason=${encodeURIComponent(reason)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("Reservation declined.");
          location.reload();
        } else {
          alert("Failed to decline reservation.");
        }
      });
    }

    function changeStatus(select, reservationID) {
      const newStatus = select.value;
      let reason = '';
      if (newStatus === 'Rejected') {
        reason = prompt("Please enter the reason for declining this reservation:");
        if (reason === null || reason.trim() === "") {
          alert("Decline reason is required.");
          // Reset dropdown to previous value
          select.value = "Approved";
          return;
        }
      }
      fetch('change_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `reservationID=${reservationID}&status=${encodeURIComponent(newStatus)}&reason=${encodeURIComponent(reason)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("Status updated.");
          location.reload();
        } else {
          alert("Failed to update status.");
        }
      });
    }

    function toggleStatusDropdown(button) {
      const dropdown = button.nextElementSibling;
      const isVisible = dropdown.style.display === 'block';
      dropdown.style.display = isVisible ? 'none' : 'block';
    }

    function updateStatus(reservationID, status, button) {
      let reason = '';
      if (status === 'Rejected') {
        reason = prompt("Please enter the reason for declining this reservation:");
        if (reason === null || reason.trim() === "") {
          alert("Decline reason is required.");
          return;
        }
      }
      fetch('change_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `reservationID=${reservationID}&status=${encodeURIComponent(status)}&reason=${encodeURIComponent(reason)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("Status updated.");
          location.reload();
        } else {
          alert("Failed to update status. " + (data.error || ""));
        }
      });
    }

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

    document.addEventListener('DOMContentLoaded', function() {
      const bookingsLink = document.getElementById('bookings-link');
      if (bookingsLink) {
        bookingsLink.addEventListener('click', function(e) {
          e.preventDefault();
          location.reload();
        });
      }
    });
  </script>
  <script src="script.js" defer></script>
</body>
</html>

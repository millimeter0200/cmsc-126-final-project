<?php
session_start();

$isStudent = isset($_SESSION['studentID']);
$isAdmin = isset($_SESSION['adminID']);

if (!$isStudent && !$isAdmin) {
  header("Location: login.php");
  exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BooCAS - Calendar</title>
  <link rel="stylesheet" href="styles.css" />
  <!-- Add FullCalendar CSS/JS in <head> -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
</head>
<body>
  <header>
    <div class="logo">
      <img src="booCAS_logo.png" alt="booCAS Logo" class="logo-icon" />
    </div>
    <nav>
      <ul>
        <?php if ($isStudent): ?>
          <li><a href="bookings1.php">My Bookings</a></li>
        <?php elseif ($isAdmin): ?>
          <li><a href="bookings2.php">Reservations</a></li>
        <?php endif; ?>
        <li><a href="calendar.php">Calendar</a></li>
        <li><a href="login.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="calendar-layout">
      <div class="room-status">
        <h2>Upcoming</h2>
        <div class="room-status-list">
          <?php
            include 'db_connection.php';
            date_default_timezone_set('Asia/Manila'); // Set to your timezone

            $now = date('Y-m-d H:i:s');
            $statusQuery = "SELECT r.reservation_date, r.start_time, r.end_time, r.purpose, r.subjectActivity, r.divisionOffice, r.roomID, rm.name AS room_name
                            FROM reservation r
                            JOIN room rm ON r.roomID = rm.roomID
                            WHERE r.status = 'Approved' AND CONCAT(r.reservation_date, ' ', r.end_time) >= ?
                            ORDER BY r.reservation_date ASC, r.start_time ASC";
            $stmt = $conn->prepare($statusQuery);
            $stmt->bind_param("s", $now);
            $stmt->execute();
            $statusResult = $stmt->get_result();

            if ($statusResult && $statusResult->num_rows > 0):
              while ($row = $statusResult->fetch_assoc()):
                $date = date("F j, Y", strtotime($row['reservation_date']));
                $time = date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time']));
                ?>
                <div class="room-status-card"
                     data-room="<?= htmlspecialchars($row['room_name']) ?>"
                     data-date="<?= $date ?>"
                     data-time="<?= $time ?>"
                     data-purpose="<?= htmlspecialchars($row['purpose']) ?>"
                     data-subject="<?= htmlspecialchars($row['subjectActivity']) ?>"
                     data-division="<?= htmlspecialchars($row['divisionOffice']) ?>">
                  <p><strong>Date and Time:</strong> <?= $date ?> | <?= $time ?></p>
                  <p><strong>Room:</strong> <?= htmlspecialchars($row['room_name']) ?></p>
                  <p><strong>Purpose:</strong> <?= htmlspecialchars($row['purpose']) ?></p>
                </div>
              <?php endwhile;
            else: ?>
              <p>No approved reservations yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="calendar">
        <h2>Calendar</h2>
        <div id="calendar"></div>
      </div>
    </div>
  </main>

  <!-- Modal for reservation details -->
  <div class="modal" id="detailsModal" style="display:none;">
    <div class="modal-content">
      <span class="close-modal" onclick="closeDetailsModal()">&times;</span>
      <h3>Reservation Details</h3>
      <div id="modalDetails"></div>
    </div>
  </div>

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
  document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          events: 'fetch_events.php',
          eventColor: '#378006',
          eventDidMount: function(info) {
              const status = info.event.extendedProps.status;
              info.el.setAttribute('data-status', status);
              info.el.title = `${info.event.title} (${status})`;
          },
          eventClick: function(info) {
              showReservationModal({
                  room: info.event.title,
                  date: info.event.start.toLocaleDateString(),
                  time: info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) +
                      (info.event.end ? ' - ' + info.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : ''),
                  subject: info.event.extendedProps.subject || '',
                  division: info.event.extendedProps.division || '',
                  purpose: info.event.extendedProps.purpose || '',
                  status: info.event.extendedProps.status || ''
              });
          }
      });

      calendar.render();

      // Add click event to room status cards
      document.querySelectorAll('.room-status-card').forEach(function(card) {
          card.addEventListener('click', function() {
              showReservationModal({
                  room: card.dataset.room || '',
                  date: card.dataset.date || '',
                  time: card.dataset.time || '',
                  subject: card.dataset.subject || '',
                  division: card.dataset.division || '',
                  purpose: card.dataset.purpose || '',
                  status: 'Approved'
              });
          });
      });
  });

  // Show reservation details in modal
  function showReservationModal(data) {
      const modal = document.getElementById("detailsModal");
      const details = document.getElementById("modalDetails");
      details.innerHTML = `
          <p><strong>Room:</strong> ${data.room}</p>
          <p><strong>Date:</strong> ${data.date}</p>
          <p><strong>Time:</strong> ${data.time || ''}</p>
          <p><strong>Subject/Activity:</strong> ${data.subject}</p>
          <p><strong>Division/Office:</strong> ${data.division}</p>
          <p><strong>Purpose:</strong> ${data.purpose}</p>
          <p><strong>Status:</strong> ${data.status}</p>
      `;
      modal.style.display = "flex";
  }

  // Close modal function
  function closeDetailsModal() {
      document.getElementById("detailsModal").style.display = "none";
  }
  </script>
</body>
</html>

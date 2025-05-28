<?php
session_start();

if (!isset($_SESSION['studentID'])) {
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
        <li><a href="bookings1.php">My Bookings</a></li>
        <li><a href="calendar.php">Status</a></li>
        <li><a href="login.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="bookings-grid">
      <section class="bookings">
        <h2>Calendar</h2>
        <div id="calendar"></div>
      </section>

      <section class="booking-history">
        <h2>Room Status</h2>
        <ul class="room-status-list">
          <li>Room 101: <span class="status available">Available</span></li>
          <li>CL2: <span class="status booked">Booked</span></li>
          <li>PL2: <span class="status available">Available</span></li>
          <li>CL3: <span class="status booked">Booked</span></li>
        </ul>
        <h3>Announcements</h3>
        <p>🛠️ Maintenance on <strong>May 15, 2025</strong></p>
        <p>📌 Submit booking requests <strong>48 hours</strong> in advance.</p>
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
          }
      });

      calendar.render();
  });
  </script>
</body>
</html>

<?php
  include("database.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <li><a href="bookings2.html" id="bookings-link">Bookings</a></li>
        <li><a href="calendar.html">Schedule & Events</a></li> 
      </ul>
    </nav>
  </header>
  <main>
    <div class="admin-grid">
      <section class="dashboard-stats">
        <div class="stat">
          <img src="confirmed.png" alt="Confirmed">
          <h3>CONFIRMED BOOKINGS</h3>
          <p>1</p>
        </div>
        <div class="stat">
          <img src="pending.png" alt="Pending">
          <h3>PENDING REQUESTS</h3>
          <p>3</p>
        </div>
        <div class="stat">
          <img src="available_rooms.png" alt="Available">
          <h3>AVAILABLE ROOMS</h3>
          <p>-</p>
        </div>
      </section>
<section class="booking-requests">
        <h2>Pending Booking Requests</h2>
        <?php
        $sql = "SELECT * FROM reservations WHERE status = 'pending'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="booking-item">
                    <div class="booking-details">
                        <p class="room-name"><?php echo htmlspecialchars($row['room_name']); ?></p>
                        <p><?php echo date('M d, Y h:i A', strtotime($row['booking_date'])) . ' - ' . date('h:i A', strtotime($row['end_time'])); ?></p>
                        <p>Student: <?php echo htmlspecialchars($row['student_name']); ?></p>
                        <p>Reason: <?php echo htmlspecialchars($row['reason']); ?></p>
                    </div>
                    <div class="booking-actions">
                        <form action="approve_booking.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="confirmed">Approve</button>
                        </form>
                        <form action="decline_booking.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="pending">Decline</button>
                        </form>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p>No pending bookings found.</p>';
        }
        $conn->close();
        ?>
    </section>
      <section class="booking-history">
        <h2>Booking History</h2>
        <div class="booking-item">
          <div class="booking-details">
            <p class="room-name">Room 106</p>
            <p>April 20, 2025 10:00 AM - 12:00 PM</p>
          </div>
          <button class="confirmed">CONFIRMED</button>
        </div>
      </section>
    </div>
  </main>
  <footer>
    <p>FOOTNOTE</p>
  </footer>
  <script src="script.js"></script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'93da717d3f72bcea',t:'MTc0Njg5MDY2NC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>

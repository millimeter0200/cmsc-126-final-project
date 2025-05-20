<?php
include "functions.php";

$pendingResult = getPendingReservations($conn);
$historyResult = getHistoryReservations($conn);
$confirmedCount = getConfirmedCount($conn);

include "header.php";
?>

<div class="admin-grid">
  <section class="dashboard-stats">
    <div class="stat">
      <img src="confirmed.png" alt="Confirmed" />
      <h3>CONFIRMED BOOKINGS</h3>
      <p><?php echo $confirmedCount; ?></p>
    </div>
    <div class="stat">
      <img src="pending.png" alt="Pending" />
      <h3>PENDING REQUESTS</h3>
      <p><?php echo mysqli_num_rows($pendingResult); ?></p>
    </div>
  </section>

  <section class="booking-requests">
    <h2>Pending Booking Requests</h2>
    <?php if (mysqli_num_rows($pendingResult) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($pendingResult)): ?>
        <div class="booking-item">
          <div class="booking-details">
            <p class="room-name"><?php echo ($row['roomID']); ?></p>
            <p>
              <?php
                $start = date("F j, Y h:i A", strtotime($row['start_time']));
                $end = date("h:i A", strtotime($row['end_time']));
                echo "$start - $end";
              ?>
            </p>
            <p>Student: <?php echo ($row['studentName']); ?></p>
            <p>Reason: <?php echo ($row['purpose']); ?></p>
          </div>
          <div class="booking-actions">
            <form method="post" action="process_reservation.php" style="display: inline;">
              <input type="hidden" name="reservationID" value="<?php echo $row['reservationID']; ?>" />
              <input type="hidden" name="action" value="approve" />
              <input type="hidden" name="adminID" value="1" />
              <button class="confirmed" type="submit">Approve</button>
            </form>
            <form method="post" action="process_reservation.php" style="display: inline;">
              <input type="hidden" name="reservationID" value="<?php echo $row['reservationID']; ?>" />
              <input type="hidden" name="action" value="decline" />
              <input type="hidden" name="adminID" value="1" />
              <button class="pending" type="submit">Decline</button>
            </form>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No pending requests.</p>
    <?php endif; ?>
  </section>

  <section class="booking-history">
    <h2>Booking History</h2>
    <?php if (mysqli_num_rows($historyResult) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($historyResult)): ?>
        <div class="booking-item">
          <div class="booking-details">
            <p class="room-name"><?php echo ($row['roomID']); ?></p>
            <p>
              <?php
                $start = date("F j, Y h:i A", strtotime($row['start_time']));
                $end = date("h:i A", strtotime($row['end_time']));
                echo "$start - $end";
              ?>
            </p>
            <p>Student: <?php echo ($row['studentName']); ?></p>
          </div>
          <button class="<?php echo strtolower($row['status']); ?>">
            <?php echo strtoupper($row['status']); ?>
          </button>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No booking history yet.</p>
    <?php endif; ?>
  </section>
</div>

<?php
include "footer.php";
?>

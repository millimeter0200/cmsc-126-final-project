<?php
include 'db_connection.php';

$room = $_GET['room'];
$start = $_POST['start']; // e.g., "2025-05-27 11:00 AM"
$end = $_POST['end'];     // e.g., "2025-05-27 12:00 PM"

$startTime = date("Y-m-d H:i:s", strtotime($start));
$endTime = date("Y-m-d H:i:s", strtotime($end));

$sql = "SELECT * FROM reservation 
        WHERE roomID = ? 
        AND (
          (start_time < ? AND end_time > ?) OR
          (start_time >= ? AND start_time < ?)
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $roomID, $endTime, $startTime, $startTime, $endTime);
$stmt->execute();
$result = $stmt->get_result();

$booked = [];
while ($row = $result->fetch_assoc()) {
  $booked[] = $row['start_time'] . ' - ' . $row['end_time'];
}

// All possible 30-min slots from 7 AM to 9 PM
$allSlots = [];
$start = 7 * 60;
$end = 21 * 60;
for ($mins = $start; $mins < $end; $mins += 30) {
  $sh = floor($mins / 60);
  $sm = $mins % 60;
  $eh = floor(($mins + 30) / 60);
  $em = ($mins + 30) % 60;

  $format = fn($h, $m) => date("h:i A", strtotime("$h:$m"));
  $label = $format($sh, $sm) . " - " . $format($eh, $em);

  if (!in_array($label, $booked)) {
    $allSlots[] = $label;
  }
}

header('Content-Type: application/json');
echo json_encode($allSlots);

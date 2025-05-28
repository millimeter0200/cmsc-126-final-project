<?php
include 'db_connection.php';

header('Content-Type: application/json');

// Get only approved or pending reservations
$query = "SELECT roomID, reservation_date, start_time, end_time, status FROM reservation WHERE status IN ('pending', 'approved')";
$result = $conn->query($query);

$events = [];

while ($row = $result->fetch_assoc()) {
    $start = $row['reservation_date'] . 'T' . $row['start_time'];
    $end = $row['reservation_date'] . 'T' . $row['end_time'];
    $events[] = [
        'title' => "Room " . $row['roomID'] . " (" . ucfirst($row['status']) . ")",
        'start' => $start,
        'end' => $end,
        'status' => $row['status']
    ];
}

echo json_encode($events);
?>

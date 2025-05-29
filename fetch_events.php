<?php
include 'db_connection.php';
header('Content-Type: application/json');

$sql = "SELECT r.*, rm.name AS room_name 
        FROM reservation r
        JOIN room rm ON r.roomID = rm.roomID
        WHERE r.status = 'Approved'";
$result = $conn->query($sql);

$events = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $start = $row['reservation_date'] . 'T' . $row['start_time'];
        $end = $row['reservation_date'] . 'T' . $row['end_time'];
        $events[] = [
            'title' => $row['room_name'],
            'start' => $start,
            'end'   => $end,
            'color' => '#28a745',
            'extendedProps' => [
                'student' => $row['full_name'] ?? '',
                'purpose' => $row['purpose'],
                'division' => $row['divisionOffice'],
                'subject' => $row['subjectActivity'],
                'status' => $row['status']
            ]
        ];
    }
}

echo json_encode($events);
?>

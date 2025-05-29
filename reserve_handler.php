<?php
session_start();
header("Content-Type: application/json");
ini_set("display_errors", 1);
error_reporting(E_ALL);

include 'db_connection.php';

// Use $_POST instead of JSON decode
$roomName = $_POST['roomNumber'] ?? '';
$roomType = $_POST['roomType'] ?? '';
$startDateTime = $_POST['startDateTime'] ?? '';
$endDateTime = $_POST['endDateTime'] ?? '';
$subjectActivity = $_POST['subjectActivity'] ?? '';
$purpose = $_POST['purpose'] ?? '';
$divisionOffice = $_POST['divisionOffice'] ?? '';
$studentID = $_SESSION['studentID'] ?? 1;

if (!$roomName || !$roomType || !$startDateTime || !$endDateTime || !$subjectActivity || !$purpose || !$divisionOffice) {
  echo json_encode(["success" => false, "message" => "Missing required fields"]);
  exit;
}

$startDateTime = $_POST['startDateTime'];
$endDateTime = $_POST['endDateTime'];

if (strtotime($startDateTime) < time()) {
    // Reject reservation in the past
    echo json_encode(['success' => false, 'message' => 'You cannot reserve a room in the past.']);
    exit;
}

// Get roomID
$roomQuery = $conn->prepare("SELECT roomID FROM room WHERE name = ?");
$roomQuery->bind_param("s", $roomName);
$roomQuery->execute();
$roomResult = $roomQuery->get_result();

if ($roomResult->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Room not found"]);
  exit;
}

$roomID = $roomResult->fetch_assoc()['roomID'];

// Format time
$reservationDate = date('Y-m-d', strtotime($startDateTime));
$startTime = date('H:i:s', strtotime($startDateTime));
$endTime = date('H:i:s', strtotime($endDateTime));

// Check for conflict with approved reservations
$stmt = $conn->prepare(
    "SELECT 1 FROM reservation 
     WHERE roomID = ? AND reservation_date = ? AND status = 'Approved'
     AND (
        (start_time < ? AND end_time > ?) -- overlap
     )"
);
$stmt->bind_param("isss", $roomID, $reservationDate, $endTime, $startTime);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This room is already booked for the selected date and time.']);
    exit;
}
$stmt->close();

// Insert reservation (without equipment)
$stmt = $conn->prepare("INSERT INTO reservation (studentID, roomID, reservation_date, start_time, end_time, subjectActivity, purpose, divisionOffice, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");

$stmt->bind_param("iissssss", $studentID, $roomID, $reservationDate, $startTime, $endTime, $subjectActivity, $purpose, $divisionOffice);

if ($stmt->execute()) {
  echo json_encode(["success" => true, "message" => "Reservation submitted"]);
} else {
  echo json_encode(["success" => false, "message" => "MySQL error: " . $stmt->error]);
}
?>

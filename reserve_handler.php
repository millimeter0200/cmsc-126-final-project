<?php
session_start();
header("Content-Type: application/json");
ini_set("display_errors", 1);
error_reporting(E_ALL);

include 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  echo json_encode(["success" => false, "message" => "Invalid JSON payload"]);
  exit;
}

// Extract data
$roomName = $data['roomNumber'] ?? '';
$roomType = $data['roomType'] ?? '';
$startDateTime = $data['startDateTime'] ?? '';
$endDateTime = $data['endDateTime'] ?? '';
$subjectActivity = $data['subjectActivity'] ?? '';
$purpose = $data['purpose'] ?? '';
$divisionOffice = $data['divisionOffice'] ?? '';
$studentID = $_SESSION['studentID'] ?? 1;

if (!$roomName || !$roomType || !$startDateTime || !$endDateTime || !$subjectActivity || !$purpose || !$divisionOffice) {
  echo json_encode(["success" => false, "message" => "Missing required fields"]);
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

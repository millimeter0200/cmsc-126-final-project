<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

include 'db_connection.php';
include 'alert_message.php';
include 'validate_input.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, "Invalid request method");
}

//json input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    sendResponse(false, "Invalid JSON payload");
}

// Extract and validate required fields
$roomName = $data['roomNumber'] ?? '';
$roomType = $data['roomType'] ?? '';
$startDateTime = $data['startDateTime'] ?? '';
$endDateTime = $data['endDateTime'] ?? '';
$subjectActivity = $data['subjectActivity'] ?? '';
$purpose = $data['purpose'] ?? '';
$divisionOffice = $data['divisionOffice'] ?? '';
$studentID = $_SESSION['studentID'] ?? null;

if (!$studentID) {
    sendResponse(false, "User not authenticated. Please log in before making a reservation.");
}

if (!$roomName) {
    sendResponse(false, "Missing room name. Please select a valid room.");
}

if (!$roomType) {
    sendResponse(false, "Missing room type.");
}

if (!$startDateTime || !$endDateTime) {
    sendResponse(false, "No start or end time. Both must be provided.");
}

if (!$subjectActivity) {
    sendResponse(false, "No subject/activity details. This field is required.");
}

if (!$purpose) {
    sendResponse(false, "No reservation purpose. Please provide a reason.");
}

if (!$divisionOffice) {
    sendResponse(false, "This field is required.");
}

//validate time
$start = validateDateTime($startDateTime);
$end = validateDateTime($endDateTime);

if (!$start) {
    sendResponse(false, "Wrong start date format");
}

if (!$end) {
    sendResponse(false, "Wrong end date format");
}

//start time is not in the past
$currentDateTime = new DateTime('now');
if ($start < $currentDateTime) {
    sendResponse(false, "Start time cannot be in the past");
}

//start before end
if ($start >= $end) {
    sendResponse(false, "start time must be before end time.");
}

//datetime values
$reservationDate = $start->format('Y-m-d');
$startTime = $start->format('H:i:s');
$endTime = $end->format('H:i:s');

//get room ID
$roomQuery = $conn->prepare("SELECT roomID FROM room WHERE name = ? AND type = ?");
$roomQuery->bind_param("ss", $roomName, $roomType);
$roomQuery->execute();
$roomResult = $roomQuery->get_result();

if ($roomResult->num_rows === 0) {
    sendResponse(false, "Selected room not found. Please choose a valid room.");
}

$roomID = $roomResult->fetch_assoc()['roomID'];

//scheduling conflict
$conflictQuery = $conn->prepare("
    SELECT studentID, status FROM reservation 
    WHERE roomID = ? 
    AND reservation_date = ? 
    AND (
        (start_time < ? AND end_time > ?) 
        OR (start_time < ? AND end_time > ?) 
        OR (start_time >= ? AND end_time <= ?)
    )
");

$conflictQuery->bind_param("isssssss", $roomID, $reservationDate, $endTime, $startTime, $endTime, $startTime, $startTime, $endTime);
$conflictQuery->execute();
$conflictResult = $conflictQuery->get_result();

$hasPendingConflict = false;
$roomApproved = false;

while ($row = $conflictResult->fetch_assoc()) {
    if ($row['studentID'] == $studentID && $row['status'] == 'pending') {
        $hasPendingConflict = true;
    }
    if ($row['status'] == 'approved') {
        $roomApproved = true;
    }
}

if ($hasPendingConflict) {
    sendResponse("You have a pending reservation for this room and time. Timeslot conflict");
} elseif ($roomApproved) {
    sendResponse("Already booked! Please choose a different time slot.");
}

//insert reservation
$stmt = $conn->prepare("
    INSERT INTO reservation (studentID, roomID, reservation_date, start_time, end_time, subjectActivity, purpose, divisionOffice, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");
$stmt->bind_param("iissssss", $studentID, $roomID, $reservationDate, $startTime, $endTime, $subjectActivity, $purpose, $divisionOffice);

if ($stmt->execute()) {
    sendResponse(true, "Reservation submitted successfully.", ["reservationID" => $stmt->insert_id]);
} else {
    sendResponse(false, "failed to insert reservation. error: " . $stmt->error);
}

$stmt->close();
?>
<?php
include 'db_connection.php';
header("Content-Type: application/json");

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['reservationID'], $data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$reservationID = intval($data['reservationID']);
$status = $data['status'];

// Validate status value
if (!in_array($status, ['Approved', 'Rejected'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Update reservation
$stmt = $conn->prepare("UPDATE reservation SET status = ? WHERE reservationID = ?");
$stmt->bind_param("si", $status, $reservationID);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();

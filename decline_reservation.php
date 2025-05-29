<?php
session_start();
if (!isset($_SESSION['adminID'])) {
  echo json_encode(['success' => false]);
  exit;
}
include 'db_connection.php';

$reservationID = $_POST['reservationID'] ?? '';
$reason = $_POST['reason'] ?? '';

if ($reservationID && $reason) {
  $stmt = $conn->prepare("UPDATE reservation SET status='Rejected', decline_reason=? WHERE reservationID=?");
  $stmt->bind_param("si", $reason, $reservationID);
  $success = $stmt->execute();
  $stmt->close();
  echo json_encode(['success' => $success]);
} else {
  echo json_encode(['success' => false]);
}
?>
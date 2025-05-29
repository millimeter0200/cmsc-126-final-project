<?php
session_start();
if (!isset($_SESSION['adminID'])) {
  echo json_encode(['success' => false]);
  exit;
}
include 'db_connection.php';

$reservationID = $_POST['reservationID'] ?? '';
$status = $_POST['status'] ?? '';
$reason = $_POST['reason'] ?? '';

if ($reservationID && $status) {
  if ($status === 'Rejected') {
    $stmt = $conn->prepare("UPDATE reservation SET status=?, decline_reason=? WHERE reservationID=?");
    $stmt->bind_param("ssi", $status, $reason, $reservationID);
  } else {
    $stmt = $conn->prepare("UPDATE reservation SET status=?, decline_reason=NULL WHERE reservationID=?");
    $stmt->bind_param("si", $status, $reservationID);
  }
  $success = $stmt->execute();
  $error = $stmt->error;
  $stmt->close();
  echo json_encode(['success' => $success, 'error' => $error]);
} else {
  echo json_encode(['success' => false, 'error' => 'Missing reservationID or status']);
}
?>
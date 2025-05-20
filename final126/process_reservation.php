<?php
include "database.php"; // Your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationID = isset($_POST['reservationID']) ? (int)$_POST['reservationID'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $adminID = isset($_POST['adminID']) ? (int)$_POST['adminID'] : 0;
    $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';

    if ($reservationID > 0 && ($action === 'approve' || $action === 'decline') && $adminID > 0) {
        $newStatus = ($action === 'approve') ? 'Approved' : 'Rejected';

        $remarkEscaped = mysqli_real_escape_string($conn, $remark);


        mysqli_begin_transaction($conn);

        try {
            //update reservation status
            $updateSQL = "UPDATE Reservation SET status = '$newStatus' WHERE reservationID = $reservationID";
            if (!mysqli_query($conn, $updateSQL)) {
                throw new Exception(mysqli_error($conn));
            }

            //insert to manages
            $insertSQL = "INSERT INTO Manages (reservationID, adminID, remark, action_date) 
                          VALUES ($reservationID, $adminID, '$remarkEscaped', NOW())";
            if (!mysqli_query($conn, $insertSQL)) {
                throw new Exception(mysqli_error($conn));
            }

            //commit
            mysqli_commit($conn);

            // Redirect
            header("Location: admin_bookings.php?message=Reservation $newStatus successfully");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            die("Error processing reservation: " . $e->getMessage());
        }
    } else {
        die("Invalid input.");
    }
} else {
    die("Invalid request method.");
}

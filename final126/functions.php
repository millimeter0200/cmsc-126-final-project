<?php
include "database.php";

function getPendingReservations($conn) {
    $pendingSQL = "SELECT R.reservationID, R.roomID, R.start_time, R.end_time, R.status, R.purpose, 
                   CONCAT(S.first_name, ' ', S.middle_initial, '. ', S.last_name) AS studentName
                   FROM Reservation R
                   JOIN Student S ON R.studentID = S.studentID
                   WHERE R.status = 'Pending'";
    return mysqli_query($conn, $pendingSQL);
}

function getHistoryReservations($conn) {
    $historySQL = "SELECT R.roomID, R.start_time, R.end_time, R.status, 
                   CONCAT(S.first_name, ' ', S.middle_initial, '. ', S.last_name) AS studentName
                   FROM Reservation R
                   JOIN Student S ON R.studentID = S.studentID
                   WHERE R.status IN ('Approved', 'Declined')
                   ORDER BY R.start_time DESC";
    return mysqli_query($conn, $historySQL);
}

function getConfirmedCount($conn) {
    $confirmedCountQuery = "SELECT COUNT(*) AS total FROM Reservation WHERE status='Approved'";
    $result = mysqli_query($conn, $confirmedCountQuery);
    return mysqli_fetch_assoc($result)['total'] ?? 0;
}

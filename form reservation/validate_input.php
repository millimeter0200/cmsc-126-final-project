<?php
function validateDateTime($dateTime) {
    try {
        return new DateTime($dateTime);
    } catch (Exception $e) {
        return false;
    }
}

function validateFutureDate($start, $end) {
    $now = new DateTime();
    if ($start < $now) return "We cannot time travel, sorry!";
    if ($start >= $end) return "Start time must be before end time!";
    return null;
}
?>
<?php
header("Access-Control-Allow-Origin: *"); // Allow cross domain API calls
header('Content-Type: application/json; charset=utf-8');
if(isset($_GET['course'])) {
    if(isset($_GET['term'])) {

        // Noarchive flag is set if parameter is provided
        if(isset($_GET['noarchive'])) {
            $noarchive = true;
        } else {
            $noarchive = false;
        }

        require_once('ldap.php');
        // Create list
        $attendeeList = new AttendeeList($_GET['course'], $_GET['term'], $noarchive);
        if ($attendeeList->error == 0) { // Everything OK
            http_response_code(200);
            unset($attendeeList->error); // Remove error variable before returning
            echo json_encode($attendeeList, JSON_UNESCAPED_UNICODE); // Return as JSON object
        } elseif ($attendeeList->error == 1) {
            http_response_code(403); // HTTP code 403 Forbidden (could not bind anonymously)
        } elseif ($attendeeList->error == 2) {
            http_response_code(404); // HTTP code 404 (could not find resource)
        }

    } else {
        http_response_code(400); // Bad request (missing parameters)
    }
} else {
    http_response_code(400); // Bad request (missing parameters)
}
?>

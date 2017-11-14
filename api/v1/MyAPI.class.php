<?php
require_once('API.class.php');
class MyAPI extends API
{
    public function __construct($request, $origin) {
        parent::__construct($request);
    }

    /**
     * Example of an Endpoint
     */
    protected function attendees() {
        $course = strtolower($this->args[0]);
        $term = strtolower($this->args[1]);
        if (isset($this->args[2])) {
            $noarchive = ($this->args[2]=="noarchive")? true: false;
        } else {
            $noarchive = false;
        }

        require_once('LDAP.class.php');
        // Create list
        $attendeeList = new AttendeeList($course, $term, $noarchive);
        if ($attendeeList->error == 0) { // Everything OK
            unset($attendeeList->error); // Remove error variable before returning
            setlocale(LC_COLLATE, "sv_SV");
            usort($attendeeList->list, "localeCompare");
            return array("data"=>$attendeeList,"status"=>200); // Return as JSON object
        } elseif ($attendeeList->error == 1) {
            return array("data"=>"Could not bind to directory.", "status"=>403); // HTTP code 403 Forbidden (could not bind anonymously)
        } elseif ($attendeeList->error == 2) {
            return array("data"=>"Course or term not found.","status"=>404); // HTTP code 404 (could not find resource)
        }
    }
 }
?>

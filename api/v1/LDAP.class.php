<?php

class AttendeeList
{
	public static $ds; // Try to reuse connection
	var $list; // List of attendees
	var $attendees; // Number of attendees
	var $error = 0;

	function __construct($course, $term, $noarchive = false)
	{
		// Create new connection if none is already set
		if (!self::$ds) self::$ds = ldap_connect("ldap.chalmers.se");
		$r = @ldap_bind(self::$ds); // Try to bind with directory
		if(!$r){
			$this->error = 1; // 403 Forbidden to bind
		} else {
			// Search for course and term
			$sr = ldap_search(self::$ds, "ou=groups,dc=chalmers,dc=se", "cn=" . "s_studier_kursdeltagare_chalmers_" . $course . "_" . $term . ($noarchive ? "" : "_arkiv"));

			if(ldap_count_entries(self::$ds, $sr) == 0)	{ // If no record
				$this->error = 2; // HTTP code 404 (could not find resource)
			} else { // If record found
				$entries = ldap_get_entries(self::$ds, $sr);
				$cid = $entries[0]["memberuid"]; // Get list of users
				if (!$cid) { // If users list is empty
					$this->attendees = 0;
					$this->list = [];
				} else {
					$this->attendees = $cid["count"]; // Save number of attendees
					unset($cid["count"]); // Remove count key from object array so it becomes pure ordered array
					$this->list = array_map("createAttendee", $cid); // Create new array of user objects
				}
			}
		}
	}

	function AttendeeList() {
		self::__construct();
	}
}

class Attendee
{
	var $given_name;
	var $surname;
	var $full_name;
	var $email;
	var $cid;

	function __construct($cid, $ds)
	{
		$sr = ldap_search($ds, "ou=people,dc=chalmers,dc=se", "uid=" . $cid); // Search user directory

		if(ldap_count_entries($ds, $sr) == 0) {
			$this->guess($cid); // If not found, use CID as default value
		} else {
			// Set user properties
			$entries = ldap_get_entries($ds, $sr);
			$this->full_name = $entries[0]["cn"][0];
			if (array_key_exists("mail",$entries[0])) {
				$this->email = $entries[0]["mail"][0];
			} else {
				$this->email = $cid . "@student.chalmers.se"; // If email is not found it is probably just CID
			}
			$this->given_name = $entries[0]["givenname"][0];
			$this->surname = $entries[0]["sn"][0];
			$this->cid = $cid;
		}
	}

	function Attendee() {
		self::__construct();
	}

	function guess($cid)
	{
		$this->given_name = $this->surname = $this->full_name = $this->cid = $cid;
		$this->email = $cid . "@student.chalmers.se";
	}
}

// Return an Attendee object
function createAttendee($cid) {
	return new Attendee($cid, AttendeeList::$ds);
}

?>

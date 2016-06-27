<?php

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) .'/../externallib.php');
require_once(dirname(__FILE__) .'/../include/event.php');
require_once(dirname(__FILE__) .'/../include/event_handler.php');
class local_gamification_observer {

	/**
	* Triggers when a competence is assigned in exacomp.
	* Ignores students assigning compentences to themselves
	* @param 	\core\event			The moodle event object	
	**/

	public static function grade_set_as_teacher($e) {
		$data = $e->get_data();
		if ($data['relateduserid'] == $data['userid'] ) return; // Student rating herself. We should already have this event through a Dakora webservice call.
		// We don't have the grade. Get it from DB.
		global $DB;
		$r = $DB->get_record('block_exacompcompuser',array('compid'=>$data['objectid'], 'reviewerid' => $data['userid'], 'userid' => $data['relateduserid'] ));
		$value = $r?$r->value:false;

		// Create event and send it to the handler. Handled the same as a webservice triggered event from here.
		$event = new local\gamification\event('grade_set_as_teacher',array(
			'relateduserid' => $data['relateduserid'],
			'courseid' => $data['courseid'],
			'descriptorid' => $data['objectid'],
			'value' => $value
			),$value);
		local\gamification\event_handler::trigger($event,true);
	}

	
}


?>
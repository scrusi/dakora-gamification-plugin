<?php
namespace local\gamification;

class event {
	public $action;
	public $user_id;
	public $value;
	public $parameter;

	function __construct($action,$parameter=false,$value=false) {
		if ($action) $this->action=$action;
		if ($parameter) $this->parameter=$parameter;
		unset($this->parameter['wstoken']);
		global $USER;
		$this->user_id = $USER->id;  // always the user that has logged in. 
		//if ($course_id) $this->course_id=$course_id;
		$this->course_id = $this->get_p('courseid');
		if ($action == 'example_submitted' || $action =='example_added_to_calendar') $this->parameter['descriptorid'] = $this->get_p('exampleid');
		$this->descriptor_id= $this->get_p('descriptorid');
		$this->created = time();
		//if ($descriptor_id) $this->descriptor_id=$descriptor_id;
		if ($value) $this->value=$value;
		if (method_exists($this, 'process_'.$action)) {
			call_user_func(array($this,'process_'.$action));
		}
		
	}

	/**
	* Builds a record of the event for insertion into the log table
	* @return 	\stdClass	Log record of an event
	*
	**/
	function get_record() {
		$record = new \stdClass();
		$record->id = false;
		$record->action = $this->action;
		$record->user_id = $this->user_id;
		$record->course_id = $this->get_p('courseid');
		$record->descriptor_id = $this->get_p('descriptorid');
		$record->related_user_id = $this->get_p('relateduserid');
		$record->value = $this->value;
		$record->created = $this->created;
		$record->parameter = json_encode($this->parameter);
		return $record;
	}


	/**
	* accesses event values. Mostly used to extract values from the parameter array, but also accesses some fixed attributes for compatibility 
	* @param 	string 	$pname The name of the parameter fo fetch
	* @return 	mixed 	the value of the requested parameter or false if not found.
	**/

	function get_p($pname) {
		switch ($pname) {
			case 'user_id': return $this->user_id;
							break;
			case 'action': 	return $this->action;
							break;	
			case 'value': 	return $this->value;
							break;			
		}
		if (array_key_exists($pname, $this->parameter)) return $this->parameter[$pname];
		else return false;
	}

	/**
	* functions called in the constructor if certain actions are taken. 
	* Naming convention process_ . name of the action
	**/

	function process_example_added_to_calendar() {
		global $DB;

		$schedule = $DB->get_record('block_exacompschedule',array('id'=>$this->parameter['scheduleid']));
		if ($schedule) {
			$this->parameter['courseid'] = $schedule->courseid;
		} else {
			trigger_error('Moodle Gamification: No Schedule Found');
		}

	} 

	// End processing functions
}
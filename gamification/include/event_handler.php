<?php
namespace local\gamification;
require_once($CFG->libdir . "/badgeslib.php");
require_once("badger.php");
require_once("responses.php");
/**
* Constants for the ids of counters in local_gamification_counters
* Necessary because Moodle doesn't like text comparisons in the database.
* Must be a positive integer.
* DO NOT change IDs - just add new ones instead.
**/
const COUNT_RATE_SELF = 1;
const COUNT_ADD_EXAMPLE = 2;
const COUNT_CATEGORY_COMPLETE = 3;
const COUNT_CORRECT_RATING = 4;
const COUNT_SUBMIT_EXAMPLE = 5;


/**
* constants for the ids of badges.
* These are internal ids - database ids will differ!
* Must be a positive integer.
* DO NOT change IDs - just add new ones instead.
**/

//const BADGE_RATE_SELF = 1;
//const BADGE_RATE_SELF_BRONZE = 2;
const BADGE_CORRECT_GRADE = 3;
const BADGE_RATE_SELF_BRONZE = 5;
const BADGE_RATE_SELF_SILVER = 6;
const BADGE_RATE_SELF_GOLD = 7;
const BADGE_RATE_SELF_DIAMOND = 8;
const BADGE_CORRECT_GRADE_BRONZE = 9;
const BADGE_CORRECT_GRADE_SILVER = 10;
const BADGE_CORRECT_GRADE_GOLD = 11;
const BADGE_PLANNER_BRONZE = 12;
const BADGE_CATEGORY_COMPLETE_BRONZE = 13;
const BADGE_CATEGORY_COMPLETE_SILVER = 14;
const BADGE_CATEGORY_COMPLETE_GOLD = 15;
const BADGE_PLANNER_SILVER = 16;
const BADGE_PLANNER_GOLD = 17;
const BADGE_PLANNER_DIAMOND = 18;

class event_handler {

	/**
	* array of functions that listen to gamification events.
	* Actions without a listener will not be logged either.
	* key is the name of the event, value is an array of function names.
	**/
	private static $listeners = array(
		'grade_set_as_student' => array ('count_rate_self'),
		'example_added_to_calendar' => array('count_add_example'),
		'grade_set_as_teacher' => array('check_correct_self_rating','check_all_siblings'),
		'example_submitted'	=> array('count_submit_example') 
	);

	/**
	* functions that watch for certain counter levels to be reached
	* outer key is the id of the counter to watch
	* inner key is the number to look for
	* values in the array are functions to call
	* TODO: Remake
	**/
	private static $watchers = array(
		COUNT_RATE_SELF => array(
			1 => array('badge_rate_self_bronze'),
			10 => array('badge_rate_self_silver'),
			30 => array('badge_rate_self_gold'),
			100 => array('badge_rate_self_diamond')
		),
		COUNT_CORRECT_RATING => array(
			1 => array('badge_correct_rating_bronze'),
			10 => array('badge_correct_rating_silver'),
			30 => array('badge_correct_rating_gold')
		),
		COUNT_CATEGORY_COMPLETE => array(
			1 => array('badge_category_complete_bronze'),
			5 => array('badge_category_complete_silver'),
			20 => array('badge_category_complete_gold')
		),
		COUNT_ADD_EXAMPLE => array(
			1 => array('badge_planner_bronze'),
			10 => array('badge_planner_silver'),
			30 => array('badge_planner_gold'),
			100 => array('badge_planner_diamond'),
		)
	);

	/**
	* Functions for use in triggers.
	* Should probably be moved to a separate file.	
	**/

	

	private static function count_rate_self($event) {
		global $USER, $DB;
		$sql = 'SELECT count(*) as cnt FROM mdl_local_gamification_log WHERE  user_id = ? AND action = ? AND descriptor_id = ? AND value >=?';
		if ($DB->count_records_sql($sql,array($USER->id,'grade_set_as_student',$event->descriptor_id,$event->value)) > 1) return false;
		$responses = new responses();
		$responses->add( self::update_counter(COUNT_RATE_SELF,$USER->id,'inc'));
		$responses->add(self::issue_points(50,$USER->id));
		return $responses;
		
	}




/*
	private static function check_for_parent($event) {
		global $DB;
		$descriptor = $DB->get_record('block_exacompdescriptors',array('id'=>$event->descriptorid));
		// This is a child competence
		if ($descriptor && $descriptor->parentid > 0) {
			$responses->add( self::update_counter(COUNT_RATE_SELF_CHILD,$USER->id,'inc'));
		} else {

		}
		
	}
*/

	private static function check_all_siblings($event) {
		global $DB;
		$descriptor = $DB->get_record('block_exacompdescriptors',array('id'=>$event->descriptor_id));
		// check if this category has been completed before
		$sql = "SELECT * FROM mdl_local_gamification_log WHERE ".$DB->sql_compare_text('action')." LIKE ? AND related_user_id = ? AND descriptor_id = ?";
		if ($DB->record_exists_sql($sql,array('category_completed',$event->get_p('relateduserid'),$descriptor->parentid))) return false;
		
		// This is a child competence
		if ($descriptor && $descriptor->parentid > 0) {
			$sql = 'SELECT count(*) as cnt FROM mdl_block_exacompdescriptors as a RIGHT  JOIN mdl_block_exacompcompuser as b ON a.id = b.compid  WHERE a.parentid = ? AND b.role =1 AND b.userid =?';
			$competencies_gained = $DB->count_records_sql($sql,array($descriptor->parentid,$event->get_p('relateduserid')));
			$sql = 'SELECT count(*) as cnt FROM `mdl_block_exacompdescriptors` WHERE parentid = ?';
			$number_of_siblings = $DB->count_records_sql($sql,array($descriptor->parentid));
			// All competencies in this category have been gained
			if ($competencies_gained > 0 && $competencies_gained == $number_of_siblings) {
				$responses = new responses();
				$responses->add(self::update_counter(COUNT_CATEGORY_COMPLETE,$event->get_p('relateduserid'),'inc'));
				$responses->add(self::issue_points(500,$event->get_p('relateduserid')));
				$e = new event('category_completed',array('relateduserid'=>$event->get_p('relateduserid'),'descriptorid'=>$descriptor->parentid));
				self::log_event($e);
				return $responses;
			}
		}
		return false;
	}

	private static function count_add_example($event) {
		global $USER, $DB;
		$sql = 'SELECT count(*) as cnt FROM mdl_local_gamification_log WHERE  user_id = ? AND action = ? AND descriptor_id = ?';
		if ($DB->count_records_sql($sql,array($USER->id,'example_added_to_calendar',$event->descriptor_id)) > 1) return false;
		// does something when the event is triggered
		$responses = new responses();
		$responses->add(self::update_counter(COUNT_ADD_EXAMPLE,$USER->id,'inc'));
		$responses->add(self::issue_points(50,$USER->id));
		return $responses;
	}

	private static function count_submit_example($event) {
		global $USER, $DB;
		$sql = 'SELECT count(*) as cnt FROM mdl_local_gamification_log WHERE  user_id = ? AND action = ? AND descriptor_id = ?';
		if ($DB->count_records_sql($sql,array($USER->id,'example_submitted',$event->descriptor_id)) > 1) return false;
		// does something when the event is triggered
		$responses = new responses();
		$responses->add(self::update_counter(COUNT_SUBMIT_EXAMPLE,$USER->id,'inc'));
		$responses->add(self::issue_points(100,$USER->id));
		return $responses;
		
	}

	private static function check_correct_self_rating($event) {
		//resume normal
		global $DB;
		$self_grade = $DB->get_record('block_exacompcompuser',array('compid'=>$event->get_p('descriptorid'), 'reviewerid' => $event->get_p('relateduserid'), 'userid' => $event->get_p('relateduserid')));
		if ($self_grade && $self_grade->value == $event->value) {
			$responses = new responses();
			$responses->add(self::update_counter(COUNT_CORRECT_RATING,$event->get_p('relateduserid'),'inc'));
			$responses->add(self::issue_points(100,$event->get_p('relateduserid')));
			return $responses;
		}

	}
	// End trigger functions

	/**
	* Functions for use in watchers.
	* Should probably be moved to a separate file.	
	**/
	private static function badge_rate_self_bronze($user_id) {
		return self::issue_badge(BADGE_RATE_SELF_BRONZE, $user_id);
	}
	private static function badge_rate_self_silver($user_id) {
		return self::issue_badge(BADGE_RATE_SELF_SILVER, $user_id);
	}
	private static function badge_rate_self_gold($user_id) {
		return self::issue_badge(BADGE_RATE_SELF_GOLD, $user_id);
	}
	private static function badge_rate_self_diamond($user_id) {
		return self::issue_badge(BADGE_RATE_SELF_DIAMOND, $user_id);
	}
	private static function badge_correct_rating_bronze($user_id) {
		return self::issue_badge(BADGE_CORRECT_GRADE_BRONZE,$user_id);
	}
	private static function badge_correct_rating_silver($user_id) {
		return self::issue_badge(BADGE_CORRECT_GRADE_SILVER,$user_id);
	}
	private static function badge_correct_rating_gold($user_id) {
		return self::issue_badge(BADGE_CORRECT_GRADE_GOLD,$user_id);
	}
	private static function badge_planner_bronze($user_id) {
		return self::issue_badge(BADGE_PLANNER_BRONZE,$user_id);
	}
	private static function badge_planner_silver($user_id) {
		return self::issue_badge(BADGE_PLANNER_SILVER,$user_id);
	}
	private static function badge_planner_gold($user_id) {
		return self::issue_badge(BADGE_PLANNER_GOLD,$user_id);
	}
	private static function badge_planner_diamond($user_id) {
		return self::issue_badge(BADGE_PLANNER_DIAMOND,$user_id);
	}
	private static function badge_category_complete_bronze($user_id) {
		return self::issue_badge(BADGE_CATEGORY_COMPLETE_BRONZE,$user_id);
	}
	private static function badge_category_complete_silver($user_id) {
		return self::issue_badge(BADGE_CATEGORY_COMPLETE_SILVER,$user_id);
	}
	private static function badge_category_complete_gold($user_id) {
		return self::issue_badge(BADGE_CATEGORY_COMPLETE_GOLD,$user_id);
	}
	
 

	// End watcher functions


	/**
	* Get a badge object from moodle.
	* @param 	internal_id			An internal badge_id, one of the BADGE_ constants defined above.
	* @return 	mixed 				returns a badge on success or false
	**/

	private static function get_badge($internal_id) {
		global $DB;
		$badges = \badges_get_badges(1);
		$r = $DB->get_record('local_gamification_badgemap',array('internal_id' => $internal_id));
		if ($r && array_key_exists($r->badge_id, $badges)) return $badges[$r->badge_id];
		else return false;
	}

	/**
	* Issues a badge to the current user
	* @param 	internal_id			An internal badge_id, one of the BADGE_ constants defined above.
	* @return 	response			returns a badge response on success, false on failure
	**/
	private static function issue_badge($internal_id,$user_id) {
		if ($b = self::get_badge($internal_id)) {
			if (!$b->is_issued($user_id)) {
				$b->issue($user_id);
				return new response('badge',\local_gamification_external::resolve_badge_images($b));
			} else {
				return false; //new response('badge',\local_gamification_external::resolve_badge_images($b)); //TODO: change to false;
			} 
		} else {
			return false;
		}
	}


	/**
	* Issues points to a user
	* @param	int 	$points 	The amount of points to be issued. Can be negative!
	* @param	int 	$user_id 	The id of the user that the points will be issued to
	* @return 	response 			Returns a point response
	**/ 	 
 	
 	private static function issue_points($points, $user_id) {
 		global $DB;
 				// TODO needs to go to install!
			//badger::import_badges();
 		$points_before = 0;
 		if ($DB->record_exists('local_gamification_points',array( // user already has points
				'userid' => $user_id
 			))) 
 		{
 			$record = $DB->get_record('local_gamification_points',array(
				'userid' => $user_id
 			));
 			$points_before = $record->points;
 			$record->points += $points;

 			$DB->update_record('local_gamification_points',$record);

 		} else {	// first time the user receives points
 			$record = new \stdClass();
 			$record->userid = $user_id;
 			$record->points = $points;
			$DB->insert_record('local_gamification_points',$record); 			
 		}
 		$responses = new responses();
		$responses->add(new response('points',$points));

 		if (max(1,floor(0.2*sqrt($record->points))) > max(1,floor(0.2*sqrt($points_before)))) {
 			$responses->add(new response('level',max(1,floor(0.2*sqrt($record->points)))));
 		}
 		
 		return $responses;
 		
	}
	
	/**
	* Triggers all listeners for an event and calls log_event
	* @param 	local\gamification\event 	$event 		a local\gamification\event object
	* @param 	boolean						$delayed	if true, responses are saved as messages to the database
	* @return 	boolean 								returns true on success
	* @throws	moodle_exception 						unknownaction
	**/
	public static function trigger($event,$delayed = false) {
		if (!array_key_exists($event->action,self::$listeners)) throw new \moodle_exception('unknownaction');
		// Commit event to DB
		self::log_event($event);
		// Call listeners
		$responses = new responses();
		$listeners =  self::$listeners[$event->action];
		if ($listeners) {
			foreach ($listeners as $l) {
				$responses->add(call_user_func('self::'.$l,$event));
			}
		}

		if ($delayed && count($responses->get())) {
			global $DB,$USER;
			foreach ($responses->get() as $r) {
				$message = new \stdClass;
				$message->user_id = $event->get_p('relateduserid')?$event->get_p('relateduserid'):$USER->id;
				$message->created = time();
				$message->message = json_encode($r);
				$DB->insert_record('local_gamification_messages',$message);
			}
		}

		return $responses->get();
	}

	/**
	* Writes each event into the log_gamification_log table
	* @param event A local\gamification\event object
	* @return void
	**/
	private static function log_event($event) {
		global $DB;
		$DB->insert_record('local_gamification_log',$event->get_record(),false);
	}

	/**
	* Updates or sets a counter in the database.
	* @param counter_id int 	Should be one of the COUNT_ constants. Defines what is being counted.
	* @param user_id	int 	The user that is counted for.
	* @param action 	string 	The action to be taken with the counter. One of inc, dec, or set. 
	* @param value 		int 	The amount to increase or decrease by or set to. If not set, increase or decreases by 1, or sets to 0.
	* Note: Actions other than inc (1) currently may break badges that trigger on certain counts.
	**/
	private static function update_counter($counter_id, $user_id, $action, $value = false) {
		global $DB;	
		$db_value = 0;
		$record = $DB->get_record('local_gamification_counters',array('counter_id' => $counter_id, 'user_id' => $user_id ));
		if ($record && $record->counter_id) $db_value = $record->counter_value;
		switch ($action) {
			case 'inc': if ($value) $db_value += $value;
						else $db_value++;
						break;
			case 'dec': if ($value) $dbvalue -= $value;
						else $db_value--;
						break;
			case 'set': if (is_numeric($value)) $db_value = $value;
						else $db_value = 0;
						break;
		}
		if (is_numeric($counter_id) && is_numeric($db_value)) {
			$db_value = ($db_value>0)?$db_value:0;
			if ($record && $record->counter_id) { 	// Counter already exists
				$record->counter_value = $db_value;
				$DB->update_record('local_gamification_counters',$record,false);
			}
			else  {
				$record = new \stdClass();
				$record->id = false;
				$record->counter_id = $counter_id;
				$record->user_id = $user_id;
				$record->counter_value = $db_value;
				$DB->insert_record('local_gamification_counters',$record,false);
			}
		}
		$responses = new responses();
		// Call all watchers that are looking for this counter reaching this value
		if (array_key_exists($counter_id,self::$watchers)) {
			
			foreach (self::$watchers[$counter_id] as $key => $w) {
				
				if ($db_value >= $key) {
					foreach ($w as $fn)  {
						$responses->add(call_user_func('self::'.$fn,$user_id));
					}
				}
			}
		}
		return $responses;
		
	}
	
	private static function concat_responses($response,$responses=array()) {
		if (is_array($response)) {
			foreach ($response as $r) {
				$responses = self::concat_responses($r,$responses);
			}
		}
		elseif ($response && $response->type) {
			$responses[] = $response;	
		}
		return $responses;
	}	
}
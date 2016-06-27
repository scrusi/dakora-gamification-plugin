<?php
	defined('MOODLE_INTERNAL') || die();

	$observers = array(
	    array(
	        'eventname'   => '\block_exacomp\event\competence_assigned',
	        'callback'    => 'local_gamification_observer::grade_set_as_teacher',
	    ) 
);

?>
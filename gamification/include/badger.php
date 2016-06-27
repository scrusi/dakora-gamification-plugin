<?php
namespace local\gamification;



// Imports badges and fills the badgemap table
class badger {

	/**
	* array of badges to import
	**/
	private static $badges = array(
		5 => array (
			'name' => 'Ich kann das! - Bronze',
			'description' => 'Du hast eine deiner Kompetenzen eingeschätzt.',
			'image' => 'tutorial_completion_1.png' // in local/gamification/files/badgeimages
		),
		6 => array (
			'name' => 'Ich kann das! - Silber',
			'description' => 'Du hast 10 deiner Kompetenzen eingeschätzt.',
			'image' => 'tutorial_completion_2.png' // in local/gamification/files/badgeimages
		),
		7 => array (
			'name' => 'Ich kann das! - Gold',
			'description' => 'Du hast 30 deiner Kompetenzen eingeschätzt.',
			'image' => 'tutorial_completion_3.png' // in local/gamification/files/badgeimages
		),
		8 => array (
			'name' => 'Ich kann das! - Diamant',
			'description' => 'Du hast 100 deiner Kompetenzen eingeschätzt.',
			'image' => 'tutorial_completion_4.png' // in local/gamification/files/badgeimages
		),
		9 => array (
			'name' => 'Draufhaber - Bronze',
			'description' => 'Eine deiner Kompetenzen wurde bestätigt.',
			'image' => 'forum_participation_level_1.png' // in local/gamification/files/badgeimages
		),
		10 => array (
			'name' => 'Draufhaber - Silber',
			'description' => '10 deiner Kompetenzen wurden bestätigt.',
			'image' => 'forum_participation_level_2.png' // in local/gamification/files/badgeimages
		),
		11 => array (
			'name' => 'Draufhaber - Gold',
			'description' => '30 deiner Kompetenzen wurden bestätigt.',
			'image' => 'forum_participation_level_3.png' // in local/gamification/files/badgeimages
		),
		12 => array (
			'name' => 'Planer - Bronze',
			'description' => 'Du hast Zeit für eine Aufgabe eingeplant.',
			'image' => 'time-management_level_1.png' // in local/gamification/files/badgeimages
		),
		13 => array (
			'name' => 'Ein Auge für\'s Detail - Bronze',
			'description' => 'Du hast alle Teilkompetenzen in einem Lernfortschritt erreicht.',
			'image' => 'inspections_level_1.png' // in local/gamification/files/badgeimages
		),
		14 => array (
			'name' => 'Ein Auge für\'s Detail - Silber',
			'description' => 'Du hast alle Teilkompetenzen in fünf Lernfortschritten erreicht.',
			'image' => 'inspections_level_2.png' // in local/gamification/files/badgeimages
		),
		15 => array (
			'name' => 'Ein Auge für\'s Detail - Gold',
			'description' => 'Du hast alle Teilkompetenzen in 20 Lernfortschritten erreicht.',
			'image' => 'inspections_level_3.png' // in local/gamification/files/badgeimages
		),
		16 => array (
			'name' => 'Planer - Silber',
			'description' => 'Du hast Zeit für 10 Aufgaben eingeplant.',
			'image' => 'time-management_level_2.png' // in local/gamification/files/badgeimages
		),
		17 => array (
			'name' => 'Planer - Gold',
			'description' => 'Du hast Zeit für 30 Aufgaben eingeplant.',
			'image' => 'time-management_level_3.png' // in local/gamification/files/badgeimages
		),
		18 => array (
			'name' => 'Planer - Diamant',
			'description' => 'Du hast Zeit für 100 Aufgaben eingeplant.',
			'image' => 'time-management_level_4.png' // in local/gamification/files/badgeimages
		)
		
		

	);	

	public static function import_badges() {
		global $DB;
		foreach (self::$badges as $internal_id => $b) {
			if ($DB->get_record('local_gamification_badgemap',array('internal_id' => $internal_id))) continue; // Don't add multiples. 
			$r = new \stdClass();
			$r->name = $b['name'];
			$r->description = $b['description'];
			$r->timecreated = time();
			$r->timemodified = time();
			$r->usercreated = 2;
			$r->usermodified = 2;
			$r->issuername =  "Moodle Gamification Plugin for DAKORA";
			$r->issuerurl = "URL HERE";							// TODO: fix
			$r->issuercontact = "admin@nomail.belwue.de";
			$r->expiredate = null;
			$r->expireperiod = null;
			$r->type = 1;
			$r->courseid = null;
			$r->message = '<p>Ihnen wurde die Auszeichnung "%badgename%" verliehen.</p>
<p>Weitere Informationen zu dieser Auszeichnung finden Sie unter %badgelink%.</p>';
			$r->messagesubject = 'Herzlichen Glückwunsch! Sie haben eine Auszeichnung erhalten!';
			$r->attachment = 1;
			$r->notification = 0;
			$r->status = 0;
			$r->nextcron = null;

			$id = $DB->insert_record('badge',$r, true);
			if ($id) { // Update Badgemap
				$r = new \stdClass();
				$r->internal_id = $internal_id;
				$r->badge_id = $id;
				$DB->insert_record('local_gamification_badgemap',$r, false);

				$badge = new \badge($id);
				global $CFG;
			    require_once($CFG->libdir. '/gdlib.php');
			    // Add the image to moodle
			    if (!empty($CFG->gdversion)) {
			        process_new_icon($badge->get_context(), 'badges', 'badgeimage', $badge->id, dirname(__FILE__).'/../files/badgeimages/'.$b['image'], true);
				}
			}
		}

	}


}





/*

id
name
description
timecreated
timemodified
usercreated
usermodified
issuername
issuerurl
issuercontact
expiredate
expireperiod
type
courseid
message
messagesubject
attachment
notification
status
nextcron
*/
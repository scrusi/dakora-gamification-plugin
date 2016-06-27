<?php
defined('MOODLE_INTERNAL') || die;
function xmldb_local_gamification_install() {
	require_once(dirname(dirname(__FILE__))."/include/badger.php");
	local\gamification\badger::import_badges();
	return true;
}


?>
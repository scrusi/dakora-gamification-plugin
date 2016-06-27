<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwstemplate
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
                'local_gamification_action' => array(
                'classname'   => 'local_gamification_external',
                'methodname'  => 'action',
                'classpath'   => 'local/gamification/externallib.php',
                'description' => 'Register a user action from Dakora and store it in the database. Returns OK or an error code.',
                'type'        => 'write',
        ),
                'local_gamification_get_badges' => array(
                'classname'   => 'local_gamification_external',
                'methodname'  => 'get_badges',
                'classpath'   => 'local/gamification/externallib.php',
                'description' => 'Fetches the user\'s badges from moodle.',
                'type'        => 'read',
        ),
                'local_gamification_get_competencies' => array(
                'classname'   => 'local_gamification_external',
                'methodname'  => 'get_competencies',
                'classpath'   => 'local/gamification/externallib.php',
                'description' => 'Fetches the user\'s competencies from moodle.',
                'type'        => 'read',
        ),
                'local_gamification_get_competency_values' => array(
                'classname'   => 'local_gamification_external',
                'methodname'  => 'get_competency_values',
                'classpath'   => 'local/gamification/externallib.php',
                'description' => 'Fetches the user\'s assigned competencies from moodle.',
                'type'        => 'read',
        ),
                'local_gamification_get_points' => array(
                'classname'   => 'local_gamification_external',
                'methodname'  => 'get_points',
                'classpath'   => 'local/gamification/externallib.php',
                'description' => 'Fetches the user\'s points from moodle.',
                'type'        => 'read',
        ),
                'local_gamification_get_gamification_mode' => array(
                'classname'   => 'local_gamification_external',
                'methodname'  => 'get_gamification_mode',
                'classpath'   => 'local/gamification/externallib.php',
                'description' => 'Retrieves the mode setting for the gamification plugin.',
                'type'        => 'read',
        ),
                'local_gamification_get_messages' => array(
                'classname'   => 'local_gamification_external',
                'methodname'  => 'get_messages',
                'classpath'   => 'local/gamification/externallib.php',
                'description' => 'Retrieves messages waiting for the user.',
                'type'        => 'read',
        ),
                'local_gamification_messages_seen' => array(
                'classname'   => 'local_gamification_external',
                'methodname'  => 'messages_seen',
                'classpath'   => 'local/gamification/externallib.php',
                'description' => 'Marks messages as seen.',
                'type'        => 'write',
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Gamification' => array(
                'functions' => array ('local_gamification_action','local_gamification_get_badges', 'local_gamification_get_competencies', 'local_gamification_get_competency_values', 'local_gamification_get_points','local_gamification_get_gamification_mode','local_gamification_get_messages','local_gamification_messages_seen' ),
                'restrictedusers' => 0,
                'enabled'=>1,
                'shortname' => 'gamification'
        )
);

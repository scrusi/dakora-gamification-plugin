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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//use local\gamification;
require_once($CFG->libdir . "/externallib.php");
require_once('include/event.php');
require_once('include/event_handler.php');

class local_gamification_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function action_parameters() {
        return new external_function_parameters(
                array(
                    'action' => new external_value(PARAM_TEXT, 'The action to be registered.', VALUE_REQUIRED),
                    'parameter' => new external_value(PARAM_TEXT, 'A json string of all parameters ', VALUE_DEFAULT, false),
                    'value' => new external_value(PARAM_TEXT, 'A value (text) associated with the action. ', VALUE_DEFAULT, false)          
                    )
        );
    }

    /**
     * Returns success or failure
     * @return string OK or ERROR
     */

    public static function action($action,$parameter,$value) {
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::action_parameters(),
                array('action' => $action, 'parameter' => $parameter, 'value' => $value)); 
        $parameter = json_decode(urldecode($parameter),true);
        $event = new local\gamification\event($action,$parameter,$value);
        return json_encode(local\gamification\event_handler::trigger($event));
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function action_returns() {
        return new external_value(PARAM_RAW, 'A JSON response.');
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function check_update_parameters() {
        return new external_function_parameters(
                array()
        );
    }

    /**
     * Returns success or failure
     * @return string OK or ERROR
     */

    public static function check_update() {
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::check_update_parameters(),
                array()); 
        
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function check_update_returns() {
        return new external_value(PARAM_TEXT, 'OK or an error message.');
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_badges_parameters() {
        return new external_function_parameters(
                array(
                    'page' => new external_value(PARAM_INT, 'The page to be loaded.', VALUE_DEFAULT, 0),
                    'perpage' => new external_value(PARAM_INT, 'The number of badges to be loaded ', VALUE_DEFAULT, 10)       
                    )
        );
    }

    /**
     * Returns success or failure
     * @return string OK or ERROR
     */

    public static function get_badges($page, $perpage) {
        global $USER;
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_badges_parameters(),
                array('page' => $page, 'perpage' => $perpage));  
        global $DB;
        $numbadges = $DB->count_records('badge_issued',array(
            'userid' => $USER->id
            ));
        $badges = badges_get_user_badges($USER->id,0,$page,$perpage);
        $badges = array_map('local_gamification_external::resolve_badge_images',$badges);
        $badges[] = $numbadges;
        return json_encode($badges);
        
    }

    public static function resolve_badge_images($b) {
        $imageurl = moodle_url::make_pluginfile_url(1, 'badges', 'badgeimage', $b->id, '/', 'f1', false);
        $b->imageurl = $imageurl->out();
        return $b;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_badges_returns() {
        return new external_value(PARAM_RAW, 'JSON response with user badges.');
    }


     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_competencies_parameters() {
       return new external_function_parameters(
                array(
                    )
        );
    }

    /**
     * Returns success or failure
     * @return string OK or ERROR
     */

    public static function get_competencies() {

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::check_update_parameters(),
                array()); 
        global $USER, $CFG;

        //echo $CFG->dirroot . '/blocks/exacomp/';
        require_once $CFG->dirroot . '/blocks/exacomp/lib/lib.php';
        $courses = block_exacomp_get_exacomp_courses($USER);
       // print_r($courses);
        $course_info = array();
        foreach ($courses as $k => $v) {
            $r = block_exacomp_get_user_competencies_by_course($USER,$v->id);
            $course_info[$k] = block_exacomp_get_competence_tree($v->id);
        }
        return json_encode($course_info);
        

        // LFS NameN: print_r(block_exacomp_get_niveaus_for_subject($v->id));
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_competencies_returns() {
        return new external_value(PARAM_RAW, 'A JSON with the users competencies');
    }

     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_competency_values_parameters() {
       return new external_function_parameters(
                array(
                    )
        );
    }

    /**
     * Returns a json encoded array of achieved competencies
     * @return string OK or ERROR
     */

    public static function get_competency_values() {

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_competency_values_parameters(),
                array()); 
        global $USER, $CFG;

        //echo $CFG->dirroot . '/blocks/exacomp/';
        require_once $CFG->dirroot . '/blocks/exacomp/lib/lib.php';
        $courses = block_exacomp_get_exacomp_courses($USER);
       // print_r($courses);
        $course_info = array();
        foreach ($courses as $k => $v) {
            $r = block_exacomp_get_user_competencies_by_course($USER,$v->id);
            $course_info[$k] = $r->competencies;
        }
        return json_encode($course_info);
        

        // LFS NameN: print_r(block_exacomp_get_niveaus_for_subject($v->id));
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_competency_values_returns() {
        return new external_value(PARAM_RAW, 'A JSON with the user\'s assigned competencies ');
    }

     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_points_parameters() {
       return new external_function_parameters(
                array(
                    )
        );
    }

    /**
     * Returns success or failure
     * @return string OK or ERROR
     */

    public static function get_points() {

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_points_parameters(),
                array()); 
        global $DB, $USER;
        $record = $DB->get_record('local_gamification_points',array(
                'userid' => $USER->id
            ));
        $r = array();
        if ($record) {
            $points = $record->points;
            $level = max(1,floor(0.2*sqrt($points)));
            $needed = self::points_for_level($level+1) - self::points_for_level($level);
            $already = $points - self::points_for_level($level);
            
            $r['points'] = $points;
            $r['level'] = $level;
            $r['needed'] = $needed;
            $r['already'] = $already;
            

        } else {
            $r['points'] = 0;
            $r['level'] = 1;
            $r['needed'] = self::points_for_level(2)-self::points_for_level(1);
            $r['already'] = 0;
        }
        return json_encode($r);
        

        // LFS NameN: print_r(block_exacomp_get_niveaus_for_subject($v->id));
    }

    private static function points_for_level($level) {
        if ($level == 1) return 0;
        else return pow(($level)/0.2,2);
    }

    
    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_points_returns() {
        return new external_value(PARAM_RAW, 'A JSON with the user\'s assigned competencies ');
    }
    

     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_gamification_mode_parameters() {
       return new external_function_parameters(
                array(
                    )
        );
    }

    /**
     * Returns an integer representing the gamification setting on the server
     * @return int 
     */

    public static function get_gamification_mode() {

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_gamification_mode_parameters(),
                array()); 
        global $CFG;     
        return $CFG->gamificationmode?$CFG->gamificationmode:0;
        

        // LFS NameN: print_r(block_exacomp_get_niveaus_for_subject($v->id));
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_gamification_mode_returns() {
        return new external_value(PARAM_INT, 'An integer representing the gamification setting on the server.');
    }

     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_messages_parameters() {
       return new external_function_parameters(
                array(
                    )
        );
    }

    /**
     * Returns a JSON string containing an array of one or more messages
     * @return string 
     */

    public static function get_messages() {

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_messages_parameters(),
                array()); 
        global $DB,$USER;     
        $messages = $DB->get_records('local_gamification_messages',array(
                'user_id'   => $USER->id,
                'seen'      => null 
            ));
        $responses = new local\gamification\responses();
        if ($messages) {
            foreach($messages as $m) {
                $r =json_decode($m->message);
                $responses->add(new local\gamification\response($r->type,$r->content,$m->id));
            }
        }
        return json_encode($responses->get());
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_messages_returns() {
        return new external_value(PARAM_RAW, 'A JSON string containing an array of one or more messages.');
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function messages_seen_parameters() {
       return new external_function_parameters(
                array(
                    'message_ids' => new external_value(PARAM_RAW, 'A JSON encoded array of ids of seen messages.', VALUE_REQUIRED),
                )
        );
    }

    /**
     * Returns OK or false
     * @return string 
     */

    public static function messages_seen($message_ids) {
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::messages_seen_parameters(),
                array('message_ids' => $message_ids)); 
        $ids = json_decode($message_ids);
        if (is_array($ids) && count($ids)) {
            global $DB,$USER;  
            foreach($ids as $id) {
                $record = $DB->get_record('local_gamification_messages',array(
                    'id'        => $id,
                    'user_id'   => $USER->id,
                    'seen'      => null 
                ));
                if ($record) {
                    $record->seen = time();
                    $DB->update_record('local_gamification_messages',$record);
                }
            }   
            return 'OK';
        }
        return false;
        

        // LFS NameN: print_r(block_exacomp_get_niveaus_for_subject($v->id));
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function messages_seen_returns() {
        return new external_value(PARAM_TEXT, 'A JSON string containing an array of one or more messages.');
    }
    
}


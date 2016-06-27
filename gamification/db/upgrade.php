<?php
 
function xmldb_local_gamification_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();
 
    $result = TRUE;
 
 	if ($oldversion < 2016020203) {

        // Define table local_gamification_counters to be created.
        $table = new xmldb_table('local_gamification_counters');

        // Adding fields to table local_gamification_counters.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('counter', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_gamification_counters.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_gamification_counters.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Gamification savepoint reached.
        upgrade_plugin_savepoint(true, 2016020203, 'local', 'gamification');
    }

    if ($oldversion < 2016020204) {

        // Rename field counter_name on table local_gamification_counters to NEWNAMEGOESHERE.
        $table = new xmldb_table('local_gamification_counters');
        $field = new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'user_id');
        $field2 = new xmldb_field('counter', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'user_id');
        // Launch rename field counter_name.
        $dbman->rename_field($table, $field, 'counter_name');
        $dbman->rename_field($table, $field2, 'counter_value');	
        // Gamification savepoint reached.
        upgrade_plugin_savepoint(true, 2016020204, 'local', 'gamification');
    }

    if ($oldversion < 2016020205) {

        // Define field counter_name to be dropped from local_gamification_counters.
        $table = new xmldb_table('local_gamification_counters');
        $field = new xmldb_field('counter_name');

        // Conditionally launch drop field counter_name.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field counter_id to be added to local_gamification_counters.
        $field2 = new xmldb_field('counter_id', XMLDB_TYPE_INTEGER, '11', null, null, null, null, 'counter_value');

        // Conditionally launch add field counter_id.
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Gamification savepoint reached.
        upgrade_plugin_savepoint(true, 2016020205, 'local', 'gamification');
    }

    if ($oldversion < 2016020802) {

        // Define field parameter to be added to local_gamification_log.
        $table = new xmldb_table('local_gamification_log');
        $field = new xmldb_field('parameter', XMLDB_TYPE_TEXT, null, null, null, null, null, 'value');

        // Conditionally launch add field parameter.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gamification savepoint reached.
        upgrade_plugin_savepoint(true, 2016020802, 'local', 'gamification');
    }

    if ($oldversion < 2016021101) {

        // Define table local_gamification_badgemap to be created.
        $table = new xmldb_table('local_gamification_badgemap');

        // Adding fields to table local_gamification_badgemap.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('internal_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('badge_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_gamification_badgemap.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_gamification_badgemap.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Gamification savepoint reached.
        upgrade_plugin_savepoint(true, 2016021101, 'local', 'gamification');
    }

    if ($oldversion < 2016022501) {

        // Define table local_gamification_points to be created.
        $table = new xmldb_table('local_gamification_points');

        // Adding fields to table local_gamification_points.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('points', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_gamification_points.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_gamification_points.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Gamification savepoint reached.
        upgrade_plugin_savepoint(true, 2016022501, 'local', 'gamification');
    }

    if ($oldversion < 2016032301) {

        // Define table local_gamification_messages to be created.
        $table = new xmldb_table('local_gamification_messages');

        // Adding fields to table local_gamification_messages.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('message', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('seen', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table local_gamification_messages.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_gamification_messages.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Gamification savepoint reached.
        upgrade_plugin_savepoint(true, 2016032301, 'local', 'gamification');
    }






 
    return $result;
}
?>
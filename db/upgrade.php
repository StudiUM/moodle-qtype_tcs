<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * Tcs question type upgrade code.
 *
 * @package    qtype
 * @subpackage tcs
 * @copyright  2020 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the tcs question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_tcs_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020071300) {

        // Labelfeedback.
        $table = new xmldb_table('qtype_tcs_options');
        $field = new xmldb_field('labelfeedback', XMLDB_TYPE_TEXT, null, null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Showfeedback.
        $field = new xmldb_field('showfeedback', XMLDB_TYPE_INTEGER, '1', null, null, null, '1');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Labelnewinformationeffect.
        $field = new xmldb_field('labelnewinformationeffect', XMLDB_TYPE_TEXT, null, null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Labelsituation.
        $field = new xmldb_field('labelsituation', XMLDB_TYPE_TEXT, null, null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Tcs savepoint reached.
        upgrade_plugin_savepoint(true, 2020071300, 'qtype', 'tcs');
    }

    return true;
}

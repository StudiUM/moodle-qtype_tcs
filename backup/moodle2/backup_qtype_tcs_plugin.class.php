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
 * Provides the information to backup Concordance questions.
 *
 * @package    qtype_tcs
 * @subpackage backup-moodle2
 * @category   backup
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Provides the information to backup Concordance questions.
 *
 * @package    qtype_tcs
 * @subpackage backup-moodle2
 * @category   backup
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qtype_tcs_plugin extends backup_qtype_plugin {

    /**
     * @var string The qtype name.
     */
    protected static $qtypename = 'tcs';

    /**
     * @var string The tcs table name.
     */
    protected static $tablename = 'qtype_tcs';

    /**
     * @var array The additional columns names.
     */
    protected static $addcolumnsnames = ['effecttext', 'effecttextformat', 'labeleffecttext'];

    /**
     * @var array The additional file area mapping names.
     */
    protected static $addfileareamapnames = ['effecttext' => 'question_created'];

    /**
     * Returns the qtype information to attach to question element
     */
    protected function define_question_plugin_structure() {

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, '../../qtype', static::$qtypename);

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        // This qtype uses standard question_answers, add them here
        // to the tree before any other information that will use them.
        $this->add_question_question_answers($pluginwrapper);

        $columnsnames = [
            'hypothisistext', 'hypothisistextformat', 'labelhypothisistext',
            'showquestiontext', 'shownumcorrect',
            'correctfeedback', 'correctfeedbackformat',
            'partiallycorrectfeedback', 'partiallycorrectfeedbackformat',
            'incorrectfeedback', 'incorrectfeedbackformat',
            'showfeedback', 'labelfeedback', 'labelnewinformationeffect', 'labelsituation', 'showoutsidefieldcompetence'];
        $columnsnames = array_unique(array_merge($columnsnames, static::$addcolumnsnames));
        // Now create the qtype own structures.
        $tcs = new backup_nested_element(static::$qtypename, array('id'), $columnsnames);

        // Now the own qtype tree.
        $pluginwrapper->add_child($tcs);

        // Set source to populate the data.
        $tcs->set_source_table(static::$tablename . '_options',
                array('questionid' => backup::VAR_PARENTID));

        // Don't need to annotate ids nor files.

        return $plugin;
    }

    /**
     * Returns one array with filearea => mappingname elements for the qtype
     *
     * Used by link get_components_and_fileareas to know about all the qtype
     * files to be processed both in backup and restore.
     */
    public static function get_qtype_fileareas() {
        $fileareamappingnames = ['hypothisistext' => 'question_created'];
        return $fileareamappingnames + static::$addfileareamapnames;
    }
}

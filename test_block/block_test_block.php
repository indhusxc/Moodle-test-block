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
 * Course list block.
 *
 * @package    block_test_block
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once($CFG->dirroot . '/course/lib.php');

class block_test_block extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_test_block');
    }

    function has_config() {
        return true;
    }
    function applicable_formats() {
        // Default case: the block can be used in courses and site index, but not in activities
        return array(
          'course-view' => true
          
        );
      }
    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
       
        $courseid = required_param('id', PARAM_INT);
       
        $course = $DB->get_record('course', array('id' => $courseid));
        $info = get_fast_modinfo($course)->get_instances();
        foreach ($info as $moduletype => $instances) {
            foreach ($instances as $cm) {

                if (!$cm->uservisible) {
                    continue;
                }
                $modcom=$DB->get_record_sql("SELECT mc.completionstate FROM {course_modules} cm JOIN {course_modules_completion} mc ON cm.id=mc.coursemoduleid AND cm.completion=mc.completionstate where mc.userid=$USER->id AND cm.id=$cm->id");
                if(!empty($modcom)){
                    $completed="Completed";
                }else{
                    $completed="";
                }

                $this->content->text.= "<a href='$CFG->wwwroot/mod/$moduletype/view.php?id=$cm->id'>".shorten_text($cm->id." - ".$cm->get_formatted_name())." - ".date("d-M-Y",$cm->added)." ".$completed."</a></br>";
              
            }
        }
        // print_object($info);
        $this->content->footer = '';
        return $this->content;
    }

    
}



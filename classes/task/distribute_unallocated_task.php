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
 * An adhoc task for distributing unallocated users.
 *
 * Care: This task should not be run twice at the same time for the same ratingallocate course module.
 *
 * @package    mod_ratingallocate
 * @copyright  2022 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_ratingallocate\task;

use core\task\adhoc_task;

/**
 * Distribute unallocated task class.
 *
 * @copyright  2022 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class distribute_unallocated_task extends adhoc_task {

    /**
     * Executes the distribution of unallocated users.
     */
    public function execute() {
        global $DB;
        $data = $this->get_custom_data();
        $record = $DB->get_record('ratingallocate', ['id' => $data->ratingallocateid]);
        if (empty($record)) {
            // Apparently the ratingallocate instance has been deleted in the meantime.
            return;
        }
        $cm = get_coursemodule_from_instance('ratingallocate', $record->id);
        $course = get_course($record->course);
        $ratingallocate = new \ratingallocate($record, $course, $cm, \context_module::instance($cm->id));
        $ratingallocate->distribute_users_without_choice($data->action);
        mtrace('Distribution of users successful with algorithm type ' . $data->action);
    }
}

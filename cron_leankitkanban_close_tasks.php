<?php
set_time_limit(3600);

/**
 * A cron script
 * Close all the ofuz tasks which are in 'Archive' Lane in LeanKit Kanban Board.
 * 
 * @author SQLFusion
 * @date 2012-11-07
 * @see http://support.leankitkanban.com/forums/20153741-api
 */

include_once("config.php");
include_once("plugin/LeanKitKanban/class/LeanKitKanban.class.php");
$username = ''; //Your LeanKit Kanban username
$pwd = ''; //Your LeanKit Kanban password
$leankitkanban = new LeanKitKanban($username,$pwd);
$archive_lane = $leankitkanban->getArchive('16480091');

foreach($archive_lane->ReplyData as $lane_archive) {
	foreach($lane_archive as $archive) {
		foreach($archive->Lane->Cards as $card) {
			if($card->ExternalCardID) {				
				//getting the idtask from project_task table by sending idproject_task(ExternalCardID)
				$do_project_task = new ProjectTask();
				$idtask = $do_project_task->getTaskId($card->ExternalCardID);
				$do_project_task->free();

				//Close the task (update the status to 'closed')
				if($idtask) {
					$do_task = new Task();
					$status = $do_task->getStatus($idtask);
					echo $status.":";
					/*if($status != 'closed') {
						$do_task->updateStatus($idtask,"closed");
					}
					$do_task->free();*/
				}
			}
		}
	}
}
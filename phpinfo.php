<?php
include_once("config.php");
/*$do_workfeed = new WorkFeed();

 $do_workfeed->getId(305217);
 $data = $do_workfeed->iduser;
print_r(unserialize($do_workfeed->feed_data));
*/
$do_proj_task_feed = new ProjectTask();
if($do_proj_task_feed->isProjectTaskReletedToUser(5960)) {
  echo 'yes';
} else  {
  echo 'no';
}
?>
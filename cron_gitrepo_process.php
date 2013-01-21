<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


include_once('config.php');

include_once('plugin/Git/UserGitrepo.class.php');

include_once('plugin/Git/Git.php');

$do_usergit = new UserGitrepo();
$do_usergit->getAll();
$rows = $do_usergit->getNumRows();

$repo_path = "plugin/Git/repos/";
$commitlog = array();
while($do_usergit->fetch()){
	$repo_name = $do_usergit->getData('git_repo');
	$folder = "$repo_path"."$repo_name";
	if ( is_dir($folder)) {
		$repo = Git::open($folder);
		//echo'<pre>';print_r($repo->log());echo'</pre>';
		$commitlog = $repo->log();
		$commit_log = split('\^',$commitlog);
		foreach($commit_log as $commits){
			if(!empty($commits)){
				$user = split("--",$commits);
				$user_name = $user[0];
				
				$date = split(":",$user[1]);
				$date_log = $date[0];
				
				$note = $date[1];
				
				echo 'User Name : '.$user_name.'<br />';
				echo 'Date :'.$date_log.'<br />';
				
				preg_match("|\d+|", $note, $task_id);
				
				echo 'Task ID :'.$task_id[0].'<br />';
				//var_dump($m);
				echo 'Msg : '.$note.'<br /><br />';
				
				
				
			}
			
		}
		
	}

	
	
		
}

// git log --author="vivek@sqlfusion.com" --grep='#' --pretty=format:'%h %an %ci : %s'

?>

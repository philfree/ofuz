<?php
if (isset($_GET['commithash'])) {
	
	 $commithash = $_GET['commithash']; 
	
	 $repo_name = $_GET['repo_name'];
	$repo_folder = 'plugin/Git/repos/'.$repo_name.'';
	
	if (is_dir($repo_folder)) {
		include_once('class/Git.class.php');	
		
		$repo = Git::open($repo_folder);
		$commitlog = $repo->commitdetails($commithash);
		
		echo $commitlog;
	} else {
		
		echo 'Invalid Repository Please try again. ';
	}	
		
		
} else {
	echo 'Invalid Option Please try again. ';
}
?>

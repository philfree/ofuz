<?php
$iduser = $_SESSION['do_User']->iduser;


$GitRepo = new UserGitrepo();
$data = $GitRepo->GetUserGitRepo($iduser);
?>
<?php if(isset($_SESSION['msg'])){?>
<div id="msg"><?php echo $_SESSION['msg'];?></div>
<?php 
	$_SESSION['msg'] = ''; 
	unset($_SESSION['msg']); 
} ?>
<?php if(is_array($data)){ ?>
<table border=0 width="50%">
	<tr>
		<td><b>Repository Name</b> </td> <td><b>Repository URL</b> </td>
	</tr>
<?php
foreach($data as $key=>$value){
	
	echo"<tr><td>".$key."</td><td>".$value.'</td></tr>';
	
}
?>
</table>
<?php }?>
<br /><br />
<b><u>Add New Git Repository</u></b><br /><br />
<?php
$path = '/Setting/Git/git_repo';
$do_repo = new Event("UserGitrepo->eventAddGitRepo");
$do_repo->addParam("goto", $path); 
$do_repo->addParam("iduser", $iduser); 
echo $do_repo->getFormHeader();
echo $do_repo->getFormEvent();
?>
<table><tr><td>Repository Name:</td><td> <input type="text" name="repo_name" id="repo_name" value="<?php echo $repo_name;?>" /></td></tr>
<tr><td>Repository URL:</td><td> <input type="text" name="repo_url" id="repo_url" value="<?php echo $repo_url;?>" /></td></tr>
<tr><td colspan="2"><?php echo $do_repo->getFormFooter('Submit');  ?></td></tr></table>


<?php
    $iduser = $_SESSION['do_User']->iduser;
	
	$do_zend = new Zendesk();
	$data = $do_zend->GetUserZendeskDetails($iduser);
	?>
	<?php if(isset($_SESSION['msg'])){?>
	<div id="msg"><?php echo $_SESSION['msg'];?></div>
	<?php 
		$_SESSION['msg'] = ''; 
		unset($_SESSION['msg']); 
	} ?>
	<?php if(is_array($data)){ ?>
	<table border=0 width="75%">
		<tr>
			<td><b>Zend Email</b> </td> <td><b>Zend API</b> </td> <td><b>Zend URL</b></td><td><b>Project</b></td><td><b>Unlink</b></td>
		</tr>
	<?php
	
	for($i=0;$i <= max(array_map('count', $data)) - 1; $i++){
		  $un_link = new Event("Zendesk->eventunlinkZend");
		  $un_link->addParam("iduser_zendesk", $data['iduser_zendesk'][$i]);		 
		  $path = '/Setting/Zendesk/zend_api';
		  $un_link->addParam("goto", $path);
		  $u_link = $un_link->getLink("Unlink");	  
		  
		  $idproject = $data['idproject'][$i];
		  $do_project  = new Project();
		  $project_name = $do_project->getProjectName($idproject);
		  echo"<tr><td>".$data['zend_email'][$i]."</td><td>".$data['zend_api'][$i]."</td><td>".$data['zend_url'][$i]."</td><td>".$project_name."</td><td>".$u_link."</td></tr>";
		
	}
	?>
	</table>
	<?php }?>
	<br /><br />
	<b><u>Connect with Zendesk</u></b><br /><br />
	<?php
	$path = '/Setting/Zendesk/zend_api';
	$do_repo = new Event("Zendesk->eventAddZend");
	$do_repo->addParam("goto", $path); 
	$do_repo->addParam("iduser", $iduser); 
	echo $do_repo->getFormHeader();
	echo $do_repo->getFormEvent();
	?>
	<table>
	<tr><td>Zend URL:</td><td> <input type="text" name="zend_url" id="zend_url" value="<?php echo $zend_url;?>" /></td></tr>	
	<tr><td>Zend Email:</td><td> <input type="text" name="zend_email" id="zend_email" value="<?php echo $zend_email;?>" /></td></tr>
	<tr><td>Zend API Key:</td><td><input type="text" name="zend_api" id="zend_api" value="<?php echo $zend_api;?>" /></td></tr>
	<tr><td>Project to Connect:</td><td><select name="idproject"><option value="">[Select Project]</option><?php echo $do_zend->getProjectList($iduser);?></select></td></tr>
	<tr><td colspan="3"><?php echo $do_repo->getFormFooter('Submit');  ?></td></tr></table>



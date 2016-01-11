<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class ProjectDiscussionEmailAlertBlock extends BaseBlock{
     public $short_description = 'Project discussion email alert block';
     public $long_description = 'Turn off or turn on email alert for a project';


      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Discussion Email Alert'));
	  $this->setContent($this->generateAddTaskBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML
       * @see class/UserSettings.class.php
       * @see class/DiscussionEmailSetting.class.php
      */

      function generateAddTaskBlock(){

	    $output = '';
	    $idproject = $_SESSION["do_project"]->idproject;

	    if(!is_object($_SESSION['UserSettings'])) {
		$do_user_settings = new UserSettings();
		$do_user_settings->sessionPersistent("UserSettings", "logout.php", OFUZ_TTL);
	    }
	    $data = $_SESSION['UserSettings']->getSettingValue("task_discussion_alert");
	    $global_discussion_email_on = 'Yes';
	    if(!$data){
		$global_discussion_email_on = 'Yes';
	    }else{
		if(is_array($data)){
		    if($data["setting_value"] == 'Yes'){
			$global_discussion_email_on = 'Yes';
		    }else{ $global_discussion_email_on = 'No'; }
		}
	    }
	    $_SESSION['UserSettings']->global_task_discussion_alert = $global_discussion_email_on;
	    if($global_discussion_email_on == 'Yes'){
		  $DiscussionEmailSetting = new DiscussionEmailSetting();
		  $data = $DiscussionEmailSetting->isDiscussionAlertSet($idproject,'Project');
		  if($data && is_array($data)){
		      $output .= _('You have turned off email alert for this project.<br /> If you want to get email alerts for this project please turn it on. <br />');
		      $set_email_alert_on =  new Event("DiscussionEmailSetting->eventSetOnDiscussionAlert");
		      $set_email_alert_on->addParam("setting_level","Project");
		      $set_email_alert_on->addParam("id",$data["iddiscussion_email_setting"]);
		      $output .= '<br />';
		      $output .= $set_email_alert_on->getLink('Turn On');
		  }else{
		      $output .= _('Your email alert for the project discussion is set on by default. You can turn off if you do not want to receive emails for this project discussion.<br />');
		      $set_email_alert_off =  new Event("DiscussionEmailSetting->eventSetOffDiscussionAlert");
		      $set_email_alert_off->addParam("id",$idproject);
		      $set_email_alert_off->addParam("setting_level","Project");
		      $output .= '<br />';
		      $output .= $set_email_alert_off->getLink('Turn Off');
		  }
	    }

	    return $output;

      }

      

      
}

?>

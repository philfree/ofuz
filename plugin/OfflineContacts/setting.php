<?php 
            $UserSettings = new UserSettings();
			$e_set_ggear =  new Event("UserSettings->eventSetSetting");
			$e_set_ggear->addParam("goto",$_SERVER['PHP_SELF']);
			$e_set_ggear->addParam("setting_name", "google_gears");
			
			echo   '<div class="messageshadow">';
			echo     '<div class="messages">';
			$msg = new Message();  
		   
			if($UserSettings->getSetting("google_gears") == 'Yes'){
				echo $msg->getMessage('google_gears');
				echo '<br />';
				$e_set_ggear->addParam("setting_value", "No");
				echo $e_set_ggear->getLink(_('Turn Off'));
			}else{
				echo $msg->getMessage('google_gears');
				echo '<br />';
				$e_set_ggear->addParam("setting_value", "Yes");
				echo $e_set_ggear->getLink(_('Turn On'));
			}
			echo '</div></div>';
			echo '<br />';

?>

<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

?>
    <div class="layout_header">
        <div class="layout_logo">
            <a href="/index.php"><img src="/images/ofuz_logo.jpg" width="188" height="90" alt="" /></a>
        </div>
        <div class="layout_textlinks">
        <?php
          if($_SESSION['do_User']->global_fb_connected && $_SESSION['do_User']->fb_user_id){
               echo '<span style="float: left;"><b>Hello , </b>&nbsp;&nbsp;</span><span style="float: left;"><fb:profile-pic uid="'.$_SESSION['do_User']->fb_user_id.'" facebook-logo="true" linked="true" width="50" height="50"></fb:profile-pic></span>';
               echo '<span class="sep1">|</span>';
          }else if($_SESSION['do_User']->iduser){
              echo '<span style="float: left;"><b>Hello , '.$_SESSION['do_User']->firstname.' </b>&nbsp;&nbsp;</span>';
             
          }
        ?>
        <?php 
            if (!is_object($_SESSION['do_coworker'])) {
                $co_worker_form = new UserRelations();
                $co_worker_form->sessionPersistent("do_coworker", "index.php", 36000);
            }
            $_SESSION['do_coworker']->getAllRequest();
            $co_worker_str = '';
            if ($_SESSION['do_coworker']->getNumrows()) {
                $no_invitation = $_SESSION['do_coworker']->getNumrows();
                $co_worker_str = '&nbsp;('.$no_invitation.')';
            }
            if ($co_worker_str !='') {
        ?>
            <span style="background-color: #ffffcc;">
                <a style="color: orange;" href="/co_workers.php"><?php echo _('Co-Workers'), $co_worker_str; ?></a>
        <?php } else { ?>
            <span>
                <a href="/co_workers.php"><?php echo _('Co-Workers'); ?></a>
        <?php } ?>
            </span>
            <span class="sep1">|</span>
            <a href="/settings_info.php"><?php echo _('Settings'); ?></a>
            <span class="sep1">|</span> 
        <?php 
              if($_SESSION['do_User']->global_fb_connected && $_SESSION['do_User']->fb_user_id){
                  echo '<a href="#" onclick=\'FB.Connect.logoutAndRedirect("/fb_logout.php")\'>Logout</a>';
              }else{
        ?>
        <?php 
            $e_logout = new Event("do_User->eventLogout");
            $e_logout->addParam("goto", "user_login.php");
        ?>
            <a href="<?php echo $e_logout->getUrl(); ?>"><?php echo _('Logout'); ?></a>
        <?php } ?>
        </div>

        <!--  <div class="layout_social">
            <a href="http://www.facebook.com/ofuzfan"><img src="/images/facebook_icon.png" width="38" height="38" alt="" /></a>
            <a href="http://twitter.com/ofuz"><img src="/images/t_logo-a.png" width="36" height="36" alt="" /></a>
        </div> -->

        <div class="layout_navbar_left">
        <?php
   	     if (!isset($_SESSION['dashboard_link'])) {  $_SESSION['dashboard_link'] = "index"; }
	     //$do_user_settings = new UserSettings();
	     //$setting_gears_arr = $do_user_settings->getSettingValue("google_gears");
	     //$contacts_page = ($setting_gears_arr['setting_value'] == 'Yes') ? 'ggears_contacts' : 'contacts';  
	     
	     //print_r($GLOBALS['cfg_tab_placement']); echo '<br />';
	     if($GLOBALS['cfg_tab_placement']->count() > 0 ){
              	foreach($GLOBALS['cfg_tab_placement'] as  $tab ){   
                  if (is_object($tab)) {  
          		    if ($tab->getTabName() == _('Dashboard')) { $tab->setDefaultPage($_SESSION['dashboard_link']); }
	                //if ($tab->getTabName() == _('Contacts')) { $tab->setDefaultPage($contacts_page); }
                    if($tab->isActive() === true ){
                        $tab->processTab();
                    }
                  }  
		        }
         }
           
        ?>
        </div>
        <!-- <div class="layout_navbar_right">
        <?php
            //$arrRightTabs[] = array('Sync', '/sync.php');
            //$arrRightTabs[] = array('Blog', 'http://www.ofuz.com/blog/');

            /*
            foreach ($arrRightTabs as $arrTab) {
                if (isset($thistab) && $thistab == $arrTab[0]) {
                    echo '<div class="layout_navtab_on"><div class="layout_navtab_on_l"></div><div class="layout_navtab_on_text"><a href="',$arrTab[1],'">',_($arrTab[0]),'</a></div><div class="layout_navtab_on_r"></div></div>';
                } else {
                    echo '<div class="layout_navtab"><a href="',$arrTab[1],'">',_($arrTab[0]),'</a></div>';
                }
            }
            */
        ?>
        </div> //-->
    </div>

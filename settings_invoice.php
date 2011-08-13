<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Invoice Settings';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
?>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn settingsbg">
        <div class="settingsbar"><div class="spacerblock_16"></div>
            <?php
		$GLOBALS['thistabsetting'] = 'Invoice Settings';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Invoice Settings'); ?></div>
        <div class="contentfull">
        <?php
                if($_SESSION['in_page_message'] != ''){
                  echo '<div style="margin-left:0px;">';
                  echo '<div class="messages_unauthorized">';
                  echo htmlentities($_SESSION['in_page_message']);
                  $_SESSION['in_page_message'] = '';
                  echo '</div></div><br /><br />';
              }
          ?>


        <?php 
            $UserSettings = new UserSettings();
            $UserSettings->sessionPersistent("InvLogo", "index.php", OFUZ_TTL);
            //$UserSettings->sessionPersistent("InvCurrency", "index.php", OFUZ_TTL);
            //$UserSettings->sessionPersistent("InvDateFormat", "index.php", OFUZ_TTL);
            //$UserSettings->sessionPersistent("InvAuthNet", "index.php", OFUZ_TTL);
            //$UserSettings->sessionPersistent("InvPaypal", "index.php", OFUZ_TTL);
            
            // Invoice Logo section
            $inv_logo = $UserSettings->getSettingValue("invoice_logo");

            if($inv_logo && is_array($inv_logo)){
                $_SESSION['InvLogo']->getId($inv_logo["iduser_settings"]);
                $img = $_SESSION['InvLogo']->setting_value;
                $e_inv_logo = new Event("InvLogo->eventValuesFromForm");
                $e_inv_logo->addEventAction("InvLogo->update", 2000);
                $e_inv_logo->addEventAction("InvLogo->eventCheckInvLogoExtension", 2);
                $e_inv_logo->setGotFile(true);
                $e_inv_logo->addEventAction("mydb.gotoPage", 2333);
                $e_inv_logo->addParam("goto", $_SERVER['PHP_SELF']);
                echo $e_inv_logo->getFormHeader();
                echo $e_inv_logo->getFormEvent();
                $_SESSION['InvLogo']->setFields("invoice_logo");
                $_SESSION['InvLogo']->setApplyRegistry(true, "Form");
            
                echo '<div class="in290x18">';
                echo _('Logo (GIF files are not supported)').'<br />';
                echo $_SESSION['InvLogo']->setting_value; 
                echo '<img src="files/'.$img.'">';
                echo "<br><br>";
                echo $e_inv_logo->getFormFooter(_('Save'));
                echo'  </div>
                   <div class="dashedline"></div>';
          }else{
              $e_inv_logo = new Event("InvLogo->eventValuesFromForm");
              $e_inv_logo->addEventAction("InvLogo->add", 2000);
              $e_inv_logo->addEventAction("InvLogo->eventCheckInvLogoExtension", 2);
              $e_inv_logo->setGotFile(true);
              $e_inv_logo->addEventAction("mydb.gotoPage", 2333);
              $e_inv_logo->addParam("goto", $_SERVER['PHP_SELF']);
              echo $e_inv_logo->getFormHeader();
              echo $e_inv_logo->getFormEvent();
              $_SESSION['InvLogo']->setFields("invoice_logo"); 
              $_SESSION['InvLogo']->setApplyRegistry(true, "Form");
              echo '<div class="in290x18">';
              echo _('Logo (GIF files are not supported)').'<br />';
              echo $_SESSION['InvLogo']->setting_value; 
              echo $_SESSION['InvLogo']->setting_name;
              echo $_SESSION['InvLogo']->iduser;
              echo "<br><br>";
              echo $e_inv_logo->getFormFooter(_('Save'));
              echo '</div>
                  <div class="dashedline"></div>';
             
          }
          // Invoice Logo ends here
           echo '<br /><br />';
          // AuthNet Login And Transaction Key
          $inv_authnet_login = $UserSettings->getSettingValue("authnet_login");
          $inv_authnet_marchent_id = $UserSettings->getSettingValue("authnet_merchant_id");
          if($inv_authnet_login && $inv_authnet_marchent_id && is_array($inv_authnet_login) && is_array($inv_authnet_marchent_id)){
              $e_inv_authnet = new Event("UserSettings->eventUpdateAuthNetDetail");
              $e_inv_authnet->setLevel(10);
              $e_inv_authnet->addParam("id_authlogin",$inv_authnet_login["iduser_settings"]);
              $e_inv_authnet->addParam("id_authmerchant",$inv_authnet_marchent_id["iduser_settings"]);
              $e_inv_authnet->addParam("goto", $_SERVER['PHP_SELF']);
              echo $e_inv_authnet->getFormHeader();
              echo $e_inv_authnet->getFormEvent();

              $e_del_authnet = new Event("UserSettings->eventDelAuthNetDetail");
              $e_del_authnet->setLevel(15);
              $e_del_authnet->addParam("id_authlogin",$inv_authnet_login["iduser_settings"]);
              $e_del_authnet->addParam("id_authmerchant",$inv_authnet_marchent_id["iduser_settings"]);
              $e_del_authnet->addParam("goto", $_SERVER['PHP_SELF']);


              echo '<div class="in290x18">';
              echo '<b>'._('Authorized.net details').'</b>&nbsp;&nbsp;'.$e_del_authnet->getLink(_('delete')).'<br />';
              echo _('Login :').'<br />
              <input type = "text" name = "auth_login" id="auth_login" value ="'.$inv_authnet_login["setting_value"].'"><br />';
              echo _('Transaction Key :').'<br />
              <input type = "text" name = "auth_merchant_id" id="auth_merchant_id" 
              value ="'.$inv_authnet_marchent_id["setting_value"].'"><br />';
              echo "<br>";
              echo $e_inv_authnet->getFormFooter(_('Save'));
              echo '</div>
                  <div class="dashedline"></div>';
          }else{
              $e_inv_authnet = new Event("UserSettings->eventAddAuthNetDetail");
              $e_inv_authnet->setLevel(10);
              $e_inv_authnet->addParam("goto", $_SERVER['PHP_SELF']);
              echo $e_inv_authnet->getFormHeader();
              echo $e_inv_authnet->getFormEvent();
              
              echo '<div class="in290x18">';
              echo '<b>'._('Authorized.net details').'</b><br />';
              echo _('Login :').'<br /><input type = "text" name = "auth_login" id="auth_login" value = ""><br />';
              echo _('Transaction Key :').'<br /><input type = "text" name = "auth_merchant_id" id="auth_merchant_id" value = ""><br />';
              echo "<br>";
              echo $e_inv_authnet->getFormFooter(_('Save'));
              echo '</div>
                  <div class="dashedline"></div>';
          }
          
          // Authnet Setting ends here
          echo '<br /><br />';
          // Paypal details starts here
          $inv_paypal_email = $UserSettings->getSettingValue("paypal_business_email");
          if($inv_paypal_email && is_array($inv_paypal_email)){
              $e_inv_paypal = new Event("UserSettings->eventUpdatePaypalDetail");
              $e_inv_paypal->setLevel(20);
              $e_inv_paypal->addParam("id_paypal_business",$inv_paypal_email["iduser_settings"]);
              $e_inv_paypal->addParam("goto", $_SERVER['PHP_SELF']);
              echo $e_inv_paypal->getFormHeader();
              echo $e_inv_paypal->getFormEvent();

              $e_del_paypal = new Event("UserSettings->eventDelPayPalDetail");
              $e_del_paypal->setLevel(25);
              $e_del_paypal->addParam("id_paypal_business",$inv_paypal_email["iduser_settings"]);
              $e_del_paypal->addParam("goto", $_SERVER['PHP_SELF']);

              echo '<div class="in290x18">';
              echo '<b>'._('Paypal details').'</b>&nbsp;&nbsp;'.$e_del_paypal->getLink(_('delete')).'<br />';
              echo _('Paypal Business Email :').'<br />
              <input type = "text" name = "paypal_b_email" id="paypal_b_email"
               value ="'.$inv_paypal_email["setting_value"].'" size="40"><br />';
              echo "<br>";
              echo $e_inv_paypal->getFormFooter(_('Save'));
              echo '</div>
                  <div class="dashedline"></div>';
              
          }else{
              $e_inv_paypal = new Event("UserSettings->eventAddPaypalDetail");
              $e_inv_paypal->setLevel(20);
              $e_inv_paypal->addParam("goto", $_SERVER['PHP_SELF']);
              echo $e_inv_paypal->getFormHeader();
              echo $e_inv_paypal->getFormEvent();
              
              echo '<div class="in290x18">';
              echo '<b>'._('Paypal details').'</b><br />';
              echo _('Paypal Business Email :').'<br />
              <input type = "text" name = "paypal_b_email" id="paypal_b_email" value ="" size="40"><br />';
              echo "<br>";
              echo $e_inv_paypal->getFormFooter(_('Save'));
              echo '</div>
                  <div class="dashedline"></div>';
          }  
          
           // Paypal details Ends Here here

             echo '<br /><br />';
          // Currency details starts here 
             $inv_currency = $UserSettings->getSettingValue("currency");
             if($inv_currency && is_array($inv_currency)){
                  $e_inv_currency = new Event("UserSettings->eventUpdateCurrency");
                  $e_inv_currency->setLevel(30);
                  $e_inv_currency->addParam("id_currency",$inv_currency["iduser_settings"]);
                  $e_inv_currency->addParam("goto", $_SERVER['PHP_SELF']);
                  echo $e_inv_currency->getFormHeader();
                  echo $e_inv_currency->getFormEvent();
                  
                  echo '<div class="in290x18">';
                  echo '<b>'._('Currency details').'</b><br />'; 
                  echo _('Select Currency :').'<br />';
                  echo $UserSettings->listCurrencies($inv_currency["setting_value"]);
                  echo "<br>";
                  echo $e_inv_currency->getFormFooter(_('Save'));
                  echo '</div>
                      <div class="dashedline"></div>';
             }else{
                  $e_inv_currency = new Event("UserSettings->eventAddCurrency");
                  $e_inv_currency->setLevel(30);
                  $e_inv_currency->addParam("goto", $_SERVER['PHP_SELF']);
                  echo $e_inv_currency->getFormHeader();
                  echo $e_inv_currency->getFormEvent();
                  
                  echo '<div class="in290x18">';
                  echo '<b>'._('Currency details').'</b><br />'; 
                  echo _('Select Currency :').'<br />';
                  echo $UserSettings->listCurrencies();
                  echo "<br>";
                  echo $e_inv_currency->getFormFooter(_('Save'));
                  echo '</div>
                      <div class="dashedline"></div>';
             }

             echo '<br /><br />';
          // Date Format settings 
             $inv_date_format = $UserSettings->getSettingValue("inv_date_format");
             if($inv_date_format && is_array($inv_date_format)){
                  $e_inv_date_format = new Event("UserSettings->eventUpdateInvDateFormat");
                  $e_inv_date_format->setLevel(30);
                  $e_inv_date_format->addParam("id_inv_date_format",$inv_date_format["iduser_settings"]);
                  $e_inv_date_format->addParam("goto", $_SERVER['PHP_SELF']);
                  echo $e_inv_date_format->getFormHeader();
                  echo $e_inv_date_format->getFormEvent();
                  
                  echo '<div class="in290x18">';
                  echo '<b>'._('Date Format').'</b><br />'; 
                  echo $UserSettings->listInvDateFormat($inv_date_format["setting_value"]);
                  echo "<br>";
                  echo $e_inv_date_format->getFormFooter(_('Save'));
                  echo '</div>
                      <div class="dashedline"></div>';
             }else{
                  $e_inv_date_format = new Event("UserSettings->eventAddInvDateFormat");
                  $e_inv_date_format->setLevel(30);
                  $e_inv_date_format->addParam("goto", $_SERVER['PHP_SELF']);
                  echo $e_inv_date_format->getFormHeader();
                  echo $e_inv_date_format->getFormEvent();
                  
                  echo '<div class="in290x18">';
                  echo '<b>'._('Date Format').'</b><br />'; 
                  echo $UserSettings->listInvDateFormat();
                  echo "<br>";
                  echo $e_inv_date_format->getFormFooter(_('Save'));
                  echo '</div>
                      <div class="dashedline"></div>';
             }
            
        ?>
        </div>
        <div class="solidline"></div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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
			  $authnet_details = 'yes';
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
				
		  	 // Stripe details starts here
          $inv_stripe_api = $UserSettings->getSettingValue("stripe_api_key");
          $inv_stripe_publish = $UserSettings->getSettingValue("stripe_publish_key");
          if($inv_stripe_api && $inv_stripe_publish && is_array($inv_stripe_api) && is_array($inv_stripe_publish)){
			  $stripe_details = 'yes';
              $u_stripe = new Event("UserSettings->eventUpdateStripeDetails");
              $u_stripe->setLevel(20);
              $u_stripe->addParam("id_stripe_api_key",$inv_stripe_api["iduser_settings"]);
              $u_stripe->addParam("id_stripe_publish_key",$inv_stripe_publish["iduser_settings"]);
              $u_stripe->addParam("goto", $_SERVER['PHP_SELF']);
              echo $u_stripe->getFormHeader();
              echo $u_stripe->getFormEvent();

              $d_stripe = new Event("UserSettings->eventDelStripeDetail");
              $d_stripe->setLevel(25);
              $d_stripe->addParam("id_stripe_api_key",$inv_stripe_api["iduser_settings"]);
              $d_stripe->addParam("id_stripe_publish_key",$inv_stripe_publish["iduser_settings"]);
              $d_stripe->addParam("goto", $_SERVER['PHP_SELF']);

              echo '<div class="in290x18">';
              echo '<b>'._('Stripe.com Details').'</b>&nbsp;&nbsp;'.$d_stripe->getLink(_('delete')).'<br />';
              echo _('Stripe API Key :').'<br />
              <input type = "text" name = "stripe_api_key" id="id_stripe_api_key" value ="'.$inv_stripe_api["setting_value"].'" size="40"><br />';
              echo _('Stripe Publish Key :').'<br />
              <input type = "text" name = "stripe_publish_key" id="id_stripe_publish_key" value ="'.$inv_stripe_publish["setting_value"].'" size="40"><br />';
              echo "<br>";
              echo $u_stripe->getFormFooter(_('Save'));
              echo '</div>
                  <div class="dashedline"></div>';
              
          }else{
              $stripe_api = new Event("UserSettings->eventAddStripeDetail");
              $stripe_api->setLevel(20);
              $stripe_api->addParam("goto", $_SERVER['PHP_SELF']);
              echo $stripe_api->getFormHeader();
              echo $stripe_api->getFormEvent();
              
              echo '<div class="in290x18">';
              echo '<b>'._('Stripe Details').'</b><br />';
              echo _('Stripe Api Key :').'<br />
              <input type = "text" name = "stripe_api_key" id="stripe_api_key" value ="" size="40"><br />';
              echo _('Stripe Publish Key :').'<br />
              <input type = "text" name = "stripe_publish_key" id="stripe_publish_key" value ="" size="40"><br />';
              echo "<br>";
              echo $stripe_api->getFormFooter(_('Save'));
              echo '</div>
                  <div class="dashedline"></div>';
          } 		
		// Stripe details Ends Here here		
		
		if(($authnet_details === 'yes')&&($stripe_details === 'yes')){
		  echo '<br /><br />';
		  //Select the payment gatway to be used for payment
		  
		  $inv_payment_selection = $UserSettings->getSettingValue("payment_selection");
          if($inv_payment_selection && is_array($inv_payment_selection)){
              $e_inv_paymentsele = new Event("UserSettings->eventUpdatePaymentSelection");
              $e_inv_paymentsele->setLevel(20);
              $e_inv_paymentsele->addParam("id_payment_selection",$inv_payment_selection["iduser_settings"]);
              $e_inv_paymentsele->addParam("goto", $_SERVER['PHP_SELF']);
              echo $e_inv_paymentsele->getFormHeader();
              echo $e_inv_paymentsele->getFormEvent();

              $e_del_paymentselction = new Event("UserSettings->eventDelPaymentSelection");
              $e_del_paymentselction->setLevel(25);
              $e_del_paymentselction->addParam("id_payment_selection",$inv_payment_selection["iduser_settings"]);
              $e_del_paymentselction->addParam("goto", $_SERVER['PHP_SELF']);

              echo '<div class="in290x18">';
              echo '<b>'._('Payment Gateway Selection').'</b>&nbsp;&nbsp;'.$e_del_paymentselction->getLink(_('delete')).'<br />';
                  if($inv_payment_selection["setting_value"] == 'authorized.net'){
					echo '<table width=10%"><tr><td>'._('Authorized.net :').'</td><td>
					<input type = "radio" name = "payment_selection" id="payment_selection" value ="authorized.net" checked></td></tr>';
					echo '<tr><td>'._('Stripe.com :').'</td><td>
					<input type = "radio" name = "payment_selection" id="payment_selection" value ="stripe.com"></td></tr></table>';
				} elseif($inv_payment_selection["setting_value"] == 'stripe.com') {
					echo '<table width=10%"><tr><td>'._('Authorized.net :').'</td><td>
					<input type = "radio" name = "payment_selection" id="payment_selection" value ="authorized.net"></td></tr>';
					echo '<tr><td>'._('Stripe.com :').'</td><td>
					<input type = "radio" name = "payment_selection" id="payment_selection" value ="stripe.com" checked></td></tr></table>';
				}
              echo "<br>";
              echo $e_inv_paymentsele->getFormFooter(_('Save'));
              echo '</div>
                  <div class="dashedline"></div>';
              
          }else{
              $e_inv_paymentsele = new Event("UserSettings->eventAddPaymentSelection");
              $e_inv_paymentsele->setLevel(20);
              $e_inv_paymentsele->addParam("goto", $_SERVER['PHP_SELF']);
              echo $e_inv_paymentsele->getFormHeader();
              echo $e_inv_paymentsele->getFormEvent();
              
              echo '<div class="in290x18">';
              echo '<b>'._('Payment Gateway Selection').'</b><br />';
			  echo '<table width=10%"><tr><td>'._('Authorized.net :').'</td><td>
              <input type = "radio" name = "payment_selection" id="payment_selection" value ="authorized.net"></td></tr>';
              echo '<tr><td>'._('Stripe.com :').'</td><td>
              <input type = "radio" name = "payment_selection" id="payment_selection" value ="stripe.com"></td></tr></table>';
              echo "<br>";
              echo $e_inv_paymentsele->getFormFooter(_('Save'));
              echo '</div>
                  <div class="dashedline"></div>';
          }
		  
		  
		  //Payment gateway selection ends here
		 // echo '<br /><br />';
		}
		
		
		
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

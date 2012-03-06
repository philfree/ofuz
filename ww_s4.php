<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: Setup your Invoices';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');
     
?>
<style type="text/css">
#simplemodal-overlay {background-color:#000;}
#simplemodal-container {background-color:#333; height:auto; border:8px solid #444; padding:12px;}
</style>

<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Setup your Invoices');?></span>
        </div>
    </div>
    <div class="contentfull">        
      <div class="messageshadow">
	<div class="messages">Ofuz Getting started wizard</div>
      </div>

      <div align="center">
      <p id="pYourFirstProject" style="font-size:1.8em;">Setup your Invoices</p>

	<div id="setup_invoices">
	  <div class="spacerblock_20"></div>
	  <div>
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
		echo '<table width="50%" height="100px"><tr><td width="40%">';
                $e_inv_logo->setGotFile(true);
                $e_inv_logo->addEventAction("mydb.gotoPage", 2333);
                $e_inv_logo->addParam("goto", $_SERVER['PHP_SELF']);
                echo $e_inv_logo->getFormHeader();
                echo $e_inv_logo->getFormEvent();
                $_SESSION['InvLogo']->setFields("invoice_logo");
                $_SESSION['InvLogo']->setApplyRegistry(true, "Form");
                echo _('Logo (GIF files are not supported)').'<br />';
                echo $_SESSION['InvLogo']->setting_value; 
                echo '<img src="files/'.$img.'">';
		echo '</td>';
		echo '<td width="10%" style="text-align:left;">';
		echo $e_inv_logo->getFormFooter(_('Save'));
		echo '</td></tr></table>';
          }else{
              $e_inv_logo = new Event("InvLogo->eventValuesFromForm");
              $e_inv_logo->addEventAction("InvLogo->add", 2000);
              $e_inv_logo->addEventAction("InvLogo->eventCheckInvLogoExtension", 2);
	      echo '<table width="50%" height="100px"><tr><td width="40%">';
              $e_inv_logo->setGotFile(true);
              $e_inv_logo->addEventAction("mydb.gotoPage", 2333);
              $e_inv_logo->addParam("goto", $_SERVER['PHP_SELF']);
              echo $e_inv_logo->getFormHeader();
              echo $e_inv_logo->getFormEvent();
              $_SESSION['InvLogo']->setFields("invoice_logo"); 
              $_SESSION['InvLogo']->setApplyRegistry(true, "Form");
              echo _('Logo (GIF files are not supported)').'<br />';
              echo $_SESSION['InvLogo']->setting_value; 
              echo $_SESSION['InvLogo']->setting_name;
              echo $_SESSION['InvLogo']->iduser;
	      echo '</td>';
	      echo '<td width="10%" style="text-align:left;">';
	      echo $e_inv_logo->getFormFooter(_('Save'));
	      echo '</td></tr></table>';
             
          }
?>
	  </div>

	  <div>
<?php
          // Currency details starts here 
             $inv_currency = $UserSettings->getSettingValue("currency");
             if($inv_currency && is_array($inv_currency)){
	      $e_inv_currency = new Event("UserSettings->eventUpdateCurrency");
	      $e_inv_currency->setLevel(30);
	      $e_inv_currency->addParam("id_currency",$inv_currency["iduser_settings"]);
	      $e_inv_currency->addParam("goto", $_SERVER['PHP_SELF']);
	      echo '<table width="50%" height="100px"><tr><td width="40%">';
	      echo $e_inv_currency->getFormHeader();
	      echo $e_inv_currency->getFormEvent();
	      echo '<b>'._('Currency details').'</b><br />'; 
	      echo _('Select Currency :').'<br />';
	      echo $UserSettings->listCurrencies($inv_currency["setting_value"]);
	      echo '</td>';
	      echo '<td width="10%" style="text-align:left;">';
	      echo $e_inv_currency->getFormFooter(_('Save'));
	      echo '</td></tr></table>';
             } else{
	      $e_inv_currency = new Event("UserSettings->eventAddCurrency");
	      $e_inv_currency->setLevel(30);
	      $e_inv_currency->addParam("goto", $_SERVER['PHP_SELF']);
	      echo '<table width="50%" height="100px"><tr><td width="40%">';
	      echo $e_inv_currency->getFormHeader();
	      echo $e_inv_currency->getFormEvent();

	      echo '<b>'._('Currency details').'</b><br />'; 
	      echo _('Select Currency :').'<br />';
	      echo $UserSettings->listCurrencies();
	      echo '</td>';
	      echo '<td width="10%" style="text-align:left;">';
	      echo $e_inv_currency->getFormFooter(_('Save'));
	      echo '</td></tr></table>';
             }
?>
	  </div>

	  <div>
<?php
          // Date Format settings 
             $inv_date_format = $UserSettings->getSettingValue("inv_date_format");
             if($inv_date_format && is_array($inv_date_format)){
	      $e_inv_date_format = new Event("UserSettings->eventUpdateInvDateFormat");
	      $e_inv_date_format->setLevel(30);
	      $e_inv_date_format->addParam("id_inv_date_format",$inv_date_format["iduser_settings"]);
	      $e_inv_date_format->addParam("goto", $_SERVER['PHP_SELF']);
	      echo '<table width="60%" height="75px"><tr><td width="50%">';
	      echo $e_inv_date_format->getFormHeader();
	      echo $e_inv_date_format->getFormEvent();
	      echo '<b>'._('Date Format').'</b><br />'; 
	      echo $UserSettings->listInvDateFormat($inv_date_format["setting_value"]);
	      echo '</td>';
	      echo '<td width="10%" style="text-align:left;">';
	      echo $e_inv_date_format->getFormFooter(_('Save'));
	      echo '</td></tr></table>';
             }else{
	      $e_inv_date_format = new Event("UserSettings->eventAddInvDateFormat");
	      $e_inv_date_format->setLevel(30);
	      $e_inv_date_format->addParam("goto", $_SERVER['PHP_SELF']);
	      echo '<table width="50%" height="75px"><tr><td width="40%">';
	      echo $e_inv_date_format->getFormHeader();
	      echo $e_inv_date_format->getFormEvent();
	      echo '<b>'._('Date Format').'</b><br />'; 
	      echo $UserSettings->listInvDateFormat();
	      echo '</td>';
	      echo '<td width="10%" style="text-align:left;">';
	      echo $e_inv_date_format->getFormFooter(_('Save'));
	      echo '</td></tr></table>';
             }
?>
	  </div>

	  <div>
<?php
          // Paypal details starts here
          $inv_paypal_email = $UserSettings->getSettingValue("paypal_business_email");
          if($inv_paypal_email && is_array($inv_paypal_email)){
	    $e_inv_paypal = new Event("UserSettings->eventUpdatePaypalDetail");
	    $e_inv_paypal->setLevel(20);
	    $e_inv_paypal->addParam("id_paypal_business",$inv_paypal_email["iduser_settings"]);
	    $e_inv_paypal->addParam("goto", $_SERVER['PHP_SELF']);
	    echo '<table width="60%" height="75px"><tr><td width="50%">';
	    echo $e_inv_paypal->getFormHeader();
	    echo $e_inv_paypal->getFormEvent();
	    $e_del_paypal = new Event("UserSettings->eventDelPayPalDetail");
	    $e_del_paypal->setLevel(25);
	    $e_del_paypal->addParam("id_paypal_business",$inv_paypal_email["iduser_settings"]);
	    $e_del_paypal->addParam("goto", $_SERVER['PHP_SELF']);

	    echo '<b>'._('Paypal details').'</b>&nbsp;&nbsp;'.$e_del_paypal->getLink(_('delete')).'<br />';
	    echo _('Paypal Business Email :').'<br />
	    <input type = "text" name = "paypal_b_email" id="paypal_b_email"
	    value ="'.$inv_paypal_email["setting_value"].'" size="40"><br />';
	    echo '</td>';
	    echo '<td width="10%" style="text-align:left;">';
	    echo $e_inv_paypal->getFormFooter(_('Save'));
	    echo '</td></tr></table>';
              
          }else{
	    $e_inv_paypal = new Event("UserSettings->eventAddPaypalDetail");
	    $e_inv_paypal->setLevel(20);
	    $e_inv_paypal->addParam("goto", $_SERVER['PHP_SELF']);
	    echo '<table width="50%" height="75px"><tr><td width="40%">';
	    echo $e_inv_paypal->getFormHeader();
	    echo $e_inv_paypal->getFormEvent();
	    echo '<b>'._('Paypal details').'</b><br />';
	    echo _('Paypal Business Email :').'<br />
	    <input type = "text" name = "paypal_b_email" id="paypal_b_email" value ="" size="32"><br />';
	    echo '</td>';
	    echo '<td width="10%" style="text-align:left;">';
	    echo $e_inv_paypal->getFormFooter(_('Save'));
	    echo '</td></tr></table>';
          } 
?>
	  </div>
	</div>
      <div class="spacerblock_20"></div>
      <div>
	<a id="next" href="ww_s5.php"><img src="/images/next.jpg" border="0" /></a> <br />
	<a href="index.php" title="">Skip >></a>
      </div>
</form>
      <div class="spacerblock_80"></div>

      <div class="layout_footer"></div>

     </div>
</td><td class="layout_rmargin"></td></tr></table>
</body>
</html>
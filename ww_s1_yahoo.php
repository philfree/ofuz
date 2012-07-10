<?php

include_once("config.php");

$inviter=new openinviter();
$oi_services=$inviter->getPlugins();

?>
<table align='center' class='thTable' cellspacing='2' cellpadding='0' style='border:none;'>
  <tr>
    <td colspan="2" align="center">
<?php 
$e_ooi = new Event("OfuzEmailImporter->eventGetContacts");
$e_ooi->addParam("goto", $_SERVER["PHP_SELF"]);
$e_ooi->addParam("provider_box", "yahoo");
echo $e_ooi->getFormHeader();
echo $e_ooi->getFormEvent();
if($_SESSION['in_page_message'] != "") {
  echo $_SESSION['in_page_message'];
}
?>
    </td>
  </tr>
  <tr><td align='right'><label for='email_box'>Email</label></td><td><input type='text' name='email_box' value=''></td></tr>
  <tr><td align='right'><label for='password_box'>Password</label></td><td><input type='password' name='password_box' value=''></td></tr>
  <tr ><td colspan='2' align='center'><?php echo $e_ooi->getFormFooter("Import Contacts");?></td></tr>
</table>
<?php $_SESSION['in_page_message'] = "";?>
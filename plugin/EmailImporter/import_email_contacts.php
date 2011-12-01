<?php
$required_email_provider = array("yahoo", "hotmail", "linkedin");

$inviter=new OpenInviter();
$oi_services=$inviter->getPlugins();

$e_ooi = new Event("OfuzEmailImporter->eventGetContacts");
$e_ooi->addParam("goto", "Setting/EmailImporter/import_email_contacts");
echo $e_ooi->getFormHeader();
echo $e_ooi->getFormEvent();

?>
<table align='center' class='thTable' cellspacing='2' cellpadding='0' style='border:none;'>
		<tr><td colspan="2" align="center">
			<?php 
			if($_SESSION['in_page_message'] != "") {
			  echo $_SESSION['in_page_message'];
			}
			?>
			</td>
		</tr>
		<tr><td align='right'><label for='email_box'>Email</label></td><td><input type='text' name='email_box' value=''></td></tr>
		<tr><td align='right'><label for='password_box'>Password</label></td><td><input type='password' name='password_box' value=''></td></tr>
		<tr><td align='right'><label for='provider_box'>Email provider</label></td><td><select name='provider_box'><option value=''></option>
<?php 
		
		foreach ($oi_services as $type=>$providers)	{
			if($type == 'email') {
?>
				<optgroup label='<?php echo $inviter->pluginTypes[$type];?>'>
<?php		
				foreach ($providers as $provider=>$details) {
					if(in_array($provider, $required_email_provider)) {
?>
					<option value='<?php echo $provider;?>'><?php echo $details['name'];?></option>
<?php 
					}
				} 
?>				
				</optgroup>
<?php			
			}
		}
?>
	</select></td></tr>
		<tr ><td colspan='2' align='center'><?php echo $e_ooi->getFormFooter("Import Contacts");?></td></tr>
</table>
</form>
<?php $_SESSION['in_page_message'] = "";?>

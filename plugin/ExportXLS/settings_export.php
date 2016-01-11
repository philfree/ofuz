<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


?>
       
        <?php 			
			$msg = new Message();
			$msg->setContent("Click on Export my contacts to download all your contact in a spreadsheet");
			$msg->displayMessage()
         ?>
        <div class="spacerblock_20"></div>
                <div class="solidline"></div>
        <div class="spacerblock_20"></div>
		<div>
			<?php
			$e_export_contacts =  new Event("Export->eventExportContacts");
			$e_export_contacts->addParam("goto",$_SERVER['PHP_SELF']);
			echo $e_export_contacts->getLink(_('Export My Contacts'));
			?>
		</div>
		<div class="spacerblock_20"></div>


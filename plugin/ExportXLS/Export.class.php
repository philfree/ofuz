<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
	 *
     */


class Export extends DataObject {
    
    public $message = "";

	function eventExportContacts(EventControler $evtcl) {

		$iduser = $_SESSION['do_User']->iduser;
		$do_contact = new Contact();
		$do_contact->getAllContactsForAUser();
		$num_contacts = $do_contact->getNumRows();
	
		$report_name = "ofuz_".$iduser."_report".".xls";
		$fname = "xls_report/{$report_name}";
	
		$workbook = &new writeexcel_workbook($fname);
		$worksheet =& $workbook->addworksheet('Report');
		$heading =& $workbook->addformat(array('align' => 'center', 'bold' => 1, 'fg_color' => 'yellow'));
	
		$right  =& $workbook->addformat(array('align' => 'right'));
		$left  =& $workbook->addformat(array('align' => 'left'));
		
		# Create a border format
		$border1 =& $workbook->addformat();
		$border1->set_color('magenta');
		$border1->set_bold();
		$border1->set_size(15);
		$border1->set_pattern(0x1);
		$border1->set_fg_color('aqua');
		$border1->set_border_color('yellow');
		$border1->set_top(6);
		$border1->set_bottom(6);
		$border1->set_left(6);
		$border1->set_align('center');
		$border1->set_align('vcenter');
		$border1->set_merge(); # This is the key feature
		
		# Create another border format.
		$border2 =& $workbook->addformat();
		$border2->set_color('magenta');
		$border2->set_bold();
		$border2->set_size(15);
		$border2->set_pattern(0x1);
		$border2->set_fg_color('aqua');
		$border2->set_border_color('yellow');
		$border2->set_top(6);
		$border2->set_bottom(6);
		$border2->set_right(6);
		$border2->set_align('center');
		$border2->set_align('vcenter');
		$border2->set_merge(); # This is the key feature
	
/*
		# Set the row height for row 0 (heading current date)
		$worksheet->set_row(0, 24);
	
		$worksheet->write      (0, 0, "Welcome", $border1);
		$worksheet->write_blank(0, 1,                        $border2);
		$worksheet->write_blank(0, 2,                        $border2);
*/
	
		# Set the row height for row 0 (heading current date)
		$worksheet->set_row(1, 24);
		$current_date = _("As on ").date('m/d/Y h:i:s').", ";
		$heading_total_contacts = $current_date._("Total Contacts : ").$num_contacts;
		$worksheet->write      (1, 0, $heading_total_contacts, $border1);
		$worksheet->write_blank(1, 1,                        $border2);
		$worksheet->write_blank(1, 2,                        $border2);
		$worksheet->write_blank(1, 3,                        $border2);
		$worksheet->write_blank(1, 4,                        $border2);
		$worksheet->write_blank(1, 5,                        $border2);
	
		$report_heading = array('First Name', 'Last Name', 'Company', 'Position', 'Email', 'Phone', 'Tags');
	
		$col=0;
		foreach($report_heading as $colum){
			$worksheet->write(3, $col, $colum, $heading);
			$col++;
		}
	
		$row = 4;
			
		if($num_contacts) {
			while($do_contact->next()) {
				$col = 0;
				$worksheet->write($row, $col, $do_contact->getData('firstname'), $left);
				$col++;
				$worksheet->write($row, $col, $do_contact->getData('lastname'), $left);		
				$col++;
				$worksheet->write($row, $col, $do_contact->getData('company'), $left);		
				$col++;
				$worksheet->write($row, $col, $do_contact->getData('position'), $left);
				$col++;
				$worksheet->write($row, $col, $do_contact->getData('email_address'), $left);	
				$col++;
				$worksheet->write($row, $col, $do_contact->getData('phone_number'), $left);	
				$col++;
				$worksheet->write($row, $col, $do_contact->getData('tags'), $left);	
				$row++;	
			}
		} else {
		}
	
		$workbook->close();
		//header("Location: {$fname}");
		$evtcl->setDisplayNext(new Display($fname));
	}

}
?>

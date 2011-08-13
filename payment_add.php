<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
    include_once("class/Invoice.class.php");
    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    
  
?>
<?php $thistab = ''; include_once('ofuz_navtabs.php'); ?>
<script type="text/javascript">
function fnHighlight(area) {
	var div=$("#cw"+area);
        div.css("background-color", "#ffffdd");
}
function fnNoHighlight(area) {
	var div=$("#cw"+area);
        div.css("background-color", "#ffffff");
}
function setContactForCoworker(){
  $("#do_contact_sharing__eventShareContactsMultiple").submit();
}
</script>
<div class="content">
    <table class="main">
        <tr>
            <td class="main_left">
                <div class="col_pad_25">
                   <div class="sidebox1a"><div class="sidebox1b"><div class="sidebox1c">
		       <a href="invoice_add.php"><?php echo _('Add Invoice');?></a> <br />
                       <a href="quote_add.php"><?php echo _('Add Quote');?></a> <br />
                       <a href="payment_add.php"><?php echo _('Add Payment');?></a> <br />
                    </div></div></div>
                </div>
            </td>
            <td class="main_right">
                <div class="mainheader">
                    <div class="pad20">
                        <span class="headline14"><?php echo _('Add Payment');?></span>
                    </div>
                </div>

                <div class="contentfull">
                    Coming Soon!   
                 </div> 
                      <div class="solidline"></div>
                
                    <div class="bottompad40"></div>
                </div>
            </td>
        </tr>
    </table>
</div>
 

</body>
</html>

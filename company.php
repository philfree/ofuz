<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Company detail';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
    if (is_object($_SESSION["eDetail_company"])) {
        $idcompany = $_SESSION["eDetail_company"]->getparam("idcompany");
    }elseif (isset($_GET['id']) && !isset($idcompany))  {
        $idcompany = $_GET['id'];
    } elseif(is_object($_SESSION['CompanyEditSave'])) {
        $idcompany = $_SESSION['CompanyEditSave']->idcompany;
    }

    $do_contact = new Contact($GLOBALS['conx']);
    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
?>
<script type="text/javascript">
    //<![CDATA[
    function showOpt(){
        $("#notes_options").show(0);
    }
    function showFullNote(idnote){
        $.ajax({
            type: "GET",
<?php
$e_CompanyNote = new Event("ContactNotes->eventAjaxGetCompanyNote");
$e_CompanyNote->setEventControler("ajax_evctl.php");
$e_CompanyNote->setSecure(false);
?>
            url: "<?php echo $e_CompanyNote->getUrl(); ?>",
            data: "idnote="+idnote,
            success: function(notetext){
                $("#notepreview"+idnote)[0].innerHTML = notetext;
            }
        });
    }
    $(document).ready(function() {
    	$("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
    });
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        <div class="left_text_links">
            <a href="/contact_add.php"><?php echo _('Add a new contact');?></a>
        </div><br />
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('Company Information'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
                    <?php
                          $do_company->getCompanyDetails($idcompany);
                          $do_company->setBreadcrumb();
                          $do_company->sessionPersistent("CompanyEditSave", "logout.php", OFUZ_TTL);
                          $compadd = $_SESSION['CompanyEditSave']->getChildCompanyAddress();

								if($compadd->getNumRows()){
								echo "<b>"._('Address')."</b><br />";
								
								while($compadd->next()){
									echo _('Address :').$compadd->address.'<br />';
									echo _('Type :').$compadd->address_type.'<br />';
									echo _('Street :').$compadd->street.'<br />';
									echo _('City :').$compadd->city.'<br />';
									echo _('State :').$compadd->state.'<br />';
									echo _('Country :').$compadd->country.'<br />';
									echo '<br />';
								}
								}
	
								$CompanyPhone = $_SESSION['CompanyEditSave']->getChildCompanyPhone();
								if($CompanyPhone->getNumRows()){
								echo "<b>"._('Phone')."</b><br />";
								
								while($CompanyPhone->next()){
									echo $CompanyPhone->phone_type;
									echo ': '.$CompanyPhone->phone_number;
									echo '<br />';
								}
								}
	
								$CompanyWebsite = $_SESSION['CompanyEditSave']->getChildCompanyWebsite();
								if($CompanyWebsite->getNumRows()){
								echo "<b>"._('Website')."</b><br />";
								
								while($CompanyWebsite->next()){
									echo $CompanyWebsite->website_type;
									echo ': '.$_SESSION['CompanyEditSave']->formatTextDisplayWithStyle($CompanyWebsite->website);
									echo '<br />';
								}
								}


                    ?>
            </div>
        </div>
        <div class="left_menu_footer"></div>
        <br /><br />
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('People in this Company'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
                        <?php 
                              $do_comp_cont = new Contact();
                              $do_comp_cont->getCompanyRelatedContacts($idcompany);

                              if($do_comp_cont->getNumRows()){
                                    while($do_comp_cont->next()){
                                      $currentpage = $_SERVER['PHP_SELF'];
                                      //$e_detail = new Event("mydb.gotoPage");
                                      //$e_detail->addParam("goto", "contact.php");
                                      //$e_detail->addParam("idcontact",$do_comp_cont->idcontact);
                                      //$e_detail->addParam("tablename", "contact");
                                      //$e_detail->requestSave("eDetail_contact", $currentpage);
                                      
                                      $id_shared_owner = $do_comp_cont->isContactRelatedToUser($do_comp_cont->idcontact)
                                      
                                    ?>
                                      
                                      <span class="contact_name">
                                      <?php 
                                            if($id_shared_owner){
                                      ?>
                                      <a href="/Contact/<?php echo $do_comp_cont->idcontact;?>"><?php echo $do_comp_cont->firstname;?>&nbsp;<?php echo $do_comp_cont->lastname;?></a></span><br />
                                      <?php
                                        }else{
                                      ?>
                                       <?php echo $do_comp_cont->firstname;?>&nbsp;<?php echo $do_comp_cont->lastname;?></span><br />
                                      <?php }
                                            if ($do_comp_cont->phone_number != ''){?>
                                        <?php echo $do_comp_cont->phone_number;?><br/>
                                      <?php }?>
                                       <?php if ($do_comp_cont->email_address != ''){?>
                                        <a style="color:orange;" href="mailto:<?php echo $do_comp_cont->email_address;?>"><?php echo $do_comp_cont->email_address;?></a><br/>
                                      <?php
                                         }
                                        if($do_comp_cont->website != ''){ ?>
                                          <!--<a style="color:orange;" href="<?php echo $do_comp_cont->website;?>">-->
                                              <?php echo $do_comp_cont->formatTextDisplayWithStyle($do_comp_cont->website);?>
                                          <!--</a>-->
                                           
                                          <br/>
                                      <?php
                                        }
                                      ?>
                                     <?php
                                      echo '<br />' ;
                                    }
                              }
                         ?>
            </div>
        </div>
        <div class="left_menu_footer"></div>
        <?php
            if (!is_object($_SESSION['do_invoice_list'])) { // Set the same as in the invoice.php page.
                                    $do_invoice_list = new Invoice();
                                    $do_invoice_list->sessionPersistent("do_invoice_list", "index.php", OFUZ_TTL);
                                    
                                }
				$user_settings = $_SESSION['do_User']->getChildUserSettings();
				if($user_settings->getNumRows()){
					while($user_settings->next()){
						if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
							$currency =  explode("-",$user_settings->setting_value) ;
                                                        $_SESSION['do_invoice_list']->currency_iso_code = $currency[0];
                                                        $_SESSION['do_invoice_list']->currency_sign = $currency[1];
                                                        //$_SESSION['do_invoice_list']->currency = $_SESSION['do_invoice_list']->currecy_sign ;
                                                        $_SESSION['do_invoice_list']->setCurrencyDisplay() ;
                                                        $_SESSION['do_invoice_list']->getCurrencyPostion() ;
						}
					}
				}
                                $e_filter_inv  = new Event("do_invoice_list->eventFilterInvoice");
                                $e_filter_inv->addParam("type","Company");
                                $e_filter_inv->addParam("idcompany",$idcompany);
                                $e_filter_inv->addParam("goto", "invoices.php"); 
                                $e_filter_inv->setLevel(10);
         if ($_SESSION['do_invoice_list']->hasInvoicesForEntity($idcompany,'Company')) { 
        ?>
        <div class="left_menu_header">
            <div class="left_menu_header_content">Invoices</div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
				<table width="100%">
            <?php
				
                                $_SESSION['do_invoice_list']->getInvoiceTotals($e_filter_inv,0,$idcompany);
             ?>
				</table>
            </div>
        </div>
        <div class="left_menu_footer"></div>
    	<div class="spacerblock_20"></div>
      <?php }  ?>
    </td><td class="layout_rcolumn">
        <div class="contacts_top">
            <?php
                //$do_contact->getContactDetails($idcontact);
                //if ($do_contact->getNumRows()) {
                    //$do_contact->next();
                //}
                $do_company->getCompanyDetails($idcompany);
                $do_company->sessionPersistent("CompanyEditSave", "logout.php", OFUZ_TTL);
             ?>
            <div class="pad20">
                <span class="headline14"><?php echo $do_company->name; ?></span>
            </div>
            <span class="headerlinks"><a href="company_edit.php"><?php echo _('Edit this Company');?></a></span>
        </div>
        <div class="contentfull">
            <div class="headline_fuscia"><?php echo _('Add a Note About ').$do_company->name; ?></div>
            <div class="percent95">
            <?php
                $ContactNotes  = new ContactNotes($GLOBALS['conx']);
                $ContactNotes->sessionPersistent("ContactNoteEditSave", "company.php", 300);
                $_SESSION['ContactNoteEditSave']->idcompany = $idcompany;
                $return_page = $_SERVER['PHP_SELF'];
                $e_addContactNote = $_SESSION['ContactNoteEditSave']->newForm("ContactNoteEditSave->eventAdd");
                $e_addContactNote->setLevel(123);
                $e_addContactNote->setGotFile(true);
                $e_addContactNote->addEventAction("mydb.gotoPage", 90);
                $e_addContactNote->addEventAction("ContactNoteEditSave->eventFormatNoteInsert", 119);
                $e_addContactNote->addParam("goto", $return_page);
                $e_addContactNote->addParam("errPage", $_SERVER['PHP_SELF']);
                $noteFields = new FieldsForm("ofuz_add_company_note");

                // form headers:
                echo $e_addContactNote->getFormHeader();
                echo $e_addContactNote->getFormEvent();

                // display the note text field:
                //echo $noteFields->idcontact;
                //echo $noteFields->date_added;
                echo $noteFields->note;
                echo $noteFields->iduser;
             ?>
            <span id="more_options"><a href="#" onclick="showOpt(); return false;">&#9654; <?php echo _('more options');?></a></span>
            <div class="div_right">
                <div id="notes_options" style="display:none;">
                    <?php echo _('Attach a file: ');?><?php echo $noteFields->document; ?> 
                    <br/>
                    <?php echo _('When this happened: ')?><?php echo $noteFields->date_added; ?>
                </div>
            </div>
            <div class="div_right">
                <?php echo $e_addContactNote->getFormFooter(_('Add this note')); ?>
            </div>
           <?php
           // print_r($_SESSION['NoteDeleted']->delete_note_array);
            $deleted_note = $do_notes->getNotesDataFromDeleted($idcompany,"company");
            if(is_array($deleted_note) && count($deleted_note) > 0 ){
                   echo $_SESSION['ContactNotesEditSave']->viewDeletedNote($deleted_note,"ContactNote"); 
            }
        ?>
            <?php
                $do_notes->getCompanyNotes($idcompany);
                $do_notes->sessionPersistent("ContactNotesEditSave", "logout.php", OFUZ_TTL);
                if ($do_notes->getNumRows()) {
                    echo '<div class="headline_fuscia">', $do_company->name, '\'s Notes:</div>', "\n";
                    $note_count = 0;
                    while ($do_notes->next()) {
                        $file = '';
                        $preview_note = '';
                        $contact_name = '';
                        if($do_notes->idcontact){
                            $do_contact->idcontact = $do_notes->idcontact;
                            $contact_url = $do_contact->getContactUrl();
                            $contact_name = $do_contact->getContactFullName();
                            $contact_url = _(' on ').'<a href="'.$contact_url.'">'.$contact_name.'</a>'; 
                            //$contact_url .= ' on '.$contact_url;
                        }else{ $contact_url = ''; }
                        if($do_notes->document != ''){
                            $doc_name = $do_notes->document;
                            $doc_name = str_replace("  ","%20%20",$do_notes->document);
                            $doc_name = str_replace(" ","%20",$doc_name);
                            $file_url = "/files/".$doc_name;
                            //$file_url = "files/".$do_notes->document;
                            $file = '<br /><a href="'.$file_url.'" target="_blank">'.$do_notes->document.'</a>';
                        }
                        $note_text = $do_notes->note;
                        $note_text = $do_notes->formatNoteDisplayShort($note_text,400);
                        if (substr_count($note_text, '<br />') > 4) {
                        	$preview_note = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$note_text)).' ...';
                        } else if (strlen($note_text) > 500) {
                            $preview_note = substr($note_text, 0, 500).' ...';
                        }
                        $added_by = $_SESSION['do_User']->getFullName($do_notes->iduser);
                        $e_PrioritySort = new Event('ContactNotes->eventPrioritySortNotes');
                        $e_PrioritySort->addParam('goto', 'company.php');
                        $e_PrioritySort->addParam('idnote', $do_notes->idcontact_note);
                        
                        $star_img_url = '<img src="/images/'.($do_notes->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="" />';
                        if (is_object($_SESSION["ContactNotesEditSave"])) {
                            $e_note_del = new Event("ContactNotesEditSave->eventTempDelNoteById");
                        }
                        $e_note_del->addParam("goto", "company.php");
                        $e_note_del->addParam("id", $do_notes->idcontact_note);
                        $e_note_del->addParam("context", "ContactNote");
                        $del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';
                        echo '<div id="notetext', $note_count, '" class="vpad10">';
                        echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/note_icon.gif" class="note_icon" width="16" height="16" alt="" />',$e_PrioritySort->getLink($star_img_url);
                        echo '<b>'.$dis.'&nbsp;'.date('l, F j', strtotime($do_notes->date_added)).'</b>&nbsp;(Added By :&nbsp;'.$added_by.$contact_url.')</div> 
                        <div id="trashcan', $note_count++, '" class="deletenote" style="right:0;">'.$e_note_del->getLink($del_img_url).'</div></div>';
                        if ($do_notes->is_truncated) {
                            echo '<div id="notepreview',$do_notes->idcontact_note,'">',$note_text,'<br /><br /><a href="#" onclick="showFullNote(',$do_notes->idcontact_note,'); return false;" />'._('more ...').'</a><br /></div>';
                        } else {
                            echo $note_text;
                        }  
                        echo $file.'</div>';
                    }
                }
            ?>
            </div>
            
            <div class="spacerblock_20"></div>
            <div class="solidline"></div>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>

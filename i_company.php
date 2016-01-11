<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Contact detail';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/i_header.inc.php');

    if (is_object($_SESSION["eDetail_company"])) {
        $idcompany = $_SESSION["eDetail_company"]->getparam("idcompany");
    }elseif (isset($_GET['id']) && !isset($idcompany))  {
        $idcompany = $_GET['id'];
    } elseif(is_object($_SESSION['ContactEditSave'])) {
        $idcompany = $_SESSION['ContactEditSave']->idcompany;
    }
    $do_contact = new Contact($GLOBALS['conx']);
    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
?>
<?php $thistab = 'Contacts'; include_once('i_ofuz_navtabs.php'); ?>
<script>
 function showOpt(){
        document.getElementById('notes_options').style.display = "block";
    }
</script>
<div class="mobile_main main">
  <div class="mainheader">
                <?php
                    //$do_contact->getContactDetails($idcontact);
                    //if ($do_contact->getNumRows()) {
                      //  $do_contact->next();
                    //}
                     $do_company->getCompanyDetails($idcompany);
                     $do_company->sessionPersistent("CompanyEditSave", "company.php", 3600);
                ?>
                <div class="mobile_head_pad5">
                       <span class="headline14"><?php echo $do_company->name; ?></span>
                </div>
                <div class="mobile_head_pad5">
                    <?php
                      // Company Info
                      echo '<b>Company Information</b><br />' ;
                      $do_company->getCompanyDetails($idcompany);
                      $do_company->sessionPersistent("CompanyEditSave", "company.php", 3600);
                      $compadd = $_SESSION['CompanyEditSave']->getChildCompanyAddress();
                        if($compadd->getNumRows()){
                          echo "<b>Address</b><br />";
                          
                          while($compadd->next()){
                              echo 'Address :'.$compadd->address.'<br />';
                              echo 'Type :'.$compadd->address_type.'<br />';
                              echo 'Street :'.$compadd->street.'<br />';
                              echo 'City :'.$compadd->city.'<br />';
                              echo 'State :'.$compadd->state.'<br />';
                              echo 'Country :'.$compadd->country.'<br />';
                              echo '<br />';
                          }
                        }

                          $CompanyPhone = $_SESSION['CompanyEditSave']->getChildCompanyPhone();
                          if($CompanyPhone->getNumRows()){
                          echo "<b>Phone</b><br />";
                          
                          while($CompanyPhone->next()){
                              echo $CompanyPhone->phone_type;
                              echo ': '.$CompanyPhone->phone_number;
                              echo '<br />';
                          }
                        }

                          $CompanyWebsite = $_SESSION['CompanyEditSave']->getChildCompanyWebsite();
                          if($CompanyWebsite->getNumRows()){
                          echo "<b>Website</b><br />";
                          
                          while($CompanyWebsite->next()){
                              echo $CompanyWebsite->website_type;
                              echo ': '.$_SESSION['CompanyEditSave']->formatTextDisplayWithStyle($CompanyWebsite->website);
                              echo '<br />';
                          }
                        }
                        echo '<div class="solidline"></div>';
                        //Company info ends here
                        $do_comp_cont = new Contact();
                        $do_comp_cont->getCompanyRelatedContacts($idcompany);
                        if($do_comp_cont->getNumRows()){
                              echo '<b>People in this Company</b><br />';
                              while($do_comp_cont->next()){
                                $currentpage = $_SERVER['PHP_SELF'];
                                $e_detail = new Event("mydb.gotoPage");
                                $e_detail->addParam("goto", "i_contact.php");
                                $e_detail->addParam("idcontact",$do_comp_cont->idcontact);
                                $e_detail->addParam("tablename", "contact");
                                $e_detail->requestSave("eDetail_contact", $currentpage);
                              ?>
                                
                                <span class="contact_name"><a href="<?php echo $e_detail->getUrl(); ?>"><?php echo $do_comp_cont->firstname;?>&nbsp;<?php echo $do_comp_cont->lastname;?></a></span><br />
                                <?php if ($do_comp_cont->phone_number != ''){?>
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
                    <div class="solidline"></div>
                    <div class="bottompad40"></div>

 <?php $mobile_local_bottom_nav_links = '<div align="right" style="right:3px;" class="navtab"><div class="navtab_text"><a href="i_company_edit.php">Edit</a></div></div>'; 
 include_once('i_ofuz_logout.php'); ?>
</div>
</body>
</html>

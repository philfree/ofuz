<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Invoices';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    $invoice_access = true;
    if($_SESSION['do_invoice']->discount){
      $dis = $_SESSION['do_invoice']->discount;
    }else{$dis = "";}
   
    $do_user_detail = new User();
    $do_user_detail->getId($_SESSION['do_invoice']->iduser);
    $user_settings = $do_user_detail->getChildUserSettings();    
    if($user_settings->getNumRows()){
        while($user_settings->next()){
            if($user_settings->setting_name == 'invoice_logo' &&  $user_settings->setting_value != ''){
                $_SESSION['do_invoice']->inv_logo =  $user_settings->setting_value ;
            }
             if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
                $currency =  explode("-",$user_settings->setting_value) ;
                $_SESSION['do_invoice']->currency_iso_code = $currency[0];
                $_SESSION['do_invoice']->currency_sign = $currency[1];
                $_SESSION['do_invoice']->setCurrencyDisplay() ;
                $_SESSION['do_invoice']->getCurrencyPostion() ;
            }
            if($user_settings->setting_name == 'inv_date_format' &&  $user_settings->setting_value != ''){
                $_SESSION['do_invoice']->inv_dd_format = $user_settings->setting_value;
	    }
        }
    }
    
?>
<div align="center">
             <div style="width:800px;margin-left:0px;margin-top:3px;position: relative;border-style: solid;border-width:thin;border-color:#C0C0C0;">
                <div style="width:auto;height:100px;text-align:center;position: relative;margin-left:20px;margin-right:20px;">
                <?php
                      if($_SESSION['do_invoice']->status == 'Paid'){
                          $color = "#009900";
                      }elseif($_SESSION['do_invoice']->status == 'Partial'){
                          $color = "#EA8484";
                      }elseif($_SESSION['do_invoice']->status == 'Sent'){
                          $color = "#677CDF";
                      }
                      else{  
                          $color ="#000000";
                      }
                   ?>
                    <div style="float:left;width:450px;position: relative;margin-top:4px;text-align:left;font-size:12px; ">
                      <?php
                          if( $_SESSION['do_invoice']->inv_logo == ''){
                            //echo '<a href="/settings_invoice.php">Upload Logo</a>';
                          }else{
                            echo '<img src="/files/'.$_SESSION['do_invoice']->inv_logo.'" /><br />';
                          }
                      ?>
					 <table>
						 <tr>
							 <td valign="top"> 
							 <?php
									  echo '<br /><b>'._('From : ').'</b><br />';
									  echo  $_SESSION['do_invoice']->Only1br($_SESSION['do_invoice']->getInvoiceSenderAddr($do_user_detail));
							  ?> 							 
							 </td>
							 <td valign="top"> 
						                           <?php
                              echo '<br /><b>'._('To :').'</b> <br />';
                              //echo nl2br($_SESSION['do_invoice']->invoice_address);
                              echo $_SESSION['do_invoice']->Only1br($_SESSION['do_invoice']->invoice_address); 
                              
                          ?> 
							 </td>
						 </tr>
					 </table>
	
                    </div> 
                    <div style="float:left;auto;position:relative;margin-left:150px;margin-top:4px;">
                      <?php 
                                        if($_SESSION['do_invoice']->status == 'Quote'){
                                                    echo _('Q U O T E');
                                        }else{
                                                    echo _('I N V O I C E');
                                        }
                      ?>
                    </div> 
                     <div style="float:right;width:100px;margin-top:4px;text-align:center;color:white;background-color:white;">

                    </div>
                </div>
				<!-- was used to display the To: 
				<div style="text-align:left;left: 0; top: 0; margin-top:50px;font-size:12px;">
  
                    </div> 
					--->
               <div style="width:auto;height:140px;position:relative;margin-left:20px;margin-right:12px;">

                    <div style="width:300px;position: absolute; right: 0; margin-right:10px;top: 0;">
                         <table width="100%" style="border-collapse: collapse;font-size:12px;">
                            <tr style="border: 1px solid black;">
                                <td width="50%" style="background-color:#CCCCCC;text-align:left;border: thin;font-size:12px; ">
                                      <?php   
                                            if($_SESSION['do_invoice']->status == 'Quote'){
                                                echo _('Quote #');
                                            }else{
                                                echo _('Invoice #');
                                            }
                                        ?>
                                </td>
                                <td width="50%" style= "text-align:right;border: 1px solid black; font-size:12px;"><?php echo $_SESSION['do_invoice']->num; ?></td>
                            </tr>
                            <tr style="border: 1px solid black;">
                                <td width="50%" style="background-color:#CCCCCC;text-align:left;border: thin;font-size:12px; "><?php echo _('Date Created')?></td>
                                <td width="50%" style= "text-align:right;border: 1px solid black; font-size:12px;">
				  <?php
				    //echo date("F j ,Y",strtotime($_SESSION['do_invoice']->datecreated));
				    echo $_SESSION['do_invoice']->getInvFormattedDate($_SESSION['do_invoice']->datecreated);
				  ?>
				</td>
                            </tr>
                            <tr style="border: 1px solid black;">
                                <td width="50%" style="background-color:#CCCCCC;text-align:left;border: thin; font-size:12px;"><?php echo _('Due Date')?></td>
                                <td width="50%" style= "text-align:right;border: 1px solid black; font-size:12px;">
				  <?php
				    //echo date("F j ,Y",strtotime($_SESSION['do_invoice']->due_date));
				    echo $_SESSION['do_invoice']->getInvFormattedDate($_SESSION['do_invoice']->due_date);
				  ?>
				</td>
                            </tr>
                            <tr style="border: 1px solid black;">
                                <td width="50%" style="background-color:#CCCCCC;text-align:left;border: thin;font-size:12px; "><?php echo _('Amount Due')?></td>
                                <td width="50%" style= "text-align:right;border: 1px solid black;font-size:12px; ">
                                    <?php
                                        echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due);
                                      ?>
                                </td>
                            </tr>
                            <tr style="border: 1px solid black;">
                                <td width="50%" style="background-color:#CCCCCC;text-align:left;border: thin;font-size:12px; "><?php echo _('Terms')?></td>
                                <td width="50%" style= "text-align:right;border: 1px solid black; font-size:12px;"><?php echo $_SESSION['do_invoice']->invoice_term; ?></td>
                            </tr>
                         </table>
                    </div>
               </div> 
               <div style="position: relative;margin-right:20px; margin-left:20px;text-align:left;">
                  <?php echo nl2br($_SESSION['do_invoice']->description); ?>
               </div>
                <div style="position: relative;margin-right:20px; margin-left:20px;margin-top:15px;">
                      <table width="100%" style="border-collapse: collapse;font-size:12px;">
                          <tr style="border: 1px solid black;">
                              <td  width="20%" style="background-color:#CCCCCC;text-align:center;border: 1px solid black;font-size:12px;"><b><?php echo _('Item')?></b></td>
                              <td width="40%" style="background-color:#CCCCCC;text-align:center;border:  1px solid black; font-size:12px;"><b><?php echo _('Description')?></b></td>
                              <td width="10%" style="background-color:#CCCCCC;text-align:center;border:  1px solid black; font-size:12px;"><b><?php echo _('Price')?></b></td>
                              <td width="10%" style="background-color:#CCCCCC;text-align:center;border:  1px solid black; font-size:12px;"><b><?php echo _('Quantity')?></b></td>
                              <?php
                                    $show_tax_amout = false ; 
                                    if($_SESSION['do_invoice']->getTotalLineTax($_SESSION['do_invoice']->idinvoice)){
                                    $show_tax_amout = true;
                              ?>
                              <td width="10%" style="background-color:#CCCCCC;text-align:center;border:  1px solid black; font-size:12px;"><b><?php echo _('Tax(%)')?></b></td>
                              <?php } ?>
                              <td width="20%" style="background-color:#CCCCCC;text-align:center;border:  1px solid black; font-size:12px;"><b><?php echo _('Total')?></b></td>
                          </tr>
                      </table>
                    </div>
                  <div style="position: relative;margin-left:19px;margin-right:20px; margin-top:0px;font-size:12px;border-style: solid;border-width:thin;border-color:black;">
                    <div style="position: relative; margin-top:5px;">
                          <table width="100%" style="font-size:12px;">
                      <?php
                          $do_invoice_line = $_SESSION['do_invoice']->getChildinvoiceline();
                          $price = 0;
                          while($do_invoice_line->next()){ 
                        ?>
                            <tr>
                                 <td  width="20%" style="text-align:left;font-size:12px;">
                                      <?php  echo $do_invoice_line->item;?>
                                 </td>
                                 <td  width="40%" style="text-align:celeftnter;font-size:12px;">
                                      <?php  echo nl2br($do_invoice_line->description);?>
                                 </td>
                                <td  width="10%" style="text-align:center;font-size:12px;">
                                      <?php  
                                              echo $_SESSION['do_invoice']->viewAmount($do_invoice_line->price);
                                      ?>
                                 </td>
                                <td  width="10%" style="text-align:center;font-size:12px;">
                                      <?php  echo $do_invoice_line->qty;?>
                                 </td>
                                <?php if($show_tax_amout === true ){ ?>
                                <td  width="10%" style="text-align:center;font-size:12px;">
                                      <?php  echo $do_invoice_line->line_tax;?>
                                 </td>
                                <?php } ?>
                                <td  width="20%" style="text-align:right;font-size:12px;">
                                      <?php  
                                         $line_sub_tot = floatval($do_invoice_line->qty*$do_invoice_line->price);
                                  //echo $line_sub_tot;
                                          echo $_SESSION['do_invoice']->viewAmount($line_sub_tot); 
                                        ?>
                                </td>
                           </tr>
                      <?php } ?>
                         </table> 
                    </div>
                   
                 </div>
                 <div style="position: relative;margin-left:19px;margin-right:20px; margin-top:5px;font-size:12px;border-style: solid;border-width:thin;border-color:black;">
                      <div style="position: relative;font-size:12px;margin-top:0px;">
                        <table width="100%" style="border-collapse: collapse;font-size:12px;">
                          <tr style="border: 1px solid black;">
                              <td width="50%" style="border: 1px solid black;text-align:left;font-size:12px;">
                                <?php  //echo nl2br($_SESSION['do_invoice']->description); ?>
                              </td>
                              <td width="50%" style="border: 1px solid black;font-size:12px;">
                                  <table width="100%" style="border-collapse: collapse;font-size:12px;">
                                        <tr>
                                            <td width="50%" style="text-align:right;font-size:12px; " >
                                                <?php echo '<b>'._('Subtotal:').'</b>'; ?>
                                            </td>
                                            <td width="50%" style="text-align:right;font-size:12px;">
                                              <?php
                                                  //echo '<b> '.number_format($invoice_cal_data["line_total"],2, '.', ',' ).'</b>';
                                                //echo '<b> '.$_SESSION['do_invoice']->currecy_iso_code.' '.$_SESSION['do_invoice']->currecy_sign.' '.number_format($_SESSION['do_invoice']->sub_total,2, '.', ',' ).'</b>';
                                                  echo '<b>'.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->sub_total).'</b>';
                                              ?>
                                            </td>  
                                        </tr>
                                         <?php
                                            if($_SESSION['do_invoice']->discount != ''){
                                          ?>
                                         <tr>
                                            <td width="50%" style="text-align:right;font-size:12px; " >
                                                <?php echo _('Discount -').$_SESSION['do_invoice']->discount.'% :'; ?>
                                            </td>
                                            <td width="50%" style="text-align:right;font-size:12px;">
                                              <?php
                                                  
                                                 echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->total_discounted_amt);
                                              ?>
                                            </td>  
                                        </tr>
                                         <?php } ?>
                                        <?php 
                                            if($_SESSION['do_invoice']->total_taxed_amount > 0 ){
                                        ?>
                                        <tr>
                                            <td width="50%" style="text-align:right;font-size:12px; " >
                                                <?php echo _('Tax + :'); ?>
                                            </td>
                                            <td width="50%" style="text-align:right;font-size:12px;">
                                              <?php
                                                 echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->total_taxed_amount);
                                              ?>
                                            </td>  
                                        </tr>
                                        <?php } ?>
                                        <tr  style="border: 1px solid black;">
                                            <td width="50%" style="text-align:right;font-size:12px; " >
                                                <?php echo '<b>'._('Total :').'</b>'; ?>
                                            </td>
                                            <td width="50%" style="text-align:right;font-size:12px;">
                                              <?php
                                                 //echo '<b> '.number_format($invoice_cal_data["total_after_discount"],2, '.', ',' ).'</b>';
                                                //echo '<b> '.$_SESSION['do_invoice']->currecy_iso_code.' '.$_SESSION['do_invoice']->currecy_sign.' '.number_format($_SESSION['do_invoice']->net_total,2, '.', ',' ).'</b>';
                                                echo '<b> '.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->net_total).'</b>';
                                              ?>
                                            </td>  
                                        </tr>
                                        <tr  style="border: 1px solid black;">
                                            <td width="50%" style="text-align:right;font-size:12px; " >
                                                <?php echo '<b>'._('Amount Paid :').'</b>'; ?>
                                            </td>
                                            <td width="50%" style="text-align:right;font-size:12px;">
                                              <?php
                                                  //echo '<b>'.$_SESSION['do_invoice']->currecy_iso_code.' '.$_SESSION['do_invoice']->currecy_sign.' '. number_format($_SESSION['do_invoice']->amount,2, '.', ',' );
                                                  echo '<b> '.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amount).'</b>';
                                                     
                                              ?>
                                            </td>  
                                        </tr>
                                        <tr  style="border: 1px solid black;height:30px;">
                                            <td width="50%" style="text-align:right;font-size:12px;background-color:#CCCCCC;vertical-align:middle; " >
                                                <?php echo '<b>'._('Balance Due :').'</b>'; ?>
                                            </td>
                                            <td width="50%" style="text-align:right;font-size:12px;background-color:#CCCCCC;vertical-align:middle;">
                                              <?php
                                                 //echo '<b>$'. number_format($invoice_cal_data["total_due_amt"],2, '.', ',' ).'</b>';
                                                //echo '<b>'.$_SESSION['do_invoice']->currecy_iso_code.' '.$_SESSION['do_invoice']->currecy_sign.' '. number_format($_SESSION['do_invoice']->amt_due,2, '.', ',' ).'</b>';
                                                echo '<b> '.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due).'</b>';
                                              ?>
                                            </td>  
                                        </tr>
                                  </table>
                              </td>
                          </tr>
                         <tr >
                              <td colspan="2" style="text-align:center;border: 1px solid black;font-size:12px;">
                                    
                              </td>
                          </tr>
                         
                        </table>
                     </div>
                 </div>
                 <div style="position: relative;margin-left:19px;margin-right:20px; margin-top:10px;height:50px;">
                    
                 </div>
            </div>
        </div>
    </td></tr></table>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php //include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>

<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Page to list all the Expenses
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzPage
     * @license ##License##
     * @version 0.6
     * @date 2010-09-06
     * @since 0.4
     */

    include_once('config.php');
    $pageTitle = _('Expenses').' :: Ofuz ';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Ofuz Expenses';
    $Description = 'list and filter the Expenses ';
    $background_color = 'white';
 //   include_once('includes/ofuz_check_access.script.inc.php');
 //   include_once('includes/header.inc.php');

    if (!is_object($_SESSION['do_expense_list'])) {
        $do_expense_list = new Expense();
        $do_expense_list->sessionPersistent("do_expense_list", "index.php", OFUZ_TTL);
        
    }
//echo $_SESSION['do_invoice_list']->getSqlQuery();
    $user_settings = $_SESSION['do_User']->getChildUserSettings();    
    if($user_settings->getNumRows()){
        while($user_settings->next()){
            if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
                $currency =  explode("-",$user_settings->setting_value) ;
                $_SESSION['do_expense_list']->currency_iso_code = $currency[0];
                $_SESSION['do_expense_list']->currency_sign = $currency[1];

                $_SESSION['do_expense_list']->setCurrencyDisplay() ;
                $_SESSION['do_expense_list']->getCurrencyPostion() ;
            }
            if($user_settings->setting_name == 'inv_date_format' &&  $user_settings->setting_value != ''){
                $_SESSION['do_expense_list']->inv_dd_format = $user_settings->setting_value;
	    }
        }
    }
    
?>
<script type="text/javascript">
//<![CDATA[

<?php include_once('includes/ofuz_js.inc.php'); ?>

$(document).ready(function() {
    $(".invoice_list_row").hover(
        function (){$(this).css('background-color','#edf6f7');},
        function (){$(this).css('background-color','#ffffff');}
    );

});

function hideTotals(){

    $(".layout_lcolumn").hide(0);
    $("#totals_txt").show(0);

    $.ajax({
        type: "GET",
	<?php
	$e_hide = new Event("do_invoice_list->eventHideTotal");
	$e_hide->setEventControler("ajax_evctl.php");
	$e_hide->setSecure(false);
	?>
        url: "<?php echo $e_hide->getUrl(); ?>",
        success: function(hide_inv){ 
        }
    });
}

function showTotals(){
    $(".layout_lcolumn").show(0);
    $("#totals_txt").hide(0);
    $.ajax({
        type: "GET",
	<?php
	$e_show = new Event("do_invoice_list->eventShowTotal");
	$e_show->setEventControler("ajax_evctl.php");
	$e_show->setSecure(false);
	?>
        url: "<?php echo $e_show->getUrl(); ?>",
        success: function(hide_inv){ 
        }
    });
}

function showExtraAmt(){
  $("#extra_amt").slideToggle("slow");
}

function showPastDue(){
    $.ajax({
        type: "GET",
<?php
$e_show_past_due = new Event("do_invoice_list->eventShowPastDue");
$e_show_past_due->setEventControler("ajax_evctl.php");
$e_show_past_due->setSecure(false);
?>
        url: "<?php echo $e_show_past_due->getUrl(); ?>",
        data: "a=1",
        success: function(html_data){ 
           $("#past_due_txt").hide("fast");
           $("#inv_msgs").hide("fast");
           $("#show_past_due")[0].innerHTML = html_data;
        }
    });
}

//]]>
</script>
       
           <div class="banner60_mid text14">
              <?php
                    if($_SESSION['do_expense_list']->from_expense_page === true){
              ?>
              <!--Invoice Menu -->
               <span class="fuscia_text text16"><?php echo _('Showing:'); ?></span> &nbsp; &nbsp;
               <?php
                    $e_filter_exp  = new Event("do_expense_list->eventSetFilter");
                    $e_filter_exp->setLevel(10);
                                        
                 
                  echo '&nbsp; &nbsp; ';
                  $e_filter_exp->addParam("type","date");
                  $e_filter_exp->addParam("goto", $_PHP['SELF']); 
                  
                  echo '<form id="setFilterInvMonth" name="setFilterInvMonth" method="post" action="/eventcontroler.php" style="display:inline;">';
                  echo $e_filter_inv->getFormEvent();
                  echo $_SESSION['do_invoice_list']->getYearDropDownFilter();
                  echo '&nbsp; &nbsp; ';
                  echo $_SESSION['do_invoice_list']->getMonthDropDownFilter();
                  echo '</form>';
               ?>
               <?php
                   }else{
                       $e_filter_inv  = new Event("do_invoice_list->eventUnsetFilter");
                       $e_filter_inv->setLevel(10);
                       $e_filter_inv->addParam("goto",$_SERVER['PHP_SELF']);
                       echo $e_filter_inv->getLink(_("View all invoices"));
                       echo '&nbsp; &nbsp; ';
                  }
               ?>
 
            </div>
           
            
            <table class="invoice_list">
                <tr>
                    <th class="invoice_list_12pct center_text"><?php echo _('Expense #');?></th>
                    <th class="invoice_list_40pct left_text"><?php echo _('Description');?></th>
                    <th class="invoice_list_12pct center_text"><?php echo _('Date Paid');?></th>
                     <!--
                    <th class="invoice_list_12pct center_text"><?php echo _('Total Paid');?></th>
                    <th class="invoice_list_12pct center_text"><?php echo _('DE');?></th>-->
                    <th class="invoice_list_12pct center_text"><?php echo _('Amount');?></th>
                </tr>
                <?php
		  if(!$_SESSION['do_expense_list']->filter_set){
			$_SESSION['do_expense_list']->getAllExpense();
		   }else{
			$_SESSION['do_expense_list']->query($_SESSION['do_expense_list']->getSqlQuery());
		   }               
                  if($_SESSION['do_expense_list']->getNumRows() > 0 ){
                    $do_company = new Company();
                    while($_SESSION['do_expense_list']->next()){
                      $currentpage = $_SERVER['PHP_SELF'];
                ?>
                <tr class="invoice_list_row" onclick="window.location.href='<?php echo $cfg_plugin_expense_path."/All/".$_SESSION['do_expense_list']->idexpense ?>'" title="<?php echo str_replace('"', "'", $_SESSION['do_expense_list']->description); ?>">
                    <td class="center_text"><?php echo $_SESSION['do_expense_list']->num; ?></td>
                    <td class="left_text"><?php echo $_SESSION['do_expense_list']->description ?></td>
                    <td class="center_text">
		      <?php
			echo $_SESSION['do_expense_list']->getFormattedDate($_SESSION['do_expense_list']->date_paid);
			echo $inv_formatted_date;
		      ?>
                    </td>
                    <td class="center_text"><?php echo $_SESSION['do_expense_list']->viewAmount($_SESSION['do_expense_list']->amount); ?></td>              
                </tr>
                <?php } } ?>
            </table>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>

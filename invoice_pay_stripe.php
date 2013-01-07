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
    //include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header_secure.inc.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    $invoice_access = true;

    if($invoice_access){
        if (!is_object($_SESSION['do_invoice'])) {
            echo _('Your page session has been expired. Please go back to the Invoice page and try again.');
            exit();
        }
        $do_company = new Company();
        if($_SESSION['do_invoice']->discount){
          $dis = $_SESSION['do_invoice']->discount;
        }else{$dis = "";}
    }

	if($_SESSION['updatecustomer'] <> 'Yes'){
	$idcontact = $_SESSION['do_invoice']->idcontact;
	$stripe_customer_id = $_SESSION['do_invoice']->getStripeCustomerId($_SESSION['do_invoice']->iduser,$idcontact);
	}
	$_SESSION['updatecustomer'] = '';
	//echo $stripe_customer_id;die();
	
	
if(empty($stripe_customer_id)){
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
        <script type="text/javascript" src="https://js.stripe.com/v1/"></script>
        <script type="text/javascript">
          Stripe.setPublishableKey("<?php echo $_SESSION['do_invoice']->stripe_publish_key;?>");
            $(document).ready(function() {
                function addInputNames() {
                    // Not ideal, but jQuery's validate plugin requires fields to have names
                    // so we add them at the last possible minute, in case any javascript 
                    // exceptions have caused other parts of the script to fail.
                    $(".card-number").attr("name", "card-number")
                    $(".card-cvc").attr("name", "card-cvc")
                    $(".card-expiry-year").attr("name", "card-expiry-year")
                }

                function removeInputNames() {
                    $(".card-number").removeAttr("name")
                    $(".card-cvc").removeAttr("name")
                    $(".card-expiry-year").removeAttr("name")
                }

                function submit(form) {
                    // remove the input field names for security
                    // we do this *before* anything else which might throw an exception
                    removeInputNames(); // THIS IS IMPORTANT!

                    // given a valid form, submit the payment details to stripe
                    $(form['submit-button']).attr("disabled", "disabled")

                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(), 
                        exp_year: $('.card-expiry-year').val()
                    }, function(status, response) {
                        if (response.error) {
                            // re-enable the submit button
                            $(form['submit-button']).removeAttr("disabled")
        
                            // show the error
                            $(".payment-errors").html(response.error.message);

                            // we add these names back in so we can revalidate properly
                            addInputNames();
                        } else {
                            // token contains id, last4, and card type
                            var token = response['id'];

                            // insert the stripe token
                            var input = $("<input name='stripeToken' value='" + token + "' style='display:none;' />");
                            form.appendChild(input[0])
                            //alert(token);

                            // and submit
                            form.submit();
                        }
                    });
                    
                    return false;
                }
                
                // add custom rules for credit card validating
                jQuery.validator.addMethod("cardNumber", Stripe.validateCardNumber, "Please enter a valid card number");
                jQuery.validator.addMethod("cardCVC", Stripe.validateCVC, "Please enter a valid security code");
                jQuery.validator.addMethod("cardExpiry", function() {
                    return Stripe.validateExpiry($(".card-expiry-month").val(), 
                                                 $(".card-expiry-year").val())
                }, "Please enter a valid expiration");

                // We use the jQuery validate plugin to validate required params on submit
                $("#do_invoice__eventProcessStripePayment").validate({
                    submitHandler: submit,
                    rules: {
                        "card-cvc" : {
                            cardCVC: true,
                            required: true
                        },
                        "card-number" : {
                            cardNumber: true,
                            required: true
                        },
                        "card-expiry-year" : "cardExpiry" // we don't validate month separately
                    }
                });

                // adding the input field names is the last step, in case an earlier step errors                
                addInputNames();
            });
        </script><?php } //$do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php include_once('includes/ofuz_navtabs_invoice.php'); ?>
<?php //$do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php
          if(!$invoice_access){
              $msg = new Message(); 
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo $msg->getMessage("wrong_invoice_url");
              echo '</div></div><br /><br />';
              exit;
          }
    ?>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
    </td><td class="layout_rcolumn">
        <div class="contentfull">
			<?php 
                if($_SESSION['in_page_message'] != ''){
                    echo '<div style="margin-left:0px;">';
                    echo '<div class="messages_unauthorized">';
                    echo htmlentities($_SESSION['in_page_message']);
                    $_SESSION['in_page_message'] = '';
                    echo '</div></div><br /><br />';
                }
             ?>
			<div style="margin-left:0px;display:none;" id="msg_unauth"></div>
			<?php
					
				echo '<div style="margin-left:0px;">';
                echo '<div class="messages_unauthorized">';
				echo 'Clicking Below the Submit charge your credit card with the Total due Amount '.$_SESSION['do_invoice']->amt_due.'</div></div>';		
			
				 if(empty($stripe_customer_id)){	
				echo nl2br($_SESSION['do_invoice']->invoice_address);
				 //echo '<br />'. _('Total due :').'<b>$'. number_format($invoice_cal_data["total_due_amt"],2, '.', ',' ).'</b>';
				 echo '<br />'. _('Total due :').'<b>'. $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due).'</b>';
				 echo '<br /><br />';
				 $do_user_rel = new UserRelations();
				 $invoice_url = $GLOBALS['cfg_ofuz_site_http_base'].'inv/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idinvoice).'/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idcontact);
				 $do_payment = new Event("do_invoice->eventProcessStripePayment");
				 $do_payment->addParam("goto", $invoice_url); // send 0 if no CC else send 1
				 $do_payment->addParam("amt", $_SESSION['do_invoice']->amt_due); 
				 $do_payment->addParam("error_page", "invoice_pay_stripe.php");
				 $idcontact = $_SESSION['do_invoice']->idcontact;
				 $stripe_customer_id = $_SESSION['do_invoice']->getStripeCustomerId($_SESSION['do_invoice']->iduser,$idcontact);
				if(!empty($stripe_customer_id)){	
					$do_payment->addParam("stripecustomer_id", $stripe_customer_id);
					$do_payment->addParam("updateStripecustomer", "Yes");
				 }
				 echo $do_payment->getFormHeader();
				 echo $do_payment->getFormEvent();
				 echo $_SESSION['do_invoice']->prepareAuthnetForm("stripe",$_SESSION['do_invoice']->amt_due);//sending the from type and amout
             ?>
             <span class="payment-errors"></span>
             <script>if (window.Stripe) $("#do_invoice__eventProcessStripePayment").show()</script>
        <noscript><p>JavaScript is required for the registration form.</p></noscript>
       <?php } else { 
								
				echo nl2br($_SESSION['do_invoice']->invoice_address);
				 //echo '<br />'. _('Total due :').'<b>$'. number_format($invoice_cal_data["total_due_amt"],2, '.', ',' ).'</b>';
				 echo '<br />'. _('Total due :').'<b>'. $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due).'</b>';
				 echo '<br /><br />';
				 $do_user_rel = new UserRelations();
				 $invoice_url = $GLOBALS['cfg_ofuz_site_http_base'].'inv/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idinvoice).'/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idcontact);
				 $do_payment = new Event("do_invoice->eventProcessStripePayment");
				 $do_payment->addParam("stripecustomer_id",$stripe_customer_id);
				 $do_payment->addParam("goto", $invoice_url); // send 0 if no CC else send 1
				 $do_payment->addParam("amt", $_SESSION['do_invoice']->amt_due); 
				 $do_payment->addParam("error_page", "invoice_pay_stripe.php");
				 echo $do_payment->getFormHeader();
				 echo $do_payment->getFormEvent();
			?> <table>
				<tr>
					<td><B><?php  echo _('Total Amount') ?> : </B></td>
					<td><input type="text" name="tot_amt" MAXLENGTH=16 value = "<?php echo $_SESSION['do_invoice']->amt_due; ?>"></td>
					<td></td>
				</tr>	
				</table>
       <?php echo $do_payment->getFormFooter('Submit'); } ?>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php// include_once('includes/ofuz_facebook.php'); ?>
<?php //include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>

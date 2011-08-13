<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Form header script
     * This is the first layer that will contain the header
     * information to start the script.
     * 
     *
     <template>
    <?php  // this is a blank script type your template code here us \[ and \] if you need users to
        // insert custom values to run the script
       global $e_sendMail;
       $email_to_send_to = "[Email_to_send_Form_Content_to]";
       $email_next_page = "[Page_to_display_next]";
       $email_template_name = "[Email_template_name]";  
       $email_admin_template_name = "[Email_Admin_template_name]";     
       $e_sendMail = new Event("mailingtools.sendFormMergeEmail", 20);
       $e_sendMail->addEvent("mydb.callDisplay", 30);
       $e_sendMail->addParam("emailAdmin", $email_to_send_to );
       $e_sendMail->addParam("templatename", $email_template_name);
       $e_sendMail->addParam("templateAdmin", $email_admin_template_name);
       $e_sendMail->addParam("goto", $email_next_page.".php");
       echo $e_sendMail->getFormHeader();
       echo $e_sendMail->getFormEvent();   
    ?>
    </template>
    <templateform>
    <table width="450">
     <tr>
        <td>Send the form to email:</td><td>[Email_to_send_Form_Content_to]</td>
     </tr>    
     <tr>
       <td>Page to display next:</td><td>[Page_to_display_next]</td>
     </tr>
     <tr><td>Optional email template:</td><td>[Email_Admin_template_name]</td></tr>   
     <tr><td colspan="2"><br>If you wish the user filling up the form to receive an confirmation email, 
     you will need to add a field called : email in your form. And provide a templatename for the return message.<br>
     </td>
     </tr>
     <tr><td>User Email Template:</td><td>[Email_template_name]</td></tr>
     </table> 
    
    </templateform>
     **/
// enter bellow your full script. If you write code bellow dont forget to add 
// include("includes/yourscriptname.script.inc.php"); in your template code.
?>

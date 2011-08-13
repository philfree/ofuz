<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Form footer script
     * Close the form and if provied a submit text 
     * display the submit button.
     * 
     *
     <template>
    <?php
       global $e_sendMail;
       $form_footer_submit_text = "[Submit_Button_Label]";
            
       echo $e_sendMail->getFormFooter($form_footer_submit_text);   
    ?>
    </template>
     **/
?>

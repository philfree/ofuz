<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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

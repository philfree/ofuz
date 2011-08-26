<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Sync class
     * Using the DataObject
     */

class Sync extends dataObject {
    //public $table = "";
    //protected $primary_key = "";
    //protected $prefix = "Sync";  // Should be the same as the class name 

    function eventAjaxEnterEmailForm(EventControler $evctl) {
        $form = '<div class="taskbox1a"><div class="taskbox1b"><div class="taskbox1c">';

        // use $evctl->referer value to generate the form header for diff post values
        if ($evctl->referrer == 'gmail') { 

            if($evctl->act == '2'){
              $form .='<form method="post" action="google_import.php">';
              $form .= _('Email : ').'<input type="text" name="email" style="width: 200px;" />';
              $form.= '<input type ="hidden" name = "action" value = "import">';
              $form .='<input type="submit" name="usubmit" value = "'._('Import').'">';
            }elseif($evctl->act == '1'){
              $form .='<form method="post" action="google_export.php">';
              $form .= _('Email : ').'<input type="text" name="email" style="width: 200px;" />';
              $form.= '<input type ="hidden" name = "action" value = "export">';
              $form .='<input type="submit" name="usubmit" value = "'._('Export').'">';
            }
                        
        }
        
        $form .= '</form></div></div></div>';
        $evctl->addOutputValue($form);
    }
}
?>
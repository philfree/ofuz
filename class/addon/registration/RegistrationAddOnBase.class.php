<?php
    /**
     * SampleAddOn
     * Is the sample AddOn to describe and show 
     * how an add works.
     */

class RegistrationAddOnBase extends PageBuilderAddOn {
    
   protected $help_url = "http://radria.sqlfusion.com/documentation/package:application:user_registration"; 
      
    /**
     * headerFullPageForm()
     * Header of the full page form to be consistant with all the 
     * other AddOn (css includes possible).
     */ 

    public function headerFullPageForm() {
        $form = '<table valign="top" class="tableborder">';
        $form .= '<tr>
        <td valign="top" width="250" align="right" class=tableheader><a href="#" onClick="submitformtext()" class="tablinksave">'.__("Add", PBC).'</a></td>
        </tr>
        </table>';
        $form .='<table>
        <tr>
            <td>'.__("Layer Name", PBC).':</td><td> <input type="text" name="layername" size="20" value="'.$this->default_layer_name.'"></td>
        </td>
        </tr></table>';

        return $form;
    }

}


?>

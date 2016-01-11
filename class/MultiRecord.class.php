<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * MultiRecord class
     * Using the DataObject and additional
     * method to manage multiple record forms.
     */

class MultiRecord extends DataObject {
    
    public $table = "";
    protected $primary_key = "";
    protected $prefix = "";  // Should be the same as the class name 
 
   
   function formMultiEntry() {
     if ($this->getNumRows()>0) {
        while ($this->next()) {
            $phone_form .= '<script type="text/javascript">
                            //<![CDATA[
                            var new_'.$this->getPrefix().'_count=1;
                            //]]>
                            </script>';
            $phone_form .= '<div id="edit'.$this->getPrefix().$this->getPrimaryKeyValue().'">';
            $phone_form .= $this->getUpdateFormFields();
            $e_delPhone = new Event($this->getPrefix()."->eventAjaxDelete");
            $e_delPhone->addParam("id", $this->getPrimaryKeyValue());
            $e_delPhone->setSecure(false);
            $e_delPhone->setEventControler("ajax_evctl.php");

            $phone_form .= '<a href="#" id="delete'.$this->getPrefix().$this->getPrimaryKeyValue().'" title="'._('Delete this fields').'"><img src="images/delete.gif"></a></div>';
            $phone_form .= '<script type="text/javascript">
                            //<![CDATA[
                            $("#delete'.$this->getPrefix().$this->getPrimaryKeyValue().'").click(
                                function () {
                                $.get("'.$e_delPhone->getUrl().'");
                                $("#edit'.$this->getPrefix().$this->getPrimaryKeyValue().'").hide(1000).empty();
                                return false;
                                }
                            );
                            //]]>
                            </script>';
            $phone_form .= '<div class="spacerblock_2"></div>';
        }
        $phone_form .= '<div id="ListNew'.$this->getPrefix().'"></div>';
        $phone_form .= '<a href="#" id="addOneMore'.$this->getPrefix().'">'._('Add another').'</a>';
        
        $e_addform = new Event($this->getPrefix()."->eventAjaxFormEntry");
        $e_addform->setEventControler("ajax_evctl.php");
        $e_addform->setSecure(false);
        $phone_form .= '
            <script type="text/javascript">
            //<![CDATA[
                $("#addOneMore'.$this->getPrefix().'").click(function () {
                    new_'.$this->getPrefix().'_count++;
                    $("#ListNew'.$this->getPrefix().'").append(\'<div id="new'.$this->getPrefix().'\'+new_'.$this->getPrefix().'_count+\'" style="margin-bottom:2px;display:none"></div>\');                    
                    $("#new'.$this->getPrefix().'"+new_'.$this->getPrefix().'_count).load("'.$e_addform->getUrl().'&count="+new_'.$this->getPrefix().'_count, function(){$(this).slideDown(200);});
                    return false;
                });
            //]]>
            </script>';
        
        return $phone_form;
     } else {  
        $this->setLog("\n (".$this->getPrefix().") Multiline form with no data");
        $phone_form .= '<script type="text/javascript">
                       //<![CDATA[
                       var new_'.$this->getPrefix().'_count=2;
                       //]]>
                       </script>';
        $phone_form .= '<div id="ListNew'.$this->getPrefix().'">';
        $new_phone_count = 1;
        $phone_form .= '
        <div id="new'.$this->getPrefix().'C'.$new_phone_count.'">';
        $phone_form .= $this->getNewFormFields($new_phone_count);
        $phone_form .= '<a href="#" id="deleteAdd'.$this->getPrefix().$new_phone_count.'" title="'._('Delete this field').'">
            <img src="images/delete.gif">
        </a>
        </div>
        <script type="text/javascript">
        //<![CDATA[
            $("#deleteAdd'.$this->getPrefix().$new_phone_count.'").click( function () { 
                $("#new'.$this->getPrefix().'C'.$new_phone_count.'").hide(1000).empty();
                return false;
            });
        //]]>
        </script>
        ';
        $phone_form .= '<div class="spacerblock_2"></div></div>';
        $phone_form .= '<a href="#" id="addOneMore'.$this->getPrefix().'">'._('Add another').'</a>';
        $e_addform = new Event($this->getPrefix()."->eventAjaxFormEntry");
        $e_addform->setEventControler("ajax_evctl.php");
        $e_addform->setSecure(false);
        $phone_form .= '
            <script type="text/javascript">
            //<![CDATA[
                $("#addOneMore'.$this->getPrefix().'").click(function () {
                    new_'.$this->getPrefix().'_count++;
                    $("#ListNew'.$this->getPrefix().'").append(\'<div id="new'.$this->getPrefix().'\'+new_'.$this->getPrefix().'_count+\'" style="margin-bottom:2px;display:none"></div>\');                    
                    $("#new'.$this->getPrefix().'"+new_'.$this->getPrefix().'_count).load("'.$e_addform->getUrl().'&count="+new_'.$this->getPrefix().'_count, function(){$(this).slideDown(200);});
                    return false;
                });
            //]]>
            </script>';
       return $phone_form;
     } 
    
   }
   
   /**
    * this method is called by the ajax_evctl.php as an event action
    * and return a new form entry to add a value to the phone_contact table.
    * 
    * @param EventControler
    */
   function eventAjaxFormEntry(EventControler $evctl) {
      $new_phone_count = $evctl->count; 
      $form = '
        <div id="new'.$this->getPrefix().'C'.$new_phone_count.'">';
        $form .= $this->getNewFormFields($new_phone_count);
        $form .= '<a href="#" id="deleteAdd'.$this->getPrefix().$new_phone_count.'" title="'._("Delete this fields").'">
        <img src="images/delete.gif">
        </a>
        </div>
        <script type="text/javascript">
        //<![CDATA[
            $("#deleteAdd'.$this->getPrefix().$new_phone_count.'").click( function () { 
                    $("#new'.$this->getPrefix().'C'.$new_phone_count.'").hide(1000).empty();  
                    return false; 
            });
        //]]>
        </script>
   ';
   $evctl->addOutputValue($form);
   
   }
   function eventAjaxDelete(EventControler $evctl) {
        //echo "Delete called".print_r($evctl);
   	$this->delete($evctl->id);
   }
   
   protected function getPrefix() { 
     return $this->prefix;
   }
   

}
?>

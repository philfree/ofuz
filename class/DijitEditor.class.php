<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
 * Class DijitEditor RegistryField class
 *
 * This is the Dojo / Dijit Editor for Radria
 *  
  <rfield name="description">
    <rdata type="fieldtype">DijitEditor</rdata>
    <rdata type="databasetype">text</rdata>
    <rdata type="label">Log entry</rdata>
    <rdata type="undo">Yes</rdata>
    <rdata type="redo">Yes</rdata>
    <rdata type="cut"></rdata>
    <rdata type="copy"></rdata>
    <rdata type="paste"></rdata>
    <rdata type="selectAll"></rdata>
    <rdata type="bold">Yes</rdata>
    <rdata type="italic">Yes</rdata>
    <rdata type="underline">Yes</rdata>
    <rdata type="strikethrough"></rdata>
    <rdata type="subscript"></rdata>
    <rdata type="superscript"></rdata>
    <rdata type="removeFormat">Yes</rdata>
    <rdata type="insertOrderedList">Yes</rdata>
    <rdata type="insertUnorderedList">Yes</rdata>
    <rdata type="insertHorizontalRule">Yes</rdata>
    <rdata type="indent">Yes</rdata>
    <rdata type="outdent">Yes</rdata>
    <rdata type="justifyLeft"></rdata>
    <rdata type="justifyRight"></rdata>
    <rdata type="justifyCenter"></rdata>
    <rdata type="justifyFull"></rdata>
    <rdata type="createLink">Yes</rdata>
    <rdata type="unlink">Yes</rdata>
    <rdata type="delete"></rdata>
    <rdata type="foreColor"></rdata>
    <rdata type="hiliteColor"></rdata>
    <rdata type="hidden"></rdata>
    <rdata type="readonly"></rdata>
    <rdata type="css_form_class"></rdata>
    <rdata type="css_form_class"></rdata>
    <rdata type="css_disp_class"></rdata>
    <rdata type="css_form_style"></rdata>
    <rdata type="css_disp_style"></rdata>
    <rdata type="id"></rdata>
  </rfield>

 * @package PASClass
 */
Class DijitEditor extends RegistryFieldStyle {
    function default_Form($field_value="") {
        include_once("includes/dojo.dijit.editor.js.inc.php");
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            //$field_class = "adformfield";
            //if (strlen($this->getRData("css_form_class")) > 0) {
            //   $field_class = $this->getRData("css_form_class");
            //}
            //if($this->getRData("width") == '' || $this->getRData("width") == 0)
            if($field_value ==''){$field_value='';}
            $plugin = '';
            if($this->getRData('undo') == 'Yes'){$plugin = '\'undo\''; $addcomma = true ;}
            if($this->getRData('redo') == 'Yes'){
               if($addcomma){ $plugin .= ',\'redo\''; $addcomma = true ; }else{ $plugin .= '\'redo\''; $addcomma = true ; }
            }
            if($this->getRData('cut') == 'Yes'){
               if($addcomma){ $plugin .= ',\'cut\''; $addcomma = true ; }else{ $plugin .= '\'cut\''; $addcomma = true ; }
            }
            if($this->getRData('copy') == 'Yes'){
               if($addcomma){ $plugin .= ',\'copy\''; $addcomma = true ; }else{ $plugin .= '\'copy\''; $addcomma = true ; }
            }
            if($this->getRData('paste') == 'Yes'){
               if($addcomma){ $plugin .= ',\'paste\''; $addcomma = true ; }else{ $plugin .= '\'paste\''; $addcomma = true ; }
            }
             if($this->getRData('selectAll') == 'Yes'){
               if($addcomma){ $plugin .= ',\'selectAll\''; $addcomma = true ; }else{ $plugin .= '\'selectAll\''; $addcomma = true ; }
            }
            if($this->getRData('bold') == 'Yes'){
               if($addcomma){ $plugin .= ',\'bold\''; $addcomma = true ; }else{ $plugin .= '\'bold\''; $addcomma = true ; }
            }
            if($this->getRData('italic') == 'Yes'){
               if($addcomma){ $plugin .= ',\'italic\''; $addcomma = true ; }else{ $plugin .= '\'italic\''; $addcomma = true ; }
            }
            if($this->getRData('underline') == 'Yes'){
               if($addcomma){ $plugin .= ',\'underline\''; $addcomma = true ; }else{ $plugin .= '\'underline\''; $addcomma = true ; }
            }
            if($this->getRData('strikethrough') == 'Yes'){
               if($addcomma){ $plugin .= ',\'strikethrough\''; $addcomma = true ; }else{ $plugin .= '\'strikethrough\''; $addcomma = true ; }
            }
            if($this->getRData('subscript') == 'Yes'){
               if($addcomma){ $plugin .= ',\'subscript\''; $addcomma = true ; }else{ $plugin .= '\'subscript\''; $addcomma = true ; }
            }
            if($this->getRData('superscript') == 'Yes'){
               if($addcomma){ $plugin .= ',\'superscript\''; $addcomma = true ; }else{ $plugin .= '\'superscript\''; $addcomma = true ; }
            }
            if($this->getRData('removeFormat') == 'Yes'){
               if($addcomma){ $plugin .= ',\'removeFormat\''; $addcomma = true ; }else{ $plugin .= '\'removeFormat\''; $addcomma = true ; }
            }
            if($this->getRData('insertOrderedList') == 'Yes'){
               if($addcomma){ $plugin .= ',\'insertOrderedList\''; $addcomma = true ; }else{ $plugin .= '\'insertOrderedList\''; $addcomma = true ; }
            }
            if($this->getRData('insertUnorderedList') == 'Yes'){
               if($addcomma){ $plugin .= ',\'insertUnorderedList\''; $addcomma = true ; }else{ $plugin .= '\'insertUnorderedList\''; $addcomma = true ; }
            }
            if($this->getRData('insertHorizontalRule') == 'Yes'){
               if($addcomma){ $plugin .= ',\'insertHorizontalRule\''; $addcomma = true ; }else{ $plugin .= '\'insertHorizontalRule\''; $addcomma = true ; }
            }
            if($this->getRData('indent') == 'Yes'){
               if($addcomma){ $plugin .= ',\'indent\''; $addcomma = true ; }else{ $plugin .= '\'indent\''; $addcomma = true ; }
            }
            if($this->getRData('outdent') == 'Yes'){
               if($addcomma){ $plugin .= ',\'outdent\''; $addcomma = true ; }else{ $plugin .= '\'outdent\''; $addcomma = true ; }
            }
            if($this->getRData('justifyLeft') == 'Yes'){
               if($addcomma){ $plugin .= ',\'justifyLeft\''; $addcomma = true ; }else{ $plugin .= '\'justifyLeft\''; $addcomma = true ; }
            }
            if($this->getRData('justifyRight') == 'Yes'){
               if($addcomma){ $plugin .= ',\'justifyRight\''; $addcomma = true ; }else{ $plugin .= '\'justifyRight\''; $addcomma = true ; }
            }
            if($this->getRData('justifyCenter') == 'Yes'){
               if($addcomma){ $plugin .= ',\'justifyCenter\''; $addcomma = true ; }else{ $plugin .= '\'justifyCenter\''; $addcomma = true ; }
            }
            if($this->getRData('justifyFull') == 'Yes'){
               if($addcomma){ $plugin .= ',\'justifyFull\''; $addcomma = true ; }else{ $plugin .= '\'justifyFull\''; $addcomma = true ; }
            }
            if($this->getRData('createLink') == 'Yes'){
               if($addcomma){ $plugin .= ',\'createLink\''; $addcomma = true ; }else{ $plugin .= '\'createLink\''; $addcomma = true ; }
            }
            if($this->getRData('unlink') == 'Yes'){
               if($addcomma){ $plugin .= ',\'unlink\''; $addcomma = true ; }else{ $plugin .= '\'unlink\''; $addcomma = true ; }
            }
            if($this->getRData('delete') == 'Yes'){
               if($addcomma){ $plugin .= ',\'delete\''; $addcomma = true ; }else{ $plugin .= '\'delete\''; $addcomma = true ; }
            }
            if($this->getRData('foreColor') == 'Yes'){
               if($addcomma){ $plugin .= ',\'foreColor\''; $addcomma = true ; }else{ $plugin .= '\'delete\''; $addcomma = true ; }
            }
            if($this->getRData('hiliteColor') == 'Yes'){
               if($addcomma){ $plugin .= ',\'hiliteColor\''; $addcomma = true ; }else{ $plugin .= '\'delete\''; $addcomma = true ; }
            }
            if (strlen($this->getStyleParam()) > 0) {
                $style_dom .= $this->getStyleParam();
            } 
            if($plugin != ''){
                $plugin_adds ='plugins="['.$plugin .']"';
            }else{
                $plugin_adds = 'plugins= "[\'copy\',\'cut\',\'paste\',\'|\',\'bold\']"';
            }
            $fval .= '<script type="text/javascript">
   dojo.addOnLoad(function () {
      dojo.connect(dojo.byId(\''.$this->getFormName().'\'), \'onsubmit\', function () {
         dojo.byId(\'hidden_field_'.$this->getFieldName().'\').value = dijit.byId(\'editor_'.$this->getFieldName().'\').getValue(false);
      });
   });
</script>';
            $fval .= '<div id="editor_'.$this->getFieldName().'"  
	                dojoType="dijit.Editor" 
                        '.$plugin_adds.' 
                        height=""
                        extraPlugins="[\'dijit._editor.plugins.AlwaysShowToolbar\']">';
            $fval .= $field_value;
            $fval .= '</div>';
            $fval .= '<textarea id="hidden_field_'.$this->getFieldName().'" style="visibility:hidden;" name="fields['.$this->getFieldName().']"></textarea>';

            $this->processed .= $fval;
        }
    }

     function rdataDisp_substring($field_value="") {
        $field_value = substr( $this->getFieldValue(), 0, $this->getRData("substring")) ;
        $this->setFieldValue($field_value);
    }

    function default_Disp($field_value="") {
    if (!$this->getRData('hidden')) {
            if (!$this->getRdata('execute')) {
                $field_value = $this->no_PhpCode($field_value);
            }
            $this->processed .= $field_value;
        }
    }

}
?>
<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
   * Merge Template
   * Functions to merge text with []
   */

   class MergeString extends BaseObject {

     /**
      * Load the field in the field attribute from the HTML template.
      * get Table Field could be used instead but it will not get the
      * extra fields and multiple tables fields
      * @param String $template HTML template (row, header, footer) where there is fields to be used
      * @access public
      * @return Array $fields indexed on the field name.
      */
      static function getField($template) {
         while (ereg('\[([^\[]*)\]', $template, $fieldmatches)) {
               $fields[] = $fieldmatches[1];
               $template = str_replace($fieldmatches[0], "", $template) ;
         }
	 return $fields ;
      }

      /**
       * The secret sauce.
       * Take a string, extract the fields in [] and replace the fields in [] with
       * their respective values from the $values array.
       * @param string with fields to merge in []
       * @param array $values array with format $values[field_name] = $value
       * @return string merged
     */
      static function withArray($thestring, $values) {
         $fields = MergeString::getField($thestring) ;
        if (is_array($fields)) {
           foreach ($fields as $field) {
              $thestring = str_replace('['.$field.']', $values[$field], $thestring) ;
           }
        }
        return $thestring;
     }

   }

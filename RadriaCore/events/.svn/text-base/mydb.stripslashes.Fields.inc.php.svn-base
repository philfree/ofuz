<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt

  /**
   *  Event mydb.stripslashes.fields
   *
   * Event that stripslashes from values in array fields when the GPC in on.
   *  It enable compatibility with site using gpc on and other gpc off
   *  Call the event to be executed before mydb.updateRecord or mydb.addRecord with a level lower than 1000
   *
   * <br>- param Array fields array with all the values of the fiels indexed on the name of the field.
   *
   * @package RadriaEvents
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007
   * @version 3.0   
   */
    /**
     * webide.stripslashesfields
     * Event that strip slashes to fields from forms
     * when GPC is on
     */
    if (is_array($fields)) {
        while(list($key, $value) = each($fields)) {
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value) ;
            }
            $newfields[$key] = $value  ;
        }
        $fields = $newfields ;
        $this->updateParam("fields", $newfields) ;
    }
?>
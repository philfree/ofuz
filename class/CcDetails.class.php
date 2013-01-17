<?php 
    /** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html 
     *  Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
     * Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html 
   
    Table Name :cc_details;
    +--------------+--------------+------+-----+---------+----------------+
    | Field        | Type         | Null | Key | Default | Extra          |
    +--------------+--------------+------+-----+---------+----------------+
    | idcc_details | int(50)      | NO   | PRI | NULL    | auto_increment |
    | iduser       | int(50)      | NO   |     | NULL    |                |
    | token        | varchar(100) | NO   |     | NULL    |                |
    | type         | varchar(10)  | NO   |     | NULL    |                |
    +--------------+--------------+------+-----+---------+----------------+

  ***/


class CcDetails extends DataObject {
  
      
  public $table = "cc_details";
  protected $primary_key = "idcc_details";
    
  




}
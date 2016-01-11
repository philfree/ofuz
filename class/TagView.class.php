<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * TagView class
     * Using the DataObject
     * Class to record the number of time a tag is clicked
     * @author Philippe Lewicki info@sqlfusion.com
     */

class TagView extends DataObject {
    
    public $table = "tagview";
    protected $primary_key = "idtagview";

    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       $this->setLogRun(RADRIA_LOG_RUN_OFUZ);
    } 

}

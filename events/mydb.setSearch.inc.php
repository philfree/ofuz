<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

 /**
  *  clear search event
  *  requires <br>
  * @param string search_string reference to variable containing the search strings<br>
  * @param string goto page to display next<br>
  **/
  
  $search_value = $this->getParam("search_value");
  global $$search_string;
  $$search_string = $search_value;
  session_register($search_string) ; 
  $disp = new Display($goto);
  $this->setDisplayNext($disp) ; 

?>
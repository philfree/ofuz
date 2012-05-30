<?php   
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
    /**
     * This event set the next display to be the page set in 
     * the goto parameter.
     *
     * @package RadriaEvents
     * @author Philippe Lewicki  <phil@sqlfusion.com>
     * @copyright  SQLFusion LLC 2001-2004
     * @version 3.0
     */

  $goto = $this->getParam("goto");
  $curdisp = $this->getDisplayNext();
  if (is_object($curdisp)) {
    $curdisp->setPage($goto);
    $this->setDisplayNext($curdisp);
  } elseif (strlen($goto) > 0) {
    $nextpage = new Display($goto);
    $this->setDisplayNext($nextpage);
  }

?>
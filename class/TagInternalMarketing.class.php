<?php 

// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


class TagInternalMarketing extends Tag {
    

  function setContactTag($tag_name, $iduser, $idcontact){
    $tag_exists = $this->ifContactTagExists($tag_name, $iduser, $idcontact);
    if(!$tag_exists) {
      $this->tag_name = $tag_name;
      $this->iduser = $iduser;
      $this->reference_type = 'contact';
      $this->idreference = $idcontact;
      $this->add();
    }
  }

  function deleteContactTag($tag_name, $iduser, $idcontact) {
    $sql = "SELECT *
            FROM ".$this->table."
            WHERE `tag_name` = ".$tag_name." AND iduser=".$iduser." AND idreference = ".$idcontact;
    $this->query($sql);
    if ($this->getNumRows() == 1) {
      $this->delete();
    }
  }

  function ifContactTagExists($tag_name, $iduser, $idcontact) {
    $sql = "SELECT idtag
            FROM ".$this->table."
            WHERE `tag_name` = '".$tag_name."' AND iduser = ".$iduser." AND idreference = ".$idcontact;
    $this->query($sql);
    if($this->getNumRows()) {
      return true;
    } else {
      return false;
    }
  }

}

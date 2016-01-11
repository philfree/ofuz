<?php 
// Copyright 2001 - 2010 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
   * Class UserInternalMarketing
   *
   * @author SQLFusion's Dream Team <info@sqlfusion.com>
   * @package OfuzCore
   * @license GNU Affero General Public License
   * @version 0.6
   * @date 2010-10-21
   * @since 0.1
   */

class UserInternalMarketing extends User {

  /**
   * Gets all the users(iduser) who are in Phil's Contacts.
   * dev : phil's iduser = 20
   * @return obj : query object
   */

  public function getUsersFromPhilsContacts() {
    $sql = "SELECT u.iduser,c.idcontact
            FROM `user` AS u INNER JOIN `contact` AS c ON u.firstname = c.firstname AND u.lastname = c.lastname
            INNER JOIN contact_email AS ce ON c.idcontact = ce.idcontact AND u.email = ce.email_address
            WHERE c.iduser = 20
            GROUP BY u.iduser
            ";
    $this->query($sql);
  }

  public function setActiveInactiveTag($iduser, $idcontact){
    $phil_iduser = "20";
    $login_status = $this->GetUserLoginStatus($iduser);
    $do_contact_view = new ContactView();
    $do_contact_view->setUser($phil_iduser);
    $do_tag = new TagInternalMarketing();
    if($login_status == "Active") {
      $do_contact_view->deleteTag("Inactive", $idcontact);
      $do_contact_view->addTag("Active", $idcontact);
      $do_tag->deleteContactTag("Inactive", $phil_iduser, $idcontact);
      $do_tag->setContactTag("Active", $phil_iduser, $idcontact);
    } 
    if($login_status == "Inactive") {
      $do_contact_view->deleteTag("Active", $idcontact);
      $do_contact_view->addTag("Inactive", $idcontact);
      $do_tag->deleteContactTag("Active", $phil_iduser, $idcontact);
      $do_tag->setContactTag("Inactive", $phil_iduser, $idcontact);
    }
  }

  public function GetUserLoginStatus($iduser){
    $sql = "SELECT user.iduser
            FROM user
            INNER JOIN login_audit ON user.iduser = login_audit.iduser
            WHERE user.iduser = ".$iduser." AND DATEDIFF( NOW( ) , login_audit.last_login )<= 7";
    $this->query($sql);
    if($this->getNumRows()) {
      return "Active";
    } else {
      return "Inactive";
    }
  }

  /**
   * This event suspends an User.Just changes the status to 'suspend'.
   * @param obj : EventControler
   * @return void
   */
  public function eventSuspendUser(EventControler $evtcl) {
    $do_oex = new OfuzExportXML();
    $do_oex->exportUserAccount($evtcl->iduser);

    $sql = "UPDATE `user`
            SET `status` = 'suspend'
            WHERE `iduser` = ".$evtcl->iduser."
          ";
    $this->query($sql);
    $evtcl->setDisplayNext(new Display($evtcl->goto)) ;
  }

  /**
   * This event un-suspends (makes active) a User.Just changes the status to 'active'.
   * @param obj : EventControler
   * @return void
   */
  public function eventUnsuspendUser(EventControler $evtcl) {

    $sql = "UPDATE `user`
            SET `status` = 'active'
            WHERE `iduser` = ".$evtcl->iduser."
          ";
    $this->query($sql);
    $evtcl->setDisplayNext(new Display($evtcl->goto)) ;
  }

  /**
   * This event deletes entire User Account from Ofuz.
   * @param obj : EventControler
   * @return void
   */
  public function eventDeleteUser(EventControler $evtcl) {
    $do_oex = new OfuzExportXML();
    $do_oex->exportUserAccount($evtcl->iduser);

    $do_oca = new OfuzCancelAccount();
    $do_oca->deleteUserAccount($evtcl->iduser);
  }

} //end of class

?>
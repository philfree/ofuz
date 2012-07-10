<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

    /**
    * WorkFeedProjectAssignCoworker class
    * Copyright 2001 - 2008 SQLFusion LLC, Author: Philippe Lewicki, Abhik Chakraborty ,Jay Link info@sqlfusion.com 
    *
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package WorkFeed
    * @license GNU Affero General Public License
    * @version 0.6
    * @date 2010-09-04
    * @since 0.6
   */


class WorkFeedProjectAssignCoworker extends WorkFeedItem{
    private $project_name;
    private $user_full_name;
    private $idproject;
    private $idcontact;
    


    function display(){
      
      $html .= '<div style="width:50px;float:left;">';
      $html .= '<img src="/images/note_icon.gif" width="34" height="34" alt="" />';
      $html .= '</div>';
      $html .= '<div style="text-align:middle;">';
      //$html .= '<a href ="/Contact/'.$this->idcontact.'">'.$this->user_full_name.'</a>';
      $username=$this->user_full_name;
      $user_name = explode(' ',$username);
      $html .='<a href="/profile/'.$user_name[0].'">'.$this->user_full_name.'  </a>';    
      $html .= ' '._('added you to the project: ').' '. ' <a href="/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
      $html .= '</div>';
      $html .= '<div style = "color: #666666;font-size: 8pt; margin-left:50px;">';      
      $html .= OfuzUtilsi18n::formatDateLong(date("Y-m-d H:i:s",$this->date_added),true);
      $html .= '</div>';
      $html .='<br />';
      $html .= '<div class="dottedline"></div>';      
      return $html;
    }





   function eventAddFeed($coworkerid,$projectid){             
        $do_project = new Project();
        $do_project->getId($projectid);
        $this->project_name = $do_project->getProjectName();

	$user = array($coworkerid);

        $do_user = new User();
        $do_user->getId($_SESSION["do_User"]->iduser);
        $this->user_full_name = $do_user->getFullName();
        $this->idcontact = $coworkerid;
        $this->idproject = $projectid;
        $this->addFeed($user);     

    }

//@class ends
}

?>
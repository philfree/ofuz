<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
   * Class Breadcrumb
   * Keep a history and link of specific tag search, contact and company viewed, should be easy to extend to 
   * others like project, invoices
   * 
   */


class Breadcrumb extends DataObject {

	public $table = 'breadcrumb';
    public $primary_key = 'idbreadcrumb';

    /*
     * Master method. Called by webpages and returns HTML containing breadcrumbs.
     * Max number of breadcrumbs set by $limit .
     * If no breadcrumbs, exit. Don't print a confusing blank bar.
     */
    function getBreadcrumbs() {
        $limit = 100;
        $URLstring = '';
    	$this->query("select type, id, MAX(`when`) AS latest from breadcrumb where iduser = ".$_SESSION['do_User']->iduser." group by type, id order by latest desc limit ".$limit);
        while ($this->fetch()) {
            if ($URLstring != '') $URLstring .= ' <span class="sep2">|</span> ';
            $type = $this->getData('type');
            $id = $this->getData('id');
            switch ($type) {
              case 'Contact':
                  $URLstring .= $this->getContactUrl($id);
                  break;
              case 'Company':
                  $URLstring .= $this->getCompanyUrl($id);
                  break;
              case 'Project':
                  $URLstring .= $this->getProjectUrl($id);
                  break;
              case 'Invoice':
                  $URLstring .= $this->getInvoiceUrl($id);
                  break;
            }
        }
        if ($URLstring == '') {
            echo '<div id="breadcrumbs"></div>';
            return;
        };
        $str_view = _('You recently viewed');
        echo <<<HTML
        <div id="breadcrumbs">
            <div class="bctext">$str_view:</div>
            <div id="bclarrows" class="bcstop">||</div><div id="bcslider">$URLstring</div><div id="bcrarrows" class="bcstop">||</div>
            <script type="text/javascript">
            //<![CDATA[
            \$(document).ready(function(){
                var right=\$("#breadcrumbs").width() - \$("#bcslider").width() - 40;
                if (right<220) {
                    \$("#bcrarrows").addClass("bcarrows").removeClass("bcstop").html("&raquo;");
                    \$("#bclarrows").hover(function(){
                        \$("#bcslider").animate({left:"180px"},Math.max(2000, Math.abs(right)),'swing',function(){\$("#bclarrows").addClass("bcstop").removeClass("bcarrows").html("||");});
                        \$("#bcrarrows").addClass("bcarrows").removeClass("bcstop").html("&raquo;");
                    },function(){
                        \$("#bcslider").stop();
                    });
                    \$("#bcrarrows").hover(function(){
                        \$("#bcslider").animate({left:right+"px"},Math.max(2000, Math.abs(right)),'swing',function(){\$("#bcrarrows").addClass("bcstop").removeClass("bcarrows").html("||");});
                        \$("#bclarrows").addClass("bcarrows").removeClass("bcstop").html("&laquo;");
                    },function(){
                        \$("#bcslider").stop();
                    });
                }
            });
            //]]>
            </script>
        </div>
HTML;
    }
    function getContactUrl($id) {
        $q = new sqlQuery($this->getDbCon());
    	$q->query("select firstname, lastname from contact where idcontact = ".$id);
        $q->fetch();
        $URLstring = '<a href="/Contact/'.$id.'">'.trim($q->getData('firstname').' '.$q->getData('lastname')).'</a>';
        //'<a href="/contact.php?idcontact='.$q->getData('id').''
        return $URLstring;
    }
    function getCompanyUrl($id) {
        $q = new sqlQuery($this->getDbCon());
    	$q->query("select name from company where idcompany = ".$id);
        $q->fetch();
        $URLstring = '<a href="/Company/'.$id.'">'.trim($q->getData('name')).'</a>';
        return $URLstring;
    }
    function getProjectUrl($id) {
        $q = new sqlQuery($this->getDbCon());
    	$q->query("select name from project where idproject = ".$id);
        $q->fetch();
        $URLstring = '<a href="/Project/'.$id.'">'.trim($q->getData('name')).'</a>';
        return $URLstring;
    }
    function getInvoiceUrl($id){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select num from invoice where idinvoice = ".$id);
      $q->fetch();
      $URLstring = '<a href="/Invoice/'.$id.'">Invoice #'.$q->getData('num').'</a>';
      return $URLstring;
    }
    function getTagSearchUrl() {

    }
    function AddTagSearch(Array $tags) {

    }
}

?>
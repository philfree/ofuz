<?php 
/**COPYRIGHTS**/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

    $pageTitle = 'Ofuz :: '._('Edit Team');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $idteam = $_SESSION["eDetail_team"]->getParam("idteam");
    $do_team = new Teams();
    $do_team->getId($idteam);
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	
	//clicked on 'Skip' button
    $('#btnSkipCW').click(function() {
      window.location = 'teams.php';
    });
    
    //button clicked to edit a Team
    $('#btnEditTeam').click(function () {
		var team_name;
		var auto_share;
		var idteam = <?php echo $idteam; ?>

		team_name = $("#team_name").val();

		if($("#auto_share").attr('checked')) {
			auto_share = 'Yes';
		} else {
			auto_share = 'No';
		}
		
		if(team_name) {
			$.ajax({
				type: "GET",
				<?php
				$e_team = new Event("Teams->eventAjaxEditTeam");
				$e_team->setEventControler("ajax_evctl.php");
				$e_team->setSecure(false);
				?>
				url: "<?php echo $e_team->getUrl(); ?>",
				data: "idteam="+idteam+"&team_name="+team_name+"&auto_share="+auto_share,
				success: function(response){
					if(response == 'ok') {
						$("#divTeamMsg")[0].innerHTML = "The Team has been updated.";
						$('#DivEditTeam').hide('slow');
						$('#DivEditCW').show('slow');
					} else {
						$("#divTeamMsg")[0].innerHTML = "The Team Could not be updated. Please try again.";
					}										
				}
			});			
		} else {
			$('#divTeamMsg')[0].innerHTML = 'Please enter a Team Name.';
		}					
	});
	
});

	
//]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
	<?php
	  if(isset($_GET["message"])){
	      $message = new Message();
	      $message->setContent(_(htmlentities($_GET["message"])));
	      $message->displayMessage();
	  }
	  $GLOBALS['page_name'] = 'co_workers';
	  include_once('plugin_block.php');
	?>
    </td>
    <td class="layout_rcolumn">
        <table class="mainheader pad20" width="100%">
            <tr>
                <td><span class="headline14">Edit Team</span>
                </td>
                <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<a href="teams.php"><?php echo _('Teams'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="co_workers.php"><?php echo _('All Co-Workers'); ?></a></td>
            </tr>
        </table>
		<div class="contentfull">
		  <div class="spacerblock_20"></div>
		 <div id="divTeamMsg"></div>
		 
		 <!-- Edit Team -->
		 <div id="DivEditTeam" style="display:block;margin-top:20px;">
		 Team Name: <input type="text" name="team_name" id="team_name" value="<?php echo $do_team->team_name;?>" /> <br />
		 Auto share: <input type="checkbox" name="auto_share" id="auto_share" <?php if($do_team->auto_share=="Yes") echo "checked"; ?>/> <br />
		 <input type="button" id="btnEditTeam" name="btnEditTeam" value="Edit" />
		 </div>
		 <!-- Edit Co-Worker/s-->
		 <div id="DivEditCW" style="display:none;margin-top:20px;">
<?php
$e_cw = new Event("Teams->eventUpdateTeamCWs");
$e_cw->addParam("idteam", $idteam);
$e_cw->addParam("goto", "teams.php");
echo $e_cw->getFormHeader();
echo $e_cw->getFormEvent();

$arr_cw = $do_team->getTeamCoWorkersId($idteam);
$do_ur = new UserRelations();

$do_ur->getAllCoWorker();
if($do_ur->getNumRows()) {
  while($do_ur->next()) {
?>
<div><input type="checkbox" name="coworker[]" value="<?php echo $do_ur->idcoworker;?>" <?php if(in_array($do_ur->idcoworker, $arr_cw)) echo "checked";?> /><?php echo $do_ur->firstname." ".$do_ur->lastname;?></div>
<?php
  }
} else {
  echo "You do not have a Co-Worker.";
}
?>
<input type="submit" name="btnEditCW" value="Edit" />
<input type="button" name="btnSkipCW" id="btnSkipCW" value="Skip" />
</form>
		 </div>
    </td>
    </tr>
    </table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>

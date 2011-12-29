<?php 
/**COPYRIGHTS**/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

    $pageTitle = 'Ofuz :: '._('Team');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_teams = new Teams();
    $do_teams->getTeams();
    $teams_count = $do_teams->getNumRows();    
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	
	//'Create New' link clicked
    $('#AncCreateNewTeam').click(function() {
		$("#divTeamMsg")[0].innerHTML = "";
		$('#DivTeamsList').hide('slow');
		$('#DivCreateNewTeam').show('slow');
    });
    
    //button clicked, to add a team
    $('#btnCreateTeam').click(function () {
		var team_name;
		var auto_share;

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
				$e_team = new Event("Teams->eventAjaxAddTeam");
				$e_team->setEventControler("ajax_evctl.php");
				$e_team->setSecure(false);
				?>
				url: "<?php echo $e_team->getUrl(); ?>",
				data: "team_name="+team_name+"&auto_share="+auto_share,
				success: function(response){
					if(response == 'exists') {
						$("#divTeamMsg")[0].innerHTML = "The team "+team_name+" already exists.";
					} else if(response != '') {
						$("#divTeamMsg")[0].innerHTML = "The Team "+team_name+" is added";
						$("#DivCreateNewTeam")[0].innerHTML = response;
					}										
				}
			});			
		} else {
			$('#divTeamMsg')[0].innerHTML = 'Team Name can not be empty.';
		}					
	});
	
});

//'Skip' button is clicked
function skipAddCoWorker() {
	window.location = 'teams.php';
	return false;
}

function addCoWorkerToTeam(){

	var idteam_users;
	var values = new Array();
	
	idteam_users = $('#idteam_users').val();
	$.each($("input[name='coworker']:checked"), function() {
	  values.push($(this).val());
	});	

	if(values.length > 0) {
		$.ajax({
			type: "GET",
			<?php
			$e_cw = new Event("Teams->eventAjaxAddCoWorkerToTeam");
			$e_cw->setEventControler("ajax_evctl.php");
			$e_cw->setSecure(false);
			?>
			url: "<?php echo $e_cw->getUrl(); ?>",
			data: "idcoworker="+values+"&idteam_users="+idteam_users,
			success: function(response){
				if(response != "") {
					$('#divTeamMsg')[0].innerHTML = "The CoWorker/s added to the Team.";
					$("#DivCreateNewTeam")[0].innerHTML = response;
				}
			}
		});	
	} else {
		$('#divTeamMsg')[0].innerHTML = 'Co-Worker is not selected.';
	}		
}
	
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
                <td><span class="headline14">Teams</span>
                </td>
                <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<a href="teams.php"><?php echo _('Teams'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="co_workers.php"><?php echo _('All Co-Workers'); ?></a></td>
            </tr>
        </table>

		<div class="contentfull">	
		  <div class="spacerblock_20"></div>	 
		 <div><a id="AncCreateNewTeam" href="javascript:;"><?php echo ('Create New');?></a></div>
		  <div class="spacerblock_20"></div>
		 <div id="divTeamMsg"></div>
		 <div id="DivTeamsList" style="display:block;">
		 <?php
		 if($teams_count) {
			while($do_teams->next()) {
			  $e_detail = new Event("mydb.gotoPage");
			  $e_detail->addParam("goto", "team_edit.php");
			  $e_detail->addParam("idteam", $do_teams->idteam);
			  $e_detail->addParam("tablename", "team");
			  $e_detail->requestSave("eDetail_team", $_SERVER["PHP_SELF"]);

			  echo $do_teams->team_name.' <a href="'.$e_detail->getUrl().'" class="linkdetail">edit</a><br />';
			}
		 } else {
			 echo '<p>'.('You have not yet created a team.').'</p>';
		 }
		 ?>
		 </div>
		 
		 <!-- Add Team -->
		 <div id="DivCreateNewTeam" style="display:none;margin-top:20px;">
		 Team Name: <input type="text" name="team_name" id="team_name" value="" /> <br />
		 Auto share: <input type="checkbox" name="auto_share" id="auto_share" /> <br />
		 <input type="button" id="btnCreateTeam" name="btnCreateTeam" value="Add" />
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

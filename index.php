<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: '._('Dashboard');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white'; 

    include_once('config.php');
	//if (!isset($_COOKIE['ofuz'])) {
	//	header("Location: http://www.ofuz.com/");
	//	exit;		
	//}
	//if ($_COOKIE['ofuz'] != '1') {
	//	header("Location: http://www.ofuz.com/");
	//	exit;
	//}
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    $ProjectDiscuss = new ProjectDiscuss();
    $ProjectTask = new ProjectTask();
    $ProjectDiscuss->sessionPersistent("ProjectDiscuss", "index.php", OFUZ_TTL);
    //$do_notes->sessionPersistent("ContactNotesEditSave", "index.php", OFUZ_TTL);
	$ContactNoteExpend  = new ContactNotes($GLOBALS['conx']);
    $ContactNoteExpend->sessionPersistent("ContactNoteExpend", "contacts.php", OFUZ_TTL);
	$DiscussNoteExpend  = new ProjectDiscuss($GLOBALS['conx']);
    $DiscussNoteExpend->sessionPersistent("DiscussNoteExpend", "contacts.php", OFUZ_TTL);

	if (!is_object($_SESSION['do_work_feed'])) {
		$do_work_feed  = new WorkFeed($GLOBALS['conx']);
		$do_work_feed->sessionPersistent("do_work_feed", "contacts.php", OFUZ_TTL);
	}

	$_SESSION['do_work_feed']->sql_qry_start = 0;
	$_SESSION['do_work_feed']->getWorkfeedCount();
	


?>
<script type="text/javascript">
    //<![CDATA[
	
	<?php include_once('includes/ofuz_js.inc.php'); ?>
	
	
    var openform;
    function fnEditTask(task) {
        if ($("#e"+openform).length > 0) fnCancelEdit(openform);
        openform = task;
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("Task->eventAjaxEditTaskForm");
$e_editForm->setEventControler("ajax_evctl.php");
$e_editForm->setSecure(false);
?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "id="+task,
            success: function(html){
            	$("#t"+task).hide(0);
            	$("#e"+task)[0].innerHTML = html;
                $("#e"+task).show(0);
            }
        });
    };
    function fnCancelEdit(task) {
        $("#e"+task).hide(0);
        $("#e"+task)[0].innerHTML = "";
        $("#t"+task).show(0);
    };
    function showFullNote(idnote){
        $.ajax({
            type: "GET",
        <?php
        $e_ContactNote = new Event("ContactNoteExpend->eventAjaxGetContactNote");
        $e_ContactNote->setEventControler("ajax_evctl.php");
        $e_ContactNote->setSecure(false);
        ?>
            url: "<?php echo $e_ContactNote->getUrl(); ?>",
            data: "idnote="+idnote,
            success: function(notetext){
                $("#notepreview"+idnote)[0].innerHTML = notetext;
            }
        });
    }
    function showFullProjDiscuss(idproject_discuss){
    $.ajax({
        type: "GET",
		<?php
		$e_ProjectDiscuss = new Event("DiscussNoteExpend->eventAjaxProjectTaskDiscussion");
		$e_ProjectDiscuss->setEventControler("ajax_evctl.php");
		$e_ProjectDiscuss->setSecure(false);
		?>
        url: "<?php echo $e_ProjectDiscuss->getUrl(); ?>",
        data: "idnote="+idproject_discuss,
        success: function(notetext){
            $("#discusspreview"+idproject_discuss)[0].innerHTML = notetext;
        }
    });
}

    function fnTaskComplete(task) {
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("Task->eventAjaxTaskComplete");
$e_editForm->setEventControler("ajax_evctl.php");
$e_editForm->setSecure(false);
?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "id="+task,
            success: function(){
            	$("#t"+task).css("text-decoration", "line-through");
            	$("#t"+task).fadeOut("slow", function() {
            	    $("#e"+task).remove();
            	    $("#b"+task).remove();
                });
            }
        });
    };
    $(document).ready(function() {
    	$("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
    });
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php 
 $thistab = _('Dashboard');
 $_SESSION['dashboard_link'] = "index";
 include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns">
      <tr>
    <td class="layout_lcolumn">
      <?php include_once('plugin_block.php'); ?>
      <div class="center_text">
          <a href="/contacts.php"><img src="/images/icon_contact_150.png" width="150" height="150" alt="" /></a><br />
          <a href="/contacts.php"><?php echo _('Add A Contact'); ?></a>
      </div>
      <div class="spacerblock_40"></div>
      <div class="center_text">
          <a href="/projects.php"><img src="/images/icon_project_150.png" width="150" height="150" alt="" /></a><br />
          <a href="/projects.php"><?php echo _('Create A Project'); ?></a>
      </div>
      <div class="spacerblock_40"></div>
      <div class="center_text">
          <a href="/invoices.php"><img src="/images/icon_invoice_150.png" width="150" height="150" alt="" /></a><br />
          <a href="/invoices.php"><?php echo _('Send An Invoice'); ?></a>
      </div>
      <div class="spacerblock_40"></div>
    </td>
    <td class="layout_rcolumn"><div class="min660">

            <?php
            $msg = new Message(); 
            $do_contact = $_SESSION['do_User']->getChildContact();
            if ($do_contact->getNumRows() == 0) {
                $msg->getMessage("welcome");
                $msg->displayMessage();
            } else {
                $msg->getMessageFromContext("dashboard");
                $msg->displayMessage();
            }
            ?>

        <div class="mainheader pad20">
                <span class="page_title"><?php
                    $do_workfeed = new WorkFeed();
                    printf(_("%s 's Work Feed:"),$_SESSION['do_User']->firstname) . "\n";
                ?></span>
                <?php
                if (is_object($GLOBALS['cfg_submenu_placement']['index'] ) ) {
                	echo  $GLOBALS['cfg_submenu_placement']['index']->getMenu();
                }
                ?>
        </div>
        
        
        <script type="text/javascript">
        function autoLoadWorkfeed() {
                $('div#last_feed_loader').html('<img src="/images/loader1.gif">');
                $.ajax({
                    type: "GET",
                <?php
                $e_workfeed = new Event("do_work_feed->autoLoadWorkfeedOnScrollDown");
                $e_workfeed->setEventControler("ajax_evctl.php");
                $e_workfeed->setSecure(false);
                ?>
                    url: "<?php echo $e_workfeed->getUrl(); ?>",
                    //data: "dob="+dob,
                    success: function(data){
                            $(".message_box:last").after(data);
                            $('div#last_feed_loader').empty();
                    }
                });
                
        }
        
        $(document).ready(function()
        {
	  $(window).scroll(function(){
	    var scrollTop = $(window).scrollTop();
            var docHeight = $(document).height();
            var winHeight = $(window).height();
	    //alert(scrollTop+' : '+docHeight+' : '+winHeight);
	    if ($(window).scrollTop() == ($(document).height() - $(window).height() - 1)){
	      autoLoadWorkfeed();
	    }
	  });
        });
        </script>
        <?php
             //$do_workfeed = new WorkFeed();
             //echo '<div class="headline_fuscia">', $_SESSION['do_User']->firstname, '\'s Work Feed:</div>', "\n";
             $do_workfeed->displayUserFeeds();
         ?>
		<div id="last_feed_loader"></div>		
        <div class="dottedline"></div>
        <?php $footer_note = 'emailstream'; include_once('includes/footer_notes.php'); ?>

        <!-- Add ofuz to Browser Search option begins -->
        
        <!--<script src="/browser_search/browser_detect.js" type="text/javascript"></script>

        
        <div style="text-align:center;cursor:pointer">
            <script src="/browser_search/browser_functions.js" type="text/javascript"></script>
        </div>-->


        <!-- Add ofuz to Browser Search option ends -->
    </div></td></tr></table>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>

<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
/** New help page **/

    $pageTitle = 'Ofuz :: Help Support';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
?>
<style>
.video_box{
  margin: 14px 3px 0 80px; 
  height:370px;
  width: 240px;
  float:left;
  text-align:center;
}

.video_thumb_desc {
   width:230px; 
   text-align:left;
   margin-left: 10px;
   margin-right: 10px;
}

.video_desc{
  margin: 14px 3px 0 120px; 
  height:auto;
  width:auto;
  float:left;
  clean:left;
  text-align:left;
}

.more_video_link{
  margin: 14px 3px 0 170px; 
  height:auto;
  width:auto;
  float:left;
  clean:left;
  text-align:left;
}

.ofuzcom_button {
width: 300px;
margin: 20px auto;
text-align: center;
padding: 10px 15px 10px;
font-weight: bold;
font-size: 20px;
background-color: #ff9966;
outline: 1px solid #ff6600;
border-top: 1px solid #fcbd93;
border-left: none;
border-bottom: none;
border-right: none;
text-shadow: 1px 1px #ff6600;
}
.ofuzcom_button a{color: white !important;}

.ofuzcom_button:hover {
cursor: pointer;

}

h1.ofuzcom { font-size:44px; }

h2.tutorial { color:#ff9966; font-size:24px;}

</style>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Help'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns">
     <tr>
      <td>
        <?php  $GLOBALS['page_name'] = 'help';  include_once('plugin_block.php'); ?>
      </td>
      
      <td class="layout_rcolumn">
         <div class="mainheader">
            <div class="pad20">
                <span class="page_title"><?php echo _('Help &amp; Support'); ?></span>
                <?php
                // Menues are defined in includes/x_ofuz_hooks_plugin.conf.inc.php
                if (is_object($GLOBALS['cfg_submenu_placement']['help']) ) {
                	echo  $GLOBALS['cfg_submenu_placement']['help']->getMenu();
                }
                ?>
            </div>
        </div>
        <div class="solidline"></div>
        <div class="spacerblock_40"></div>
        
        <div class="contentfull center_text">
			
		<div class="center_text text_blue"><h1 class="ofuzcom">Video tutorials</h1></div>	
        <div class="video_box">
			<div class="text32 center_text text_bold">Share Contacts</div>
			<div class="spacerblock_20"></div>
			<a class="center_elem"  target="_new"
				 href="http://www.ofuz.com/tour_video_play.php?vid=contact_sharing_r2.mov"  
				 style="display:block;width:230px;height:129px;  " > 
				 <img src="http://www.ofuz.com/images/video_thumb_contact.jpg" alt="Collaborate on Contacts">
			</a>
			<div class="spacerblock_20"></div>
			<div class="video_thumb_desc text_16 text_bold"><a class="text_16 text_bold" href="tour_video_play.php?vid=contact_sharing_r2.mov">Sharing Contacts</a>
				<span class="text_14">Put your address book to work and colaborate with your co-workers on  making the most of your network.</span>
			</div>
		  </div>
	
		<div class="video_box">
			<div class="text32 center_text text_bold">First Project</div>
			<div class="spacerblock_20"></div>
			<a class="center_elem"    target="_new"
				href="http://www.ofuz.com/tour_video_play.php?vid=getting_started_r3.mp4"  
				style="display:block;width:230px;height:129px; "> 
				<img src="http://www.ofuz.com/images/video_thumb_project.jpg" alt="First Project">
			</a>
			<div class="spacerblock_20"></div>
			<div class="video_thumb_desc text16 text_bold"><a class="text16 text_bold" href="tour_video_play.php?vid=getting_started_r3.mp4">Getting Started</a>
				<span class="text_14">This dense tutorial demonstrate how to create your first project, collaborate with co-workers on tasks and share documents and files. </span>
			</div>
		</div>
		
		<div class="video_box">
			<div class="text32 center_text text_bold">5 min invoice</div>
			<div class="spacerblock_20"></div>
			<a class="center_elem"    target="_new"
				href="http://www.ofuz.com/tour_video_play.php?vid=900w_direct_invoices_r1.mp4"  
				style="display:block;width:230px;height:129px;"> 
				<img src="http://www.ofuz.com/images/video_thumb_invoice_4min.jpg" alt="Invoice in 4 minutes">
			</a> 
			<div class="spacerblock_20"></div>
			<div class="video_thumb_desc text16 text_bold"><a class="text16 text_bold" href="tour_video_play.php?vid=900w_direct_invoices_r1.mp4">Invoice in 4 min</a>
				<span class="text_14">Create a professional invoice and get paid with PayPal in 4 minutes.</span>
			</div>
		</div> 
		<div style="clear:left"></div>     
        <div class="ofuzcom_button"><a href="http://www.ofuz.com/tour_videos.php" target="_new">Watch More Videos</a></div>
        
		<div class="spacerblock_20"></div>        
        <div class="solidline"></div>
		<div class="spacerblock_20"></div>
		
		<div class="center_text text_blue"><h1 class="ofuzcom">Tutorials from the Blog</h1></div>	
		<div class="center_text">
			<h2 class="tutorial">The Freelancer Guide to Ofuz</h2>
			
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2010/04/the-freelance-tipping-point/">The Freelance Tipping Point</a>
			<div class="spacerblock_20"></div>
			
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2010/04/reducing-friction-losses-for-the-freelancer-pt-1/">Reducing Friction Losses for the Freelancer, pt. 1</a>
			<div class="spacerblock_20"></div>
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2010/04/reducing-friction-losses-for-the-freelancer-pt-2/">
			Reducing Friction Losses for the Freelancer, pt. 2</a>
			<div class="spacerblock_20"></div>			
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2010/04/reducing-opportunity-losses-for-the-freelancer-pt-1/">
			Reducing Opportunity Losses for the Freelancer, pt. 1</a>
			<div class="spacerblock_20"></div>			
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2010/04/reducing-opportunity-losses-for-the-freelancer-pt-2/">
			Reducing Opportunity Losses for the Freelancer, pt. 2</a>
			<div class="spacerblock_20"></div>			
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2010/04/keeping-in-touch-micro-mailing-for-the-solo-entrepreneur/">
			Keeping in Touch: Micro Mailing for the Solo Entrepreneur</a>
			<div class="spacerblock_20"></div>		
			
				
			<h2 class="tutorial">More blog tutorials</h2>
			
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2010/08/how-to-create-an-invoice-with-online-payment/">
			how to create an invoice with online payment
			</a>
			<div class="spacerblock_20"></div>		
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2010/03/time-management-with-ofuz-online-time-tracking/">
			Time management with Ofuz online time tracking
			</a>
			<div class="spacerblock_20"></div>		
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2009/12/get-emails-automatically-attached-to-your-contacts/">
			Get emails automatically attached to your contacts
			</a>
			<div class="spacerblock_20"></div>		
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2009/12/nudge-a-participant-in-a-project-discussion/">
			Nudge a participant in a project discussion
			</a>
			<div class="spacerblock_20"></div>		
			<a class="text16 text_bold" target="_new" href="http://www.ofuz.com/blog/2009/07/add-task-to-a-project-using-your-email/">
			Add task to a project using your email
			</a>
			<div class="spacerblock_20"></div>		
			        <div class="ofuzcom_button"><a href="http://www.ofuz.com/blog/" target="_new">More Blog Posts</a></div>
		</div>
		
		<div class="spacerblock_20"></div>        
        <div class="solidline"></div>
		<div class="spacerblock_20"></div>
		
		<div class="center_text text_blue"><h1 class="ofuzcom">Community</h1></div>	
		<div class="center_text">
			If you want to reach other Ofuz users we are on <a href="https://www.facebook.com/ofuzfan" target="_new">Facebook</a> 
			and <a href="https://twitter.com/#!/ofuz/">Twitter</a>.
			<br>		
		    We have built a community portal where we have forums and technical documention on Ofuz its mainly for developers but everybody is welcome.
		   <div class="ofuzcom_button"><a href="http://www.ofuz.com/opensource/" target="_new">Open Source Community</a></div>
		   
	    </div>
		<div class="spacerblock_20"></div>        
        <div class="solidline"></div>
		<div class="spacerblock_20"></div>		
		
		<div class="center_text text_blue"><h1 class="ofuzcom">Contact Us</h1></div>
		<div style="margin-left:250px; margin-right:200px;">
						<script type="text/javascript" src="http://www.ofuz.net/js_form.php?fid=175"></script>
		</div>				
        </div>
        <div class="solidline"></div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>

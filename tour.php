<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Tour';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/header.inc.php');
    $_SESSION["page_from"] = '';
?>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<script>
$(document).ready(function(){
	$(".video_tour_contacts").click(function () {
		$(".video_contacts").show();
	});
	
	$(".video_tour_projects").click(function () {
		$(".video_projects").show();
	});
	
	$(".video_tour_paid").click(function () {
		$(".video_paid").show();
	});
	
	$(".x").click(function () {
		$(".video_contacts").hide();
		$(".video_paid").hide();
		$(".video_projects").hide();
	});
	
	
});
</script>
<style>
.layout_content{font-family: Georgia !important;}
.indent30 {padding: 20px 50px; border: 1px solid #e0e0e0}

#tour_points {
width: 400px;
float: left;
margin: 10px 0px 15px;

}

#tour_screens {
float: right;
border: 1px solid #e0e0e0;
padding: 5px;
}

#clear {
clear: both;}

h1{
font-size: 36px !important;
color: #1e8cd3;
}

h1 a:hover {
text-decoration: underline;}

h2{
font-size: 28px;
color: #161616;
text-decoration: underline;
margin-bottom: 10px;
}

h3{
font-family: Tahoma;
font-size: 14px;
color: #c52687;
}

#video_tour {
width: 300px;
background-color: #ff6600;
margin: 20px auto;
text-align: center;
-moz-border-radius: 5px;-khtml-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;
border: 3px solid #ff9966;
padding: 10px 15px 10px;
font-weight: bold;
font-size: 20px;
}

#video_tour a{color: white !important;}

#video_tour:hover {
background-color: #ff9966;
border: 3px solid #ff6600;
cursor: pointer;
text-shadow: 1px 1px #ff6600;
}

#video_tour_frame {
position: absolute;
display: none;
width: 600px;
height: 400px;
border: 4px solid #ff6600;
margin: 0px auto;
background: white;
left: 25% !important;
}

.x {
cursor: pointer;
margin: 3px 5px 0px;
text-align: right;
}

hr {
height: 1px;
background-color: #ccc;
border: none;}

#sign_up{
width: 300px;
background-color: #c52687;
margin: 20px auto;
text-align: center;
-moz-border-radius: 5px;-khtml-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;
border: 3px solid #edbddb;
padding: 10px 15px 10px;
color: white;
font-weight: bold;
font-size: 30px;
}

#sign_up a{
color: white;
}


#sign_up:hover {
background-color: #edbddb;
border: 3px solid #c52687;
cursor: pointer;
text-shadow: 1px 1px #c52687;
}

</style>


<div class="layout_content">
<?php $thistab = 'Welcome'; include_once('includes/ofuz_navtabs.php'); ?>

   	<br/>
    <br/>
    <div class="indent30">
    	<h1 align="center">Manage <a href="#manage_contracts">Contacts</a>, <a href="#manage_projects">Projects</a> & <a href="#getting_paid">Getting Paid</a> </h1>
        <hr/>
        <hr/>
        <a name="manage_contacts"></a><h2 align="center">MANAGE CONTACTS</h2>
        
        <div id="tour_points">
        
        	<h3>Stay Connected & updated with clients & co-workers.</h3>
        	<span>oFuz allows your to easily import and add new contacts so you never go without important contact information. When one of your contacts changes their personal information that contacts info is globally changed so you have the most up to date contact information.</span>
        
        </div>
        
        <div id="tour_screens"><img src="tour/m_c_1.jpg"/></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Easily add notes to any contact so you never forget where you last left off.</h3>
        	<span>oFuz gives you the power to write public or privates notes about your contacts. You will now never forget when are what your last conversation was about. Check out other public notes to get familiar with what others are talking about with a specific contact.</span>
        	
        </div>
        
        <div id="tour_screens"><img src="tour/m_c_2.jpg"/></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Create tasks for a contact so you never miss an appointment or deadline.</h3>
        	<span>oFuz will never let you miss a meeting or appointment with your contacts again. Add a new task if you have planned a meeting or just want to keep that contact fresh in your mind.</span>
        
        </div>
        
        <div id="tour_screens"><img src="tour/m_c_3.jpg"/></div>
        
        <div id="clear"></div>
        
        <br/>
        
       <a name="video_tour_contacts"></a> <div id="video_tour" class="video_tour_contacts"><a href="#video_tour_contacts">Watch Video Tour</a></div>
        
        	<div id="video_tour_frame" class="video_contacts">
        		<div class="x">x</div>
        		<h2 align="center">MANAGE CONTACTS VDIEOS</h2>
        	</div>
        
        <br/>
        
        <hr/>
        <hr/>
        
        <!-- MANAGE PROEJCTS -->
        
        <a name="manage_projects"></a><h2 align="center">MANAGE PROJECTS</h2>
        
        <div id="tour_points">
        
        	<h3>Collaborate with Co-Workers or Clients</h3>
        	<span>Let your clients follow along and keep update with how their project is doing. Eliminate barriers that keep your clients from  helping you get projects done faster. You can also assign co-workers to projects so they can particpate in complete the project.</span>
        	
        </div>
        
        <div id="tour_screens"><img src="tour/m_p_1.jpg"/></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Create tasks within your project to stay on track</h3>
        	<span>No more writing on napkins for how your day will go. Create tasks for any deadline that will keep you organized so you can get things done faster.</span>
        	
        </div>
        
        <div id="tour_screens"><img src="tour/m_p_2.jpg"/></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Create notes within your Projects Task so everybody is on the same page</h3>
        	<span>Each task lets all those assigned keep notes for those who are working to complete the project. Are you used to going back and forth on Email with clients or co-workers? Simply add a unique oFuz BCC so the content of important emails don't get lost in your inbox.</span>
        </div>
        
        <div id="tour_screens"><img src="tour/m_p_3.jpg"/></div>
        
        <div id="clear"></div>
        
        <br/>
        
        <a name="video_tour_projects"></a> <div id="video_tour" class="video_tour_projects"><a href="#video_tour_projects">Watch Video Tour</a></div>
        
        	<div id="video_tour_frame" class="video_projects">
        		<div class="x">x</div>
        		<h2 align="center">MANAGE PROJECTS VDIEOS</h2>
        	</div>
        
        <br/>
		<hr/>
		<hr/>
        
        <!-- GET PAID -->
        
        <a name="getting_paid"></a><h2 align="center" name="getting_paid">GETTING PAID</h2>
        
        <div id="tour_points">
        
        	<h3>Create Custom Quotes & Invoices</h3>
        	<span>oFuz allows you to freely create quotes or invoices for your clients. Break down each quote or invoice by line item. Setup invoices to recur how ever often you need to charge your clients.</span>
        
        </div>
        
        <div id="tour_screens"><img src="tour/m_i_1.jpg"/></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Send Quotes or Invoices directly from oFuz</h3>
        	<span>Once you create your custom quote or invoice oFuz will deliver it to any contact. Your clients can then interact once they receive their quote or invoice in their email. </span>
        	
        </div>
        
        <div id="tour_screens"><img src="tour/m_i_2.jpg"/></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Get Paid faster with PayPal integration.</h3>
        	<span>oFuz integrates PayPal so you have a fast secure way of taking payments. Don't have a PayPal account? You can use your own payment methods or create a PayPal account.</span>
        
        </div>
        
        <div id="tour_screens"><img src="tour/m_i_3.jpg"/></div>
        
        <div id="clear"></div>
        <br/>
        <a name="video_tour_paid"></a>
        <div id="video_tour_frame" class="video_paid">
        	<div class="x">x</div>
        	<h2 align="center">GETTING PAID VDIEOS</h2>
        </div>
        
        <div id="video_tour" class="video_tour_paid"><a href="#video_tour_paid">Watch Video Tour</a></div>
        
        	
        
        <br/>        
        <hr/>
        <hr/>
        
        <h2 align="center" style="text-decoration:none;">A variety of account plans are available. Choose the plan that's best for you.</h2>
        
        <div id="sign_up"><a href="choose_plan.php">Sign Up for oFuz</a></div>
        
        <hr/>
        <hr/>
        


        
    </div><!-- END INDENT30 -->
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
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

<style>
.layout_content{font-family: Georgia !important;}
.indent30 {padding: 20px 40px;}
#tour_points {
width: 400px;
float: left;

}

#tour_screens {
float: right;
border: 3px solid #e0e0e0;
padding: 5px;
}

#clear {
clear: both;}

h1{
font-size: 34px !important;
color: #1e8cd3;
}

h2{
font-size: 20px;
color: #161616;
text-decoration: underline;
margin-bottom: 10px;
}

h3{
font-family: Tahoma;
font-size: 14px;
color: #161616;
}

#video_tour {
width: 300px;
background-color: #ff6600;
margin: 20px auto;
text-align: center;
-moz-border-radius: 5px;-khtml-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;
border: 3px solid #ff9966;
padding: 10px 15px 10px;
color: white;
font-weight: bold;
font-size: 20px;
}

#video_tour:hover {
background-color: #ff9966;
border: 3px solid #ff6600;
cursor: pointer;
}

</style>


<div class="layout_content">
<?php $thistab = 'Welcome'; include_once('includes/ofuz_navtabs.php'); ?>

   	<br/>
    <h1 align="center">Manage Contacts, Projects & Getting Paid </h1>
    <div class="indent30">
        <h2 align="center">MANAGE CONTACTS</h2>
        
        <div id="tour_points">
        
        	<h3>Stay Connected & updated with clients & co-workers.</h3>
        
        </div>
        
        <div id="tour_screens"><img src="tour/m_c_1.jpg"/></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Easily add notes to any contact so you never forget where you last left off.</h3>
        	
        </div>
        
        <div id="tour_screens"><img src="tour/m_c_2.jpg"/></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Set tasks for a contact so you never miss an appointment or deadline.</h3>
        
        </div>
        
        <div id="tour_screens"><img src="tour/m_c_3.jpg"/></div>
        
        <div id="clear"></div>
        
        <br/>
        
        <div id="video_tour">Watch Video Tour</div>
        
        <br/>
        
        <!-- MANAGE PROEJCTS -->
        
        <h2 align="center">MANAGE PROJECTS</h2>
        
        <div id="tour_points">
        
        	<h3>Stay Connected & updated with clients & co-workers.</h3>
        
        </div>
        
        <div id="tour_screens"></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Easily add notes to any contact so you never forget where you last left off.</h3>
        	
        </div>
        
        <div id="tour_screens"></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Set tasks for a contact so you never miss an appointment or deadline.</h3>
        
        </div>
        
        <div id="tour_screens"></div>
        
        <div id="clear"></div>
        
        <br/>
        
        <div id="video_tour">Watch Video Tour</div>
        
        <br/>

        
        <!-- GET PAID -->
        
        <h2 align="center">GETTING PAID</h2>
        
        <div id="tour_points">
        
        	<h3>Stay Connected & updated with clients & co-workers.</h3>
        
        </div>
        
        <div id="tour_screens"></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Easily add notes to any contact so you never forget where you last left off.</h3>
        	
        </div>
        
        <div id="tour_screens"></div>
        
        <div id="clear"></div><br/>
        
        <div id="tour_points">
        
        	<h3>Set tasks for a contact so you never miss an appointment or deadline.</h3>
        
        </div>
        
        <div id="tour_screens"></div>
        
        <div id="clear"></div>
        
        <br/>
        
        <div id="video_tour">Watch Video Tour</div>
        
        <br/>


        
    </div><!-- END INDENT30 -->
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
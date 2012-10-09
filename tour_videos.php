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
.indent30 {padding: 20px 50px; border: 1px solid #ccc}

#tour_points {
width: 400px;
float: left;
margin: 10px 0px 15px;
}

#tour_points span {
font-size: 16px !important;
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
font-size: 28px !important;
color: #161616;
text-decoration: underline;
margin-bottom: 10px;
}

h2 a{
font-size: 28px !important;
color: #161616 !important;
text-decoration: underline;
margin-bottom: 10px;
}

h3{
font-family: Tahoma;
font-size: 18px;
color: #c52687;
}



hr {
height: 1px;
background-color: #ccc;
border: none;}

#sign_up{
width: 300px;
margin: 20px auto;
text-align: center;
background-color: #e37ebc;
outline: 1px solid #c52687;
border-top: 1px solid #eea5d3;
border-left:none;
border-right:none;
border-bottom:none;
text-shadow: 1px 1px #c52687;
padding: 10px 15px 10px;
color: white;
font-weight: bold;
font-size: 30px;
}

#sign_up a{
color: white;
}


#sign_up:hover {
cursor: pointer;
}

#video_thumbs li {
float: left;
width: 300px;
list-style: none;
margin: 0px 20px;

}

#video_thumbs li #thumb {
border: 1px solid black;
width: 300px;
height: 175px;
}

#video_thumbs li p {
width: 300px;
font-size: 12px;
}

#main_video{
width: 800px;
height: 500px;
border: 1px solid black;
margin: 0px auto;}

#video_desc {
width: 800px;
margin: 10px auto 15px;
font-size: 12px;
}


</style>


<div class="layout_content">
<?php $thistab = 'Welcome'; include_once('includes/ofuz_navtabs.php'); ?>

   	<br/>
    <br/>
    <div class="indent30">
    	<h1 align="center">oFuz Video Tour</h1>
        <hr/>
        <hr/>
        <a name="manage_contacts"></a><h2 align="center">Video #1</h2>
       
        
        <br/>
        
        	<div id="main_video">
        	
        	
        	</div>
        	<p id="video_desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur interdum ultricies blandit. Maecenas elit nunc, vehicula ac elementum ac, placerat in justo. Curabitur consequat nisi at enim interdum pulvinar porttitor elit iaculis. Quisque ultrices tincidunt tellus suscipit sagittis. Cras non arcu eleifend est vehicula volutpat. Suspendisse elementum sodales dictum. In elit nisi, vehicula bibendum suscipit a, posuere sit amet urna.</p>
        	
        

        
       
       
        
        <div id="clear"></div>
      
        
        <hr/>
        <hr/>
        
        <ul id="video_thumbs">
        
        	<li>
        		<h3>Video #2</h3>
        		<div id="thumb"></div>
        		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur interdum ultricies blandit. Maecenas elit nunc, vehicula ac elementum ac, placerat in justo. Curabitur consequat nisi at enim interdum pulvinar porttitor elit iaculis.</p>
        	</li>
        	
        	<li>
        		<h3>Video #3</h3>
        		<div id="thumb"></div>
        		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur interdum ultricies blandit. Maecenas elit nunc, vehicula ac elementum ac, placerat in justo. Curabitur consequat nisi at enim interdum pulvinar porttitor elit iaculis.</p>

        	</li>
        	
        	<li>
        		<h3>Video #4</h3>
        		<div id="thumb"></div>
        		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur interdum ultricies blandit. Maecenas elit nunc, vehicula ac elementum ac, placerat in justo. Curabitur consequat nisi at enim interdum pulvinar porttitor elit iaculis.</p>

        	</li>
        	
        	<li>
        		<h3>Video #5</h3>
        		<div id="thumb"></div>
        		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur interdum ultricies blandit. Maecenas elit nunc, vehicula ac elementum ac, placerat in justo. Curabitur consequat nisi at enim interdum pulvinar porttitor elit iaculis.</p>
        	</li>
        	
        	<li>
        		<h3>Video #6</h3>
        		<div id="thumb"></div>
        		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur interdum ultricies blandit. Maecenas elit nunc, vehicula ac elementum ac, placerat in justo. Curabitur consequat nisi at enim interdum pulvinar porttitor elit iaculis.</p>

        	</li>
        	
        	<li>
        		<h3>Video #7</h3>
        		<div id="thumb"></div>
        		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur interdum ultricies blandit. Maecenas elit nunc, vehicula ac elementum ac, placerat in justo. Curabitur consequat nisi at enim interdum pulvinar porttitor elit iaculis.</p>

        	</li>
        
        
        </ul>
        
        <div id="clear"></div>  
        
        <hr/><hr/>
       
        <div id="sign_up"><a href="choose_plan.php">Sign Up for oFuz</a></div>
        
        <p align="center" style="text-decoration:none;color:black;font-size:12px;"><i>A variety of account plans are available. Choose the plan that's best for you.</i></p>
        
       <hr/><hr/>
	  


        
    </div><!-- END INDENT30 -->
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
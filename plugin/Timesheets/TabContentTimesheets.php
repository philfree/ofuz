<?php 
  /**
   * This content can be access by the following url
   * /Tab/SamplePlugIn/TabContentSample
   */


      /*echo $_SESSION['do_User']->getFullName().' '._('Welcome to SQLFusion. This is a sample page for the plugin Tab.');
      echo '<br />';
      echo _('Creating Tab with Plugin is very simple with Ofuz Plugin API. Give it a try and we are sure you will love this !!');*/


?>
<!--<div class="dottedline"></div>
<a href="/Tab/Timesheets/TabContentTimesheetsPage2"> Check out the second page of this Sample Plug in</a>
<br/>
<br/>
<br/>
Here is an other example of plug in page: <a href="/PlugIn/Timesheets/TimesheetsPage">/PlugIn/SamplePlugin/SamplePage</a> its a simple page thats do not need a TAB its usefull if you need to link a Block to a simple page. 

<br/>
<div class="dottedline"></div>
The documentation to create Ofuz Plug-in can be found at: <br/> <a href="http://www.ofuz.com/opensource/wiki/plugin_api" title="plugin api documentation">http://www.ofuz.com/opensource/wiki/plugin_api</a>-->

<!--<link href='../../includes/fullcalendar.css' rel='stylesheet' />
<link href='../../includes/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='../../includes/moment.min.js'></script>
<script src='../../includes/jquery.min.js'></script>
<script src='../../includes/fullcalendar.min.js'></script>
<script>

	$(document).ready(function() {
	   /* $("#dijit_form_ComboBox_0").onChange(function(val){
        alert("Text: " + $("#dijit_form_ComboBox_0").text());
        //function getState(val) {
        	$.ajax({
        	type: "POST",
        	url: "get_task.php",
        	data:'get_task='+val,
        	success: function(data){
        		$("#state-list").html(data);
        	}
        	});
        //}
    });*/
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'agendaWeek,agendaDay'
			},
			defaultDate: '2016-01-12',
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			events: {
				url: 'php/get-events.php',
				error: function() {
					$('#script-warning').show();
				}
			},
			loading: function(bool) {
				$('#loading').toggle(bool);
			}
		});
		
	});

</script>
<style>

	body {
		margin: 0;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}

	#script-warning {
		display: none;
		background: #eee;
		border-bottom: 1px solid #ddd;
		padding: 0 10px;
		line-height: 40px;
		text-align: center;
		font-weight: bold;
		font-size: 12px;
		color: red;
	}

	#loading {
		display: none;
		position: absolute;
		top: 10px;
		right: 10px;
	}

	#calendar {
		max-width: 900px;
		margin: 40px auto;
		padding: 0 10px;
	}

</style>
	<div id='script-warning'>
		<!--<code>php/get-events.php</code> must be running.
	</div>

	<div id='loading'>loading...</div>

	<div id='calendar'></div>-->
  <style>
  ul {list-style-type: none;}
body {font-family: Verdana, sans-serif;}

/* Month header */
.month {
    padding: 70px 25px;
    width: 100%;
    background: #1abc9c;
}

/* Month list */
.month ul {
    margin: 0;
    padding: 0;
}

.month ul li {
    color: white;
    font-size: 20px;
    text-transform: uppercase;
    letter-spacing: 3px;
}

/* Previous button inside month header */
.month .prev {
    float: left;
    padding-top: 10px;
}

/* Next button */
.month .next {
    float: right;
    padding-top: 10px;
}

/* Weekdays (Mon-Sun) */
.weekdays {
    margin: 0;
    padding: 10px 0;
    background-color:#ddd;
}

.weekdays li {
    display: inline-block;
    width: 13.6%;
    color: #666;
    text-align: center;
    border-right: 2px ridge silver;
}

/* Days (1-31) */
.days {
    padding: 10px 0;
    background: #eee;
    margin: 0;
    height: 300px;
}

.days li {
    list-style-type: none;
    display: inline-block;
    width: 13.6%;
    text-align: center;
    margin-bottom: 5px;
    font-size:12px;
    color:#777;
}

/* Highlight the "current" day */
.days li .active {
    padding: 5px;
    background: #1abc9c;
    color: white !important
}
  </style>
  <script>
  function hideTotals(){

    $("td.layout_rcolumn").prev('td').hide();
    $("#totals_txt").show();
	 $("#close_tot").hide();
    $.ajax({
        type: "GET",
	<?php
	$e_hide = new Event("do_invoice_list->eventHideTotal");
	$e_hide->setEventControler("ajax_evctl.php");
	$e_hide->setSecure(false);
	?>
        url: "<?php echo $e_hide->getUrl(); ?>",
        success: function(hide_inv){ 
        }
    });
}

function showTotals(){
    //$(".layout_lcolumn").show(0);
    $("td.layout_rcolumn").prev('td').show();
    $("#close_tot").show();
    $("#totals_txt").hide();
    $.ajax({
        type: "GET",
	<?php
	$e_show = new Event("do_invoice_list->eventShowTotal");
	$e_show->setEventControler("ajax_evctl.php");
	$e_show->setSecure(false);
	?>
        url: "<?php echo $e_show->getUrl(); ?>",
        success: function(hide_inv){ 
        }
    });
}
  </script>
  
  <?php
					if($_SESSION["show_total"]) {
						$show_total = $_SESSION["show_total"];
					} else {
						$show_total = 'display:none;';
					}
                    //if($_SESSION['inv_past_due_hide'] == 'Yes'){
                        echo '<span id="totals_txt" style="'.$show_total.'"><a href="#" onclick="showTotals();return false;">'._('show totals').'</a></span>';   
                    //}
                ?>

  <a href="#" id="close_tot" onclick="hideTotals();return false;"><?php echo _('( hide totals )');?></a>
<ul>
<ul>
<li><?php $today = date("D M j"); echo $today; ?> </li><li><input type="button" name="Day" value="Day"><li><input type="button" name="Week" value="Week"></li>
</ul>
<li>Total Hours: </li>
</ul>
<ul class="weekdays">
  <li>M</li>
  <li>T</li>
  <li>W</li>
  <li>T</li>
  <li>F</li>
  <li>S</li>
  <li>S</li>
</ul>

<ul class="days"> 
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li><span class="active"></span></li>
<li></li>
</ul>
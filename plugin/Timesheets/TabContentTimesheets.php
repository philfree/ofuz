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

<link href='../../includes/fullcalendar.css' rel='stylesheet' />
<link href='../../includes/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='../../includes/moment.min.js'></script>
<script src='../../includes/jquery.min.js'></script>
<script src='../../includes/fullcalendar.min.js'></script>
<script>

	$(document).ready(function() {
	
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
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
		<!--<code>php/get-events.php</code> must be running.-->
	</div>

	<div id='loading'>loading...</div>

	<div id='calendar'></div>

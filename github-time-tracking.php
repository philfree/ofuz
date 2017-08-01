<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>GitHub Issues Time Tracking System</title>

    <!-- Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<style type="text/css">
		.right-container-main {
			border-left: 1px solid #ff0000;
		}
		.top-margin-20 {
			margin-top: 20px;
		}
		.top-margin-10 {
			margin-top: 10px;
		}
		.bottom-margin-50 {
			margin-bottom: 20px;
		}
		.ajaxIndicator {
			display: none;
		}
		.heading-hr-red {
			border-bottom: 1px solid #ff0000;
		}
		.heading-hr-green {
			border-bottom: 1px solid green;
		}
	</style>
  </head>
  <body>
<?php
include_once('config.php');
include_once('includes/ofuz_check_access.script.inc.php');

$do_github = new OfuzGitHubAPI();

$current_year = date("Y");
$previous_year = $current_year - 1;
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Ofuz :: GitHub Time Tracking System</a>
        </div>
      </div>
    </nav>
    <div class="jumbotron">
    </div>

	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<h4>Time Spent by Authors</h4>
				<div id="leftContainer" class="top-margin-20"></div>
				<div class="ajaxIndicator"><img src="images/ajax-loader1.gif"/></div>
			</div>
			<div class="col-md-8 right-container-main bottom-margin-50">
				<select class="report_selects" id="year" name="year">
					<option value="<?php echo $current_year;?>"><?php echo $current_year;?></option>
					<option value="<?php echo $previous_year;?>"><?php echo $previous_year;?></option>
				</select>
<?php
$months_dropdown_html = $do_github->getMonthsDropDown();
$weeks_dropdown_html = $do_github->getWeekRangeDropDown();
?>
				<span id="monthsDropdown"><?php echo $months_dropdown_html;?></span>
				<span id="weeksDropdown"><?php echo $weeks_dropdown_html;?></span>
				<input type="button" id="submit" name="submit" value="Submit" />	
				<div id="rightContainer" class="top-margin-20"></div>
				<div class="ajaxIndicator"><img src="images/ajax-loader1.gif"/></div>
			</div>
		</div>
	</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	getTimeSheetReport();

	$('.report_selects').on('change', function() {
		var month = $('#months').val();
		var year = $('#year').val();

		$.ajax({
				type: "GET",
				<?php
				  $e_ofuz_github = new Event("OfuzGitHubAPI->eventGetWeeksRangeDropdown");
				  $e_ofuz_github->setEventControler("ajax_evctl.php");
				  $e_ofuz_github->setSecure(false);
				?>
				url: "<?php echo $e_ofuz_github->getUrl(); ?>",
				data: "month="+month+"&year="+year,
				success: function(result){
					$("#weeksDropdown").html(result);
				}
			  });
	});

	$('#submit').on('click', function() {
		getTimeSheetReport();
	});

});

/*
 *
 */
function getTimeSheetReport() {

	var year = $('#year').val();
	var month = $('#months').val();
	var weeks = $('#weeks').val();
	$.ajax({
			type: "GET",
			<?php
			  $e_report = new Event("OfuzGitHubAPI->eventGetTimesheetReport");
			  $e_report->setEventControler("ajax_evctl.php");
			  $e_report->setSecure(false);
			?>
			url: "<?php echo $e_report->getUrl(); ?>",
			data: "month="+month+"&year="+year+'&weeks='+weeks,
			beforeSend: function(){
				$('.ajaxIndicator').show();
			},
			complete: function(){
				$('.ajaxIndicator').hide();
			},
			success: function(result){

				//console.log(result);
				var data = JSON.parse(result);
				//console.log(data);
				var dataLength = Object.keys(data).length;
				var leftContainer = "";
				var rightContainer = "";

				if(dataLength) {
					$.each(data.authorsTime, function(index, value){
						leftContainer += '<div>'+value.commentAuthor+' : <b>'+value.timeTaken+' hrs</b></div>';
					});

					$.each(data.repositories, function(index, repo){
						rightContainer += '<div class="top-margin-20"><span class="heading-hr-red">Total time spent on <b>'+repo.organization + ' / ' + repo.repository+'</b> : '+repo.totalTimeSpent+' hrs</span></div>';
						rightContainer += '<div class="top-margin-20"><b class="heading-hr-green">Per Issues:</b></div>';
						
						$.each(repo.issues.issue, function(index, issue){
							rightContainer += '<div><b>'+issue.time_taken+' hrs</b> on '+issue.title+'</div>';
						});

						rightContainer += '<div class="top-margin-20"><b class="heading-hr-green">Per Authors:</b></div>';
						
						$.each(repo.authors.author, function(index, author){
							rightContainer += '<div><b>'+author.time_taken+' hrs</b> by '+author.login+'</div>';
						});
					});
				} else {
					leftContainer = "Time not yet entered.";
				}

				$("#leftContainer").html(leftContainer);
				$("#rightContainer").html(rightContainer);

			}
	});

}
	</script>
  </body>
</html>

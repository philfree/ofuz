<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>AfterNow :: Time Tracking System</title>

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
include_once('OfuzGitHubAPI.class.php');

$do_github = new OfuzGitHubAPI($conn);

$current_year = date("Y");
$previous_year = $current_year - 1;
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">AfterNow :: Time Tracking System</a>
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
				<div class="ajaxIndicator"><img src="ajax-loader1.gif"/></div>
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
				<div class="ajaxIndicator"><img src="ajax-loader1.gif"/></div>
			</div>
		</div>
	</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	// On page load, display the Timesheet report
	getTimeSheetReport();

	$('.report_selects').on('change', function() {
		var month = $('#months').val();
		var year = $('#year').val();
		var weeks = $('#weeks').val();

		$.ajax({
			type: "POST",
			url: "tracking-process.php",
			data: "month="+month+"&year="+year+"&weeks="+weeks+"&feature=week-range",
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
 * Generates the Timesheet report fetching Data from Database
 * Uses Radria Ajax event controler
 *
 */
function getTimeSheetReport() {

	var year = $('#year').val();
	var month = $('#months').val();
	var weeks = $('#weeks').val();
	$.ajax({
			type: "POST",
			url: "tracking-process.php",
			data: "month="+month+"&year="+year+'&weeks='+weeks+"&feature=timesheet-all",
			beforeSend: function(){
				$('.ajaxIndicator').show();
			},
			complete: function(){
				$('.ajaxIndicator').hide();
			},
			success: function(result){
				$("#leftContainer").html(result);

				var data = JSON.parse(result);
				var dataLength = Object.keys(data).length;
				var leftContainer = "";
        var rightContainer = "";

        console.log(data);

        if(dataLength) {
          // GitHub
          leftContainer += '<div><b class="heading-hr-green">GitHub Board</b></div>';
					$.each(data.authorsTime, function(index, value){
						leftContainer += '<div>'+value.commentAuthor+' : <b>'+value.timeTaken+' hrs</b></div>';
          });

          // Wekan
          leftContainer += '<div><b class="heading-hr-green">Wekan Board</b></div>';
					$.each(data.users, function(index, value){
						leftContainer += '<div>'+value.commentAuthor+' : <b>'+value.timeTaken+' hrs</b></div>';
          });

          $.each(data.repositories, function(index, repo){
            // GitHub
						rightContainer += '<div class="top-margin-20"><span class="heading-hr-red">Total time spent on <b>'+repo.organization + ' / ' + repo.repository+'</b> : '+repo.totalTimeSpent+' hrs</span></div>';
						rightContainer += '<div class="top-margin-20"><b class="heading-hr-green">Per Issues:</b></div>';
						
						$.each(repo.issues.issue, function(index, issue){
							rightContainer += '<div><b>'+issue.time_taken+' hrs</b> on '+issue.title+'</div>';
						});

						rightContainer += '<div class="top-margin-20"><b class="heading-hr-green">Per Pull Requests:</b></div>';
						
						$.each(repo.pullRequests.pullRequest, function(index, pr){
							rightContainer += '<div><b>'+pr.time_taken+' hrs</b> on '+pr.title+'</div>';
						});

						rightContainer += '<div class="top-margin-20"><b class="heading-hr-green">Per Authors:</b></div>';
						
						$.each(repo.authors.author, function(index, author){
							rightContainer += '<div><b>'+author.time_taken+' hrs</b> by '+author.login+'</div>';
						});
          });

          // wekan board
          $.each(data.boards, function (index, board){
            rightContainer += '<div class="top-margin-20"><span class="heading-hr-red">Total time spent on <b>'+board.organization + ' / ' + board.board+'</b> : '+board.totalTimeSpent+' hrs</span></div>';

						rightContainer += '<div class="top-margin-20"><b class="heading-hr-green">Per Cards:</b></div>';
						
						$.each(board.cards.card, function(index, card){
							rightContainer += '<div><b>'+card.time_taken+' hrs</b> on '+card.title+'</div>';
            });

						rightContainer += '<div class="top-margin-20"><b class="heading-hr-green">Per Authors:</b></div>';
						
						$.each(board.users.author, function(index, author){
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

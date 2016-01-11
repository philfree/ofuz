<script type="text/javascript" src="jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#btnSubmit').click(function() {
    var username = $.trim($('#username').val());
    var pwd = $.trim($('#password').val());
    //Blank validation
    if(username != '' && pwd != '') {
	//An ajax call to add user's LeanKit Kanban login credentials.
        $.ajax({
            type: "GET",
        <?php
        $e = new Event("OfuzLeanKitKanban->eventAjaxAddLoginCredentials");
        $e->setEventControler("ajax_evctl.php");
        $e->setSecure(false);
        ?>
            url: "<?php echo $e->getUrl(); ?>",
            data: "un="+username+"&pwd="+pwd,
            success: function(response){
	      $('#msg').html(response);
	      $('#msg').slideDown('slow');
            }
        });
    } else {
      $('#msg').html('Your Email Address and Password are required.');
      $('#msg').slideDown('slow');
    }
  });
});
</script>
<?php
$username = "";
$password = "";
$do_olk = new OfuzLeanKitKanban();
$do_olk->getUserLoginCredentials();
if($do_olk->getNumRows()) {
  $username = $do_olk->username;
  $password = $do_olk->password;
}
?>

<div id="msg" style="display:none;"></div>
<div>
<form>
<div>Email Address: <input type="text" name="username" id="username" value="<?php echo $username;?>" /></div>
<div>Password: <input type="password" name="password" id="password" value="<?php echo $password;?>" /></div>
<div><input type="button" name="btnSubmit" id="btnSubmit" value="Submit" /></div>
</form>
</div>
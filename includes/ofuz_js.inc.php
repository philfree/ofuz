<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
?>
// JS functions from includes/ofuz_js.inc.php

// close message hide is and record it on the server.
	<?php
		$e_closeMessage = new Event("Message->eventAjaxCloseMessage");
		$e_closeMessage->setEventControler("ajax_evctl.php");
		$e_closeMessage->setSecure(false);
	?>
    function close_message(idmessage) {
			$.ajax({
            type: "GET",
            url: "<?php echo $e_closeMessage->getUrl(); ?>",
            data: "idmessage="+idmessage,
            success: function(){
            	$("#message_"+idmessage).fadeOut("slow");
            }
        });
    }

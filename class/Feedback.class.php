<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  
  /**
   * Class Feedback
   * Creates a form to submit user opinions about the site
   * 
   */


class Feedback extends DataObject {

    /*
     * Builds a form to collect feedback
     */
    function createFeedbackBox() {
        $e_SendFeedback = new Event("Feedback->eventAjaxSendFeedback");
        $e_SendFeedback->setEventControler("ajax_evctl.php");
        $e_SendFeedback->setSecure(false);
        $strURL = $e_SendFeedback->getUrl();
        echo <<<HTML
        <div id="feedback_box">
            <script type="text/javascript">
            //<![CDATA[
            $(document).ready(function(){
            $("#feedback_button").click(function(){
                if($("#feedback_box").css("left")!="-300px"){
                feedback_close();
                }else{
                $("#feedback_box").animate({left:"0"},500);
                $("#feedback_text").focus();
                }
            });});
            function feedback_close(){
                $("#feedback_box").animate({left:"-300px"},500);
                $("#feedback_text").val("");
                $("#feedback_ty").hide(1500);
            }
            function feedback_ty(){
                $("#feedback_ty").fadeIn(2000);
                setTimeout(feedback_close, 5000);
            }
            function submitFeedback(){
                $.ajax({
                    type: "GET",
                    url: "$strURL",
                    data: "url="+escape(document.location.href)+"&text="+escape($("#feedback_text").val()),
                    success: feedback_ty()
                });
            }
            //]]>
            </script>
            <div class="feedback_main"><div class="feedback_form">
            <span class="headline12">
HTML;
	    echo _('Send Us Feedback');
echo <<<HTML
	    </span><br />
HTML;
            echo _('Let us know what you think. &nbsp; Likes, dislikes, questions, comments, bugs, or anything.');
echo <<<HTML
            <form method="post" action="/"><textarea id="feedback_text"></textarea>
            <input type="button" value="Submit" onclick="submitFeedback();" /> or <a href="#" onclick="feedback_close();">Cancel</a>
            </form></div><div id="feedback_ty">
            <span class="headline12">
HTML;
	    echo _('Thank You');
echo <<<HTML
	    </span><br /><br />
HTML;
            echo _('Your comments are valuable to us. &nbsp; We appreciate your taking the time to help make Ofuz even better.');
echo <<<HTML
            </div></div>
            <input id="feedback_button" type="image" src="/images/send_feedback.jpg" />
        </div>
HTML;
    }

    /*
     * Receives and emails feedback
     */
    function eventAjaxSendFeedback(EventControler $event_controler) {
        $subject = 'Ofuz User Feedback: '.$event_controler->url;
        //$headers = 'From: "Ofuz User Feedback" <ofuz@ofuz.net>'."\n\n";
        $headers = 'From: "'.$_SESSION['do_User']->firstname.' '.$_SESSION['do_User']->lastname.'" <'.$_SESSION['do_User']->email.'>'."\n\n";
        mail('philippe@sqlfusion.com',$subject,$event_controler->text,$headers);
        $event_controler->addOutputValue(true);
    }
}
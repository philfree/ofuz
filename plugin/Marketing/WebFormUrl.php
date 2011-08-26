<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/*
 *      Web form creator
 *      This will let the user selec the fields that will display 
 *      in the web form. He will also add initial default tags.
 *      
 *      Copyright 2009 SQLFusion LLC, Philippe Lewicki <philippe@sqlfusion.com>
 *      
 */

				$msg = new Message(); 
				$msg->getMessage('web form url instruction');
				$msg->displayMessage();

		            $_SESSION['do_userform']->setApplyRegistry(false);
		            echo _('The url for the web form '). '<b>'.$_SESSION['do_userform']->title.'</b>'._(' is ').
		                $GLOBALS['cfg_ofuz_site_http_base'].'form/'.
						$_SESSION['do_userform']->getPrimaryKeyValue(); 
						?>
		</div>
		<?php echo _('Embed code to insert in your blog or web site'); ?>
		<div>
		<textarea rows="2" cols="100"><script type="text/javascript" src="<?php echo $GLOBALS['cfg_ofuz_site_http_base'].'js_form.php?fid='.$_SESSION['do_userform']->getPrimaryKeyValue(); ?>"></script>
        </textarea>
		</div>

	   <?php 
              if($_SESSION['setting_mode'] == 'Yes'){
                    echo '<a href="'.$cfg_plugin_mkt_path.'WebForm">'._('Back').'</a>';
                    $_SESSION['setting_mode'] = '';
              }
           ?>
    </div>
    <div class="spacerblock_40"></div>

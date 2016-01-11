<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/*
 *      Web form creator
 *      This will let the user selec the fields that will display 
 *      in the web form. He will also add initial default tags.
 *      
 *      Copyright 2009 SQLFusion LLC, Philippe Lewicki <philippe@sqlfusion.com>
 *      
 */


    include_once('config.php');
    $pageTitle = _('Ofuz :: Contacts Web Form Creator');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
		
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

?>


<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">


<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); 

 $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Web Form Url'); ?></span>
        </div>
    </div>
    <div class="contentfull">
		<div class="messageshadow">
            <div class="messages">
            <?php
				$msg = new Message(); 
				echo $msg->getMessage('web form url instruction');
            ?>
            </div>
        </div>
		<div><?php 
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
                    echo '<a href="/settings_wf.php">'._('Back').'</a>';
                    $_SESSION['setting_mode'] = '';
              }
           ?>
    </div>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>

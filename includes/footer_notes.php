<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
?>
<div class="section20">
<?php
if (isset($footer_note)) {
    echo '<span class="text8"><b>', _('Did you know? '),'</b>';

    switch($footer_note) {
    	case 'emailstream':
            echo _('You can create Notes via email by using your'),' <a href="/email_stream.php">',_('create-a-note-by-email'),'</a> ',_('stream addresses.');
            break;
    	case 'dropboxtask':
            echo _('You can create tasks via email by using your'),' <a href="/drop_box_task.php">',_('create-a-task-by-email'),'</a> ',_('dropbox addresses.');
            break;
    	case 'contact':
            echo '';
            break;
    }

    echo '</span>', "\n";
}
?>
</div>
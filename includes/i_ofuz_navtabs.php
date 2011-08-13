<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
?>
<div class="itopblue">
    <div class="navtabs">
<?php
//$arrTabs[] = array('Welcome', 'i_index.php', 0);
$arrTabs[] = array('Contacts', 'i_contacts.php', 0);
$arrTabs[] = array('Tasks', 'i_tasks.php', 0);
//$arrTabs[] = array('Sync', 'sync.php', 1);

foreach ($arrTabs as $arrTab) {
    $tabclass = (isset($thistab) && $thistab == $arrTab[0]) ? 'inavtab_on' : 'inavtab';
    $style = ($arrTab[2] == 1) ? ' style="float: right;"' : '';
    echo '<div class="', $tabclass, '"', $style, '><div class="inavtab_text"><a href="',
         $arrTab[1], '">', $arrTab[0], '</a></div></div>';
}
?>
    </div>
</div>

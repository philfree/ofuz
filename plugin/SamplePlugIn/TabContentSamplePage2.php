<?php 

      echo _('Just an empty second page, The iteresting part is that here we can catch the id passed in the Url');
      echo '<br />';
      if (isset($plugin_item_value)) {
        echo _('The id passed by URL is:').'  <b>'.$plugin_item_value.' </b>';
      }
      echo '<br />';

?>
<div class="dottedline"></div>
<a href="/Tab/SamplePlugIn/TabContentSample">Back</a>



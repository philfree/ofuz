<?php 
  /**
   * This content can be access by the following url
   * /Tab/SamplePlugIn/TabContentSample
   */


      echo $_SESSION['do_User']->getFullName().' '._('Welcome to SQLFusion. This is a sample page for the plugin Tab.');
      echo '<br />';
      echo _('Creating Tab with Plugin is very simple with Ofuz Plugin API. Give it a try and we are sure you will love this !!');


?>
<div class="dottedline"></div>
<a href="/Tab/SamplePlugIn/TabContentSamplePage2/1234"> Check out the second page of this Sample Plug in</a>
<br/>
<br/>
<br/>
Here is an other example of plug in page: <a href="/PlugIn/SamplePlugin/SamplePage">/PlugIn/SamplePlugin/SamplePage</a> its a simple page thats do not need a TAB its usefull if you need to link a Block to a simple page. 

<br/>
<div class="dottedline"></div>
The documentation to create Ofuz Plug-in can be found at: <br/> <a href="http://www.ofuz.com/opensource/wiki/plugin_api" title="plugin api documentation">http://www.ofuz.com/opensource/wiki/plugin_api</a> 

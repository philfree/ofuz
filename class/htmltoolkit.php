<?php
/*
This script is supposed to be used together with the HTML2FPDF.php class
Copyright (C) 2004 Renato Coelho                                         
*/

function ConvertColor($color="#000000"){
//returns an associative array (keys: R,G,B) from a html code (e.g. #3FE5AA)
  if ($color{0} == '#') //case of #nnnnnn or #nnn
  {
  	$cor = strtoupper($color);
  	if (strlen($cor) == 4) // Turn #RGB into #RRGGBB
  	{
	 	  $cor = "#" . $cor{1} . $cor{1} . $cor{2} . $cor{2} . $cor{3} . $cor{3};
	  }  
	  $R = substr($cor, 1, 2);
	  $vermelho = hexdec($R);
	  $V = substr($cor, 3, 2);
	  $verde = hexdec($V);
	  $B = substr($cor, 5, 2);
	  $azul = hexdec($B);
	  $color = array();
	  $color['R']=$vermelho;
	  $color['G']=$verde;
	  $color['B']=$azul;
  }
  else //case of RGB(r,g,b)
  {
  	$color = str_replace("rgb(",'',$color); //remove ´rgb(´
  	$color = str_replace("RGB(",'',$color); //remove ´RGB(´ -- PHP < 5 does not have str_ireplace
  	$color = str_replace(")",'',$color); //remove ´)´
    $cores = explode(",", $color);
    $color = array();
	  $color['R']=$cores[0];
	  $color['G']=$cores[1];
	  $color['B']=$cores[2];
  }
  return $color;
}

function ConvertSize($size=5,$maxsize=0){
// Depends of maxsize value to make % work properly. Usually maxsize == pagewidth

  //Identify size (remember: we are using ´mm´ units here)
  if ( strstr($size,'px') ) $size *= 0.2645; //pixels
  elseif ( strstr($size,'cm') ) $size *= 10; //centimeters
  elseif ( strstr($size,'mm') ) $size += 0; //millimeters
  elseif ( strstr($size,'in') ) $size *= 25.4; //inches 
  elseif ( strstr($size,'pc') ) $size *= 38.1/9; //PostScript picas 
  elseif ( strstr($size,'pt') ) $size *= 25.4/72; //72dpi
  elseif ( strstr($size,'%') )
  {
  	$size += 0; //make "90%" become simply "90" 
  	$size *= $maxsize/100;
  }
  else $size *= 0.2645; //nothing == px
  
  return $size;
}

function lesser_entity_decode($html)
{
//supports the most used entity codes
 	$html = str_replace("&nbsp;"," ",$html); 
 	$html = str_replace("&#380;","¿",$html);
 	$html = str_replace("&amp;","&",$html);
 	$html = str_replace("&lt;","<",$html);
 	$html = str_replace("&gt;",">",$html);
 	$html = str_replace("&#728;","¢",$html); 
 	$html = str_replace("&#321;","£",$html); 
 	$html = str_replace("&euro;","€",$html);
 	$html = str_replace("&#260;","¥",$html); 
 	$html = str_replace("&trade;","™",$html);
 	$html = str_replace("&copy;","©",$html); 
 	$html = str_replace("&reg;","®",$html); 
  return $html;
}

function txtentities($html){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	return strtr($html, $trans);
}

function AdjustHTML(&$html,$usepre=true)
{
//Try to make the html text more manageable (turning it into XHTML)

 	$html = str_replace("\r\n","\n",$html); //replace carriagereturn-linefeed-combo by a simple linefeed
 	if ($usepre) //used to keep \n on content inside <pre> or <textarea>
 	{
  // Preserve '\n's between the tags <pre> and </pre>
	$regexp = '/<pre(.*?)>(.+?)<\/pre>/si';
	$thereispre = preg_match_all($regexp,$html,$temp);
  // Preserve '\n's between the tags <textarea> and </textarea>
	$regexp2 = '/<textarea(.*?)>(.+?)<\/textarea>/si';
	$thereistextarea = preg_match_all($regexp2,$html,$temp2);
	$iterator = 0;
	$html = str_replace("\f",'',$html); //replace formfeed by nothing
	$html = str_replace("\r",'',$html); //replace carriage return by nothing
	$html = str_replace("\n",'',$html); //replace linefeed by nothing
	$html = str_replace("\t",' ',$html); //replace tabs by spaces
	while($thereispre != 0) //Recover <pre>content</pre>
	{
  	$html = preg_replace($regexp,'<erp'.$temp[1][$iterator].'>'.$temp[2][$iterator].'</erp>',$html,1);
  	$thereispre--;
  	$iterator++;
  }
	while($thereistextarea != 0) //Recover <textarea>content</textarea>
	{
  	$html = preg_replace($regexp2,'<aeratxet'.$temp2[1][$iterator].'>'.trim($temp2[2][$iterator]).'</aeratxet>',$html,1);
  	$thereistextarea--;
  	$iterator++;
  }
  $html = str_replace("<erp","<pre",$html); //restore
  $html = str_replace("</erp>","</pre>",$html); //restore
  $html = str_replace("<aeratxet","<textarea",$html); //restore
  $html = str_replace("</aeratxet>","</textarea>",$html); //restore
  // (the code above might slowdown overall performance?)
  } //if ($usepre)
  else
  {
  	$html = str_replace("\f",'',$html); //replace formfeed by nothing
  	$html = str_replace("\r",'',$html); //replace carriage return by nothing
  	$html = str_replace("\n",'',$html); //replace linefeed by nothing
  	$html = str_replace("\t",' ',$html); //replace tabs by spaces
  }
  $html = str_replace("< IMPRIMIR >",'',$html); //remover especial desta versão
	$regexp = '/\\s{2,}/s'; // turn 2+ consecutive spaces into one 
	$html = preg_replace($regexp,' ',$html);
  //Avoid crashing the script on PHP 4.0
  $version = phpversion();
  $version = str_replace('.','',$version);
  if ($version >= 430) $html = html_entity_decode($html); // changes &nbsp; and the like by the respective char
  else $html = lesser_entity_decode($html);
  // remove redundant <br>'s before </div>, avoiding huge leaps between text blocks
  // they appear on computer-generated HTML code  
	$regexp = '/(<br[ \/]?[\/]?>)+?<\/div>/si'; 
	$html = preg_replace($regexp,'</div>',$html);

}

?>
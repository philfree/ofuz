<?php
/**

 This is a test to customize fpdf and html2fpdf

*/

class ofuzFPDF extends HTML2FPDF
{

function PDF()
{
//! @return A class instance
//! @desc Constructor
//Call parent constructor
    $this->HTML2FPDF();
    //Disable some tags
//    $this->DisableTags("<big>,<small>");
  //Disable <title>/CSS/<pre> in order to increase script performance
  $this->usetitle=false;
  $this->usecss=true;
  $this->usepre=false;
}

//Common Logo for all HTML files (Montfort)
function InitLogo($src)
{
//! @desc Insert Image Logo on 1st page
//! @return void
  if ($src == '') return;
  
  $this->y = $this->tMargin - 30;
  $this->x = $this->lMargin;
  $halfwidth = $this->pgwidth/2;
//  $sizesarray = $this->Image($src, $this->GetX(), $this->GetY(), "800", "127",'','',false);
  //Alinhar imagem ao centro
  $this->x = ($halfwidth - ($sizesarray['WIDTH']/2));
//  $sizesarray = $this->Image($src, "5", "35", "204", "65",'','','http://www.aspireww.com/');
//  $sizesarray = $this->Image($src, "5", "35", "102", "32",'','','http://www.aspireww.com/');
  $sizesarray = $this->Image($src, "5", "15", "102", "16",'','','http://www.ofuz.com/');
//  $sizesarray = $this->Image($src, $this->GetX(), $this->GetY(), 0, 0,'','http://www.aspireww.com/');
  $this->Ln(1);
  //Contruir <HR> particular
	$this->SetLineWidth(0.3);
	$this->Line($this->x,$this->y,$this->x+$this->pgwidth,$this->y);
	$this->SetLineWidth(0.3);
	$this->Ln(2);
}

//Put title in page
function PutTitle($titulo)
{
//! @desc Insert Title on 1st page
//! @return void
  $this->SetTitle($titulo); 
  $this->Ln(4);
 	$this->SetFont('Arial','B',22);
	$this->divalign="C";
  $this->divwidth = $this->pgwidth;
  $this->divheight = 8.5;

  //Custom Word Wrap (para melhorar organiza�o das palvras no titulo)
  $maxwidth = $this->divwidth;
  $titulo = trim($titulo);
  $words = preg_split('/ +/', $titulo);
  $space = $this->GetStringWidth(' ');
  $titulo = '';
  $width = 0;
  $numwords = count($words);
  for($i = 0 ; $i < $numwords ; $i++)
  {
    $word = $words[$i];
    if ($i + 1 < $numwords) $nextword = $words[$i+1];
    else $nextword = '';
    $wordwidth = $this->GetStringWidth($word);
    $nextwordwidth = $this->GetStringWidth($nextword);
    if((strlen($word) <= 3) and ($nextword != '') and ($width + $wordwidth + $nextwordwidth > $maxwidth))
    {
       //Para n� ficar um artigo/preposi�o esquecido(a) no final de uma linha
       $width = $wordwidth + $space;
       $titulo = rtrim($titulo)."\n".$word.' ';
    }
    elseif ($width + $wordwidth <= $maxwidth) //Palavra cabe, inserir
    {
       $width += $wordwidth + $space;
       $titulo .= $word.' ';
    }
    else //Palavra n� cabe, pular linha e inserir na outra linha
    {
       $width = $wordwidth + $space;
       $titulo = rtrim($titulo)."\n".$word.' ';
    }
  }
  $titulo = rtrim($titulo);
  //End of Custom WordWrap
  $this->textbuffer[] = array($titulo,'','',array());

  //Print content
  $this->printbuffer($this->textbuffer);
  //Reset values
  $this->textbuffer=array();
  $this->divwidth=0;
	$this->divheight=0;
	$this->divalign="L";
 	$this->SetFont('Arial','',11);

  $this->Ln(4);
  //Contruir <HR> particular
	$this->SetLineWidth(0.3);
	$this->Line($this->x,$this->y,$this->x+$this->pgwidth,$this->y);
	$this->SetLineWidth(0.3);
	$this->Ln(2);
}

function rigthTitle($title) {

    $this->SetFont('Arial','B',16);

    $this->Cell(0,10,$title,0,1,'R');

}

//Page footer
function Footer()
{
//! @desc Insert footer on every page
//! @return void
    //Position at 1.0 cm from bottom
    $this->SetY(-10);
    //Copyright //especial para esta vers�
    $this->SetFont('Arial','B',9);
    $this->SetTextColor(0);
    $texto = "Invoice generated with Ofuz ";
    $this->Cell($this->GetStringWidth($texto),10,$texto,0,0,'L');
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->SetStyle('B',false);
    $this->Cell(0,10,"http://www.ofuz.com/",0,0,'L',0,"http://www.ofuz.com/");
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
    //Arial italic 9
    $this->SetFont('Arial','I',9);
    //Page number
    $this->Cell(0,10,'P. '.$this->PageNo().'/{nb}',0,0,'R');
    //Return Font to normal
    $this->SetFont('Arial','',11);
}

}//end of class
?>
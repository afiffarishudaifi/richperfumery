<?php
// require_once APPPATH.'/fpdf1813/fpdf.php';
require(public_path('fpdf1813/fpdf.php'));
// require(public_path(). "/fpdf1813/fpdf.php");
define('FPDF_FONTPATH',public_path('fpdf1813/font'));
// require(public_path('fpdf1813/draw.php'));


class PDF_MC_Table extends FPDF
{
var $widths;
var $aligns;

var $javascript;
var $n_js;

function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data, $hr=5)
{   
    $style = array('L' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'T' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'R' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'B' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)));
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h,'D', $style);
        //Print the text
        $this->MultiCell($w,$h/$nb,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function Row_draw($data, $hr=5)
{   
    $style = array('L' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'T' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'R' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'B' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)));
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border        
        $this->Rect($x,$y,$w,$h,'DF',$style);
        //Print the text
        $this->MultiCell($w,$h/$nb,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function Row_withAligment($data, $hr=5, $alignments=[])
{   
    $style = array('L' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'T' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'R' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'B' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)));
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h,'D', $style);
        //Print the text
        if(sizeOf($alignments) != 1){
            $this->MultiCell($w,$h/$nb,$data[$i],0,$a);
        }else{
            if(sizeOf($alignments) < 1){
                $alignments['valign'] = 'T';
            }
            $align=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

            $this->drawTextBox($w, $h, $data[$i], $align, $alignments['valign']);

        }
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function Row_hide($data, $hr=5)
{   
    $style = array('L' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'T' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'R' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'B' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)));

    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h,'D', $style);
        //Print the text
        $this->MultiCell($w,$h/$nb,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function Row_noborder($data, $hr=5)
{
       
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    
    $this->CheckPageBreak($h);
    
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
       
        $x=$this->GetX();
        $y=$this->GetY();
        
        $this->MultiCell($w,$h/$nb,$data[$i],0,$a);
        
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function Row_bawahputus($data, $hr=5, $alignments=[])
{

    $style = array('L' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'T' => array('width' => 0.25, 'cap' => 'square', 'join' => 'miter', 'dash' => '2, 2'),
                'R' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'B' => array('width' => 0.25, 'cap' => 'square', 'join' => 'miter', 'dash' => '2, 2'));
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h,'D', $style);
        //Print the text
        if(sizeOf($alignments) != 1){
            $this->MultiCell($w,$h/$nb,$data[$i],0,$a);
        }else{
            if(sizeOf($alignments) < 1){
                $alignments['valign'] = 'T';
            }
            $align=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

            $this->drawTextBox($w, $h, $data[$i], $align, $alignments['valign']);

        }
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function Row_atasputus($data, $hr=5, $alignments=[])
{

    $style = array('L' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'T' => array('width' => 0.25, 'cap' => 'square', 'join' => 'miter', 'dash' => '2, 2'),
                'R' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'B' => array('width' => 0.25, 'cap' => 'square', 'join' => 'miter', 'dash' => '0, 0'),'color' => array(0, 0, 0));
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h,'D', $style);
        //Print the text
        if(sizeOf($alignments) != 1){
            $this->MultiCell($w,$h/$nb,$data[$i],0,$a);
        }else{
            if(sizeOf($alignments) < 1){
                $alignments['valign'] = 'T';
            }
            $align=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

            $this->drawTextBox($w, $h, $data[$i], $align, $alignments['valign']);

        }
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function Row_bawahhide($data, $hr=5,$alignments=[])
{   
    $style = array('L' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'T' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'R' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)));

    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h,'D', $style);
        //Print the text
        if(sizeOf($alignments) != 1){
            $this->MultiCell($w,$h/$nb,$data[$i],0,$a);
        }else{
            if(sizeOf($alignments) < 1){
                $alignments['valign'] = 'T';
            }
            $align=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

            $this->drawTextBox($w, $h, $data[$i], $align, $alignments['valign']);

        }
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function Row_atashide($data, $hr=5, $alignments=[])
{   
    $style = array('T' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'R' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)),
                'B' => array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0,0', 'color' => array(0, 0, 0)));

    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h,'D', $style);
        //Print the text
        $this->MultiCell($w,$h/$nb,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function Row_atasbawah($data, $hr=5)
{
       
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$hr*$nb;
    
    $this->CheckPageBreak($h);
    
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
       
        $x=$this->GetX();
        $y=$this->GetY();
        
        $this->MultiCell($w,$h/$nb,$data[$i],"TB",$a);
        
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

/**
 * Draws text within a box defined by width = w, height = h, and aligns
 * the text vertically within the box ($valign = M/B/T for middle, bottom, or top)
 * Also, aligns the text horizontally ($align = L/C/R/J for left, centered, right or justified)
 * drawTextBox uses drawRows
 *
 * This function is provided by TUFaT.com
 */

/*function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)*/
function drawTextBox($w, $h, $strText, $align='L', $valign='T', $border=true)
{
    $xi=$this->GetX();
    $yi=$this->GetY();
    
    $thisFontSize = $this->FontSize+1;
    $hrow=$thisFontSize;
    $textrows=$this->drawRows($w,$hrow,$strText,0,$align,0,0,0);
    $maxrows=floor($h/$thisFontSize);
    $rows=min($textrows,$maxrows);

    $dy=0;
    if (strtoupper($valign)=='M')
        $dy=($h-$rows*$thisFontSize)/2;
    if (strtoupper($valign)=='B')
        $dy=$h-$rows*$thisFontSize;

    $this->SetY($yi+$dy);
    $this->SetX($xi);

    $this->drawRows($w,$hrow,$strText,0,$align,false,$rows,1);

    // if ($border)
    //     $this->Rect($xi,$yi,$w,$h);
}

function drawRows($w, $h, $txt, $border=0, $align='J', $fill=false, $maxline=0, $prn=0)
{
    // $h+=1;
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $b=0;
    if($border)
    {
        if($border==1)
        {
            $border='LTRB';
            $b='LRT';
            $b2='LR';
        }
        else
        {
            $b2='';
            if(is_int(strpos($border,'L')))
                $b2.='L';
            if(is_int(strpos($border,'R')))
                $b2.='R';
            $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
        }
    }
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $ns=0;
    $nl=1;
    while($i<$nb)
    {
        //Get next character
        $c=$s[$i];
        if($c=="\n")
        {
            //Explicit line break
            if($this->ws>0)
            {
                $this->ws=0;
                if ($prn==1) $this->_out('0 Tw');
            }
            if ($prn==1) {
                $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            }
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $ns=0;
            $nl++;
            if($border && $nl==2)
                $b=$b2;
            if ( $maxline && $nl > $maxline )
                return substr($s,$i);
            continue;
        }
        if($c==' ')
        {
            $sep=$i;
            $ls=$l;
            $ns++;
        }
        $l+=$cw[$c];
        if($l>$wmax)
        {
            //Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                if($this->ws>0)
                {
                    $this->ws=0;
                    if ($prn==1) $this->_out('0 Tw');
                }
                if ($prn==1) {
                    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                }
            }
            else
            {
                if($align=='J')
                {
                    $this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                    if ($prn==1) $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                }
                if ($prn==1){
                    $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                }
                $i=$sep+1;
            }
            $sep=-1;
            $j=$i;
            $l=0;
            $ns=0;
            $nl++;
            if($border && $nl==2)
                $b=$b2;
            if ( $maxline && $nl > $maxline )
                return substr($s,$i);
        }
        else
            $i++;
    }
    //Last chunk
    if($this->ws>0)
    {
        $this->ws=0;
        if ($prn==1) $this->_out('0 Tw');
    }
    if($border && is_int(strpos($border,'B')))
        $b.='B';
    if ($prn==1) {
        $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
    }
    $this->x=$this->lMargin;
    return $nl;
}

function drawTextBox2($strText, $w, $h, $align='L', $valign='T', $border=true)
{
    $xi=$this->GetX();
    $yi=$this->GetY();
    
    $hrow=$this->FontSize;
    $textrows=$this->drawRows2($w,$hrow,$strText,0,$align,0,0,0);
    $maxrows=floor($h/$this->FontSize);
    $rows=min($textrows,$maxrows);

    $dy=0;
    if (strtoupper($valign)=='M')
        $dy=($h-$rows*$this->FontSize)/2;
    if (strtoupper($valign)=='B')
        $dy=$h-$rows*$this->FontSize;

    $this->SetY($yi+$dy+2);
    $this->SetX($xi);

    $this->drawRows2($w,$hrow,$strText,0,$align,false,$rows,1);

    if ($border)
        $this->Rect($xi,$yi,$w,$h);
}

function drawRows2($w, $h, $txt, $border=0, $align='J', $fill=false, $maxline=0, $prn=0)
{
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $b=0;
    if($border)
    {
        if($border==1)
        {
            $border='LTRB';
            $b='LRT';
            $b2='LR';
        }
        else
        {
            $b2='';
            if(is_int(strpos($border,'L')))
                $b2.='L';
            if(is_int(strpos($border,'R')))
                $b2.='R';
            $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
        }
    }
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $ns=0;
    $nl=1;
    while($i<$nb)
    {
        //Get next character
        $c=$s[$i];
        if($c=="\n")
        {
            //Explicit line break
            if($this->ws>0)
            {
                $this->ws=0;
                if ($prn==1) $this->_out('0 Tw');
            }
            if ($prn==1) {
                $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            }
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $ns=0;
            $nl++;
            if($border && $nl==2)
                $b=$b2;
            if ( $maxline && $nl > $maxline )
                return substr($s,$i);
            continue;
        }
        if($c==' ')
        {
            $sep=$i;
            $ls=$l;
            $ns++;
        }
        $l+=$cw[$c];
        if($l>$wmax)
        {
            //Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                if($this->ws>0)
                {
                    $this->ws=0;
                    if ($prn==1) $this->_out('0 Tw');
                }
                if ($prn==1) {
                    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                }
            }
            else
            {
                if($align=='J')
                {
                    $this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                    if ($prn==1) $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                }
                if ($prn==1){
                    $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                }
                $i=$sep+1;
            }
            $sep=-1;
            $j=$i;
            $l=0;
            $ns=0;
            $nl++;
            if($border && $nl==2)
                $b=$b2;
            if ( $maxline && $nl > $maxline )
                return substr($s,$i);
        }
        else
            $i++;
    }
    //Last chunk
    if($this->ws>0)
    {
        $this->ws=0;
        if ($prn==1) $this->_out('0 Tw');
    }
    if($border && is_int(strpos($border,'B')))
        $b.='B';
    if ($prn==1) {
        $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
    }
    $this->x=$this->lMargin;
    return $nl;
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

function SetLineStyle($style) {
        extract($style);
        if (isset($width)) {
            $width_prev = $this->LineWidth;
            $this->SetLineWidth($width);
            $this->LineWidth = $width_prev;
        }
        if (isset($cap)) {
            $ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
            if (isset($ca[$cap]))
                $this->_out($ca[$cap] . ' J');
        }
        if (isset($join)) {
            $ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
            if (isset($ja[$join]))
                $this->_out($ja[$join] . ' j');
        }
        if (isset($dash)) {
            $dash_string = '';
            if ($dash) {
                $tab = explode(',', $dash);
                $dash_string = '';
                foreach ($tab as $i => $v) {
                    if ($i > 0)
                        $dash_string .= ' ';
                    $dash_string .= sprintf('%.2F', $v);
                }
            }
            if (!isset($phase) || !$dash)
                $phase = 0;
            $this->_out(sprintf('[%s] %.2F d', $dash_string, $phase));
        }
        if (isset($color)) {
            list($r, $g, $b) = $color;
            $this->SetDrawColor($r, $g, $b);
        }
    }

    // Draws a line
    // Parameters:
    // - x1, y1: Start point
    // - x2, y2: End point
    // - style: Line style. Array like for SetLineStyle
    function Line($x1, $y1, $x2, $y2, $style = null) {
        if ($style)
            $this->SetLineStyle($style);
        parent::Line($x1, $y1, $x2, $y2);
    }

    // Draws a rectangle
    // Parameters:
    // - x, y: Top left corner
    // - w, h: Width and height
    // - style: Style of rectangle (draw and/or fill: D, F, DF, FD)
    // - border_style: Border style of rectangle. Array with some of this index
    //   . all: Line style of all borders. Array like for SetLineStyle
    //   . L: Line style of left border. null (no border) or array like for SetLineStyle
    //   . T: Line style of top border. null (no border) or array like for SetLineStyle
    //   . R: Line style of right border. null (no border) or array like for SetLineStyle
    //   . B: Line style of bottom border. null (no border) or array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $border_style = null;
                parent::Rect($x, $y, $w, $h, $style);
                break;
            case 'DF': case 'FD':
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all'])) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                } else
                    $style = 'F';
                parent::Rect($x, $y, $w, $h, $style);
                break;
            default:
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all']) && $border_style['all']) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                    parent::Rect($x, $y, $w, $h, $style);
                }
                break;
        }
        if ($border_style) {
            if (isset($border_style['L']) && $border_style['L'])
                $this->Line($x, $y, $x, $y + $h, $border_style['L']);
            if (isset($border_style['T']) && $border_style['T'])
                $this->Line($x, $y, $x + $w, $y, $border_style['T']);
            if (isset($border_style['R']) && $border_style['R'])
                $this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
            if (isset($border_style['B']) && $border_style['B'])
                $this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
        }
    }

    function IncludeJS($script) {
        $this->javascript=$script;
    }
    function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_out('<<');
        $this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_out('>>');
        $this->_out('endobj');
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/S /JavaScript');
        $this->_out('/JS '.$this->_textstring($this->javascript));
        $this->_out('>>');
        $this->_out('endobj');
    }
    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }
    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }
    
    function AutoPrint($dialog=false)
    {
        //Open the print dialog or start printing immediately on the standard printer
        $param=($dialog ? 'true' : 'false');
        $script="print($param);";
        $this->IncludeJS($script);
    }
    function AutoPrintToPrinter($server, $printer, $dialog=false)
    {
        //Print on a shared printer (requires at least Acrobat 6)
        $script = "var pp = getPrintParams();";
        if($dialog)
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
        else
            $script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
        $script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
        $script .= "print(pp);";
        $this->IncludeJS($script);
    }
    function DirectPrint()
    {
        $script = "var WebBrowser = 
    '<OBJECT ID='WebBrowser1' WIDTH=0 HEIGHT=0
    CLASSID='CLSID:8856F961-340A-11D0-A96B-00C04FD705A2'>
    </OBJECT>';
    document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
    WebBrowser1.ExecWB(6, -1);
    WebBrowser1.outerHTML = ;";
    }
}
?>
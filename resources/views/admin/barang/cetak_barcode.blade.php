 <?php

class Pdf extends PDF_MC_Table{
	//Page header
	//public $kas_masuk,$tgl1,$tgl2;
	public $def_width = 210, $def_height = 310;
	function __construct() {
		//set Page 
		parent::__construct('P','mm',array($this->def_width , $this->def_height));
        
    }
	function Header(){
        
		if($this->PageNo() != 0){
			$spacing = 5;
            $kolom_1 = 20;
            $kolom_2 = 5;
            $kolom_3 = 30;
            $kolom_4 = 10;
            $ln = 3;
            $indent_1 = $kolom_1+$kolom_2+$kolom_3+20;

            $x = $this->GetX();
            $y = $this->GetY();

            $this->setFont('Arial','B',14);
            $this->setFillColor(255,255,255);
            $this->setTextColor(0,0,0);
            $this->cell(0, 5, "DAFTAR BARANG DAN BARCODE", 0, 1, "C");
            $this->Ln();

            /*$this->setFont('arial','B',8);
			$this->SetWidths(array(10, 30, 70, 30, 60));
	        $this->SetAligns(array('C', 'C', 'C', 'C', 'C'));
	        $this->Row(array("NO.", "KODE BARANG", "NAMA BARANG", "SATUAN", "BARCODE"));

	        $this->setFont('arial','',8);
			$this->SetWidths(array(10, 30, 70, 30, 60));
	        $this->SetAligns(array('C', 'C', 'L', 'L', 'C'));

            $this->setFont('arial','B',8);
            $this->SetWidths(array(10, 30, 100, 30, 30));
            $this->SetAligns(array('C', 'C', 'C', 'C', 'C'));
            $this->Row(array("NO.", "KODE BARANG", "NAMA PARFUM", "FISIK", "STOK"));

            $this->setFont('arial','',8);
            $this->SetWidths(array(10, 30, 100, 30, 30));
            $this->SetAligns(array('C', 'C', 'L', 'R', 'R'));*/
        }
				
	}
	
	function Content(){

        $this->setFont('arial','',8);
		/*$this->SetWidths(array(10, 30, 70, 30, 60));
        $this->SetAligns(array('C', 'C', 'L', 'L', 'C'));

        $this->SetWidths(array(10, 30, 100, 30, 30));
        $this->SetAligns(array('C', 'C', 'L', 'R', 'R'));*/

        /*$no=1;
        if(count($this->data) > 0){
        	foreach($this->data as $k){
        		$this->Row(array($no, $k['kode_barang'], $k['nama_barang'], $k['nama_satuan'], $this->Image($k['barcode'], $this->GetX()+150, $this->GetY(), 33.78) ),33.78);
        		$no++;
        	}
        }else{
        	$this->Row(array("", "", "", "", "", ""));
        }*/

        //$no=1;
        if(count($this->data) > 0){
                $setX = 0;
                $setY = 0;
                /*$kiri =$this->GetX();
                $atas =$this->GetY();                
                foreach ($this->data as $key => $value) {
                    

                    foreach ($value as $k => $e) {
                        $l_X = $this->GetX()+$setX*33;
                        $l_Y = $this->GetY()+$setY*43;
                        //$h = 0;
                        $x = $this->GetX();
                        $y = $this->GetY();
                        $this->Image($e['barcode'], $l_X, $l_Y, 33);                        
                        //$this->setXY($this->GetX(),$this->GetY());
                        //$this->Cell(3, 5, "", 1, 0, 'C', FALSE, '', 0, FALSE, 'C', 'B');
                        //$this->namalabel($e['kode_barang']." - $l_X - $k",$l_X, $l_Y,$key,$k,$r_X,$r_Y);
                        //$this->Image($e['barcode'], $l_X, $l_Y, 33);
                        //$this->ln(33);
                        //$this->Row(array($this->Image($e['barcode'], $this->GetX(), $this->GetY(), 33), "-", "-", "-", "-", "-"),33);
                        //$this->SetAutoPageBreak(true);
                        $setX++;
                     }
                    //$this->ln(2);
                    $setX=0;
                    //$setY=0;
                    $this->setX = $kiri;
                    //$this->setY = $atas;
                    $setY++;
                    if($key %7==0 && $key !=0){
                        $setY= 0;   
                        $this->AddPage();
                    }
                }*/

                foreach ($this->data as $lk => $l) {
                    foreach ($l as $ck => $c) {
                        //Get current write position.
                        $x = $this->GetX();
                        $y = $this->GetY();
                        $l_X = $this->GetX()+$setX*33;
                        $l_Y = $this->GetY()+$setY*33;
                        // The width is set to the the same as the cell containing the name.  
                        // The Y position is also adjusted slightly.
                        $this->Image($c['barcode'], $l_X, $l_Y, 33);
                        //$this->Image($c['barcode'], $l_X, $l_Y+23, 33);
                        //Reset X,Y so wrapping cell wraps around the barcode's cell.
                        $this->SetXY($x+2,$y+33);
                        $this->SetWidths(array(15, 3, 15));
                        $this->SetAligns(array('L', 'C', 'L'));
                        $this->Row_noborder(array('Kode',':',$c['kode_barang']));
                        /*$this->SetXY($x+2,$y+38);
                        $this->Row_noborder(array('Satuan',':',$c['alias_satuan']));*/
                        $this->SetXY($x,$y);  
                        $this->cell(33, 40, '', 0, 0, 'L');
                    }
                    $this->Ln();
                    if($lk %6==5 && $lk !=0){                         
                        $this->AddPage();
                    }
                    
                }
        }
                

	}
	
	function Footer()
	{
		$this->Ln();
		
		$this->SetY(-15);
		//buat garis horizontal
		
		//Arial italic 9
		$this->SetFont('Arial','B',9);
        $this->Cell(0,10,'',0,0,'L');
		//nomor halaman
	}
    function namalabel($nama,$x,$y,$key,$k,$xx,$yy){
        $this->setXY($x,$y+32);
        $this->cell(33,'5',$nama."/".$y."/key=".$key,1,0,'C');       
        $r_X = $this->GetX()-$k*33;
        $r_Y = $yy;
        if($k%6==0){
            $r_X = $this->GetX()-198;
        }
         
        $this->setXY($this->GetX(),$this->GetY());
        //$this->setXY($r_X,$y);
        // $this->setXY($x,$y);
        //$this->setX($this->GetX()-198);
    }
}

$Pdf = new Pdf();

$Pdf->data          = $data['data'];

$Pdf->SetAutoPageBreak(true ,15);
$Pdf->SetMargins(5,5,5);
$Pdf->AliasNbPages();
$Pdf->AddPage();
$Pdf->SetFont('Arial','',11);
$Pdf->Content();
$Pdf->SetTitle("Cetak Daftar Barang");
$Pdf->Output("Cetak Daftar Barang.pdf", "I");

?>
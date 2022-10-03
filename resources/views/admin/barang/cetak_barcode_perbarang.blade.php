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
            $this->Ln(5);

            $this->ln();
            $this->setFont('Arial','B',12);
            $this->SetWidths(array(3, 20, 3, 110, 20, 3, 23));
            $this->SetAligns(array("L", "L", "L", "L", "L"));
            $this->Row_noborder(array("","Barang", ": ", strtoupper($this->barang->barang_nama), "Satuan", ":", strtoupper($this->satuan->satuan_nama)));
        }
				
	}
	
	function Content(){

        $this->setFont('arial','',8);
		// $this->SetWidths(array(10, 30, 70, 30, 60));
  //       $this->SetAligns(array('C', 'C', 'L', 'L', 'C'));

        /*$this->SetWidths(array(10, 30, 100, 30, 30));
        $this->SetAligns(array('C', 'C', 'L', 'R', 'R'));*/
        for ($i=0; $i < 6; $i++) { 
            for ($x=0; $x < 7; $x++) {
                $this->Image($this->barcode, $this->GetX()+$i*33, $this->GetY()+33*$x, 33.78);
            }
            $this->Image($this->barcode, $this->GetX()+$i*33, $this->GetY(), 33.78);
        
        }
        // $no=1;
        // if(count($this->data) > 0){
        // 	foreach($this->data as $k){
        		// $this->Row(array($this->Image($k['barcode'], $this->GetX()+150, $this->GetY(), 33.78) ),33.78);
                /*$this->Row(array($no, $k->kode_barang, $k->nama_barang, format_angka($k->fisik), format_angka($k->stok)));*/
        // 		$no++;
        // 	}
        // }else{
        // 	$this->Row(array("", "", "", "", "", ""));
        // }
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
}

$Pdf = new Pdf();

$Pdf->barcode          = $data['barcode'];
$Pdf->barang        = $data['barang'];
$Pdf->satuan        = $data['satuan'];

$Pdf->SetAutoPageBreak(true ,15);
$Pdf->SetMargins(5,5,5);
$Pdf->AliasNbPages();
$Pdf->AddPage();
$Pdf->SetFont('Arial','',11);
$Pdf->Content();
$Pdf->SetTitle("Cetak Daftar Barang");
$Pdf->Output("Cetak Daftar Barang.pdf", "I");

?>
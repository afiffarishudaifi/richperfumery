 <?php

class Pdf extends PDF_MC_Table{
	//Page header
	//public $kas_masuk,$tgl1,$tgl2;
	public $def_width = 210, $def_height = 310;
	function __construct() {
		//set Page 
		parent::__construct('L','mm',array($this->def_width , $this->def_height));
        
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
            $this->cell(0, 5, ucwords($this->gudang), 0, 1, "C");
            $this->Ln(5);

            //$this->setX($indent_1);
            $this->ln();
            $this->setFont('arial','B',8);
			$this->SetWidths(array(10, 25, 25, 75, 45, 25, 30, 30, 30));
	        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
	        $this->Row(array("NO.", "TANGGAL", "KODE BARANG", "NAMA PARFUM", "NAMA GUDANG", "STOK AWAL", "STOK MASUK", "STOK KELUAR", "STOK AKHIR"));

	        $this->setFont('arial','',8);
			$this->SetWidths(array(10, 25, 25, 75, 45, 25, 30, 30, 30));
	        $this->SetAligns(array('C', 'C', 'C', 'L', 'L', 'R', 'R', 'R', 'R'));

            /*$this->setFont('arial','B',8);
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
		$this->SetWidths(array(10, 25, 25, 75, 45, 25, 30, 30, 30));
        $this->SetAligns(array('C', 'C', 'C', 'L', 'L', 'R', 'R', 'R', 'R'));

        /*$this->SetWidths(array(10, 30, 100, 30, 30));
        $this->SetAligns(array('C', 'C', 'L', 'R', 'R'));*/

        $no=1;
        if(count($this->data) > 0){
        	foreach($this->data as $k){
        		$this->Row(array($no, $k->tanggal, $k->barang_kode, $k->barang_nama, $k->gudang_nama, format_angka($k->jumlah_stokawal)." ".$k->satuan_satuan, format_angka($k->jumlah_masuk)." ".$k->satuan_satuan, format_angka($k->jumlah_keluar)." ".$k->satuan_satuan, format_angka($k->jumlah_terakhir)." ".$k->satuan_satuan ));
                /*$this->Row(array($no, $k->kode_barang, $k->nama_barang, format_angka($k->fisik), format_angka($k->stok)));*/
        		$no++;
        	}
        }else{
        	$this->Row(array("", "", "", "", "", "", "", "", ""));
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
}

$Pdf = new Pdf();

$Pdf->data          = $data['data'];
$Pdf->kasir 		= $data['kasir'];
$Pdf->gudang		= $data['gudang'];

$Pdf->SetAutoPageBreak(true ,15);
$Pdf->SetMargins(5,5,5);
$Pdf->AliasNbPages();
$Pdf->AddPage();
$Pdf->SetFont('Arial','',11);
$Pdf->Content();
$Pdf->SetTitle("Cetak Laporan Persediaan (DETAIL)");
$Pdf->Output("Cetak Laporan Persediaan (DETAIL).pdf", "I");

?>
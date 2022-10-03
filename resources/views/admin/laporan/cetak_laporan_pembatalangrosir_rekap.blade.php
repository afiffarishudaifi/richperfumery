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
            $this->setFont('Arial','B',10);
            
            if(strtoupper($this->tanggal_awal) == strtoupper($this->tanggal_akhir)){
            $this->SetWidths(array(10,15, 3, 182, 20, 3, 23));
            $this->SetAligns(array("C", "C", "C", "C", "C"));
            $this->Row_noborder(array("","KASIR", ": ", strtoupper($this->kasir), "TANGGAL", ":", strtoupper($this->tanggal_awal)));
            }else{
            $this->SetWidths(array(10,15, 3, 182, 20, 3, 23, 17, 23));
            $this->SetAligns(array("C", "C", "C", "C", "C"));
            $this->Row_noborder(array("","KASIR", ": ", strtoupper($this->kasir), "TANGGAL", ":", strtoupper($this->tanggal_awal), "Sampai", strtoupper($this->tanggal_akhir)));
            }

            $this->setFont('arial','B',8);
			$this->SetWidths(array(10, 25, 40, 40, 40, 40, 40, 60));
	        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
	        $this->Row_bawahhide(array("NO.", "TANGGAL", "NO", "NAMA", "NAMA", "TOTAL", "DIBATALKAN", "CATATAN"));
            $this->Row_atasputus(array("", "", "FAKTUR", "PELANGGAN", "GUDANG", "PENJUALAN", "OLEH", ""));

	        $this->setFont('arial','',8);
			$this->SetWidths(array(10, 25, 40, 40, 40, 40, 40, 60));
            $this->SetAligns(array(1, 2, 'R', 'R', 'R', 'R', 'R', 'R'));
            
        }
				
	}
	
	function Content(){

        $this->setFont('arial','',8);
		$this->SetWidths(array(10, 25, 40, 40, 40, 40, 40, 60));
        $this->SetAligns(array('C', 'C', 'L', 'L', 'L', 'R', 'L','L'));

        $no=1;
        if(count($this->data) > 0 && $this->data != null){
        	foreach($this->data as $k){
                $this->Row(array($no, 
                    tgl_full($k->tanggal,'0'), 
                    $k->no_faktur, 
                    $k->nama_pelanggan, 
                    $k->nama_gudang, 
                    'Rp '.format_angka($k->total_tagihan-($k->total_potongan-$k->ongkos_kirim)), 
                    $k->nama_hapus,
                    $k->catatan));
        		$no++;
        	}
        }else{
        	$this->Row(array("", "", "", "", "", "", ""));
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
$Pdf->tanggal_awal  = $data['tanggal_awal'];
$Pdf->tanggal_akhir = $data['tanggal_akhir'];
$Pdf->kasir 		= $data['kasir'];
$Pdf->gudang		= $data['gudang'];

$Pdf->SetAutoPageBreak(true ,15);
$Pdf->SetMargins(5,5,5);
$Pdf->AliasNbPages();
$Pdf->AddPage();
$Pdf->SetFont('Arial','',11);
$Pdf->Content();
$Pdf->SetTitle("Cetak Laporan Pembatalan Grosir - (".$data['tanggal_awal']."-".$data['tanggal_akhir'].")");
$Pdf->Output("Cetak Laporan Pembatalan Grosir - (".$data['tanggal_awal']."-".$data['tanggal_akhir'].").pdf", "I");

?>
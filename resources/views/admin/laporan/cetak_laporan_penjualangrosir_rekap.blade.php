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
            /*$this->SetWidths(array(10,15, 3, 80, 20, 3, 23));
            $this->SetAligns(array("L", "L", "L", "L", "L"));
            $this->Row_noborder(array("","KASIR", ": ", strtoupper($this->kasir), "TANGGAL", ":", strtoupper($this->tanggal)));*/
            if(strtoupper($this->tanggal_awal) == strtoupper($this->tanggal_akhir)){
            $this->SetWidths(array(10,15, 3, 182, 20, 3, 23));
            $this->SetAligns(array("L", "L", "L", "L", "L"));
            $this->Row_noborder(array("","KASIR", ": ", strtoupper($this->kasir), "TANGGAL", ":", strtoupper($this->tanggal_awal)));
            }else{
            $this->SetWidths(array(10,15, 3, 182, 20, 3, 23, 17, 23));
            $this->SetAligns(array("L", "L", "L", "L", "L"));
            $this->Row_noborder(array("","KASIR", ": ", strtoupper($this->kasir), "TANGGAL", ":", strtoupper($this->tanggal_awal), "Sampai", strtoupper($this->tanggal_akhir)));
            }

            $this->setFont('arial','B',8);
			$this->SetWidths(array(10, 25, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22));
	        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
	        $this->Row_bawahhide(array("NO.", "TANGGAL", "TUNAI", "DEBIT", "DEBIT", "KREDIT", "KREDIT", "TRANSFER", "TRANSFER", "OVO", "HUTANG", "ONGKOS", "TOTAL +", "TOTAL"));
            $this->Row_atasputus(array("", "", "", "BCA", "MANDIRI", "BCA", "MANDIRI", "BCA", "MANDIRI", "", "", "KIRIM", "ONGKIR", ""));

	        $this->setFont('arial','',8);
			$this->SetWidths(array(10, 25, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22));
	        $this->SetAligns(array('C', 'C', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));

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
		$this->SetWidths(array(10, 25, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22));
        $this->SetAligns(array('C', 'C', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));

        /*$this->SetWidths(array(10, 30, 100, 30, 30));
        $this->SetAligns(array('C', 'C', 'L', 'R', 'R'));*/

        $no=1;
        if(count($this->data) > 0){
        	foreach($this->data as $k){
        		$this->Row(array($no, tgl_full($k->tanggal,'0'), 'Rp. '.format_angka($k->tunai), 'Rp. '.format_angka($k->debit_bca), 'Rp. '.format_angka($k->debit_mandiri), 'Rp. '.format_angka($k->kredit_bca), 'Rp. '.format_angka($k->kredit_mandiri), 'Rp. '.format_angka($k->transfer_bca), 'Rp. '.format_angka($k->transfer_mandiri), 'Rp. '.format_angka($k->ovo), 'Rp. '.format_angka($k->hutang), 'Rp. '.format_angka($k->ongkos_kirim), 'Rp. '.format_angka($k->total_ongkir), 'Rp. '.format_angka($k->total)));
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
$Pdf->SetTitle("Cetak Laporan Penjualan Grosir - (".$data['tanggal_awal']."-".$data['tanggal_akhir'].")");
$Pdf->Output("Cetak Laporan Penjualan Grosir - (".$data['tanggal_awal']."-".$data['tanggal_akhir'].").pdf", "I");

?>
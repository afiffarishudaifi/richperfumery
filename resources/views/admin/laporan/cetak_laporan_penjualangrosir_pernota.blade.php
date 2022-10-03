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
			$this->SetWidths(array(10, 25, 30, 55, 45, 30, 15, 25, 25, 25, 15));
	        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
	        $this->Row_bawahhide(array("NO.", "TANGGAL", "NOMER", "NAMA", "NAMA", "CARA", "JUMLAH", "HARGA", "TOTAL", "KET.", "CETAK"));
            $this->Row_atasputus(array("", "JUAL", "NOTA", "PARFUM", "CUSTOMER", "BAYAR", "", "", "","", ""));

	        $this->setFont('arial','',8);
			$this->SetWidths(array(10, 25, 30, 55, 45, 30, 15, 25, 25, 25, 15));
	        $this->SetAligns(array('C', 'C', 'L', 'L', 'L', 'L', 'R', 'R', 'R', "L", "C"));

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
		$this->SetWidths(array(10, 25, 30, 55, 45, 30, 15, 25, 25, 25, 15));
        $this->SetAligns(array('C', 'C', 'L', 'L', 'L', 'L', 'R', 'R', 'R', 'L', 'C'));

        /*$this->SetWidths(array(10, 30, 100, 30, 30));
        $this->SetAligns(array('C', 'C', 'L', 'R', 'R'));*/

        $no=1;
        if(count($this->data) > 0){
        	foreach($this->data as $k){
                if($k->telp_pelanggan != '' || $k->telp_pelanggan != null){
                    $pelanggan = $k->nama_pelanggan.'/'.$k->telp_pelanggan;
                }else{
                    $pelanggan = $k->nama_pelanggan;
                }
        		$this->Row(array($no, tgl_full($k->tanggal,'0'), strtoupper($k->no_faktur), ucwords($k->nama_produk), ucwords($pelanggan), strtoupper($k->metodebayar), format_angka($k->jumlah), 'Rp. '.format_angka($k->harga), 'Rp. '.format_angka($k->total), $k->keterangan, $k->jumlah_cetak ?? 0));
                /*$this->Row(array($no, $k->kode_barang, $k->nama_barang, format_angka($k->fisik), format_angka($k->stok)));*/
        		$no++;
        	}
        }else{
        	$this->Row(array("", "", "", "", "", "", "", "", "", "", ""));
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
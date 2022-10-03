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
            $this->setFont('Arial','B',8);
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

            $this->setFont('arial','B',7);
			$this->SetWidths(array(10, 20, 30, 60, 55, 10, 80, 20, 20, 20, 23, 35));
	        $this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
	        $this->Row_bawahhide(array("NO.", "TANGGAL", "NOMER", "NAMA", "NAMA", "JUMLAH","BARANG","VOLUME BARANG"));
            $this->Row_atasputus(array("", "JUAL", "NOTA", "CUSTOMER", "PARFUM", "", "", ""));

	        $this->setFont('arial','',7);
			$this->SetWidths(array(10, 20, 30, 55, 40, 20, 15, 20, 20, 20, 23, 35));
	        $this->SetAligns(array('C', 'C', 'L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', "L"));

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

        $this->setFont('arial','',7);
		$this->SetWidths(array(10, 20, 30, 60, 55, 10, 80, 20,));
        $this->SetAligns(array('C', 'C', 'L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'L'));

        /*$this->SetWidths(array(10, 30, 100, 30, 30));
        $this->SetAligns(array('C', 'C', 'L', 'R', 'R'));*/

        $no=1;
        if($this->data == '1'){
            $this->Row(array("", "", "", "", "", "", "", "",));
        }
        else{
           
        	foreach($this->data as $key => $k){
               
                // $this->Row(array($no, tgl_full($k->tanggal,''), strtoupper($k->no_faktur), ucwords($k->nama_produk), ucwords($pelanggan), format_angka($k->jumlah)));
                 $this->Cell(10,10,$no,1,0,'C');
                 $this->Cell(20,10,tgl_full($k['tgl_jual'],''),1,0,'C');

                $this->Cell(30,10,strtoupper($k['nomer_nota']),1,0,'C');
                $this->Cell(60,10,ucwords($k['nama_customer']),1,0,'L');
                  $this->Cell(55,10,ucwords($k['nama_parfum']),1,0,'C');
               
                 $this->Cell(10,10,format_angka($k['jumlah']),1,0,'C');
                

                 if(isset($k[0][0])){
                    $this->Cell(80,10,$k[0][0]['nama_barang'],1,0,'L');
                    $this->Cell(20,10,$k[0][0]['volume_barang'],1,1,'C');
                   

                    for ($i=1; $i <= count($k[0])-1 ; $i++) {                 
                           $this->Cell(10,10,"",1,0,'C');
                           $this->Cell(20,10,"",1,0,'C');
                           $this->Cell(30,10,"",1,0,'C');
                           $this->Cell(60,10,"",1,0,'C');
                           $this->Cell(55,10,"",1,0,'C');
                           $this->Cell(10,10,"",1,0,'C');
                           $this->Cell(80,10,$k[0][$i]['nama_barang'],1,0,'L');
                           $this->Cell(20,10,$k[0][$i]['volume_barang'],1,1,'C');
                     } 
                 } else{
                    $this->Cell(80,10,'-',1,0,'L');
                    $this->Cell(20,10,'-',1,1,'C');
                 }
                /*$this->Row(array($no, $k->kode_barang, $k->nama_barang, format_angka($k->fisik), format_angka($k->stok)));*/
               
                $no++;
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

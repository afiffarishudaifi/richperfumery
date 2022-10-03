 <?php

class Pdf extends PDF_MC_Table{
	//Page header
	//public $kas_masuk,$tgl1,$tgl2;
	public $width_p = 140, $height_p = 310, $head = 0;
	function __construct() {
		//set Page 
		parent::__construct('P','mm',array($this->width_p , $this->height_p));
        
    }
	function Header(){
	    if($this->PageNo() != 0){		
                $this->setFont('Arial','',12);
                $this->setFillColor(255,255,255);
                $this->setTextColor(0,0,0);          

                $this->setXY($this->GetX(),$this->GetY()+10);
                $this->cell(20,7,'NOTA',0,0,'L',1);
                $this->setFont('Arial','',9);
                $this->setFillColor(0,0,0);
                $this->setTextColor(255,255,255); 
                $this->setXY($this->GetX(),$this->GetY()+1);
                $this->cell(7,4,'No.',1,0,'L',1);
                $this->setFillColor(255,255,255);
                $this->setTextColor(0,0,0); 
                $this->cell(20,4,$this->data->no_faktur,1,1,'L',1); 
                $this->setFillColor(0,0,0);
                $this->setTextColor(255,255,255); 
                $this->cell(7,6,'Tgl.',1,0,'L',1); 
                $this->setFillColor(255,255,255);
                $this->setTextColor(0,0,0);
                $this->cell(40,6,tgl_full($this->data->tanggal,''),1,1,'L',1);
                $this->setFillColor(0,0,0);
                $this->setTextColor(255,255,255); 
                $this->setXY($this->GetX()+80,$this->GetY()-20);
                $this->SetWidths(array($this->width_p*30/100));
                $this->SetAligns(array('C'));
                $this->row_draw(array('KEPADA YTH.'));
                $this->SetAligns(array('L'));
                $this->setX($this->GetX()+80);
                $this->setFillColor(255,255,255);
                $this->setTextColor(0,0,0);
                $this->Row_bawahputus(array($this->data->nama_pelanggan));
                $this->setX($this->GetX()+80);
                $this->Row_bawahputus(array($this->data->alamat_pelanggan));
                $this->setX($this->GetX()+80);
                $this->Row_atasputus(array($this->data->telp_pelanggan));
                
                $this->setXY($this->GetX(),$this->GetY()+$this->height_p*2/100);
                $this->setFont('arial','B', 10);
                $this->setFillColor(0,0,0);
                $this->setTextColor(255,255,255); 
                $this->SetWidths(array($this->width_p*12/100, $this->width_p*48/100, $this->width_p*17/100, $this->width_p*17/100));
                $this->SetAligns(array('C', 'L', 'L', 'L'));
                $this->Row_draw(array('Jml','Nama Barang','Harga @ Rp.','Total @ Rp.'));
        }
				
	}
	
	function Content()
	{
		$this->setFont('Arial','',9);
        $this->SetWidths(array($this->width_p*12/100, $this->width_p*48/100, $this->width_p*17/100, $this->width_p*17/100));
        $this->SetAligns(array('C', 'L', 'R', 'R'));
        if(count($this->detail) > 0){
		    foreach($this->detail as $d){
		        $this->row(array($d->jumlah." ".$d->inisial_satuan, $d->nama_produk, format_angka($d->harga), format_angka($d->total)));
		    }
		}/*else{			
		    $this->row(array('','','',''));
	    }*/
        if(count($this->barang) > 0){
            foreach($this->barang as $d){
                $this->row(array($d->jumlah." ".$d->inisial_satuan, $d->nama_barang, format_angka($d->harga), format_angka($d->total)));
            }
        }

	    $count = 17-(count($this->detail)+count($this->barang));
	    for($no=0;$no<$count;$no++){
	    	$this->row(array('','','',''));
	    }
        

	}
	
	function Footer()
	{
		//Arial italic 9
		$this->SetFont('Arial','B',9);

		$this->SetWidths(array($this->width_p*60/100, $this->width_p*17/100));
        $this->SetAligns(array('L', 'R'));
        $this->Row_noborder(array('','Potongan Rp.'));
        $this->setXY($this->GetX()+$this->width_p*77/100,$this->GetY()-5);
        $this->SetWidths(array($this->width_p*17/100));
        $this->SetAligns(array('R'));
        $this->Row(array(format_angka($this->data->total_potongan)));

		$this->SetWidths(array($this->width_p*60/100, $this->width_p*17/100));
        $this->SetAligns(array('L', 'R'));
        $this->Row_noborder(array('','Ongkir Rp.'));
        $this->setXY($this->GetX()+$this->width_p*77/100,$this->GetY()-5);
        $this->SetWidths(array($this->width_p*17/100));
        $this->SetAligns(array('R'));
        $this->Row(array(format_angka($this->data->ongkos_kirim)));

        $this->SetWidths(array($this->width_p*60/100, $this->width_p*17/100));
        $this->SetAligns(array('L', 'R'));
        $this->Row_noborder(array('','DP Rp.'));
        $this->setXY($this->GetX()+$this->width_p*77/100,$this->GetY()-5);
        $this->SetWidths(array($this->width_p*17/100));
        $this->SetAligns(array('R'));
        $this->Row(array(format_angka($this->data->uang_muka)));

        $this->SetWidths(array($this->width_p*60/100, $this->width_p*17/100));
        $this->SetAligns(array('L', 'R'));
        $this->Row_noborder(array('','Total Rp.'));
        $this->setXY($this->GetX()+$this->width_p*77/100,$this->GetY()-5);
        $this->SetWidths(array($this->width_p*17/100));
        $this->SetAligns(array('R'));
        $this->Row(array(format_angka($this->data->total_tagihan)));
		//$this->Ln();
		//$this->SetY(-15);
	}
}

$Pdf = new Pdf();

$Pdf->data          = $data['data'];
$Pdf->detail        = $data['detail'];
$Pdf->barang        = $data['barang'];

$Pdf->SetAutoPageBreak(true ,15);
$Pdf->SetMargins(5,5,5);
$Pdf->AliasNbPages();
$Pdf->AddPage();
$Pdf->SetFont('Arial','',11);
$Pdf->Content();
$Pdf->SetTitle("Cetak Nota - ".$data['data']->no_faktur);
$Pdf->Output("Cetak Nota - ".$data['data']->no_faktur.".pdf", "I");

?>
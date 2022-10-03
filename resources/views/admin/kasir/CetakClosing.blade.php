 <?php

class Pdf extends PDF_MC_Table{
	//Page header
	//public $kas_masuk,$tgl1,$tgl2;
	public $width_p = 148, $height_p = 210, $head = 0;
	function __construct() {
		//set Page 
		parent::__construct('P','mm',array($this->width_p , $this->height_p));
        
    }
	function Header(){
	    if($this->PageNo() == 1){		
                
                $this->image(public_path('richperfumery.png'),5,5,20,10,'PNG');
                $this->setFont('Arial','',8);
                $this->setFillColor(255,255,255);
                $this->setTextColor(0,0,0);
                $this->setXY($this->GetX()+20,$this->GetY()-1);
                $this->SetWidths(array($this->width_p*30/100));
                $this->SetAligns(array('C')); 
                $this->Row_noborder(array($this->rich['nama']));               
                //$this->cell(20,5,$this->rich->nama,0,1,'C',1);
                $this->setXY($this->GetX()+20,$this->GetY()-1);
                $this->Row_noborder(array($this->rich['alamat'].', '.$this->rich['telp']),4);
                //$this->cell(20,5,$this->rich->alamat,0,1,'C',1);

                $this->setFont('Arial','',12);
                $this->setFillColor(255,255,255);
                $this->setTextColor(0,0,0);
                $this->setXY($this->GetX()+92,$this->GetY()-12);
                $this->cell(18,6.5,'NOTA',0,0,'L',1);
                $this->setFont('Arial','',9);
                $this->setFillColor(0,0,0);
                $this->setTextColor(255,255,255); 
                $this->setXY($this->GetX(),$this->GetY()+1);
                $this->cell(9,4,'Nama Kasir.',1,0,'L',1);
                $this->setFillColor(255,255,255);
                $this->setTextColor(0,0,0); 
                $this->cell(20,4,$this->data['nama'],1,1,'L',1); 
                $this->setXY($this->GetX()+92,$this->GetY());
                $this->setFillColor(0,0,0);
                $this->setTextColor(255,255,255); 
                $this->cell(7,6,'Tgl.',1,0,'L',1); 
                $this->setFillColor(255,255,255);
                $this->setTextColor(0,0,0);
                $this->cell(40,6,tgl_full($this->data['tanggal'],''),1,1,'L',1);
                
                
            }
            if($this->PageNo() != 0){   
                $this->setXY($this->GetX(),$this->GetY()+$this->height_p*2/100);
                $this->setFont('arial','B', 10);
                $this->setFillColor(0,0,0);
                $this->setTextColor(255,255,255); 
                $this->SetWidths(array($this->width_p*60/100, $this->width_p*34/100));
                $this->SetAligns(array('C', 'C'));
                $this->Row_draw(array('Metode Pembayaran','Total @ Rp.'));

                $this->SetAligns(array('C', 'L', 'R', 'R'));
        }
				
	}
	
	function Content()
	{
		$this->setFont('Arial','',9);
        $this->SetWidths(array($this->width_p*60/100, $this->width_p*34/100));
        $this->SetAligns(array('L', 'R'));
        if(count($this->detail) > 0){
		    foreach($this->detail as $d){
		        $this->row(array($d->name, format_angka($d->data)));
		    }
		}
        $this->setFont('Arial','B',9);
        $this->row(array('Total @ Rp.', format_angka($this->total[0]->data)));
        $this->setFont('Arial','B',9);
        $this->row(array('Total + Ongkir @ Rp.', format_angka($this->total_ongkir[0]->data)));
	    
        

	}
	
	function Footer()
	{
		//Arial italic 9
        $this->setXY(-74,-10);
		$this->SetFont('Arial','B',9);
        $this->setFillColor(255,255,255);
        $this->setTextColor(0,0,0); 
        $this->cell(5,5,$this->PageNo(),0,1,'C',true);
		/*$this->SetWidths(array($this->width_p*60/100, $this->width_p*17/100));
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
        $this->Row(array(format_angka($this->data->total_tagihan)));*/
		//$this->Ln();
		//$this->SetY(-15);
	}
}

$Pdf = new Pdf();

$Pdf->data          = $data['data'];
$Pdf->detail        = $data['detail'];
$Pdf->total         = $data['total'];
$Pdf->total_ongkir         = $data['total_ongkir'];
$Pdf->rich          = $data['rich'];

$Pdf->SetAutoPageBreak(true ,15);
$Pdf->SetMargins(5,5,5);
$Pdf->AliasNbPages();
$Pdf->AddPage();
$Pdf->SetFont('Arial','',11);
$Pdf->Content();
$Pdf->SetTitle("Cetak Nota - ".$data['data']['tanggal']);
$Pdf->Output("Cetak Nota - ".$data['data']['tanggal'].".pdf", "I");

?>
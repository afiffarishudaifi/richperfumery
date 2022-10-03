<?php

$Pdf = new Pdf();
$Pdf->data          = $data['data'];
$Pdf->detail        = $data['detail'];
$Pdf->paper         = $data['paper'];
$Pdf->SetAutoPageBreak(true ,15);
$Pdf->SetMargins(5,5,5);
$Pdf->AliasNbPages();
$Pdf->AddPage();
$Pdf->SetFont('Arial','',11);
$Pdf->Content();
$Pdf->SetTitle("Cetak Kasir - ".$data['data']->no_faktur);
$Pdf->Output("Cetak Kasir - ".$data['data']->no_faktur.".pdf", "I");


class Pdf extends PDF_MC_Table{

	public $def_width = 80, $def_height = 150;
	function __construct() {
		parent::__construct('P','mm',array($this->def_width , $this->def_height));
	}
	function Header(){
		if($this->PageNo() == 1){
			$x = $this->GetX();
			$y = $this->GetY();
			$this->setFont('Arial','B',10);
			$this->MultiCell(70, 5, $this->data->nama_gudang!='' ? ucwords($this->data->nama_gudang) : "", 0, "C");
			$this->setFont('Arial','',9);
			$this->MultiCell(70, 5, $this->data->alamat_gudang!='' ? ucwords($this->data->alamat_gudang) : "", 0, "C");
			// $this->SetXY($x+25, $y);
			$this->MultiCell(70, 5, 'Tanggal : '.$this->data->tanggal, 0, "C");
		}
		$this->Ln(2);
	}

	function Content()
	{
		$this->SetFont('Arial','B',8, "C");
		$this->Cell(7,5,'NO',1,0, "C");
		$this->Cell(38,5,'PRODUK',1,0, "C");
		$this->Cell(10,5,'QTY',1,0, "C");
		$this->Cell(15,5,'TOTAL',1,0, "C");

		$this->SetFont('Arial','',10);
		$jml_detail = json_encode(count($this->detail));
		$this->SetFont('Arial','',8, "C");
		$this->Ln(5);
		for ($i=0; $i < $jml_detail; $i++) {
			$this->Cell(7,6,$i,1,0,'C');
			$this->Cell(38,6,substr($this->detail[$i]->nama_produk, 0,20), 1, "C");
			$this->Cell(10,6,$this->detail[$i]->jumlah,1,0,'C');
			$this->Cell(15,6,rupiah($this->detail[$i]->total),1,0,'R');
			$this->Ln(6);
		}
		$this->Ln(2);
		$this->Cell(50,6,'Total Belanja',0,0);
		$this->Cell(0.5,6,':',0,0,'R');
		$this->Cell(19.5,6,'Rp.'.rupiah($this->data->total_tagihan).',-',0,0,'R');
	}

	function Footer()
	{
		$this->Ln();
		$this->SetY(-15);
		$this->SetFont('Arial','B',9);
		$this->Cell(0,10,'',0,0,'L');
	}
}


?>
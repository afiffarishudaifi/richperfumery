<html>
<head>
<style type="text/css" media="print">
	
	table {border-collapse: collapse;}
	/*tr { border: solid 1px #000}*/
	th { border: solid 1px #000}
	h3 { margin-bottom: -17px }
	h3 { margin-top: -30px }
	h2 { margin-bottom: 0px }
	tfoot { border: solid 1px #000}
</style>
<style type="text/css" media="screen">
	table {border-collapse: collapse;}
	/*tr { border: solid 1px #000}*/
	th { border: solid 1px #000}
	h3 { margin-bottom: -17px }
	h2 { margin-bottom: 0px }
	tfoot { border: solid 1px #000}
</style>
</head>

<body >
<!-- <body  onload="window.print()"> -->
<table  border="0" width="100%">
<th style="border-style: none;">LAPORAN HARIAN</th>
</table>
<br>
<table border="0" width="100%">
	<tr>
	<td ><b>Hari :</b> {{$data['hari']}}</td>
	<td colspan="1"></td>
	<td><b>TANGGAL:</b> {{$data['tanggal']}}</td>
	<td><b>Kasir:</b> {{ucwords($data['kasir'])}}</td>
	</tr>
	
</table>
<br>
<table width="100%" border="0">
    <tr>
        <th colspan="8" style="border-style: none;"></th>
        <th colspan="3" style="border: solid 1px; text-align: center;"><b> Paper Bag</b></th>
		<th colspan="2">Kartu</th>
		<th rowspan="2">Stiker</th>
		<th colspan="4">BOX</th>
        
    </tr>
    <tr>
		<th style="border-style: none;"></th>
		<th > No. <br>Nota</th>
		<th style="border-style: none;"></th>
		<th>30 ml <br> tabung</th>
		<th>30 ml <br> kotak</th>
		<th>10 ml</th>
		<th>55 ml</th>
		<th>100 ml</th>
		<th>BSR</th>
		<th>SDG</th>
		<th>KCL</th>
		<th>Member</th>
		<th>Stiker</th>		
		<th>Hitam</th>
		<th>Pinx</th>
		<th>Silver</th>
		<th>Hijau</th>
    </tr>
	<tr>
		<th>Nota Masuk </th>
		<th></th>
		<th>Stock Awal</th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
	<tr>
		<th>Nota Retur</th>
		<th></th>
		<th>Penjualan Hari ini</th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
	<tr>
		<th style="border-style: none;"></th>
		<th style="border-style: none;"></th>
		<th>Stock Akhir</th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
    </tr>
</table>
<br>
<div align="left">
	<table  width="40%">
		<tr>
			<th width="35%">Penjualan Tunai</th>
			<th width="65%" colspan="2"></th>
		</tr>
		<!-- <tr>
			<th width="35%" rowspan="2">Penjualan Debet</th>
			<th width="20%">BCA</th>
			<th width="45%"></th>
		</tr>
		<tr>
			<th width="20%">MANDIRI</th>
			<th width="45%"></th>
		</tr>
		<tr>
			<th width="35%">Flazz</th>
			<th width="65%" colspan="2"></th>
		</tr>
		<tr>
			<th width="35%" rowspan="2">Penjualan Kredit</th>
			<th width="20%">BCA</th>
			<th width="45%"></th>
		</tr>
		<tr>
			<th width="20%">MANDIRI</th>
			<th width="45%"></th>
		</tr>
		<tr>
			<th width="35%" rowspan="2">Penjualan Transfer</th>
			<th width="20%">BCA</th>
			<th width="45%"></th>
		</tr>
		<tr>
			<th width="20%">MANDIRI</th>
			<th width="45%"></th>
		</tr>
		<tr>
			<th width="35%">Penjualan OVO</th>
			<th width="65%" colspan="2"></th>
		</tr>
		<tr>
			<th width="35%">Penjualan Hutang</th>
			<th width="65%" colspan="2"></th>
		</tr>
		<tr>
			<th width="35%" height="60px;">Total Pendapatan</th>
			<th width="65%" height="60px;" colspan="2"></th>
		</tr> -->
	</table>
</div>
<div align="right" style="margin-top: -22px ">
<table  width="40%"  border="1">
	<tr>
		<th >No. Nota</th>
		<th >JUMLAH TRANSFER</th>
		<th >ONGKIR</th>
	</tr>
	<tr>
		<td>q</td>
		<td>q</td>
		<td>q</td>
	</tr>
</table>
</div>
<br>
<div align="right" style="margin-top: 50px ">
<table  width="40%"  border="1">
	<tr>
		<th >No. Nota</th>
		<th >HUTANG</th>
		<th >ONGKIR</th>
	</tr>
	<tr>
		<td>q</td>
		<td>q</td>
		<td>q</td>
	</tr>
</table>
</div>

<div align="right" style="margin-top: 30px ">
<table  width="45%"  border="0">
	<tr>
		<th style="border-style: none; text-align: left;" width="20%">Absensi :</th>
		<th style="border-style: none; border-bottom: solid 1px #000; text-align: left;" width="50%">&nbsp;&nbsp;&nbsp; 1</th>
		<th style="border-style: none; border-bottom: solid 1px #000; text-align: left;" width="30%">4</th>
	</tr>
	<tr>
		<th style="border-style: none; text-align: left;" width="20%"></th>
		<th style="border-style: none; border-bottom: solid 1px #000; text-align: left;" width="50%">&nbsp;&nbsp;&nbsp; 2</th>
		<th style="border-style: none; border-bottom: solid 1px #000; text-align: left;" width="30%">5</th>
	</tr>
	<tr>
		<th style="border-style: none; text-align: left;" width="20%"></th>
		<th style="border-style: none; border-bottom: solid 1px #000; text-align: left;" width="50%">&nbsp;&nbsp;&nbsp; 3</th>
		<th style="border-style: none; border-bottom: solid 1px #000; text-align: left;" width="30%">6</th>
	</tr>
</table>
</div>

</body>
</html>

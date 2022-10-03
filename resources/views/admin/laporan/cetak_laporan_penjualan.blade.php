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

<!-- <body > -->
<body  onload="window.print();" onfocus="window.close()">
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
		<th>{{($data['stokawal']['569']['total_barang']=="")?"":format_angka($data['stokawal']['569']['total_barang'])}}</th>
		<th>{{($data['stokawal']['571']['total_barang']=="")?"":format_angka($data['stokawal']['571']['total_barang'])}}</th>
		<th>{{($data['stokawal']['610']['total_barang']=="")?"":format_angka($data['stokawal']['610']['total_barang'])}}</th>
		<th>{{($data['stokawal']['587']['total_barang']=="")?"":format_angka($data['stokawal']['587']['total_barang'])}}</th>
		<th>{{($data['stokawal']['594']['total_barang']=="")?"":format_angka($data['stokawal']['594']['total_barang'])}}</th>
		<th>{{($data['stokawal']['600']['total_barang']=="")?"":format_angka($data['stokawal']['600']['total_barang'])}}</th>
		<th>{{($data['stokawal']['601']['total_barang']=="")?"":format_angka($data['stokawal']['601']['total_barang'])}}</th>
		<th>{{($data['stokawal']['602']['total_barang']=="")?"":format_angka($data['stokawal']['602']['total_barang'])}}</th>
		<th>{{($data['stokawal']['606']['total_barang']=="")?"":format_angka($data['stokawal']['606']['total_barang'])}}</th>
		<th>{{($data['stokawal']['605']['total_barang']=="")?"":format_angka($data['stokawal']['605']['total_barang'])}}</th>
		<th>{{($data['stokawal']['607']['total_barang']=="")?"":format_angka($data['stokawal']['607']['total_barang'])}}</th>
		<th>{{($data['stokawal']['631']['total_barang']=="")?"":format_angka($data['stokawal']['631']['total_barang'])}}</th>
		<th>{{($data['stokawal']['632']['total_barang']=="")?"":format_angka($data['stokawal']['632']['total_barang'])}}</th>
		<th>{{($data['stokawal']['633']['total_barang']=="")?"":format_angka($data['stokawal']['633']['total_barang'])}}</th>
		<th>{{($data['stokawal']['634']['total_barang']=="")?"":format_angka($data['stokawal']['634']['total_barang'])}}</th>
	</tr>
	<tr>
		<th>Nota Retur</th>
		<th></th>
		<th>Penjualan Hari ini</th>
		<th>{{($data['penjualan']['569']['total_barang']=="")?"":format_angka($data['penjualan']['569']['total_barang'])}}</th>
		<th>{{($data['penjualan']['571']['total_barang']=="")?"":format_angka($data['penjualan']['571']['total_barang'])}}</th>
		<th>{{($data['penjualan']['610']['total_barang']=="")?"":format_angka($data['penjualan']['610']['total_barang'])}}</th>
		<th>{{($data['penjualan']['587']['total_barang']=="")?"":format_angka($data['penjualan']['587']['total_barang'])}}</th>
		<th>{{($data['penjualan']['594']['total_barang']=="")?"":format_angka($data['penjualan']['594']['total_barang'])}}</th>
		<th>{{($data['penjualan']['600']['total_barang']=="")?"":format_angka($data['penjualan']['600']['total_barang'])}}</th>
		<th>{{($data['penjualan']['601']['total_barang']=="")?"":format_angka($data['penjualan']['601']['total_barang'])}}</th>
		<th>{{($data['penjualan']['602']['total_barang']=="")?"":format_angka($data['penjualan']['602']['total_barang'])}}</th>
		<th>{{($data['penjualan']['606']['total_barang']=="")?"":format_angka($data['penjualan']['606']['total_barang'])}}</th>
		<th>{{($data['penjualan']['605']['total_barang']=="")?"":format_angka($data['penjualan']['605']['total_barang'])}}</th>
		<th>{{($data['penjualan']['607']['total_barang']=="")?"":format_angka($data['penjualan']['607']['total_barang'])}}</th>
		<th>{{($data['penjualan']['631']['total_barang']=="")?"":format_angka($data['penjualan']['631']['total_barang'])}}</th>
		<th>{{($data['penjualan']['632']['total_barang']=="")?"":format_angka($data['penjualan']['632']['total_barang'])}}</th>
		<th>{{($data['penjualan']['633']['total_barang']=="")?"":format_angka($data['penjualan']['633']['total_barang'])}}</th>
		<th>{{($data['penjualan']['634']['total_barang']=="")?"":format_angka($data['penjualan']['634']['total_barang'])}}</th>
	</tr>
	@php
	$d_569 = (float)$data['stokawal']['569']['total_barang']-(float)$data['penjualan']['569']['total_barang'];
	$d_571 = (float)$data['stokawal']['571']['total_barang']-(float)$data['penjualan']['571']['total_barang'];
	$d_610 = (float)$data['stokawal']['610']['total_barang']-(float)$data['penjualan']['610']['total_barang'];
	$d_587 = (float)$data['stokawal']['587']['total_barang']-(float)$data['penjualan']['587']['total_barang'];
	$d_594 = (float)$data['stokawal']['594']['total_barang']-(float)$data['penjualan']['594']['total_barang'];
	$d_600 = (float)$data['stokawal']['600']['total_barang']-(float)$data['penjualan']['600']['total_barang'];
	$d_601 = (float)$data['stokawal']['601']['total_barang']-(float)$data['penjualan']['601']['total_barang'];
	$d_602 = (float)$data['stokawal']['602']['total_barang']-(float)$data['penjualan']['602']['total_barang'];
	$d_606 = (float)$data['stokawal']['606']['total_barang']-(float)$data['penjualan']['606']['total_barang'];
	$d_605 = (float)$data['stokawal']['605']['total_barang']-(float)$data['penjualan']['605']['total_barang'];
	$d_607 = (float)$data['stokawal']['607']['total_barang']-(float)$data['penjualan']['607']['total_barang'];
	$d_631 = (float)$data['stokawal']['631']['total_barang']-(float)$data['penjualan']['631']['total_barang'];
	$d_632 = (float)$data['stokawal']['632']['total_barang']-(float)$data['penjualan']['632']['total_barang'];
	$d_633 = (float)$data['stokawal']['633']['total_barang']-(float)$data['penjualan']['633']['total_barang'];
	$d_634 = (float)$data['stokawal']['634']['total_barang']-(float)$data['penjualan']['634']['total_barang'];
	@endphp
	<tr>
		<th style="border-style: none;"></th>
		<th style="border-style: none;"></th>
		<th>Stock Akhir</th>
		<th>@php echo ($d_569==0)? "":format_angka((float)$d_569); @endphp</th>
		<th>@php echo ($d_571==0)? "":format_angka((float)$d_571); @endphp</th>
		<th>@php echo ($d_610==0)? "":format_angka((float)$d_610); @endphp</th>
		<th>@php echo ($d_587==0)? "":format_angka((float)$d_587); @endphp</th>
		<th>@php echo ($d_594==0)? "":format_angka((float)$d_594); @endphp</th>
		<th>@php echo ($d_600==0)? "":format_angka((float)$d_600); @endphp</th>
		<th>@php echo ($d_601==0)? "":format_angka((float)$d_601); @endphp</th>
		<th>@php echo ($d_602==0)? "":format_angka((float)$d_602); @endphp</th>
		<th>@php echo ($d_606==0)? "":format_angka((float)$d_606); @endphp</th>
		<th>@php echo ($d_605==0)? "":format_angka((float)$d_605); @endphp</th>
		<th>@php echo ($d_607==0)? "":format_angka((float)$d_607); @endphp</th>
		<th>@php echo ($d_631==0)? "":format_angka((float)$d_631); @endphp</th>
		<th>@php echo ($d_632==0)? "":format_angka((float)$d_632); @endphp</th>
		<th>@php echo ($d_633==0)? "":format_angka((float)$d_633); @endphp</th>
		<th>@php echo ($d_634==0)? "":format_angka((float)$d_634); @endphp</th>
    </tr>
</table>
<br>
<div>
	<table  width="40%" height="60%" align="left">
		<?php 
		$total_pendapatan = 0;
		foreach($data['omset'] as $d){ 
			$total_pendapatan += $d->data;
		?>
		<tr>
			<th width="35%">Penjualan Tunai{{$d->name}}</th>
			<th width="65%" style="text-align: right;">{{($d->data==null) ? "":'Rp '.format_angka($d->data)}} &nbsp;</th>
		</tr>
		<?php } ?>
		<tr>
			<th width="35%">Total Pendapatan</th>
			<th width="65%"style="text-align: right; height: 70px; font-size: 25px;">{{'Rp '.format_angka($total_pendapatan)}} &nbsp;</th>
			
		</tr>
	</table>
	<table  width="40%"  border="1" align="right" style="margin-bottom: 3%;">
	<tr>
		<th >No. Nota</th>
		<th >JUMLAH TRANSFER</th>
		<th >ONGKIR</th>
	</tr>
	@php if(count($data['transfer'])){
	foreach($data['transfer'] as $t){@endphp
	<tr>
		<td>{{$t->no_faktur}}</td>
		<td style="text-align: right;">{{"Rp ".format_angka($t->total_tagihan-$t->total_potongan)}} &nbsp;</td>
		<td style="text-align: right;">{{"Rp ".format_angka($t->ongkos_kirim)}} &nbsp;</td>
	</tr>
	@php } }else{ @endphp
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	@php } @endphp
	</table>

	<table width="40%"  border="1" align="right" style="margin-bottom: 3%;">
	<tr>
		<th>No. Nota</th>
		<th>HUTANG</th>
		<th>ONGKIR</th>
	</tr>
	@php if(count($data['hutang'])){
	foreach($data['hutang'] as $t){@endphp
	<tr>
		<td>{{$t->no_faktur}}</td>
		<td style="text-align: right;">{{"Rp ".format_angka($t->total_tagihan-$t->total_potongan)}} &nbsp;</td>
		<td style="text-align: right;">{{"Rp ".format_angka($t->ongkos_kirim)}} &nbsp;</td>
	</tr>
	@php } }else{ @endphp
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	@php } @endphp
	</table>


	<table  width="45%"  border="0" align="right">
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

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
	h3 { margin-top: -30px }
	h2 { margin-bottom: 0px }
	tfoot { border: solid 1px #000}
</style>
</head>

{{-- <body > --}}
<body  onload="window.print()">
<table border="0" width="100%">
	<tr>
	<td  style=" width: 7%;"   ><h3>TGL :</h3></td>
	<td><p style="margin-top: -25px ">{{date("d-m-Y", strtotime($data['pengirim'][0]->tanggal_pengiriman))}}</p></td>
	<td colspan="3"  style=" width: 50%;"  ></td>
	<td style="margin-top: -40px"><b>Kepada Yth.</b><br>
	{{$data['pengirim'][0]->tujuan}}<br>
		{{$data['pengirim'][0]->alamat}} <br>
		{{-- kota <br> --}}
	 </td>
	</tr>
	
	<tr>
		<td colspan="6"> <center><h4  style=" margin-bottom: 5;">SURAT JALAN</h4> 
		<p><b>NO :</b> {{$data['pengirim'][0]->kode_pengiriman}}</p> </center></td>	
	</tr>
	
</table>
<table width="100%" border="">
	<tr>
		<th>No</th>
		<th>Nama Barang</th>
		<th>Qty</th>
		<th>Keterangan</th>
	</tr>
	<?php foreach ($data['barang'] as $key => $value) {
	 ?>
	<tr >
	<td  align="center">{{$key+1}}</td>
	<td>{{$value->nama}}</td>
		<td align="center">{{$value->jumlah}}</td>
		<td>..</td>		
	</tr>
<?php } ?>
</table>
<br>
<table border="" width="100%" >
	<tr>
		<td colspan="7" align="center">Penerima</td>
		<td align="center">Keamanan</td>
		<td align="center">Mengetahui</td>
		<td align="center">Pengirim</td>
	</tr>
	<tr>
		<td style="height: 80px;" colspan="7" align="center"></td>
		<td align="center">.</td>
		<td align="center">.</td>
		<td align="center">.</td>
	</tr>
</table>
</body>
</html>

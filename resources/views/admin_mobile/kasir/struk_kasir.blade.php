<!DOCTYPE html>
<html>
<head>
	<title>Struk Belanja</title>
	<link rel="stylesheet" href="{{ asset('public/admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
</head>
<body onload="showAndroidToast()">
	<h3 align="center"><?php echo $data->nama_gudang ?></h3>
	<p align="center"><?php echo $data->alamat_gudang ?></p>
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th style="text-align: center;">No</th>
				<th style="text-align: left;">NAMA PRODUK</th>
				<th style="text-align: center;">QTY</th>
				<th style="text-align: center;">HARGA</th>
				<th style="text-align: center;">SUBTOTAL</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$no = 1;
			foreach ($detail as $value) {
				echo '<tr>';
				echo '<td align="center">'.$no++.'</td>';
				echo '<td>'.$value->nama_produk.'</td>';
				echo '<td align="center">'.$value->jumlah.'</td>';
				echo '<td align="right">'.rupiah($value->harga).'</td>';
				echo '<td align="right">'.rupiah($value->total).'</td>';
				echo '</tr>';
			}
			$no2 = $no;
			foreach ($barang as $value) {
				echo '<tr>';
				echo '<td align="center">'.$no2++.'</td>';
				echo '<td>'.$value->nama_barang.'</td>';
				echo '<td align="center">'.$value->jumlah.'</td>';
				echo '<td align="right">'.rupiah($value->harga).'</td>';
				echo '<td align="right">'.rupiah($value->total).'</td>';
				echo '</tr>';
			}
			?>
		</tbody>
		<tr>
			<td colspan="4" style="font-weight:bold">TOTAL TAGIHAN</td>
			<td colspan="4" style="font-weight:bold;text-align: right"><?php echo 'Rp.'.rupiah($data->total_tagihan).',-' ?></td>
		</tr>
	</table>


	<script type="text/javascript">
		<?php
		$id_profil = Auth::user()->id_profil;
		$nama_kasir = Auth::user()->name; 
		$profil= DB::select("SELECT mp.nama, mp.alamat, mp.telp FROM m_profil mp WHERE mp.id = '$id_profil'");
		$nama = "--";
		$alamat = "--";
		$telp = "--";
		foreach ($profil as $value) {
			$nama = $value->nama;
			$alamat = $value->alamat;
			$telp = $value->telp;
		}
		?>
		var nama_outlet = '<?php echo $nama; ?>'
		var alamat_outlet = '<?php echo $alamat; ?>'
		var telp_outlet = '<?php echo $telp; ?>'
		var id_kasir = '<?php echo $data->id_kasir ?>';
		var nama_kasir = "{{$nama_kasir}}"
		var keterangan = "{{ $keterangan }}";

		function showAndroidToast() {
			try {
				Android.moveCetak(id_kasir,nama_kasir,nama_outlet,alamat_outlet,telp_outlet,keterangan);	
			} catch (error) {
				Android.moveCetak(id_kasir,nama_kasir,nama_outlet,alamat_outlet,telp_outlet);
			}
		}
	</script>
</body>
</html>


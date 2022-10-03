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
	<h3 align="center">Gudang PTC</h3>
	<p align="center">Pakuwon Trade Center Lantai UG Blok E9 No 28 Surabaya</p>
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
			foreach ($produk_cetak as $value) {
				$id_kasir = $value['id_kasir'];
				$id_produk = $value['id_produk'];
				$jumlah = $value['jumlah'];
				$harga = $value['harga'];
				$total = $value['total'];
				for($i=0;$i<count($produk_cetak);$i++){
					$barangs= DB::select("SELECT b.nama as produk_nama FROM m_produk as b WHERE b.id = '$id_produk'");
				}
				foreach ($barangs as $value) {
					echo '<tr>';
					echo '<td align="center">'.$id_kasir.'</td>';
					echo '<td>'.$value->produk_nama.'</td>';
					echo '<td align="center">'.$jumlah.'</td>';
					echo '<td align="right">'.$harga.'</td>';
					echo '<td align="right">'.$total.'</td>';
					echo '</tr>';
				}
			}
			?>
		</tbody>
		<tr>
			<td colspan="4" style="font-weight:bold">TOTAL TAGIHAN</td>
			<td colspan="4" style="font-weight:bold;text-align: right"><?php echo 'Rp.'.rupiah($data['total_tagihan']).',-' ?></td>
		</tr>
	</table>


	<script type="text/javascript">
		<?php
		$id_profil = Auth::user()->id_profil;
		$profil= DB::select("SELECT mp.nama, mp.alamat, mp.telp FROM m_profil mp WHERE mp.id = '$id_profil'");
		foreach ($profil as $value) {
			$nama = $value->nama;
			$alamat = $value->alamat;
			$telp = $value->telp;
		}
		?>
		var nama_outlet = '<?php echo $nama; ?>'
		var alamat_outlet = '<?php echo $alamat; ?>'
		var telp_outlet = '<?php echo $telp; ?>'
		var id_kasir = <?php echo json_encode($produk_cetak['0']['id_kasir']) ?>;

		function showAndroidToast() {
			Android.moveCetak(id_kasir,nama_outlet,alamat_outlet,telp_outlet);
		}
	</script>
</body>
</html>


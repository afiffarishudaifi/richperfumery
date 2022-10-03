<!DOCTYPE html>
<html>
<head>
	<title>Closing Penjualan</title>
	<link rel="stylesheet" href="{{ asset('public/admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
</head>
<body onload="showAndroidToast()">	
	<div class="table-responsive">
        <table class="table table-bordered table-striped" style="padding-top: 0px;">
          <thead>
            <tr>
              <th style="width:30%">Jumlah Nota</th>
              <td style="width:50%" id="total" class="td_total">{{$nota[0]->jumlah_nota}}</td>
              <td style="width:20%">@if($nota[0]->status==2)<label style="background-color: #00a65a !important;color: #fff !important;">Sudah Closing</label>@endif</td>
            </tr>
          </thead>
        </table>
      </div>
      <div class="table-responsive">
        <table id="datatable1" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th style="width:30%">Nama</th>
              <th style="width:70%; text-align: right;" >Nominal</th>
            </tr>
          </thead>
          <tbody>
          	@foreach($data as $d)
          	<tr>
              <th style="width:30%">{{$d->name}}</th>
              <th style="width:70%; text-align: right;">{{'Rp '.rupiah($d->data)}}</th>
            </tr>
            @endforeach
          </tbody>

          <tr>
            <th style="width:30%">Total:</th>
            <th style="width:70%;" id="totall" class=" text-right">{{'Rp '.rupiah($total[0]->data)}}</th>
          </tr>
        </table>
      </div>


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

		var id_gudang 	= '{{$gudang}}'
		var tanggal = "{{date('d-m-Y',strtotime($tanggal))}}"
		var nama_kasir = "{{$nama_kasir}}"
		

		function showAndroidToast() {
			Android.moveCetakClosing(id_gudang,tanggal,nama_kasir,nama_outlet,alamat_outlet,telp_outlet);
		}
	</script>
</body>
</html>


<?php $hal = "stok"; ?>
@extends('layouts.admin.master')
@section('title', 'Persediaan Barang')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('public/admin/bower_components/select2/dist/css/select2.min.css')}}">

<style>
.example-modal .modal {
  position: relative;
  top: auto;
  bottom: auto;
  right: auto;
  left: auto;
  display: block;
  z-index: 1;
}

.example-modal .modal {
  background: transparent !important;
}

table, th {
  border: 1px solid black !important;
}

td {
  border: 0.1px solid black !important;
}
</style>

@endsection


@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Persediaan Barang
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{$barang_nama}} ({{$barang_kode}})</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="example2" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="text-align:center;" rowspan="2" >Tanggal</th>
                <th style="text-align:center;" colspan="3">Masuk</th>
                <th style="text-align:center;" colspan="3">Keluar</th>
                <th style="text-align:center;" colspan="3">Saldo</th>
              </tr>

              <tr>
                <th style="width:10%;text-align:center;">Unit</th>
                <th style="width:10%;text-align:center;">Harga per unit</th>
                <th style="width:10%;text-align:center;">Total</th>
                <th style="width:10%;text-align:center;">Unit</th>
                <th style="width:10%;text-align:center;">Harga per unit</th>
                <th style="width:10%;text-align:center;">Total</th>
                <th style="width:10%;text-align:center;">Unit</th>
                <th style="width:10%;text-align:center;">Harga per unit</th>
                <th style="width:10%;text-align:center;">Total</th>
              </tr>

            </thead>
            <tbody>
              @foreach($log_stok as $data)

              <td style="text-align:center;">{{date('d/m/Y', strtotime($data->log_stok_tanggal))}}</td>
              <td style="text-align:center;">{{$data->log_stok_unit_masuk}}</td>
              <td style="text-align:right;">{{number_format($data->log_stok_harga_masuk,0,',','.')}}</td>
              <td style="text-align:right;;">{{number_format($data->log_stok_total_masuk,0,',','.')}}</td>
              <td style="text-align:center;">{{$data->log_stok_unit_keluar}}</td>
              <td style="text-align:right;;">{{number_format($data->log_stok_harga_keluar,0,',','.')}}</td>
              <td style="text-align:right;">{{number_format($data->log_stok_total_keluar,0,',','.')}}</td>
              <td style="text-align:center;">{{$data->log_stok_saldo_unit}}</td>
              <td style="text-align:right;">{{number_format($data->log_stok_saldo_harga,0,',','.')}}</td>
              <td style="text-align:right;">{{number_format($data->log_stok_saldo_total,0,',','.')}}</td>
              @endforeach
            </tbody>
          </table>
          <a href="{{route('stok.index')}}"  style="margin-bottom:20px;" class="card-body-title"><button class="btn btn-warning">Kembali</button></a>

        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->
@include('admin.stok.form')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>


<script>
$(function () {
  $('#example1').DataTable()
  $('#example2').DataTable({
    'paging'      : true,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : false,
    'info'        : true,
    'autoWidth'   : false
  })
})
</script>


<script>
// $(function () {
//   //Initialize Select2 Elements
//   $('.select2').select2()
// })
$(document).ready(function() {
  $('.js-example-basic-single').select2({
    dropdownParent: $(".modal")
  });
});
</script>

@endsection

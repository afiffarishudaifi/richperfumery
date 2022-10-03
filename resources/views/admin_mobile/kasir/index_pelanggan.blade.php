<?php $hal = "kasirpelanggan"; ?>
@extends('layouts.admin.master')
@section('title', 'Pelanggan Penjualan')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">

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
  </style>

@endsection


@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Pelanggan Penjualan
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Pelanggan</h3>
        </div>
        
        <!-- <a href="{{url('kasir_tambah')}}" style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a> -->
        <!-- <button class="btn btn-primary" style="margin-bottom:20px;margin-left:10px;" type="button" id="btn_posting"> <i class="fa  fa-check-o"></i>Closing</button> -->
        <!-- /.box-header -->

        <div class="box-body">
          <div class="row">
          <form role="form" class="form form_pelanggan" action="javascript:;" method="post" autocomplete="off" >
            <input type="hidden" name="status" value="1">
            <div class="col-md-12">
            <div class="col-md-3">
              <div class="form-group">
                <label>Gudang</label> 
                 <select class="form-control select2" name="gudang" style="width: 100%;">
                  @foreach($data['gudang'] as $d)
                    <option value="{{$d->id_gudang}}" nama="{{$d->nama_gudang}}" alamat="{{$d->alamat_gudang}}">{{$d->nama_gudang}}</option>
                  @endforeach  
                 </select>
              </div>
            </div> 
            <div class="col-md-3">
              <div class="form-group">
                 <label>Pelanggan</label>
                 <select class="form-control select2" name="pelanggan" style="width: 100%;">
                  @foreach($data['pelanggan'] as $d)
                    <option value="{{$d->id}}" nama="{{$d->nama}}" telp="{{$d->telp}}">{{($d->telp==null||$d->telp==" ")?$d->nama:$d->nama." (".$d->telp.")"}}</option>
                  @endforeach  
                 </select>
                </div>
              </div>  
              <div class="col-md-6">          
                <div class="form-group"> 
                 <button type="submit" class="btn btn-success" style="margin-top: 25px;"><b><i class="fa fa-search"></i></b> Filter</button>
                 <button type="button" class="btn btn-danger reset hide" style="margin-top: 25px;"><b><i class="fa fa-refresh"></i></b> Reset</button>
                </div>
              </div>
            </div>
          </form>
          </div>
        </div>
        <hr>
        <div class="box-body">
        <div class="table-responsive">
        <table id="datatables_pelanggan" class="table table-bordered table-striped" style="padding-top: 0px;" width="100%">
              <thead>
              <tr>
                <th style="width:3%">#</th>
                <th style="width:3%">No.</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:74%">Nama Barang</th>                
                <!-- <th style="width:20%">Harga</th>
                <th style="width:15%">Jumlah</th>
                <th style="width:17%">Total</th> -->
              </tr>
              </thead>
              <tbody>
              </tbody>
        </table>
      </div>
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
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script type="text/javascript">

var table, table_pelanggan,save_method;
$(function(){
   table_pelanggan = $('#datatables_pelanggan').DataTable({    
    "processing" : true,
    "searching":true,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "info": false,
    "sDom": '<"row view-filter"<"col-md-6-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-md-6-12"<"text-center"ip>>>',
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "ajax" : {
      "url" : "{{url('kasirpelanggan_data')}}",
      "type" : "GET",
      data: function(d) {
        d.gudang     = $('.form_pelanggan select[name=gudang]').val();
        d.pelanggan  = $('.form_pelanggan select[name=pelanggan]').val();
        d.status = $('.form_pelanggan input[name=status]').val();
      }
    },
    "columns": [
      {
        "className":      'details-control',
        "orderable":      false,
        "data":           null,
        "defaultContent": ''
      },
      { "data": "no" },
      { "data": "tanggal"},
      { "data": "nama" },
      // { "data": "jumlah" },
      // { "data": "alamat" },
      // { "data": "gudang" },
      // { "data": "status" },
      // { "data": "aksi" }
    ]/*,
    columnDefs: [{
        targets: 4,
        className: 'text-right'       
        }]   */ 
  });

  function format ( d ) {
    // `d` is the original data object for the row
    return '<table id="show_tabel">'+
    '<tr>'+
    '<td>Nama</td>'+
    '<td> : '+d.nama+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Harga</td>'+
    '<td> : '+d.harga+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Jumlah</td>'+
    '<td> : '+d.jumlah+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Total</td>'+
    '<td> : '+d.total+'</td>'+
    '</tr>'+
    '</table>';
  }

  $('#datatables_pelanggan tbody').on('click', 'td.details-control', function () {
    var tabelnya = $('#datatables_pelanggan').DataTable();
    var tr = $(this).closest('tr');
    var row = tabelnya.row( tr );

    if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }else {
      // Open this row
      row.child( format(row.data()) ).show();
      tr.addClass('shown');
    }
  });

});

$(document).on("submit","form.form_pelanggan",function(e){
    $('.form_pelanggan input[name=status]').val("2");
    table_pelanggan.ajax.reload();
    $('.reset').removeClass('hide');

});
$(".reset").on("click",function(){
  $('.form_pelanggan input[name=status]').val("1");
  table_pelanggan.ajax.reload();
  $('.reset').addClass('hide');
})
</script>

@endsection

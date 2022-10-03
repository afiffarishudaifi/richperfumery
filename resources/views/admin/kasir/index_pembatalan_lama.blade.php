<?php $hal = "kasir_pembatalan"; ?>
@extends('layouts.admin.master')
@section('title', 'Penjualan')

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
    Pembatalan Penjualan
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Pembatalan Penjualan</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-toggle="collapse" title="Search" data-target="#form-search"> <i class="fa fa-search"></i> Search</button>
          </div>
        </div>
        <div class="box-body collapse" id="form-search">
          <form class="form-horizontal form-tanggal-search" method="POST" autocomplete="off">
            <div class="col-md-6">
              <div class="form-group">
                <label class="col-md-2">Tanggal</label>
                <div class="col-md-5">
                  <input type="text" name="search_tanggal" class="form-control" value="{{date('d-m-Y')}}">
                </div>
                <div class="col-md-4">
                  <a href="javascript:;" style="margin-bottom:20px;" class="card-body-title btn-search-tanggal"><button class="btn btn-success" type="button"><i class="fa fa-search"></i> Search</button></a>
                  <a href="javascript:;" style="margin-bottom:20px;" class="card-body-title btn-reset-tanggal"><button class="btn btn-warning" type="button"><i class="fa fa-undo"></i> Reset</button></a>
                </div>
              </div>
            </div>
          </form>
      </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:3%">No #</th>
                <th>Tanggal</th>
                <th>No.Faktur</th>
                <th>Pelanggan</th>
                <th>Gudang</th>
                <th>Total Penjualan</th>
                <th>Dibatalkan Oleh</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
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
@include('admin.kasir.form_infocetak')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<link rel="stylesheet" type="text/css" href="{{asset('public/js/daterangepicker/daterangepicker.css')}}" />
<script type="text/javascript" src="{{asset('public/js/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
var table, table_search, save_method;
$(function(){
  table = $('.table').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ route('kasir_pembatalan_data') }}",
      "type" : "GET"
    }
  })
  $('input[name=search_tanggal]').daterangepicker({
   locale: {
      format: 'DD-MM-YYYY',
      separator: "  s.d. ",
    }
  });
});

$(".btn-search-tanggal").on("click",function(){
    var tanggal = $("[name=search_tanggal]").val();
    var url = '<?=$hal?>_searchtanggal';
    $('.table').DataTable().clear();
    $('.table').DataTable().destroy();
    table_search = $('.table').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{url('kasir_pembatalan_searchtanggal')}}",
      "data" : {tanggal:tanggal}
    },
    "columnDefs": [
            { targets: 0, orderable: false, searchable: false, }
          ],
  });
});

$(".btn-reset-tanggal").on("click",function(){
    $('.table').DataTable().clear();
    $('.table').DataTable().destroy();
    table = $('.table').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ url('kasir_pembatalan_data') }}",
      "type" : "GET"
    }
  });
});

$(document).on('click',"#btn_infocetak",function(){
  $("[name=popupinfo_iduser]").val($(this).data('user'));
  $("[name=popupinfo_idkasir]").val($(this).data('kasir'));
  $("[name=popupinfo_keterangan]").val("");
  $('#modal-forminfo').modal('show');
});

$("#btn_popupinfo_simpan").click(function(){
    var keterangan = $("[name=popupinfo_keterangan]").val();
    var id_kasir = $("[name=popupinfo_idkasir]").val();
    var url = "<?= url('kasir_pembatalan_cetak')?>"+"/"+id_kasir;
    console.log(url);
    if(keterangan != ''){
      $.ajax({
        url: "{{ url('kasir_pembatalan_simpan_infocetak') }}",
        type: 'post',
        dataType: 'json',
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $("#form_infocetak").serialize(),
        success: function(respon){
          table.ajax.reload();
          window.open(url,'_blank');
          $('#modal-forminfo').modal('hide');
        }
      })
    }
  })


</script>

<script>
$(function () {
  $('#example1').DataTable()
  $('#example2').DataTable({
    'paging'      : true,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : true,
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

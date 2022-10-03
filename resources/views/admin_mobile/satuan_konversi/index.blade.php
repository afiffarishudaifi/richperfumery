<?php $hal = "satuankonversi"; ?>
@extends('layouts.admin.master')
@section('title', 'Satuan Konversi')

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
    Satuan Konversi
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Satuan Konversi</h3>
        </div>
        <!-- <a href="javascript:;" class="btn btn-sm btn-success btn_tambah" id="btn_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah Barang</a> -->
        <a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:3%">No #</th>
                <th style="width:20%">Satuan Awal</th>
                <th style="width:20%">Jumlah Awal</th>
                <th style="width:20%">Satuan Akhir</th>
                <th style="width:20%">Jumlah Akhir</th>
                <th style="width:17%">Action</th>
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
@include('admin.satuan_konversi.form')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script type="text/javascript">
var table, save_method;
$(function(){
  table = $('.table').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ route('satuankonversi_data') }}",
      "type" : "GET"
    }
  });

});

$("[name=popup_jumlah_awal]").on('keyup', function(){
    var jum = $(this).val();
    $("#help_popup_jumlahawal").text(format_angka(jum));
    get_jumbagi();
});

$("[name=popup_jumlah_akhir]").on('keyup', function(){
    var jum = $(this).val();
    $("#help_popup_jumlahakhir").text(format_angka(jum));
    get_jumbagi();
});

$("#btn_tambah").click(function(){
    $('[name=popup_id_table]').val('');
    $('[name=popup_satuan_awal]').val(null).trigger('change');
    $('[name=popup_jumlah_awal]').val('');
    $('[name=popup_satuan_akhir]').val(null).trigger('change');
    $('[name=popup_jumlah_akhir]').val('');
    $('[name=popup_jumlah_bagi]').val('');
    $('#modal-form').modal('show');
});

function edit(id){
    $("[name=popup_id_table]").val($("#table_id"+id).val());
    $("[name=popup_satuan_awal]").val($("#table_idsatuan_awal"+id).val()).trigger('change');
    $("[name=popup_jumlah_awal]").val($("#table_jumlah_awal"+id).val());
    $("[name=popup_satuan_akhir]").val($("#table_idsatuan_akhir"+id).val()).trigger('change');
    $("[name=popup_jumlah_akhir]").val($("#table_jumlah_akhir"+id).val());
    $("[name=popup_jumlah_bagi]").val($("#table_jumlah_bagi"+id).val());
    $("#help_popup_jumlahawal").text(format_angka($("#table_jumlah_awal"+id).val()));
    $("#help_popup_jumlahakhir").text(format_angka($("#table_jumlah_akhir"+id).val()));

    $('#modal-form').modal('show');
  }


$("#btn_popup_simpan").click(function(){
    var id = $("[name=popup_id_table]").val();
    var satuan          = $("[name=popup_satuan_awal]").val();
    var jumlah_satuan   = $("[name=popup_jumlah_awal]").val();
    var satuan_konversi = $("[name=popup_satuan_akhir]").val();
    var jumlah_satuan_konversi = $("[name=popup_jumlah_akhir]").val();
    if(satuan != '' && jumlah_satuan != '' && satuan_konversi != '' && jumlah_satuan_konversi != ''){
      $.ajax({
        url: "{{ url('satuankonversi_simpan') }}",
        type: 'post',
        dataType: 'json',
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $("#form_satuankonversi").serialize(),
        success: function(respon){
          table.ajax.reload();
          $('#modal-form').modal('hide');
        }
      })
    }
  })


function deleteData(id){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "satuankonversi_hapus",
      type : "POST",
      data: {id : id},
      headers : {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
      success : function(data){
        table.ajax.reload();
      },
      error : function(){
        alert("Tidak dapat menghapus data!");
      }
    });
  }
}

function get_jumbagi(){
  var jum_awal = parseFloat($("[name=popup_jumlah_awal]").val());
  var jum_akhir = parseFloat($("[name=popup_jumlah_akhir]").val());
  var jum = parseFloat(jum_awal/jum_akhir);
  $("[name=popup_jumlah_bagi]").val(jum);
}
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




@endsection

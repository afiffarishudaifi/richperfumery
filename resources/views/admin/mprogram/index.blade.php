<?php $hal = "menu"; ?>
@extends('layouts.admin.master')
@section('title', 'Master program')

@section('css')
<!-- DataTables -->


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
    Master Program
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Master Program</h3>
        </div>
        <a  style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:10%">No #</th>
                <th style="width:16%">Kode</th>
                <th style="width:16%">Nama</th>
                <th style="width:10%">Action</th>
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
@include('admin.mprogram.form1')
@endsection


@section('js')


<script type="text/javascript">
$(function () {
  table = $('#datatable1').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ route('data_program') }}",
      "type" : "GET"
    }
  });
$('.btn_tambah').on('click',function(e){
  // alert('as');
  $('.form_connectio1n')[0].reset();
  $('.d').html('<h4 class="modal-title">Tambah Program</h4>');
  $('#crud').val('tambah');
  $('#modal-form').modal('show');
});
$(document).on("click","#btn_edit",function() {
  $('.d').html('<h4 class="modal-title">Edit Program</h4>');
  $('#crud').val('edit');
  var id = $(this).data('id');
  var kode = $(this).data('kode');
  var nama = $(this).data('nama');
  $('#id').val(id);
  $('#kode').val(kode);
  $('#nama').val(nama);

  $('#modal-form').modal('show');
});
$(document).on("click","#btn_hapus",function() {
  var id = $(this).data('id');
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "mprogram/"+id,
      type : "POST",
      data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
      success : function(data){
        table.ajax.reload();
         location.reload();
      },
      error : function(){
        alert("Tidak dapat menghapus data!");
      }
    });
  }
});

$('#modal-form form').validator().on('submit', function(e){
  if(!e.isDefaultPrevented()){
    var id = $('#id').val();

    $.ajax({
      url : "{{ route('mprogram.store') }}",
      type : "POST",
      data : $('#modal-form form').serialize(),
      success : function(data){
        $('#modal-form').modal('hide');
        // table.ajax.reload();
        location.reload();
      },
      error : function(){
        alert("Tidak dapat menyimpan data!");
      }
    });
    return false;
  }
});

});

</script>


@endsection

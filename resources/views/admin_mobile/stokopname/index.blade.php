<?php $hal = "stok opname"; ?>
@extends('layouts.admin.master')
@section('title', 'Stok Opname')

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
  </style>

@endsection


@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Stok Opname
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Stok Opname</h3>
        </div>
        <!-- <a onclick="addForm()"  style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a> -->
        <!-- /.box-header -->
        <div class="box-body">
          <a href="{{ url('stokopname_tambah')}}" class="btn btn-success" style="margin-bottom: 1%;"><span class="glyphicon glyphicon-plus"></span> Tambah</a>
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:3%">No</th>
                <th style="width:25%">Tanggal</th>
                <th style="width:31%;">Gudang</th>
                <th style="width:31%;">Keterangan</th>
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
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script type="text/javascript">
var table;
$(function(){
  table = $('.table').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{url('stokopname_data') }}",
      "type" : "GET",
    }
    })
  });
function deleteData(id){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "stokopname_hapus",
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
</script>
@endsection

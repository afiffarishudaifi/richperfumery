<?php $hal = "penjualangrosir"; ?>
@extends('layouts.admin.master')
@section('title', 'Penjualan Grosir')

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
    Penjualan Grosir
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Penjualan Grosir</h3>
        </div>
        
        <a href="{{url('penjualangrosir_tambah')}}" style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:3%">No #</th>
                <th>tanggal</th>
                <th>No.Faktur</th>
                <th>Penyedia</th>
                <th>Status</th>
                <th>Total</th>
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
/*var table;
  $(document).ready(function(){
    table = $(".dataTables").DataTable({
      processing: true,
          serverSide: true,
          ajax: '{{ url('pembelian_data') }}',
          columns: [
              {data: 'rownum', name: 'rownum', orderable: false, searchable: false, render : function(data, type, row, meta){
            return meta.row+1;
          }, className: 'text-center'},
              {data: 'tanggal', name: 'tanggal'},
              {data: 'no_faktur', name: 'no_faktur'},
              {data: 'nama_supplier', name: 'nama_supplier'},
              {data: 'tagihan', name: 'tagihan'},
              {data: 'aksi', name: 'aksi', orderable: false, searchable: false},
          ],
          "order": [[ 3, "asc" ]]
    });
    

  });*/
var table, save_method;
$(function(){
  table = $('.table').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ route('penjualangrosir_data') }}",
      "type" : "GET"
    }
  })
});
  
function deleteData(id){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "penjualangrosir_hapus",
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

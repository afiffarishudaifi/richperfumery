<?php $hal = "persediaan"; ?>
@extends('layouts.admin.master')
@section('title', 'Persediaan')

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
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Persediaan Barang</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table id="datatable1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width:10%"></th>
                  <th style="width:10%">No</th>
                  <!-- <th>Kode Barang</th> -->
                  <th>Nama Barang</th>
                  <!-- <th>Gudang</th> -->
                  <th>Jumlah</th>
                  <!-- <th>Satuan</th> -->
                  <!-- <th>Konversi (ml)</th> -->
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
{{-- @include('admin.pembelian.form') --}}
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
      "ordering": false,//tambahan
      "pageLength": 10,//tambahan
      "lengthChange": false,//tambahan
      "searching": true,//tambahan
      "ajax" : {
        "url" : "{{ url('persediaan_data') }}",
        "type" : "GET"
      },
      "columns": [
      {
        "className":      'details-control',
        "orderable":      false,
        "data":           null,
        "defaultContent": ''
      },
      { "data": "no" },
      { "data": "nama_barang" },
      { "data": "stok" },
      // { "data": "alamat" },
      // { "data": "gudang" },
      // { "data": "status" },
      // { "data": "aksi" }
      ],
    });

    /* Formatting function for row details - modify as you need */
    function format ( d ) {
      // `d` is the original data object for the row
      return '<table>'+
      '<tr>'+
      '<td>Kode Barang:</td>'+
      '<td>'+d.kode_barang+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Nama Barang:</td>'+
      '<td>'+d.nama_barang+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Jumlah:</td>'+
      '<td>'+d.stok+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Nama Satuan:</td>'+
      '<td>'+d.nama_satuan+'</td>'+
      '</tr>'+
      '</table>';
    }
    // Add event listener for opening and closing details
    $('.table tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = table.row( tr );

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

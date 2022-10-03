<?php $hal = "persetujuanopnamebaru"; ?>
@extends('layouts.admin.master')
@section('title', 'Persetujuan Stok Opname')

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

.modal { overflow: auto !important; }
</style>

@endsection


@section('content')
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Persetujuan Stok Opname</h3>
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
        <!-- <a href="javascript:;" class="btn btn-sm btn-success btn_tambah" id="btn_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah Barang</a> -->
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table class="table table-bordered table-hover table-striped table-barang" id="table_stokopnametanggal">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Gudang</th>
                <th>Fisik</th>
                <th>Aksi</th>
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

@include('admin_mobile.stokopname_baru.detail_persetujuan')
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
var table, table_detail, save_method;
var gudang = $("[name=popup_gudang]").val();
$(function(){
  table = $("#table_stokopnametanggal").DataTable({
    processing: true,
    serverSide: true,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "searching": true,//tambahan
    ajax: '{{ url('persetujuanopnamebaru_dataTanggal') }}',
    columns: [
      {data: 'nomor', name: 'nomor'},
      {data: 'tanggal', name: 'tanggal'},
      {data: 'nama_gudang', name: 'nama_gudang'},
      {data: 'fisik', name: 'fisik'},
      {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
    ],
  });

  $('input[name=search_tanggal]').daterangepicker({
   locale: {
      format: 'DD-MM-YYYY',
      separator: "  s.d. ",
    }
  });
});


function detail(tanggal,gudang){
  $('#table_stokopnamedetail').DataTable().clear();
  $('#table_stokopnamedetail').DataTable().destroy();
  table_detail = $("#table_stokopnamedetail").DataTable({
    processing: true,
    serverSide: true,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "searching": true,//tambahan
    // "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    // "iDisplayLength": 10,
    ajax: {
      "url" : '{{ url('persetujuanopnamebaru_data') }}',
      "data": {tanggal:tanggal,gudang:gudang}
    },
    columns: [
      {
        "className":      'details-control',
        "orderable":      false,
        "data":           null,
        "defaultContent": ''
      },
      {data: 'nomor', name: 'nomor', orderable: false, searchable: false},
      {data: 'tanggal', name: 'tanggal'},
      // {data: 'nama_barang', name: 'nama_barang'},
      // {data: 'nama_gudang', name: 'nama_gudang'},
      // {data: 'fisik', name: 'fisik'},
      // {data: 'selisih', name: 'selisih'},
      // {data: 'keterangan', name: 'keterangan'},
      {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
    ],
  });

  /* Formatting function for row details - modify as you need */
  function format ( d ) {
    // `d` is the original data object for the row
    return '<table id="show_tabel">'+
    '<tr>'+
    '<td>Tanggal</td>'+
    '<td> : '+d.tanggal+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Barang</td>'+
    '<td> : '+d.nama_barang+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Gudang</td>'+
    '<td> : '+d.nama_gudang+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Fisik</td>'+
    '<td> : '+d.fisik+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Keterangan</td>'+
    '<td> : '+d.keterangan+'</td>'+
    '</tr>'+
    '</table>';
  }
  // Add event listener for opening and closing details
  $('#table_stokopnamedetail tbody').on('click', 'td.details-control', function () {
    var tabelnya = $('#table_stokopnamedetail').DataTable();
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

  $('#modal-form-detail').modal('show');
}

$("#checkAll").click(function () {
  $('input:checkbox').not(this).prop('checked', this.checked);
});

$("#btn_verifikasi").click(function(){
  var rows_selected = [];
  var jenis_selected = [];

  var rowcollection =  table_detail.$("#check_verifikasi:checked", {"page": "All"});
  rowcollection.each(function(index,elem){
    var checkbox_value = $(elem).val();
    rows_selected.push(checkbox_value);
  });
  if(rows_selected.length > 0){
    if(confirm("Anda yakin akan memverifikasi data ini?")){
      kirim_data(rows_selected);
    }
  }else{
    alert("Tidak Ada Data Terpilih!");
  }
});

function kirim_data(val, gudang){
  $.ajax({
    url: '{{ url('persetujuanopnamebaru_simpanmulti') }}',
    type: 'get',
    dataType: 'json',
    data: {id : val},
    headers : {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(respon){
      table_detail.ajax.reload();
    },
    error : function(){
      alert("Tidak dapat menyimpan data!");
    }
  })
}

$(".btn-search-tanggal").on("click",function(){
    var tanggal = $("[name=search_tanggal]").val();
    var url = '<?=$hal?>_searchtanggal';
    $('#table_stokopnametanggal').DataTable().clear();
    $('#table_stokopnametanggal').DataTable().destroy();
    table = $('#table_stokopnametanggal').DataTable({
    processing: true,
    serverSide: true,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "searching": true,//tambahan
    "ajax" : {
      "url" : "{{url('persetujuanopnamebaru_searchtanggal')}}",
      "data" : {tanggal:tanggal}
    },
    columns: [
        {data: 'nomor', name: 'nomor'},
        {data: 'tanggal', name: 'tanggal'},
        {data: 'nama_gudang', name: 'nama_gudang'},
        {data: 'fisik', name: 'fisik'},
        {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
      ],
  });
});

$(".btn-reset-tanggal").on("click",function(){
    $('#table_stokopnametanggal').DataTable().clear();
    $('#table_stokopnametanggal').DataTable().destroy();
    table = $("#table_stokopnametanggal").DataTable({
      processing: true,
      serverSide: true,
      "ordering": false,//tambahan
      "pageLength": 10,//tambahan
      "lengthChange": false,//tambahan
      "searching": true,//tambahan
      ajax: '{{ url('persetujuanopnamebaru_dataTanggal') }}',
      columns: [
        {data: 'nomor', name: 'nomor'},
        {data: 'tanggal', name: 'tanggal'},
        {data: 'nama_gudang', name: 'nama_gudang'},
        {data: 'fisik', name: 'fisik'},
        {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
      ],
  });
});

</script>





@endsection

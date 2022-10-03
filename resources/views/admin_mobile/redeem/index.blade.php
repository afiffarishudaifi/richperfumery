<?php $hal = "redeem"; ?>
@extends('layouts.admin.master')
@section('title', 'Tukar Poin')

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
          <h3 class="box-title">Data Tukar Poin</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-toggle="collapse" title="Search" data-target="#form-search"> <i class="fa fa-search"></i> Search</button>
          </div>
          <!-- <a href="{{url('kasir_tambah')}}" style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a> -->
          <?php echo $tombol_create;?>
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
                <th style="width:10%"></th>
                <th style="width:1%">#</th>
                <th style="width:30%">Tanggal</th>
                <!-- <th style="width:30%">No.Faktur</th> -->
                <th style="width:30%">Pelanggan</th>
                <!-- <th style="width:30%">Gudang</th> -->
                <!-- <th style="width:30%">Total Penjualan</th> -->
                <th style="width:30%">Action</th>
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
@include('admin.redeem.form_infocetak')
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

  var table, save_method;
  $(function(){
    table = $('.table').DataTable({
      "processing" : true,
      "ordering": false,//tambahan
      "pageLength": 10,//tambahan
      "lengthChange": false,//tambahan
      "searching": true,//tambahan
      "ajax" : {
        "url" : "{{ url('redeem_data') }}",
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
      { "data": "tanggal" },
      { "data": "nama_pelanggan" },
      // { "data": "jumlah" },
      // { "data": "gudang" },
      // { "data": "status" },
      { "data": "posting" }
      ],
    });

    $('input[name=search_tanggal]').daterangepicker({
     locale: {
        format: 'DD-MM-YYYY',
        separator: "  s.d. ",
      }
    });

    /* Formatting function for row details - modify as you need */
    function format ( d ) {
      // `d` is the original data object for the row

      return '<table>'+
      '<tr>'+
      '<td>Tanggal</td>'+
      '<td> : '+d.tanggal+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>No. Faktur</td>'+
      '<td> : '+d.no_faktur+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Nama Pelanggan</td>'+
      '<td> : '+d.nama_pelanggan+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Gudang</td>'+
      '<td> : '+d.nama_gudang+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Total redeem</td>'+
      '<td> : '+d.total_redeem+'</td>'+
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
  
  function deleteData(id){
    if(confirm("Apakah yakin data akan dihapus?")){
      $.ajax({
        url : "{{url('redeem_hapus')}}",
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

$(".btn-search-tanggal").on("click",function(){
    var tanggal = $("[name=search_tanggal]").val();
    var url = '<?=$hal?>_searchtanggal';
    $('.table').DataTable().clear();
    $('.table').DataTable().destroy();
    table = $('.table').DataTable({
    "processing" : true,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "searching": true,//tambahan
    "ajax" : {
      "url" : "{{url('redeem_searchtanggal')}}",
      "data" : {tanggal:tanggal}
    },
    "columns": [
      {
        "className":      'details-control',
        "orderable":      false,
        "data":           null,
        "defaultContent": ''
      },
      { "data": "no" },
      { "data": "tanggal" },
      { "data": "nama_pelanggan" },
      // { "data": "jumlah" },
      // { "data": "gudang" },
      // { "data": "status" },
      { "data": "posting" }
      ],
  });
});

$(".btn-reset-tanggal").on("click",function(){
    $('.table').DataTable().clear();
    $('.table').DataTable().destroy();
    table = $('.table').DataTable({
      "processing" : true,
      "ordering": false,//tambahan
      "pageLength": 10,//tambahan
      "lengthChange": false,//tambahan
      "searching": true,//tambahan
      "ajax" : {
        "url" : "{{ url('redeem_data') }}",
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
      { "data": "tanggal" },
      { "data": "nama_pelanggan" },
      // { "data": "jumlah" },
      // { "data": "gudang" },
      // { "data": "status" },
      { "data": "posting" }
      ],
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
    if(keterangan != ''){
      $.ajax({
        url: "{{ url('redeem_simpan_infocetak') }}",
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

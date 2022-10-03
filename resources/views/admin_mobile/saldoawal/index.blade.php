<?php $hal = "saldoawal"; ?>
@extends('layouts.admin.master')
@section('title', 'Saldo Awal')

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
          <h3 class="box-title">Data Saldo Awal</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-toggle="collapse" title="Search" data-target="#form-search"> <i class="fa fa-search"></i> Search</button>
          </div>
          <!-- <a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a> -->
          <?php echo $data['tombol_create'];?>
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
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:10%"></th>
                <th style="width:1%">No</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:20%">Barang</th>
                <!-- <th style="width:20%">Gudang</th> -->
                <!-- <th style="width:20%">Jumlah</th> -->
                <!-- <th style="width:20%">Ket</th> -->
                <th style="width:17%">Action</th>
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
@include('admin.saldoawal.form')
@include('admin.saldoawal.detail')
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
        "url" : "{{ route('saldoawal_data') }}",
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
      { "data": "barang_nama" },
      // { "data": "jumlah" },
      // { "data": "gudang" },
      // { "data": "status" },
      { "data": "aksi" }
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
      '<td>Nama Barang</td>'+
      '<td> : '+d.barang_nama+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Nama Gudang</td>'+
      '<td> : '+d.gudang_nama+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Jumlah</td>'+
      '<td> : '+d.jumlah+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Keterangan</td>'+
      '<td> : '+d.keterangan+'</td>'+
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

    $('[name=popup_barang]').select2({
      placeholder: "--- Pilih ---",
      ajax: {
        url: '{{url('saldoawal_get_barang')}}',
        dataType: 'json',
        data: function (params) {
          return {
            q: $.trim(params.term)
              //results: data
            };
          },
          processResults: function (data) {
            var results = [];
            $.each(data, function(index,item){
              var text_item = item.barang_nama+" || ("+item.barang_alias+")";
              if(item.barang_alias === null || item.barang_alias === "" || item.barang_alias === 0){
                text_item = item.barang_nama;
              }
              results.push({
                id:item.barang_id,
                satuan_id:item.satuan_id,
                satuan_nama:item.satuan_nama,
                nama:item.barang_nama,
                harga:item.harga,
                kode:item.barang_kode,
                alias:item.barang_alias,
                text:text_item
              });
            });
            return{
              results:results
            };
          },
          cache: true
        }
      });

    $('[name=popup_gudang1]').select2({
      placeholder: "Pilih...",
            //minimumInputLength: 2,
            ajax: {
              url: '{{url('saldoawal_get_gudang')}}',
              dataType: 'json',
              data: function (params) {
                return {
                  q: $.trim(params.term)
                };
              },
              processResults: function (data) {
                var results = [];
                $.each(data, function(index,item){
                  results.push({
                    id:item.id,
                    text:item.nama,
                  });
                });
                return{
                  results:results
                };
              },
              cache: true
            }
          });
    $("[name=popup_barang]").on('change',function(e){
      var d = $("[name=popup_barang]").select2('data')[0];
      var satuan = d.satuan_nama;
      $('[name=popup_satuan]').val(d.satuan_id).trigger('change');
    // console.log(d);
  });

  });



$("#btn_tambah").click(function(){
  $('[name=popup_id_table]').val('');
  $('[name=popup_barang]').html('<option></option>');
    // $('[name=popup_gudang]').html('<option></option>');
    $('[name=popup_harga]').val('');
    $('[name=popup_jumlah]').val('');
    $('[name=popup_satuan]').val('').trigger('change');
    $('[name=popup_tanggal]').val(null).trigger('change');
    $('[name=popup_keterangan]').val('');
    $('#modal-form').modal('show');
  });

function edit(id){
  $("[name=popup_id_table]").val($("#table_id"+id).val());
    /*$("[name=popup_barang]").val($("#table_idbarang"+id).val()).trigger('change');
    $("[name=popup_gudang]").val($("#table_idgudang"+id).val()).trigger('change');*/
    var id_barang = $("#table_idbarang"+id).val();
    var nama_barang = $("#table_namabarang"+id).val();
    var id_gudang = $("#table_idgudang"+id).val();
    var nama_gudang = $("#table_namagudang"+id).val();
    $("[name=popup_barang]").html('<option value="'+id_barang+'">'+nama_barang+'</option>');
    $("[name=popup_gudang]").val(id_gudang).trigger('change');
    $("[name=popup_tanggal]").val($("#table_tanggal"+id).val()).trigger('change');
    $("[name=popup_keterangan]").val($("#table_keterangan"+id).val()).trigger('change');
    $("[name=popup_jumlah]").val($("#table_jumlah"+id).val());
    $("[name=popup_satuan]").val($("#table_idsatuan"+id).val()).trigger('change');
    $("[name=popup_idlog_stok]").val($("#table_idlog_stok"+id).val());

    $('#modal-form').modal('show');
  }

function detail(id){
    $("[name=popup_detail_id_table]").val($("#table_id"+id).val());
    /*$("[name=popup_detail_barang]").val($("#table_idbarang"+id).val()).trigger('change');
    $("[name=popup_detail_gudang]").val($("#table_idgudang"+id).val()).trigger('change');*/
    var id_barang = $("#table_idbarang"+id).val();
    var nama_barang = $("#table_namabarang"+id).val();
    var id_gudang = $("#table_idgudang"+id).val();
    var nama_gudang = $("#table_namagudang"+id).val();
    $("[name=popup_detail_barang]").val(nama_barang);
    $("[name=popup_detail_gudang]").val(nama_gudang);
    $("[name=popup_detail_tanggal]").val($("#table_tanggal"+id).val());
    $("[name=popup_detail_tanggal2]").val($("#table_tanggal"+id).val());
    $("[name=popup_detail_keterangan]").val($("#table_keterangan"+id).val()).trigger('change');
    $("[name=popup_detail_jumlah]").val($("#table_jumlah"+id).val());
    $("[name=popup_detail_satuan]").val($("#table_namasatuan"+id).val());
    $("[name=popup_detail_idlog_stok]").val($("#table_idlog_stok"+id).val());

    $("#help_popup_detail_jumlah").text(format_angka($("#table_jumlah"+id).val()));

    $('#modal-form_detail').modal('show');
  }


  $("#btn_popup_simpan").click(function(){
    var id = $("[name=popup_id_table]").val();
    var id_barang = $("[name=popup_barang]").val();
    var jumlah = $("[name=popup_jumlah]").val();
    var satuan = $("[name=popup_satuan]").val();
    if(id_barang != '' && jumlah != '' && satuan != ''){
      $.ajax({
        url: "{{ url('saldoawal_simpan') }}",
        type: 'post',
        dataType: 'json',
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $("#form_saldoawal").serialize(),
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
        url : "saldoawal_hapus",
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
      "url" : "{{url('saldoawal_searchtanggal')}}",
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
      { "data": "barang_nama" },
      { "data": "aksi" }
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
        "url" : "{{ url('saldoawal_data') }}",
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
      { "data": "barang_nama" },
      { "data": "aksi" }
      ],
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




@endsection

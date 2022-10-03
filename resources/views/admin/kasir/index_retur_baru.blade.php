<?php $hal = "kasirreturbaru"; ?>
@extends('layouts.admin.master')
@section('title', 'Retur Penjualan')

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
    Retur Penjualan
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Retur Penjualan</h3>
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
                <div class="col-md-5">
                  <a href="javascript:;" style="margin-bottom:20px;" class="card-body-title btn-search-tanggal"><button class="btn btn-success" type="button"><i class="fa fa-search"></i> Search</button></a>
                  <a href="javascript:;" style="margin-bottom:20px;" class="card-body-title btn-reset-tanggal"><button class="btn btn-warning" type="button"><i class="fa fa-undo"></i> Reset</button></a>
                </div>
              </div>
            </div>
          </form>
      </div>
        <!-- <a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a> -->
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:3%">No #</th>
                <th style="width:15%">Tanggal</th>
                <th style="width:13%">Kode Retur</th>
                <th style="width:13%">No Faktur</th>
                <th style="width:15%">Pelanggan</th>
                <th style="width:15%">Gudang</th>
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
@include('admin.kasir.form_retur')
@include('admin.kasir.detail_retur')
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
$(function(){
  table = $("#datatable1").DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ url('kasirreturbaru_data') }}',
          columns: [
              {data: 'nomor', name: 'nomor'},
              {data: 'tanggal', name: 'tanggal'},
              {data: 'kode_retur', name: 'kode_retur'},
              {data: 'no_faktur', name: 'no_faktur'},
              {data: 'nama_pelanggan', name: 'nama_pelanggan'},
              {data: 'nama_gudang', name: 'nama_gudang'},
              {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
          ],
          "columnDefs": [
            { targets: 0, orderable: false, searchable: false, }
          ],
          createdRow: function(row, data, index){
            $('td', row).eq(6).attr('align','left'); // 6 is index of column
          },
    });

  $('input[name=search_tanggal]').daterangepicker({
   locale: {
      format: 'DD-MM-YYYY',
      separator: "  s.d. ",
    }
  });

  $('[name=popup_barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('kasirreturbaru_get_barang')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        var text_item = item.barang_kode+" || "+item.barang_nama+" || ("+item.barang_alias+")";
                        if(item.barang_alias === null || item.barang_alias === "" || item.barang_alias === 0){
                          text_item = item.barang_kode+" || "+item.barang_nama;
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
  

});

$("[name=popup_barang]").on('change',function(e){
    var d = $("[name=popup_barang]").select2('data')[0];
    var id_table =  $("[name=popup_id_table]").val();
    if(id_table==""){
    $("[name=popup_satuan]").val(d.satuan_id).trigger('change');
    }
});

$('[name=popup_jumlah]').on('keyup',function(){
  var jum = $(this).val();
  $('#help_popup_jumlah').text(format_angka(jum));
});

$("#btn_tambah").click(function(){
    $('[name=popup_id_table]').val('');
    $('[name=popup_kode]').val('');
    $('[name=popup_tanggal]').val(null).trigger('change');
    $('[name=popup_barang]').html('<option></option>');
    //$('[name=popup_pelanggan]').html('<option></option>');
    $('[name=popup_pelanggan]').val(null).trigger('change');
    $('[name=popup_jumlah]').val('');
    $('[name=popup_satuan]').val('').trigger('change');
    $('[name=popup_ket]').val('');
    $('#help_popup_jumlah').text(format_angka(parseInt(0)));
    $('#modal-form').modal('show');
});

function edit(id){
    var id_barang = $("#table_idbarang"+id).val();
    var nama_barang = $("#table_namabarang"+id).val();
    var id_gudang = $("#table_idgudang"+id).val();
    var id_pelanggan = $("#table_idpelanggan"+id).val();
    var id_satuan = $("#table_idsatuan"+id).val();

    $("[name=popup_id_table]").val($("#table_id"+id).val());
    var barangOption = new Option(nama_barang, id_barang, true, true);
    $('[name=popup_barang]').append(barangOption).trigger('change');
    $('[name=popup_pelanggan]').val(id_pelanggan).trigger('change');
    $("[name=popup_gudang]").val(id_gudang).trigger('change');
    $("[name=popup_kode]").val($("#table_kode"+id).val());
    $("[name=popup_tanggal]").val($("#table_tanggal"+id).val()).trigger('change');
    $("[name=popup_ket]").text($("#table_keterangan"+id).val());
    $("[name=popup_jumlah]").val($("#table_jumlah"+id).val());
    $("[name=popup_satuan]").val(id_satuan).trigger('change');
    $("[name=popup_idlog_stok]").val($("#table_idlog_stok"+id).val());
    $("#help_popup_jumlah").text(format_angka($("#table_jumlah"+id).val()));

    $('#modal-form').modal('show');
}

function deleteData(id){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "kasirreturbaru_hapus",
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
    $('#datatable1').DataTable().clear();
    $('#datatable1').DataTable().destroy();
    table_detail = $("#datatable1").DataTable({
          processing: true,
          serverSide: true,
          ajax:{ 
            "url":'{{ url('kasirreturbaru_searchtanggal') }}',
            "type":"GET",
            data: function(d){
              d.tanggal=tanggal;
            }
          },          
          columns: [
              {data: 'nomor', name: 'nomor'},
              {data: 'tanggal', name: 'tanggal'},
              {data: 'kode_retur', name: 'kode_retur'},
              {data: 'no_faktur', name: 'no_faktur'},
              {data: 'nama_pelanggan', name: 'nama_pelanggan'},
              {data: 'nama_gudang', name: 'nama_gudang'},
              {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
          ],
          "columnDefs": [
            { targets: 0, orderable: false, searchable: false, }
          ],
          createdRow: function(row, data, index){
            $('td', row).eq(6).attr('align','left'); // 6 is index of column
          },
    });

});

$(".btn-reset-tanggal").on("click",function(){
    $('#datatable1').DataTable().clear();
    $('#datatable1').DataTable().destroy();
    table = $("#datatable1").DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ url('kasirreturbaru_data') }}',
          columns: [
              {data: 'nomor', name: 'nomor'},
              {data: 'tanggal', name: 'tanggal'},
              {data: 'kode_retur', name: 'kode_retur'},
              {data: 'no_faktur', name: 'no_faktur'},
              {data: 'nama_pelanggan', name: 'nama_pelanggan'},
              {data: 'nama_gudang', name: 'nama_gudang'},
              {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
          ],
          "columnDefs": [
            { targets: 0, orderable: false, searchable: false, }
          ],
          createdRow: function(row, data, index){
            $('td', row).eq(6).attr('align','left'); // 6 is index of column
          },
    });
});
</script>

@endsection

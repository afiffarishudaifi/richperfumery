<?php $hal = "pengiriman_penerimaan"; ?>
@extends('layouts.admin.master')
@section('title', 'PENERIMAAN RETUR PENGIRIMAN')

@section('css')
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

/* table, th {
  border: 0.1px solid black !important;
} */

/* td {
  border: 0.1px solid black !important;
} */
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">PENERIMAAN RETUR PENGIRIMAN</h3>
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
        {{-- <a  style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a> --}}
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped" style="width: 100%;">
            <thead>
              <tr>
                <th style="width:5%">No #</th>
                <th style="width:10%">Kode retur</th>
                <th style="width:16%">Nama Barang</th>
                <th style="width:10%">Outlet</th>
                <th style="width:16%">Gudang</th>
                <th style="width:16%">Status</th>
                <th style="width:10%">Tanggal Pengiriman</th>
                <th style="width:10%">Tanggal Penerimaan</th>
                <th style="width:10%">Jumlah</th>
                <th style="width:10%">Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
    </div>
  </section>
@endsection
@include('admin.pengiriman.penerimaanretur_create')
@include('admin.pengiriman.penerimaanretur_detail')
@section('js')
<link rel="stylesheet" type="text/css" href="{{asset('public/js/daterangepicker/daterangepicker.css')}}" />
<script type="text/javascript" src="{{asset('public/js/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
  var table, table_search, save_method;
  $(function () {
    $('.tanggal').datepicker({
      format:'yyyy-mm-dd',
      autoclose:true
    });
    $('.js-example-basic-single').select2({
      dropdownParent: $(".modal")
    });

    $('input[name=search_tanggal]').daterangepicker({
     locale: {
        format: 'DD-MM-YYYY',
        separator: "  s.d. ",
      }
    });

    table = $('#datatable1').DataTable({
      "processing" : true,
      "ajax" : {
        "url" : "{{ route('pengirimanrpenerimaanliat') }}",
        "type" : "GET"
      }
    });

  $(document).on("click","#btn_acc",function() {
    var id = $(this).data('id');
    var id_barang = $(this).data('id_barang');
    var id_gudang_pusat = $(this).data('id_gudang_pusat');
    var id_gudang_outlet = $(this).data('id_gudang_outlet');
    var id_satuan = $(this).data('id_satuan');
    var jumlah = $(this).data('jumlah');
    var tanggal = $(this).data('tanggal');
    if(confirm("Apakah yakin untuk menerima retur?")){
      $.ajax({
        url : "pengirimanrpenerimaansetuju",
        type : "POST",
        data : {'id_gudang_pusat':id_gudang_pusat,'id_satuan':id_satuan,'id_gudang_outlet':id_gudang_outlet,'jumlah':jumlah,'id_barang':id_barang,'id':id, '_token' : $('meta[name=csrf-token]').attr('content')},
        success : function(data){
          // table.ajax.reload();
           location.reload();
        },
        error : function(){
          alert("Tidak dapat menerima pengiriman!");
        }
      });
    }
  });

  $(document).on("click","#btn_tambah",function() {
    var id = $(this).data('id');
    var id_barang = $(this).data('id_barang');
    var nama_barang = $(this).data('nama_barang');
    var kode_barang = $(this).data('kode_barang');
    var alias_barang= $(this).data('alias_barang');
    var id_gudangpusat   = $(this).data('id_gudang_pusat');
    var id_gudangoutlet  = $(this).data('id_gudang_outlet');
    var nama_gudangpusat = $(this).data('nama_gudangpusat');
    var nama_gudangoutlet= $(this).data('nama_gudangoutlet');
    var id_satuan   = $(this).data('id_satuan');
    var nama_satuan = $(this).data('nama_satuan');
    var jumlah      = $(this).data('jumlah');
    var jumlah_terima = $(this).data('jumlah_terima');
    var tanggal       = $(this).data('tanggal');
    var tanggal_terima = $(this).data('tanggal_terima');
    var kode        = $(this).data('kode');
    var keterangan  = $(this).data('keterangan');
    var idlog_stok  = $(this).data('id_log_stok');
    var idlog_stok_penerimaan = $(this).data('id_log_stok_penerimaan');
    var status = $(this).data('status');

    var barang = kode_barang+" || "+nama_barang;


    $("[name=popup_idbarang]").val(id_barang);
    $("[name=popup_idgudang_outlet]").val(id_gudangoutlet);
    $("[name=popup_idgudang_pusat]").val(id_gudangpusat);
    $("[name=popup_idlog_stok]").val(idlog_stok);
    $("[name=popup_idlog_stok_penerimaan]").val(idlog_stok_penerimaan);
    $("[name=popup_status_hidden]").val(status);

    $("[name=popup_idretur]").val(id);
    $("[name=popup_kode]").val(kode);
    $("[name=popup_tanggal]").val(tanggal_terima).trigger('change');
    $("[name=popup_barang]").val(barang);
    $("[name=popup_gudang_outlet]").val(nama_gudangoutlet);
    $("[name=popup_gudang_pusat]").val(nama_gudangpusat);
    $("[name=popup_jumlah]").val(jumlah);
    if(status == '1'){
    $("[name=popup_jumlahterima]").val(jumlah);
    }else{
    $("[name=popup_jumlahterima]").val(jumlah_terima);
    }
    $("[name=popup_idsatuan]").val(id_satuan);
    $("[name=popup_keterangan]").val(keterangan);
    $("[name=popup_status]").val(status).trigger('change');


    $("#help_block_jumlah").text(format_angka(jumlah)+" "+nama_satuan);
    $("#help_block_jumlahterima").text(format_angka(jumlah)+" "+nama_satuan);
    
    $('#modal-penerimaanretur').modal("show");
  });

  $(document).on("click","#btn_detail",function() {
    var id = $(this).data('id');
    var id_barang = $(this).data('id_barang');
    var nama_barang = $(this).data('nama_barang');
    var kode_barang = $(this).data('kode_barang');
    var alias_barang= $(this).data('alias_barang');
    var id_gudangpusat   = $(this).data('id_gudang_pusat');
    var id_gudangoutlet  = $(this).data('id_gudang_outlet');
    var nama_gudangpusat = $(this).data('nama_gudangpusat');
    var nama_gudangoutlet= $(this).data('nama_gudangoutlet');
    var id_satuan   = $(this).data('id_satuan');
    var nama_satuan = $(this).data('nama_satuan');
    var jumlah      = $(this).data('jumlah');
    var jumlah_terima = $(this).data('jumlah_terima');
    var tanggal       = $(this).data('tanggal');
    var tanggal_terima = $(this).data('tanggal_terima');
    var kode        = $(this).data('kode');
    var keterangan  = $(this).data('keterangan');
    var idlog_stok  = $(this).data('id_log_stok');
    var idlog_stok_penerimaan = $(this).data('id_log_stok_penerimaan');
    var status = $(this).data('status');

    var barang = kode_barang+" || "+nama_barang;

    $("[name=popup_detail_id_table]").val(id);
    $("[name=popup_detail_idbarang]").val(id_barang);
    $("[name=popup_detail_idgudang_outlet]").val(id_gudangoutlet);
    $("[name=popup_detail_idgudang_pusat]").val(id_gudangpusat);
    $("[name=popup_detail_idlog_stok]").val(idlog_stok);
    $("[name=popup_detail_idlog_stok_penerimaan]").val(idlog_stok_penerimaan);
    $("[name=popup_detail_status_hidden]").val(status);

    $("[name=popup_detail_idretur]").val(id);
    $("[name=popup_detail_kode]").val(kode);
    $("[name=popup_detail_tanggal]").val(tanggal_terima).trigger('change');
    $("[name=popup_detail_barang]").val(barang);
    $("[name=popup_detail_gudang_outlet]").val(nama_gudangoutlet);
    $("[name=popup_detail_gudang_pusat]").val(nama_gudangpusat);
    $("[name=popup_detail_jumlah]").val(jumlah);
    if(status == '1'){
    $("[name=popup_detail_jumlahterima]").val(jumlah);
    }else{
    $("[name=popup_detail_jumlahterima]").val(jumlah_terima);
    }
    $("[name=popup_detail_idsatuan]").val(id_satuan);
    $("[name=popup_detail_keterangan]").val(keterangan);
    $("[name=popup_detail_status]").val(status).trigger('change');


    $("#help_block_detail_jumlah").text(format_angka(jumlah)+" "+nama_satuan);
    $("#help_block_detail_jumlahterima").text(format_angka(jumlah)+" "+nama_satuan);
    
    $('#modal-penerimaanretur_detail').modal("show");
  });

    

  $(document).on("click","#btn_hapus",function() {
    var id = $(this).data('id');
    if(confirm("Apakah yakin data akan dihapus?")){
      $.ajax({
        url : "pengiriman/"+id,
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



  });

$(".btn-search-tanggal").on("click",function(){
    var tanggal = $("[name=search_tanggal]").val();
    var url = '<?=$hal?>_searchtanggal';
    $('#datatable1').DataTable().clear();
    $('#datatable1').DataTable().destroy();
    table_search = $('#datatable1').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{url('pengirimanpenerimaan_searchtanggal')}}",
      "data" : {tanggal:tanggal}
    }
  });

});

$(".btn-reset-tanggal").on("click",function(){
    $('#datatable1').DataTable().clear();
    $('#datatable1').DataTable().destroy();
    table = $('#datatable1').DataTable({
      "processing" : true,
      "ajax" : {
        "url" : "{{ url('pengirimanrpenerimaanliat') }}",
        "type" : "GET"
      }
    });
});

$(".btn_popup_simpan_baru").on('click',function(){
  var status = $("[name=popup_status]").val();
  var tanggal = $("[name=popup_tanggal]").val();
  var jumlah_terima = $("[name=popup_jumlahterima]").val();
  if(tanggal != "" && jumlah_terima != "" && status != ""){
    $.ajax({
      "url" : "{{url('penerimaanreturpengiriman_simpan')}}",
      "type" :'POST',
      "dataType" : "json",
      "data" : $("#form_penerimaanretur").serialize(),
      headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success: function(respon){
        table.ajax.reload();
        $("#modal-penerimaanretur").modal('hide');
      }
    })
  }
  
})

$(".btn_popup_simpan_detail").on("click",function(){
  var tanggal = $("[name=popup_detail_tanggal]").val();
  var id = $("[name=popup_detail_id_table]").val();
  if(tanggal != '' && id != ''){
    $.ajax({
      "url" : "{{url('penerimaanreturpengiriman_simpandetail')}}",
      "type" :'POST',
      "dataType" : "json",
      "data" : $("#form_penerimaanretur_detail").serialize(),
      headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success: function(respon){
        table.ajax.reload();
        console.log(respon);
        $("#modal-penerimaanretur_detail").modal('hide');
      }
    })
  }
})

  </script>
@endsection

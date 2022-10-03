<?php $hal = "pengirimanreturindex"; ?>
@extends('layouts.admin.master')
@section('title', 'RETUR PENGIRIMAN')

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
          <h3 class="box-title">RETUR PENGIRIMAN</h3>
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
        <!-- <a  style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a> -->
        <?php echo $gudang['tombol_create'];?>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped" width="100%">
            <thead>
              <tr>
                <th style="width:10%">No #</th>
                <th style="width:10%">Kode retur</th>
                <th style="width:16%">Nama Barang</th>
                <th style="width:10%">Outlet</th>
                <th style="width:16%">Gudang</th>
                <th style="width:16%">Status</th>
                <th style="width:10%">Tanggal</th>
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
@include('admin.pengiriman.formretur')
@endsection
@section('js')
<link rel="stylesheet" type="text/css" href="{{asset('public/js/daterangepicker/daterangepicker.css')}}" />
<script type="text/javascript" src="{{asset('public/js/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/daterangepicker/daterangepicker.js')}}"></script>
  <script type="text/javascript">
  var table, table_search;
  var id_gudang_outlet = $("[name=id_gudang_outlet]").val();
  var id_gudang_pusat  = $("[name=id_gudang_pusat]").val();
  $(document).ready(function(){
    get_noauto(id_gudang_outlet); 
    $('#barang').select2({
            placeholder: "Pilih...",
            ajax: {
                url: 'select2pengirimanb',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        gudang:gudang
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        results.push({
                          id:item.id_barang,
                          id_log_stok:item.log_stok_id,
                          satuan:item.alias_satuan,
                          satuan_nama:item.nama_satuan,
                          id_satuan:item.id_satuan,
                          nama:item.id_barang,
                          kode:item.kode_barang,
                          text:item.kode_barang+' || '+item.nama_barang+' || '+item.alias_satuan,
                          jumlah_keluar:item.jumlah_keluar,
                          jumlah_masuk:item.jumlah_masuk,
                          stok:item.stok
                        });
                      });
                      return{
                        results:results
                      };
                        
                },
                cache: true
            }
    
    });
  })
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
      "url" : "{{ route('pengirimanreturliat') }}",
      "type" : "GET"
    }
  });

  $('.btn_tambah').on('click',function(e){
    $('.form_connectio1n')[0].reset();
    $('.d').html('<h4 class="modal-title">Tambah Retur Pengiriman</h4>');
    $('#stok').show();
    // $('[name=id_gudang_outlet]').val(null).trigger("change");
    // $('[name=id_gudang_pusat]').val(null).trigger("change");
    // $('[name=id_gudang_outlet]').val(8).trigger("change");
    $('[name=barang]').html('<option value=""></option>');
    var gudang = $('[name=id_gudang_outlet]').val();
    get_barang(gudang);
    get_noauto(gudang);
    $('#help_popup_jumlah').text('');
    $('#crud').val('tambah');
    $('#modal-retur').modal('show');
  });

  $(document).on("click","#btn_edit",function() {
    $('.d').html('<h4 class="modal-title">Edit Program</h4>');
    $('#crud').val('edit');
    var id = $(this).data('id');
    var id_gudangoutlet   = $(this).data('id_gudang_outlet');
    var nama_gudangoutlet = $(this).data('nama_gudangoutlet');
    var id_gudangpusat    = $(this).data('id_gudang_pusat');
    var nama_gudangpusat  = $(this).data('nama_gudangpusat');
    var alamat      = $(this).data('alamat');
    var id_barang   = $(this).data('id_barang');
    var nama_barang = $(this).data('barang_nama');
    var kode_barang = $(this).data('barang_kode');
    var id_satuan   = $(this).data('id_satuan');
    var nama_satuan = $(this).data('nama_satuan');
    var kode_retur  = $(this).data('kode_retur');
    var tanggal = $(this).data('tanggal_pengiriman');
    var jumlah  = $(this).data('jumlah');
    var keterangan = $(this).data('keterangan');
    var status  = $(this).data('status');
    var id_log_stok = $(this).data('id_log_stok');
    
    $('#stok').hide();

    $('#id_retur').val(id);
    $('#alamat').val(alamat);
    $('#tanggal').val(tanggal);
    $('#jumlah').val(jumlah);
    $('#id_satuan').val(id_satuan);
    $('#barang').html('<option value="'+id_barang+'">'+kode_barang+' || '+nama_barang+'</option>');
    /*$('#id_gudang_pusat').html('<option value="'+id_gudangpusat+'">'+nama_gudangpusat+'</option>');
    $('#id_gudang_outlet').html('<option value="'+id_gudangoutlet+'">'+nama_gudangoutlet+'</option>');*/
    $('#id_gudang_pusat').val(id_gudangpusat).trigger('change');
    $('#id_gudang_outlet').val(id_gudangoutlet).trigger('change');
    $('#keterangan').val(keterangan);
    $('#status').val(status);
    $('#id_log_stok').val(id_log_stok);

    $('#help_popup_jumlah').text(format_angka(jumlah));
    $('#modal-retur').modal('show');
  });

  $('#pengiriman').select2({
            placeholder: "Pilih...",
              minimumInputLength: 2,
              ajax: {
                  url: 'select2pengiriman',
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
                            nama:item.kode_pengiriman ,
                            text:item.kode_pengiriman ,
                            kode_pengiriman:item.kode_pengiriman,
                            gudang_awal:item.gudang_awal,
                            tujuan:item.gudang_tujuan
                          });
                        });
                        return{
                          results:results
                        };                          
                  },
                  cache: true
              }      
    });

  $(document).on("change","#id_gudang_outlet",function() {
      var id = $(this).val();
      var nilai = $("#id_gudang_outlet").select2('data')[0];
      var gudang = nilai.id;    
      get_noauto(gudang);  
      $('#barang').select2({
            placeholder: "Pilih...",
            ajax: {
                url: 'select2pengirimanb',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        gudang:gudang
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        results.push({
                          id:item.id_barang,
                          id_log_stok:item.log_stok_id,
                          satuan:item.alias_satuan,
                          satuan_nama:item.nama_satuan,
                          id_satuan:item.id_satuan,
                          nama:item.id_barang,
                          kode:item.kode_barang,
                          text:item.kode_barang+' || '+item.nama_barang+' || '+item.alias_satuan,
                          jumlah_keluar:item.jumlah_keluar,
                          jumlah_masuk:item.jumlah_masuk,
                          stok:item.stok
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

    
  $('#barang').on('change',function(e){
     // var satuan = $('[name=barang] :selected').attr('satuan');
     var nilai = $("#barang").select2('data')[0];
     var jum_max = nilai.jumlah_masuk - nilai.jumlah_keluar;
     // var keluar = nilai
     // console.log(nilai);
     $('#jumlah').attr('min','0');
     $('#jumlah').attr('max',jum_max);
     $('#total').val(nilai.jumlah_masuk - nilai.jumlah_keluar);
     $('#id_barang').val(nilai.id_barang);
     $('#id_satuan').val(nilai.id_satuan);
  });

  $('#jumlah').on('keyup',function(){
    var jum = $(this).val();
    $("#help_popup_jumlah").text(format_angka(jum));
  });

  $(document).on("click","#btn_hapus",function() {
    var id = $(this).data('id');
    if(confirm("Apakah yakin data akan dihapus?")){
      $.ajax({
        url : "pengirimanreturindex/"+id,
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

  $(document).on("click","#btn_kirim",function() {
    var id = $(this).data('id');
    var id_barang = $(this).data('id_barang');
    var id_gudang_pusat = $(this).data('id_gudang_pusat');
    var id_gudang_outlet = $(this).data('id_gudang_outlet');
    var id_satuan = $(this).data('id_satuan');
    var jumlah = $(this).data('jumlah');
    if(confirm("Apakah yakin mengirim? ini akan menghilangkan tombol hapus")){
      $.ajax({
        url : "simpanreturkirim",
        type : "POST",
        data :  {'id_gudang_outlet':id_gudang_outlet,'id_satuan':id_satuan,'id_gudang_pusat':id_gudang_pusat,'jumlah':jumlah,'id_barang':id_barang,'id':id, '_token' : $('meta[name=csrf-token]').attr('content')},
        success : function(data){
          table.ajax.reload();
           location.reload();
        },
        error : function(){
          alert("Tidak dapat mengirim data!");
        }
      });
    }
  });

$('#modal-form form').validator().on('submit', function(e){
  if(!e.isDefaultPrevented()){
    var id = $('#id').val();

    $.ajax({
      url : "{{ route('pengiriman.store') }}",
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

$(".btn-search-tanggal").on("click",function(){
    var tanggal = $("[name=search_tanggal]").val();
    var url = '<?=$hal?>_searchtanggal';
    $('#datatable1').DataTable().clear();
    $('#datatable1').DataTable().destroy();
    table_search = $('#datatable1').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{url('pengirimanretur_searchtanggal')}}",
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
        "url" : "{{ route('pengirimanreturliat') }}",
        "type" : "GET"
      }
    });
});

function get_noauto(gudang){
  $('[name=kode]').val('');
  $.ajax({
        url : "{{ url('pengirimanretur_noauto') }}",
        type : "POST",
        data : {gudang:gudang},
        dataType : "json",
        headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function(respon){
          $('[name=kode]').val(respon);
        },
        error : function(){
          alert("Nomor auto error!");
        }
      });
}

function get_barang(this_){
  var gudang = this_;
  $('#barang').select2({
            placeholder: "Pilih...",
            ajax: {
                url: 'select2pengirimanb',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        gudang:gudang
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        results.push({
                          id:item.id_barang,
                          id_log_stok:item.log_stok_id,
                          satuan:item.alias_satuan,
                          satuan_nama:item.nama_satuan,
                          id_satuan:item.id_satuan,
                          nama:item.id_barang,
                          kode:item.kode_barang,
                          text:item.kode_barang+' || '+item.nama_barang+' || '+item.alias_satuan,
                          jumlah_keluar:item.jumlah_keluar,
                          jumlah_masuk:item.jumlah_masuk,
                          stok:item.stok
                        });
                      });
                      return{
                        results:results
                      };
                        
                },
                cache: true
            }
    
    });
}

  </script>
@endsection

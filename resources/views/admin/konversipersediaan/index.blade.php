<?php $hal = "konversipersediaan"; ?>
@extends('layouts.admin.master')
@section('title', 'Konversi')

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
    Konversi
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Konversi</h3>
        </div>
        <!-- <a href="javascript:;" class="btn btn-sm btn-success btn_tambah" id="btn_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah Barang</a> -->
        <a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:3%">No #</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:20%">Barang</th>
                <th style="width:20%">Gudang</th>
                <th style="width:10%">Jumlah</th>
                <th style="width:10%">Jumlah Konversi</th>
                <th style="width:20%">Ket</th>
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
@include('admin.konversipersediaan.form')
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
    "ajax" : {
      "url" : "{{ route('konversipersediaan_data') }}",
      "type" : "GET"
    }
  });

    $('[name=popup_barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('konversipersediaan_get_barang')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        var text_item = item.barang_kode+" || "+item.barang_nama+" || ("+item.barang_alias+")"+" || "+item.nama_gudang+" || "+item.jumlah+" "+item.nama_satuan;
                        if(item.barang_alias === null || item.barang_alias === "" || item.barang_alias === 0){
                          text_item = item.barang_kode+" || "+item.barang_nama+" || "+item.nama_gudang+" || "+item.jumlah+" "+item.nama_satuan;
                        }
                        results.push({
                          id:item.barang_id,
                          satuan_id:item.id_satuan,
                          satuan_nama:item.satuan_nama,
                          gudang_id:item.id_gudang,
                          nama:item.barang_nama,
                          jumlah:item.jumlah,
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

    /*$('[name=popup_gudang]').select2({
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
  });*/

  

  

});

$("[name=popup_satuan_konversi]").on('change',function(){
  var id_satuan_konversi = $(this).val();
  var id_satuan          = $("[name=popup_satuan]").val();
  var jumlah             = $("[name=popup_jumlah]").val();
  if(id_satuan != "" || id_satuan != 0){
  $.ajax({
        url: "{{ url('konversipersediaan_konversi') }}",
        type: 'post',
        dataType: 'json',
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {id_satuan : id_satuan, id_satuan_konversi : id_satuan_konversi, jumlah : jumlah},
        success: function(respon){
          $("[name=popup_jumlah_konversi]").val(respon);
          $("#help_popup_jumlah_konversi").text(format_angka(respon));
        } 
      })
  }
});

$("[name=popup_barang]").on('change',function(){
    var d = $("[name=popup_barang]").select2('data')[0];
    $('[name=popup_satuan]').val(d.satuan_id).trigger('change');
    $('[name=popup_jumlah]').val(d.jumlah).trigger('change');
    $('[name=popup_gudang]').val(d.gudang_id).trigger('change');
    $('#help_popup_jumlah').text(format_angka(d.jumlah));
  });

/*$("[name=popup_jumlah]").on('keyup',function(){
    var jum = $(this).val();
    $('#help_popup_jumlah').text(format_angka(d.jum));
  });

  $("[name=popup_jumlah_konversi]").on('keyup',function(){
    var jum = $(this).val();
    $('#help_popup_jumlah_konversi').text(format_angka(d.jum));
  });
*/


$("#btn_tambah").click(function(){
    $('[name=popup_id_table]').val('');
    $('[name=popup_barang]').html('<option></option>');
    // $('[name=popup_gudang]').html('<option></option>');
    $('[name=popup_gudang]').val('');
    $('[name=popup_jumlah]').val('');
    $('[name=popup_satuan]').val('').trigger('change');
    $('[name=popup_jumlah_konversi]').val('');
    $('[name=popup_satuan_konversi]').val('').trigger('change');
    $('[name=popup_tanggal]').val(null).trigger('change');
    $('[name=popup_ket]').val('');
    $('#help_popup_jumlah').text('');
    $('#help_popup_jumlah_konversi').text('');
    $('#modal-form').modal('show');
});

function edit(id){
    $("[name=popup_id_table]").val($("#table_id"+id).val());
    /*$("[name=popup_barang]").val($("#table_idbarang"+id).val()).trigger('change');
    $("[name=popup_gudang]").val($("#table_idgudang"+id).val()).trigger('change');*/
    var id_barang = $("#table_idbarang"+id).val();
    var nama_barang = $("#table_namabarang"+id).val();
    /*var id_gudang = $("#table_idgudang"+id).val();
    var nama_gudang = $("#table_namagudang"+id).val();*/
    $("[name=popup_barang]").html('<option value="'+id_barang+'">'+nama_barang+'</option>');
    $("[name=popup_gudang]").val(id_gudang);
    $("[name=popup_tanggal]").val($("#table_tanggal"+id).val()).trigger('change');
    $("[name=popup_keterangan]").val($("#table_keterangan"+id).val()).trigger('change');
    $("[name=popup_jumlah]").val($("#table_jumlah"+id).val());
    $("[name=popup_satuan]").val($("#table_idsatuan"+id).val()).trigger('change');
    $("[name=popup_jumlah_konversi]").val($("#table_jumlah_konversi"+id).val());
    $("[name=popup_satuan_konversi]").val($("#table_idsatuan_konversi"+id).val()).trigger('change');
    $("[name=popup_idlog_stok]").val($("#table_idlog_stok"+id).val());

    $('#modal-form').modal('show');
  }


$("#btn_popup_simpan").click(function(){
    var id = $("[name=popup_id_table]").val();
    var id_barang = $("[name=popup_barang]").val();
    var jumlah = $("[name=popup_jumlah]").val();
    var satuan = $("[name=popup_satuan]").val();
    var jumlah_konversi = $("[name=popup_jumlah_konversi]").val();
    var satuan_konversi = $("[name=popup_satuan_konversi]").val();
    if(id_barang != '' && jumlah != '' && satuan != '' && jumlah_konversi != '' && satuan_konversi != ''){
      $.ajax({
        url: "{{ url('konversipersediaan_simpan') }}",
        type: 'post',
        dataType: 'json',
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $("#form_konversipersediaan").serialize(),
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
      url : "konversipersediaan_hapus",
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




@endsection

<?php $hal = "detail stok opname"; ?>
@extends('layouts.admin.master')
@section('title', 'Detail Stok Opname')

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
        <!-- <a href="javascript:;" class="btn btn-sm btn-success btn_tambah" id="btn_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah Barang</a> -->
        <a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table class="table table-bordered table-hover table-striped table-barang">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Gudang</th>
                <th>Stok</th>
                <th>Fisik</th>
                <th>Selisih</th>
                <th>Ket.</th>
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
@include('admin.stokopname_baru.form')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script type="text/javascript">
  var table, save_method;
  var gudang = $("[name=popup_gudang]").val();
  $(function(){
    table = $('.table').DataTable({
      "processing" : true,
      "ajax" : {
        "url" : "{{ route('stokopnamebaru_data') }}",
        "type" : "GET"
      }
    });


  });

  $("#btn_tambah").click(function(){
    $('[name=popup_id_table]').val('');
    $('[name=popup_barang]').html('<option></option>');
    $('[name=popup_stok]').val('');
    $('[name=popup_fisik]').val('');
    $('[name=popup_selisih]').val('');
    $('[name=popup_satuan]').val('').trigger('change');
    /*$('[name=popup_tanggal]').val(null).trigger('change');*/
    $('[name=popup_keterangan]').val('');
    $('#help_popup_stok').text(parseInt(0));
    $('#help_popup_fisik').text(parseInt(0));
    $('#help_popup_selisih').text(parseInt(0));
    $('#modal-form').modal('show');

    var id_gudang = $("[name=popup_gudang]").val();
    $('[name=popup_gudang]').val(id_gudang).trigger('change');
    get_barang(id_gudang);
  });

  $("[name=popup_gudang]").on('change',function(){
    var id_gudang = $(this).val();
    get_barang(id_gudang);
    $("[name=popup_barang]").html('<option></option>');
    $('[name=popup_stok]').val('');
    $('[name=popup_fisik]').val('');
    $('[name=popup_selisih]').val('');
    $('#help_popup_stok').text(parseInt(0));
    $('#help_popup_fisik').text(parseInt(0));
    $('#help_popup_selisih').text(parseInt(0));
  });

  $("[name=popup_barang]").on('change', function(){
    var d = $("[name=popup_barang]").select2('data')[0];

    $("[name=popup_satuan]").val(d.satuan_id);
    $("[name=popup_stok]").val(d.jumlah).trigger('keyup');
  });

  $("[name=popup_barang]").on('keyup', function(){
    var id_gudang = $("[name=popup_gudang]").val();
    get_barang(id_gudang);
  });


  $("[name=popup_stok]").on('keyup',function(){
    var jum = $(this).val();
    $("#help_popup_stok").text(format_angka(jum));
    selisih();
  });

  $("[name=popup_fisik]").on('keyup',function(){
    var jum = $(this).val();
    $("#help_popup_fisik").text(format_angka(jum));
    selisih();
  });

  function selisih(){
    var stok = $("[name=popup_stok]").val();
    var fisik = $("[name=popup_fisik]").val();
    var selisih = parseFloat(stok-fisik);
    $("[name=popup_selisih]").val(selisih);
    $("#help_popup_selisih").text(format_angka(selisih));
  }

  function get_barang(id, status){
    $('[name=popup_barang]').select2({
      placeholder: "--- Pilih ---",
      ajax: {
        url: '{{url('stokopnamebaru_get_barang')}}',
        dataType: 'json',
        data: function (params) {
          return {
            q: $.trim(params.term),
            gudang: id,
            status: status
          };
        },
        processResults: function (data) {
          var results = [];
          $.each(data, function(index,item){
            var text_item = item.barang_kode+" || "+item.barang_nama+" || ("+item.barang_alias+")"+" || "+item.jumlah+" "+item.nama_satuan;
            if(item.barang_alias === null || item.barang_alias === "" || item.barang_alias === 0){
              text_item = item.barang_kode+" || "+item.barang_nama+" || "+item.jumlah+" "+item.nama_satuan;
            }
            results.push({
              id:item.barang_id,
              satuan_id:item.id_satuan,
              satuan_nama:item.satuan_satuan,
              nama:item.barang_nama,
              harga:item.harga,
              kode:item.barang_kode,
              jumlah:item.jumlah,
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
  }

  function edit(id){
    $("[name=popup_id_table]").val(id);
    /*$("[name=popup_barang]").val($("#table_idbarang"+id).val()).trigger('change');
    $("[name=popup_gudang]").val($("#table_idgudang"+id).val()).trigger('change');*/
    var id_barang = $("#table_idbarang"+id).val();
    var nama_barang = $("#table_namabarang"+id).val();
    var id_gudang = $("#table_idgudang"+id).val();
    var nama_gudang = $("#table_namagudang"+id).val();

    $("[name=popup_gudang]").val(id_gudang).trigger('change');
    $("[name=popup_barang]").html('<option value="'+id_barang+'">'+nama_barang+'</option>');
    $("[name=popup_tanggal]").val($("#table_tanggal"+id).val()).trigger('change');
    $("[name=popup_keterangan]").val($("#table_keterangan"+id).val());
    $("[name=popup_stok]").val($("#table_stok"+id).val());
    $("[name=popup_fisik]").val($("#table_fisik"+id).val());
    $("[name=popup_selisih]").val($("#table_selisih"+id).val());
    $("[name=popup_satuan]").val($("#table_idsatuan"+id).val()).trigger('change');
    $("[name=popup_idlog_stok]").val($("#table_idlog_stok"+id).val());

    $("#help_popup_stok").text(format_angka($("#table_stok"+id).val()));
    $("#help_popup_fisik").text(format_angka($("#table_fisik"+id).val()));
    $("#help_popup_selisih").text(format_angka($("#table_selisih"+id).val()));

    get_barang(id_gudang);
    selisih();
    $('#modal-form').modal('show');
  }


  $("#btn_popup_simpan").click(function(){
    var id = $("[name=popup_id_table]").val();
    var id_barang = $("[name=popup_barang]").val();
    var id_gudang = $("[name=popup_gudang]").val();
    var tanggal   = $("[name=popup_tanggal]").val();
    if(id_barang != '' && id_gudang != '' && tanggal != ''){
      $.ajax({
        url: "{{ url('stokopnamebaru_simpan') }}",
        type: 'post',
        dataType: 'json',
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $("#form_stokopname").serialize(),
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
        url : "stokopnamebaru_hapus",
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
      'autoWidth'   : false,
    })
  })
</script>




@endsection

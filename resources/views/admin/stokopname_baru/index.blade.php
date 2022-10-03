<?php $hal = "stokopnamebaru"; ?>
@extends('layouts.admin.master')
@section('title', 'Stok Opname')

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
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-toggle="collapse" title="Search" data-target="#form-search"> <i class="fa fa-search"></i> Search</button>
          </div>
        </div>
        <div class="box-body collapse" id="form-tanggal_update">
          <form class="form-horizontal form-tanggal-search" action="{{url('stokopnamebaru_updatetanggal')}}" method="POST" autocomplete="off">
            {{ csrf_field() }} {{ method_field('POST') }}
            <div class="col-md-6">
              <div class="form-group">
                <label class="col-md-2">Tanggal</label>
                <div class="col-md-10">
                  <input type="text" name="update_tanggal" class="form-control datepicker" value="{{date('d-m-Y')}}">
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-2">Gudang</label>
                <div class="col-md-10">
                  <select class="form-control" name="update_gudang" style="width: 100%; height: 100%;">
                    @foreach($data['gudang'] as $d)
                      <option value="{{$d->id_gudang}}" nama="{{$d->nama_gudang}}" alamat="{{$d->alamat_gudang}}">{{$d->nama_gudang}}</option>
                    @endforeach 
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-2">Barang</label>
                <div class="col-md-10">
                  <select class="form-control" name="update_barang" style="width: 100%; height: 100%;"></select>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12">
                  <br>
                  <button type="submit" class="btn btn-success btn-update-tanggal"><i class="fa fa-refresh"></i> update</button>
                  <button type="reset" class="btn btn-warning"><i class="fa fa-undo"></i> reset</button>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="box-body collapse" id="form-search">
          <form class="form-horizontal form-tanggal-search" method="POST" autocomplete="off">
            <div class="col-md-6">
              <div class="form-group">
                <label class="col-md-2">Tanggal</label>
                <div class="col-md-10">
                  <input type="text" name="search_tanggal" class="form-control" value="{{date('d-m-Y')}}">
                </div>
                <div class="col-md-12">
                  <a href="javascript:;" style="margin-bottom:20px;" class="card-body-title btn-search-tanggal"><button class="btn btn-success" type="button"><i class="fa fa-search"></i> Search</button></a>
                  <a href="javascript:;" style="margin-bottom:20px;" class="card-body-title btn-reset-tanggal"><button class="btn btn-warning" type="button"><i class="fa fa-undo"></i> Reset</button></a>
                </div>
              </div>
            </div>
          </form>
        </div>
        <!-- <a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a> -->
        <?php echo $data['tombol_create'];?>
        <?php if($data['user_group'] != 1){?>
        {{-- <a href="{{url('stokopnamebaru_update')}}" class="card-body-title btn_cekstokopname" id="btn_cekstokopname"><button class="btn btn-success"><i class="fa fa-refresh"></i> Update</button></a> --}}
        <?php } ?>
        <?php if($data['user_group'] == 1 || $data['user_group'] == 6){?>
        <button type="button" class="btn btn-success" data-target="#form-tanggal_update" data-toggle="collapse"><i class="fa fa-refresh"></i> Update</button>
        <?php } ?>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table class="table table-bordered table-hover table-striped table-barang" id="table_stokopnametanggal">
                <thead>
                  <tr>
                      <th>No</th>
                      <th>tanggal</th>
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

@include('admin.stokopname_baru.detail_form')
@include('admin.stokopname_baru.form')
@include('admin.stokopname_baru.form_detail')
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
var table, table_detail, table_search, save_method;
var gudang = $("[name=popup_gudang]").val();
$(function(){
  $('[name=update_gudang]').select2();
  $('input[name=search_tanggal]').daterangepicker({
   locale: {
      format: 'DD-MM-YYYY',
      separator: "  s.d. ",
    }
  });
  table = $("#table_stokopnametanggal").DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ url('stokopnamebaru_dataTanggal') }}',
          columns: [
              {data: 'nomor', name: 'nomor'},
              {data: 'tanggal', name: 'tanggal'},
              {data: 'nama_gudang', name: 'nama_gudang'},
              {data: 'fisik', name: 'fisik'},
              {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
          ],
          
    });

    // get_update_barang();
    get_update_barang_only();

});

$("#btn_tambah").click(function(){
    $('[name=popup_id_table]').val('');
    $('[name=popup_barang]').html('<option></option>');
    $('[name=popup_stok]').val('');
    $('[name=popup_fisik]').val('');
    $('[name=popup_selisih]').val('');
    $('[name=popup_satuan]').val('').trigger('change');
    $('[name=popup_status]').val('');
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
  $('[name=popup_satuan]').val('').trigger('change');
  $('[name=popup_status]').val('');
  $('#help_popup_stok').text(parseInt(0));
  $('#help_popup_fisik').text(parseInt(0));
  $('#help_popup_selisih').text(parseInt(0));
});

/*$("[name=update_gudang]").on('change',function(){
  var id_gudang = $(this).val();
  get_update_barang(id_gudang);
});

$("[name=update_barang]").on('keyup', function(){
  var id_gudang = $("[name=update_gudang]").val();
  get_update_barang(id_gudang);
});*/

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

function get_update_barang(id){
  $('[name=update_barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('stokopnamebaru_get_barangupdate')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        gudang: $("[name=update_gudang]").val(),
                        tanggal: $("[name=update_tanggal]").val()
                        // gudang: id,
                        // status: status
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        var text_item = item.barang_kode+" || "+item.barang_nama+" || ("+item.barang_alias+") || "+item.satuan_satuan;
                        if(item.barang_alias === null || item.barang_alias === "" || item.barang_alias === 0){
                          //text_item = item.barang_kode+" || "+item.barang_nama+" || "+item.jumlah+" "+item.nama_satuan;
                          text_item = item.barang_kode+" || "+item.barang_nama+" || "+item.satuan_satuan;
                        }
                        results.push({
                          id:item.barang_id+"/"+item.id_satuan,
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

function get_barang(id, status){
  $('[name=popup_barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('stokopnamebaru_get_barang')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        gudang: $("[name=popup_gudang]").val(),
                        tanggal: $("[name=popup_tanggal]").val()
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        var text_item = item.barang_kode+" || "+item.barang_nama+" || ("+item.barang_alias+")";
                        if(item.barang_alias === null || item.barang_alias === "" || item.barang_alias === 0){
                          //text_item = item.barang_kode+" || "+item.barang_nama+" || "+item.jumlah+" "+item.nama_satuan;
                          text_item = item.barang_kode+" || "+item.barang_nama;
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
    $("[name=popup_status]").val($("#table_status"+id).val());

    $("#help_popup_stok").text(format_angka($("#table_stok"+id).val()));
    $("#help_popup_fisik").text(format_angka($("#table_fisik"+id).val()));
    $("#help_popup_selisih").text(format_angka($("#table_selisih"+id).val()));

    get_barang(id_gudang);
    selisih();
    $('#modal-form').modal('show');
  }

function detail_form(id){
    $("[name=popup_edit_id_table]").val(id);
    var id_barang = $("#table_idbarang"+id).val();
    var nama_barang = $("#table_namabarang"+id).val();
    var id_gudang = $("#table_idgudang"+id).val();
    var nama_gudang = $("#table_namagudang"+id).val();

    $("[name=popup_detail_gudang]").val(nama_gudang);
    $("[name=popup_detail_barang]").val(nama_barang);
    $("[name=popup_detail_tanggal]").val($("#table_tanggal"+id).val());
    $("[name=popup_detail_keterangan]").val($("#table_keterangan"+id).val());
    $("[name=popup_detail_stok]").val($("#table_stok"+id).val());
    $("[name=popup_detail_fisik]").val($("#table_fisik"+id).val());
    $("[name=popup_detail_selisih]").val($("#table_selisih"+id).val());
    $("[name=popup_detail_satuan]").val($("#table_idsatuan"+id).val()).trigger('change');
    $("[name=popup_detail_idlog_stok]").val($("#table_idlog_stok"+id).val());
    $("[name=popup_detail_status]").val($("#table_status"+id).val());

    $("#help_popup_detail_stok").text(format_angka($("#table_stok"+id).val()));
    $("#help_popup_detail_fisik").text(format_angka($("#table_fisik"+id).val()));
    $("#help_popup_detail_selisih").text(format_angka($("#table_selisih"+id).val()));

    get_barang(id_gudang);
    selisih();
    $('#modal-form_detail_edit').modal('show');
}


function detail(tanggal,gudang){
    $('#table_stokopnamedetail').DataTable().clear();
    $('#table_stokopnamedetail').DataTable().destroy();
    table_detail = $('#table_stokopnamedetail').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ route('stokopnamebaru_data2') }}",
      "data" : {tanggal:tanggal,gudang:gudang}
    },
    "columnDefs": [
            { targets: 0, orderable: false, searchable: false, }
          ],
  });
    $('#modal-form-detail').modal('show');
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
          if(id != ""){
            table_detail.ajax.reload();  
          }
          
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
        table_detail.ajax.reload();
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
    $('#table_stokopnametanggal').DataTable().clear();
    $('#table_stokopnametanggal').DataTable().destroy();
    table_search = $("#table_stokopnametanggal").DataTable({
          processing: true,
          serverSide: true,
          ajax: { "url" : "{{ url('stokopnamebaru_searchtanggal') }}",
                  "type" : "GET",
                  data : function(d){
                    d.tanggal=tanggal;
                  }
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
          ajax: '{{ url('stokopnamebaru_dataTanggal') }}',
          columns: [
              {data: 'nomor', name: 'nomor'},
              {data: 'tanggal', name: 'tanggal'},
              {data: 'nama_gudang', name: 'nama_gudang'},
              {data: 'fisik', name: 'fisik'},
              {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
          ],
          
    });
});

function get_update_barang_only(){
  $('[name=update_barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: "{{url('stokopnamebaru_get_barangupdate_only')}}",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        page:params.page
                    };
                },
                processResults: function (data,params) {
                      params.page = params.page || 1;
				              var more = (params.page * 10) < data.length;
                      var results = [];
                      $.each(data, function(index,item){
                        var text_item = item.kode_barang+" || "+item.nama_barang+" || ("+item.alias_barang+") || "+item.satuan_satuan;
                        if(item.alias_barang === null || item.alias_barang === "" || item.alias_barang === 0){
                          //text_item = item.barang_kode+" || "+item.barang_nama+" || "+item.jumlah+" "+item.nama_satuan;
                          text_item = item.kode_barang+" || "+item.nama_barang+" || "+item.satuan_satuan;
                        }
                        results.push({
                          id:item.id_barang+"/"+item.id_satuan,
                          text:text_item
                        });
                      });
                      return{
                        results:results, pagination:{more: true} 
                      };
                },
                cache: true
              }        
  });
}

</script>





@endsection

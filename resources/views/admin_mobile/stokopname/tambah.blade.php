<?php $hal = "stok opname"; ?>
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


    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Tambah Stok Opname</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <form role="form" class="form_belanja" action="{{url('stokopname_simpan')}}" method="post">
              <input type="hidden" name="id_stokopname" value="{{$data['data']['id_stokopname']}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="status" value="{{$data['data']['status']}}">
            <div class="box-body">
              <div class="form-group col-md-12 " style="border:1px solid black;">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputPassword1">Tanggal</label>
                          <input class="form-control set_datepicker datepicker" type="text" name="tanggal" placeholder="Tanggal" value="{{($data['data']['tanggal']=='') ? date('d-m-Y'):$data['data']['tanggal']}}" required />
                    </div>
                    <div class="form-group">
                      <label>Gudang</label>
                      <select name="gudang" class="form-control select2" required style="width: 100%;">
                        @foreach($data['gudang'] as $d)
                            <option value="{{$d->id_gudang}}" nama="{{$d->nama_gudang}}" alamat="{{$d->alamat_gudang}}" {{($data['data']['id_gudang']==$d->id_gudang) ? "Selected":""}}>{{$d->nama_gudang}}</option>
                          @endforeach                     
                      </select>
                    </div>
                    <div class="form-group">
                    <label>Keterangan</label>
                      <textarea class="form-control" rows="2" name="keterangan">{{$data['data']['keterangan']}}</textarea>
                    </div>
                  </div>
                </div>
               <!-- end : row -->
              </div>

              <div class="form-group col-md-12" style="border:1px solid black;">
               <h3>Detail Barang</h3>
               <hr/>

               <a href="javascript:;" class=" btn btn-sm btn-success btn_tambah" id="btn_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah Barang</a>
               <br/> <br/>
               <div class="table-responsive">
               <table class="table table-bordered table-hover table-striped table-barang">
                <thead>
                  <tr>
                      <th>No</th>
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

            </div>        
        </div>

        <!-- /.box-body -->
        <div class="box-footer">
          <a href="{{url('stokopname')}}" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
          <button type="submit" class="btn bg-blue btn-md pull-right"><span class="glyphicon glyphicon-floppy-disk"></span> Simpan</button>
        </div>

      </form>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>
<!-- /.row (main row) -->



</section>

<!-- /.content -->
@include('admin.stokopname.form2')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="{{asset('public/js/accounting.js')}}" charset="utf-8"></script>

<script type="text/javascript">
  var table;
  var tb_no = parseInt(1000);
  var id_stokopname = '{{$data['data']['id_stokopname']}}';
  var id_gudang     = '{{$data['data']['id_gudang']}}';
  var nama_gudang   = '{{$data['data']['nama_gudang']}}';
  $(document).ready(function () {
  table = $(".table-barang").DataTable({
    "paging" : false,
  });
  table.on('order.dt search.dt', function(){
    table.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i){
       cell.innerHTML = i+1;
    });
  }).draw();
  
  if(id_stokopname != ''){
    get_edit(id_stokopname);
    //$("[name=gudang]").html('<option value="'+id_gudang+'" nama="'+nama_gudang+'" edit="1">'+nama_gudang+'</option>');
  }  


});

$("#btn_tambah").click(function(){
  $("[name=popup_id_table]").val('');
  $("[name=popup_barang]").find('option').remove().end();
  $("[name=popup_jumStok]").val('');
  /*$("[name=popup_gudang]").find('option').remove().end();*/
  $("[name=popup_jumFisik]").val('');
  $("[name=popup_satuan]").val('');
  $("[name=popup_selisih]").val('');
  $("[name=popup_keterangan]").val('');
  $('#help_popup_jumFisik').text('');
  $('#help_popup_jumStok').text('');
  $('#help_popup_selisih').text('');
  $("#modal-form").modal('show');
  var id_gudang = $("[name=gudang]").val();

  get_barang(id_gudang);
});

$("[name=popup_barang]").on('change', function(){
  var d = $("[name=popup_barang]").select2('data')[0];
  var g = $("[name=gudang]").select2('data')[0];

  $("[name=popup_satuan]").val(d.satuan_id);
  $("[name=popup_jumStok]").val(d.jumlah).trigger('keyup');
  $("[name=popup_gudang]").html("<option value='"+g.id+"' nama='"+g.nama+"'>"+g.nama+"</option>");
});

function get_barang(id){
  $('[name=popup_barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('stokopname_get_barang')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        gudang: id
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

function edit_table(id){
  //var id_table = $("#tabel_id"+id).val();
  var id_barang   = $("#tabel_idbarang"+id).val();
  var nama_barang = $("#tabel_namabarang"+id).val();
  var kode_barang = $("#tabel_kodebarang"+id).val();
  var alias_barang= $("#tabel_aliasbarang"+id).val();
 /* var id_gudang   = $("#tabel_idgudang"+id).val();
  var nama_gudang = $("#tabel_namagudang"+id).val();*/
  var jum_stok    = $("#tabel_jumStok"+id).val();
  var jum_fisik   = $("#tabel_jumFisik"+id).val();
  var id_satuan   = $("#tabel_idsatuan"+id).val();
  var selisih     = $("#tabel_selisih"+id).val();
  var keterangan  = $("#tabel_keterangan"+id).val();

  $("[name=popup_id_table]").val(id);
  $("[name=popup_barang]").html('<option value="'+id_barang+'" nama="'+nama_barang+'" kode="'+kode_barang+'" alias="'+alias_barang+'">'+kode_barang+" || "+nama_barang+" || "+alias_barang+'</option>');
  /*$("[name=popup_gudang]").html('<option value="'+id_gudang+'" nama="'+nama_gudang+'">'+nama_gudang+'</option>');*/
  $("[name=popup_jumStok]").val(jum_stok);
  $("[name=popup_jumFisik").val(jum_fisik);
  $("[name=popup_satuan").val(id_satuan);
  $("[name=popup_selisih]").val(selisih);
  $("[name=popup_keterangan]").val(keterangan);
  $("#help_popup_jumStok").text(format_angka(jum_stok));
  $("#help_popup_jumFisik").text(format_angka(jum_fisik));
  $("#help_popup_selisih").text(format_angka(selisih));
  
  var id_gudang = $("[name=gudang]").val();
  get_barang(id_gudang);

  $("#modal-form").modal('show');
}

function hapus(id){
  table.row(id.parents('tr')).remove().draw();
    selisih();
}

$("#btn_popup_simpan").click(function(){
  var d = $("[name=popup_barang]").select2('data')[0];
  var g = $("[name=gudang]").select2('data')[0];
  var id_data     = $("[name=popup_id_table]").val();
  var id_barang   = $("[name=popup_barang]").val();
  
  var id_gudang   = $("[name=gudang]").val();
  var nama_gudang = $("[name=gudang] :selected").attr('nama');
  var jum_stok    = parseInt($("[name=popup_jumStok]").val());
  var jum_fisik   = parseInt($("[name=popup_jumFisik]").val());
  var id_satuan   = $("[name=popup_satuan]").val();
  var selisih     = parseInt($("[name=popup_selisih]").val());
  var keterangan  = $("[name=popup_keterangan]").val();

  if(id_data == ""){
    if(d.alias === null || d.alias === "" || d.alias === 0){
      var nama_barang = d.kode+" || "+d.nama;
      var kode_barang = d.kode;
      var alias_barang= d.alias;
    }else{
      var nama_barang = d.kode+" || "+d.nama+" || "+d.alias;
      var kode_barang = d.kode;
      var alias_barang= d.alias;
    }
    /*if($("[name=gudang] :selected").attr('edit') == 1 ){
    var nama_gudang = $("[name=gudang] :selected").attr('nama');
    }else{
    var nama_gudang = g.nama;  
    }*/
  }else{
      var nama_barang = $("[name=popup_barang] :selected").attr('nama');
      var kode_barang = $("[name=popup_barang] :selected").attr('kode');
      var alias_barang= $("[name=popup_barang] :selected").attr('alias');
      /*var nama_gudang = $("[name=gudang] :selected").attr('nama');*/
  }
  

  if(id_barang != '' || (selisih != 0 || selisih != '') ){
    if(id_data == ''){
      table.row.add(['<div><center></center></div>',
          '<div id="list_barang'+tb_no+'">'+nama_barang+'</div>',
          '<div id="list_gudang'+tb_no+'">'+nama_gudang+'</div>',
          '<div id="list_jumStok'+tb_no+'">'+format_angka(jum_stok)+'</div>',
          '<div id="list_jumFisik'+tb_no+'">'+format_angka(jum_fisik)+'</div>',
          '<div id="list_selisih'+tb_no+'">'+format_angka(selisih)+'</div>',
          '<div id="list_keterangan'+tb_no+'">'+keterangan+'</div>',
          '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+tb_no+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
          '<input type="hidden" name="tabel_id[]" id="tabel_id'+tb_no+'" value=" ">'+
          '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+tb_no+'" value="'+id_barang+'">'+
          '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+tb_no+'" value="'+nama_barang+'">'+
          '<input type="hidden" name="tabel_kodebarang[]" id="tabel_kodebarang'+tb_no+'" value="'+kode_barang+'">'+
          '<input type="hidden" name="tabel_aliasbarang[]" id="tabel_aliasbarang'+tb_no+'" value="'+alias_barang+'">'+
          '<input type="hidden" name="tabel_idgudang[]" id="tabel_idgudang'+tb_no+'" value="'+id_gudang+'">'+
          '<input type="hidden" name="tabel_namagudang[]" id="tabel_namagudang'+tb_no+'" value="'+nama_gudang+'">'+
          '<input type="hidden" name="tabel_jumStok[]" id="tabel_jumStok'+tb_no+'" value="'+jum_stok+'">'+
          '<input type="hidden" name="tabel_jumFisik[]" id="tabel_jumFisik'+tb_no+'" value="'+jum_fisik+'">'+
          '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+tb_no+'" value="'+id_satuan+'">'+
          '<input type="hidden" name="tabel_selisih[]" id="tabel_selisih'+tb_no+'" value="'+selisih+'">'+
          '<input type="hidden" name="tabel_keterangan[]" id="tabel_keterangan'+tb_no+'" value="'+keterangan+'">']).draw(false);
    }else{
        $("#list_barang"+id_data).text(nama_barang);
        $("#list_gudang"+id_data).text(nama_gudang);
        $("#list_jumStok"+id_data).text(format_angka(jum_stok));
        $("#list_jumFisik"+id_data).text(format_angka(jum_fisik));
        $("#list_selisih"+id_data).text(format_angka(selisih));
        $("#list_keterangan"+id_data).text(keterangan);

        $("#tabel_id"+id_data).val(id_data);
        $("#tabel_idbarang"+id_data).val(id_barang);
        $("#tabel_namabarang"+id_data).val(nama_barang);
        $("#tabel_kodebarang"+id_data).val(kode_barang);
        $("#tabel_aliasbarang"+id_data).val(alias_barang);
        $("#tabel_idgudang"+id_data).val(id_gudang);
        $("#tabel_namagudang"+id_data).val(nama_gudang);
        $("#tabel_jumStok"+id_data).val(jum_stok);        
        $("#tabel_jumFisik"+id_data).val(jum_fisik);
        $("#tabel_idsatuan"+id_data).val(id_satuan);
        $("#tabel_selisih"+id_data).val(selisih);
        $("#tabel_keterangan"+id_data).val(keterangan);
    }
    tb_no++;
    $("#modal-form").modal('hide');
  }
});

$("[name=popup_jumStok]").on('keyup', function(){
  var jum = $(this).val();
  $("#help_popup_jumStok").text(format_angka(jum));
  selisih();
});
$("[name=popup_jumFisik]").on('keyup', function(){
  var jum = $(this).val();
  $("#help_popup_jumFisik").text(format_angka(jum));
  selisih();
});

function selisih(){
  var stok = $("[name=popup_jumStok]").val();
  var fisik = $("[name=popup_jumFisik]").val();
  var selisih = parseFloat(stok-fisik);
  $("[name=popup_selisih]").val(selisih);
  $("#help_popup_selisih").text(format_angka(selisih));
}

function get_edit(id){
  $.ajax({
      url: "{{url('stokopname_get_edit')}} ",
      type: 'post',
      data: {id : id},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
        if(respon.length > 0){
          for(i in respon){
            /*var keterangan = "";
            if(respon[i].keterangan == "null"){
              var keterangan = respon[i].keterangan;
            }*/
            var jumlah_stok = parseInt(0);
            var jumlah_fisik = parseInt(0);
            var selisih = parseInt(0);
            if(respon[i].jumlah_stok != 0){
              var jumlah_stok = parseInt(respon[i].jumlah_stok);
            }

            if(respon[i].jumlah_fisik != 0){
              var jumlah_fisik = parseInt(respon[i].jumlah_fisik);
            }

            if(respon[i].selisih != 0){
              var selisih = parseInt(respon[i].selisih);
            }
            
            table.row.add(['<div><center></center></div>',
              '<div id="list_barang'+respon[i].id+'">'+respon[i].kode_barang+" || "+respon[i].nama_barang+" || "+respon[i].alias_barang+'</div>',
              '<div id="list_gudang'+respon[i].id+'">'+respon[i].nama_gudang+'</div>',
              '<div id="list_jumStok'+respon[i].id+'">'+format_angka(jumlah_stok)+'</div>',
              '<div id="list_jumFisik'+respon[i].id+'">'+format_angka(jumlah_fisik)+'</div>',
              '<div id="list_selisih'+respon[i].id+'">'+format_angka(selisih)+'</div>',
              '<div id="list_keterangan'+respon[i].id+'">'+respon[i].keterangan+'</div>',
              '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+respon[i].id+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
              '<input type="hidden" name="tabel_id[]" id="tabel_id'+respon[i].id+'" value="'+respon[i].id+'">'+
              '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+respon[i].id+'" value="'+respon[i].id_barang+'">'+
              '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+respon[i].id+'" value="'+respon[i].nama_barang+'">'+
              '<input type="hidden" name="tabel_kodebarang[]" id="tabel_kodebarang'+respon[i].id+'" value="'+respon[i].kode_barang+'">'+
              '<input type="hidden" name="tabel_aliasbarang[]" id="tabel_aliasbarang'+respon[i].id+'" value="'+respon[i].alias_barang+'">'+
              '<input type="hidden" name="tabel_idgudang[]" id="tabel_idgudang'+respon[i].id+'" value="'+respon[i].id_gudang+'">'+
              '<input type="hidden" name="tabel_namagudang[]" id="tabel_namagudang'+respon[i].id+'" value="'+respon[i].nama_barang+'">'+
              '<input type="hidden" name="tabel_jumStok[]" id="tabel_jumStok'+respon[i].id+'" value="'+jumlah_stok+'">'+
              '<input type="hidden" name="tabel_jumFisik[]" id="tabel_jumFisik'+respon[i].id+'" value="'+jumlah_fisik+'">'+
              '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+respon[i].id+'" value="'+respon[i].id_satuan+'">'+
              '<input type="hidden" name="tabel_selisih[]" id="tabel_selisih'+respon[i].id+'" value="'+selisih+'">'+
              '<input type="hidden" name="tabel_keterangan[]" id="tabel_keterangan'+respon[i].id+'" value="'+respon[i].keterangan+'">']).draw(false);
          }
          /*selisih();*/
        }
        
      }
    });
}



</script>

@endsection

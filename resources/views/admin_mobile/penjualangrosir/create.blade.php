<?php $hal = "penjualangrosir_tambah"; ?>
@extends('layouts.admin.master')
@section('title', 'Penjualan')

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
    Penjualan Grosir
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">


    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Tambah Belanja Barang</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <form role="form" class="form_belanja" action="{{url('penjualangrosir_simpan')}}" method="post">
              <!-- {{ csrf_field() }} {{ method_field('POST') }} -->
            <input type="hidden" name="id_kasir" value="{{$data['data']['id_kasir']}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="box-body">
              <div class="form-group col-md-12 " style="border:1px solid black;">
                <div class="row">
                    <div class="form-group col-md-6">
                      <label for="exampleInputPassword1">Tanggal Input</label>
                          <input class="form-control datepicker" type="text" name="tanggal" placeholder="Tanggal" value="{{($data['data']['tanggal'] == '') ? date('d-m-Y'):$data['data']['tanggal']}}" required readonly />
                    </div>
                    <div class="form-group col-md-6">
                      <label>Tanggal Faktur</label>
                          <input class="form-control datepicker" type="text" name="tanggal_faktur" placeholder="Tanggal Faktur" value="{{($data['data']['tanggal_faktur'] == '') ? date('d-m-Y'):$data['data']['tanggal_faktur']}}" required/>
                    </div>
                    <div class="form-group col-md-6">
                    <label>Tanggal Jatuh Tempo</label>
                        <input class="form-control datepicker" type="text" name="tanggal_tempo" placeholder="Tanggal Tempo" value="{{($data['data']['tanggal_tempo'] == '') ? date('d-m-Y'):$data['data']['tanggal_tempo']}}" required/>
                    </div>

                    <div class="form-group  col-md-6">
                      <label for="exampleInputPassword1">Nomor Faktur</label>
                      <input type="text"  name="nomor" class="form-control" value="{{$data['data']['nomor']}}">
                    </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="exampleInputPassword1">Nama Pelanggan</label>
                    <select name="id_pelanggan" class="form-control" style="width: 100%;">

                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="exampleInputPassword1">Gudang</label>
                    {{-- <input type="text"  name="" class="form-control" value="" placeholder="Gudang A" readonly=""> --}}
                    <select name="gudang" id="gudang" class="form-control select2"  style="width: 100%;" >
                      <option value="">Pilih</option>
                     @foreach($data['gudang'] as $list)
                      <option value="{{$list->id}}">{{$list->nama}}</option>
                     @endforeach
                    </select>
                  </div>
                   <div class="form-group col-md-6">
                    <label for="exampleInputPassword1">Jenis Penjualan</label>
                    <input type="text"  name="" class="form-control" value="" placeholder="Grosir" readonly="">
                  </div>
                  <!-- <div class="form-group col-md-6">
                    <label for="exampleInputPassword1"></label>
                    <input type="hidden"  name="" class="form-control" value="" placeholder="Outlet" readonly="">
                  </div> -->
                  <div class="form-group col-md-6">
                    <label for="exampleInputPassword1">Keterangan</label>
                    <textarea class="form-control" rows="2" name="keterangan">{{$data['data']['keterangan']}}</textarea>
                  </div>

                  

                </div> <!-- end : row -->
              </div>

              <div class="form-group col-md-12" style="border:1px solid black;">
               <h3>Detail Barang</h3>
               <hr/>

               <a href="javascript:;" class="btn btn-sm btn-success btn_tambah" id="btn_tambah">Tambah Barang</a>
               <br/> <br/>
               <table class="table table-bordered table-hover table-striped table-barang" id="table_barang">
                <thead>
                  <tr>
                    <th width="3%">No.</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                  <tr class="footercount" style="display: none;">
                    <td  colspan="4" align="center" ></td>
                    <td  align="right" class="subtotal"></td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>

            </div>


        
        <div class=" col-md-6 col-sm-12 m-l-5">
          <div class="table-responsive">
           <table class="table table-bordered table-form">
            <tbody>
              
              <tr class="active">
                <td>Total Penjualan</td>
                <td class="text-right">
                {{-- <label class="checkbox-inline checkbox-right">
                  <input type="checkbox" name="termasuk_pajak" value="1" class="styled"> Sudah Termasuk PPN
                </label>
                </td> --}}
              </tr>
             <tr>
                <td width="30%">Sub Total</td>
                <td><input type="hidden" name="td_total" value="{{($data['data']['td_subtotal']== '') ? '0':$data['data']['td_subtotal']}}" readonly="">
                    <p class="help-block pull-right" id="td_total">Rp 0</p>
                </td>
              </tr>
               <tr>
                <td>Potongan</td>
                <td><input type="text" name="td_diskon" class="form-control number-only text-right" value="{{($data['data']['td_potongan']== '') ? '0':$data['data']['td_potongan']}}">
                    <p class="help-block pull-right" id="help_popup_totaldiskon">Rp 0</p>
                </td>
              </tr> 
              
              <tr>
                <td>Ongkos Kirim</td>
                <td><input type="text" step="0.01" min="0" name="td_ongkir" id="td_ongkir" class="form-control text-right number-only" value="{{($data['data']['td_ongkir']== '') ? '0':$data['data']['td_ongkir']}}">
                    <p class="help-block pull-right" id="help_popup_ongkir">Rp 0</p>
                </td>
              </tr>
              
              <tr class="hide rekening">
                <td>Rekening</td>
                <td><select class="form-control select2" name="id_rek" style="width: 100%;">
                      <?php
                      foreach ($data['rekening'] as $key => $value)                        
                       {
                        $selected = ($data['data']['id_rek'] == $key) ? "selected":'';
                        echo "<option value='".$key."' ".$selected."> ".$value." </option>";
                      }?>
                    </select>
                </td>
              </tr>
              <tr class="hide no_rekening">
                <td>No.Rekening</td>
                <td><input type="text" name="no_rek" class="form-control number-only" value="{{$data['data']['no_rek']}}">
                </td>
              </tr>
              <tr>
                <td>Pembayaran</td>
                <td><select class="form-control select2" name="carabayar" style="width: 100%;">
                       <?php
                      foreach ($data['carabayar'] as $key => $value) {
                        $selected = ($data['data']['carabayar'] == $key) ? "selected":'';
                        echo "<option value='".$key."' ".$selected."> ".$value." </option>";
                      }?>
                    </select>
                </td>
              </tr>
               <tr>
                <td>Metode bayar</td>
                <td><select class="form-control select2" name="viabayar" style="width: 100%;">
                    <option value=""> --- Pilih --- </option>
                       <?php
                      foreach ($data['pembayaran'] as $key => $value) {
                         $selected = ($data['data']['metode'] == $value->id) ? "selected":'';
                        echo "<option value='".$value->id."' ".$selected."> ".$value->nama." </option>";
                      }?>
                    </select>
                </td>
              </tr>
              <tr class="hide c_uangmuka">
                <td>Uang Muka</td>
                <td><input type="text" name="td_uangmuka" class="form-control text-right number-only" value="{{$data['data']['uang_muka']}}">
                  <p class="help-block pull-right" id="help_popup_uangmuka">{{$data['data']['uang_muka']}}</p>
                </td>
              </tr>
              <tr>
                <td>Tagihan</td>
                <td><p class="help-block pull-right" id="td_netto">Rp 0</p>
                  <input type="hidden" name="td_bayar" id="td_bayar" value="{{$data['data']['td_tagihan']}}">
                  <input type="hidden" name="total_netto" id="total_netto" value="{{$data['data']['td_tagihan']}}">
                </td>
              </tr>
            </tbody>
           </table>

          </div>
        </div>
        
        </div>

        <!-- /.box-body -->
        <div class="box-footer">
          <a href="{{url('penjualangrosir')}}" class="btn btn-md btn-warning">Kembali</a>
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
@include('admin.penjualangrosir.form')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/js/touchspin.min.js')}}"></script>


<script type="text/javascript">

  var formBarang = $(".form_barang");
  var formBelanja = $(".form_belanja");
  var selectPajak = formBelanja.find("select[name=pajak]");
  var inputOngkir = formBelanja.find("input[name=status_ongkir]");
  var inputNominalOngkir = formBelanja.find("input[name=nominal_ongkir]");
  var tb_no = parseInt(1000);
  var table_barang;
  var id_kasir = '{{$data['data']['id_kasir']}}';
  var id_pelanggan = '{{$data['data']['id_pelanggan']}}';
  var nama_pelanggan = '{{$data['data']['nama_pelanggan']}}';
  var potongan = '{{$data['data']['td_potongan']}}';
  var gudang = '{{$data['data']['gudang']}}';
  var id_gudang = '{{$data['data']['id_gudang']}}';
  // console.log(gudang);
  var nama_gudang = '{{$data['data']['nama_gudang']}}';




  $(document).ready(function () {
  $('#gudang').val(id_gudang).trigger('change');
  $('#id_gudang').val(id_gudang);
      $(".touchspin-step").TouchSpin({
        min: 1,
        max: 100,
        step: 1
       });
    table_barang = $("#table_barang").DataTable({
      "paging": false
    });
    table_barang.on( 'order.dt search.dt', function () {
            table_barang.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
    }).draw();

    if(id_kasir != ''){
      if(id_pelanggan != ''){
        $("[name=id_pelanggan]").val(id_pelanggan).trigger('change');
        $("[name=id_pelanggan]").html('<option value="'+id_pelanggan+'">'+nama_pelanggan+'</option>')
      }
      if (gudang != '') {
        $("[name=gudang]").val(gudang).trigger('change');
        $("[name=gudang]").html('<option value="'+gudang+'">'+nama_gudang+'</option>');
      }
      get_edit(id_kasir);
    }
    $("[name=td_diskon]").on('keyup',function(){
        var d = $(this).val();
        $("#help_popup_totaldiskon").text(accounting.formatMoney(d));
        netto();
      })
    accounting.settings = {
          currency: {
                  symbol: "",
                  precision: 2,
                  thousand: ".",
                  decimal : ",",
                  format: {
                      pos : '%s %v',
                      neg : '%s (%v)',
                      zero : '%s %v'
                  },
          },
          number: {
            precision : 0,  // default precision on numbers is 0
            thousand: ".",
            decimal : ","
          }
    };
$('[name=id_pelanggan]').select2({
            placeholder: "Pilih...",
            //minimumInputLength: 2,
            ajax: {
                url: '{{url('penjualangrosir_get_pelanggan')}}',
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
                          id:item.pelanggan_id,
                          nama:item.pelanggan_nama,
                          alamat:item.pelanggan_alamat,
                          telp:item.pelanggan_telp,
                          text:item.pelanggan_nama,
                        });
                      });
                      return{
                        results:results
                      };
                },
                cache: true
            }   
  }); 
  
$('#gudang1').select2({
            placeholder: "Pilih...",
            //minimumInputLength: 2,
            ajax: {
                url: '{{url('penjualangrosir_getgudang')}}',
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
                          nama:item.nama,
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
      $('#gudang').on('change',function(e){
        var id = $(this).val();
        // alert(id);
        $('.formgudang #id_gudang').val(id);
        // var a = 'data-gudang="'+id+'"';
        $('#btn_tambah').attr("gudang", id);
      });
     


     
 //satuan select2
  $('[name=popup_satuan22]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('penjualangrosir_getsatuan')}}',
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
                        results.push({
                          id:item.satuan_id,
                          satuan_satuan:item.satuan_satuan,
                          nama:item.satuan_nama,
                          konversi:item.konversi,
                          text:item.satuan_nama
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


$("#btn_tambah").click(function(){
    $('[name=popup_id_table]').val('');
    //$('[name=popup_barang]').find('option').remove().end();
    var id = $('.formgudang #id_gudang').val();
    $('[name=popup_barang]').html('<option></option>');
    $('[name=popup_harga]').val('');
    $('[name=popup_jumlah]').val('');
    $('[name=popup_satuan]').val('').trigger('change');
    $('[name=popup_total]').val('');
    $('[name=popup_diskon]').val('').trigger('change');
    $('#help_popup_jumlah').text('');
    $('#help_popup_harga').text('');
    $('#help_popup_total').text('');
    $('#help_popup_diskon').text('');
    $('#modal-form').modal('show');
    tambah(id)
});

$("[name=td_ongkir]").on('keyup', function(){
  var ongkir = $(this).val();
  $("#help_popup_ongkir").text(accounting.formatMoney(ongkir));
  get_jum();
  netto();
});
$("[name=td_uangmuka]").on('keyup', function(){
  var uangmuka = $(this).val();
  $("#help_popup_uangmuka").text(accounting.formatMoney(uangmuka));
  get_jum();
  netto();
});
$("[name=popup_jumlah]").on('keyup', function(){
  var jumlah = $(this).val();
  $("#help_popup_jumlah").text(format_angka(jumlah));
  get_jum();
});
$("[name=popup_harga]").on('keyup', function(){
  var harga = $(this).val();
  var jumlah = $("[name=popup_jumlah]").val();
  $("#help_popup_harga").text(accounting.formatMoney(harga));
  if(jumlah > 0){
  get_jum();
  }
});
$("[name=popup_diskon]").on('keyup', function(){
  var diskon = $(this).val();
  $("#help_popup_diskon").text(accounting.formatMoney(diskon));
  get_jum();
});
$("[name=popup_barang]").on('change', function(){
  var d = $("[name=popup_barang]").select2('data')[0];
  // console.log(d);
  // var satuan = '<option value="'+d.satuan_id+'" nama="'+d.satuan_nama+'">'+d.satuan_nama+'</option>';
  $("[name=popup_satuan]").val(d.satuan_id).trigger('change');
  $("[name=popup_nama_satuan]").val(d.satuan_nama);
  $("[name=popup_id_satuan]").val(d.satuan_id);
  $("[name=satuan_awal]").val(d.satuan_id);
  $("[name=popup_harga]").val(d.harga).trigger('keyup');
});
//   $("#popup_satuan").on('change',function(){
//     var d = $("[name=popup_satuan]").select2('data')[0];
//     // console.log(d);
//     $("[name=popup_nama_satuan]").val(d.text);
//     $("[name=popup_id_satuan]").val(d.id);
//   });

$("[name=carabayar]").on('change',function(){
  var d = $(this).val();
  if(d==2){
    $(".c_uangmuka").removeClass('hide')
  }else{
    $(".c_uangmuka").addClass('hide')
  }
  netto();
});

/*function get_satuan(){
  var d = $("[name=popup_barang]").select2('data')[0];
  $("[name=popup_satuan]").val(d.satuan_id).trigger('change');
  $("[name=popup_harga]").val(d.harga).trigger('keyup');
}*/

$("#btn_popup_simpan").click(function(){
  var d = $("[name=popup_barang]").select2('data')[0];
  // console.log(d.nama);
  var nama_barang = d.nama;
  var id_data     = $("[name=popup_id_table]").val();
  var id_barang   = $("[name=popup_barang]").val();
  var id_satuan   = $("[name=popup_id_satuan]").val();
  // var nama_satuan = $("[name=popup_satuan] :selected").attr('nama');
  var nama_satuan = $("[name=popup_nama_satuan] ").val();
  var satuan_awal = $("[name=satuan_awal] ").val();
  var jumlah      = $("[name=popup_jumlah]").val();
  var harga       = $("[name=popup_harga]").val();
  var diskon      = parseFloat($("[name=popup_diskon]").val()) || 0;
  // var subtotal    = parseFloat(harga*jumlah);
  var total       = parseFloat(harga*jumlah);
  // console.log(nama_satuan);
  if(id_barang != '' && harga != '' && id_satuan != '' && jumlah != ''){
  if(id_data == ''){
    table_barang.row.add(['<div><center></center></div>',
          '<div id="list_barang'+tb_no+'">'+nama_barang+'</div>',
          '<div id="list_harga'+tb_no+'">'+accounting.formatMoney(harga)+'</div>',
          '<div id="list_jumlah'+tb_no+'">'+format_angka(jumlah)+'</div>',
          '<div id="list_satuan'+tb_no+'">'+nama_satuan+'</div>',
          '<div id="list_total'+tb_no+'">'+accounting.formatMoney(total)+'</div>',
          '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+tb_no+')"><i class="fa fa-edit"></i> </button> <button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
          '<input type="hidden" name="tabel_id[]" id="tabel_id'+tb_no+'" value="">'+
          '<input type="hidden" name="tabel_id_tabel[]" id="tabel_id_tabel'+tb_no+'" value="'+tb_no+'">'+
          '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+tb_no+'" value="'+id_barang+'">'+
          '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+tb_no+'" value="'+nama_barang+'">'+
          '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+tb_no+'" value="'+jumlah+'">'+
          '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+tb_no+'" value="'+harga+'">'+
          '<input type="hidden" name="tabel_total[]" id="tabel_total'+tb_no+'" value="'+total+'">'+
          '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+tb_no+'" value="'+id_satuan+'">'+
          '<input type="hidden" name="tabel_satuan_awal[]" id="tabel_satuan_awal'+tb_no+'" value="'+satuan_awal+'">'+
          '<input type="hidden" name="tabel_namasatuan[]" id="tabel_namasatuan'+tb_no+'" value="'+nama_satuan+'">']).draw(false);
  }else{
        // var jumlah      = $("[name=popup_jumlah]").val();
        // var harga       = $("[name=popup_harga]").val();
        // var total     =  $("[name=popup_total]").val();
        var id_satuan   = $("[name=popup_id_satuan]").val();
        var d = $("[name=popup_barang]").select2('data')[0];
        // console.log(d.nama);
        var nama_barang = d.nama;
        $("#list_barang"+id_data).text(nama_barang);
        $("#list_harga"+id_data).text(accounting.formatMoney(harga));
        $("#list_jumlah"+id_data).text(format_angka(jumlah));
        $("#list_satuan"+id_data).text(nama_satuan);
        $("#list_diskon"+id_data).text(accounting.formatMoney(diskon));
        $("#list_total"+id_data).text(accounting.formatMoney(total));

        $("#tabel_id"+id_data).val(id_data);
        $("#tabel_id_tabel"+id_data).val(id_data);
        $("#tabel_idbarang"+id_data).val(id_barang);
        $("#tabel_namabarang"+id_data).val(nama_barang);
        $("#tabel_harga"+id_data).val(harga);        
        $("#tabel_jumlah"+id_data).val(jumlah);
        $("#tabel_idsatuan"+id_data).val(id_satuan);
        $("#tabel_namasatuan"+id_data).val(nama_satuan);
        $("#tabel_diskon"+id_data).val(diskon);
        $("#tabel_total"+id_data).val(total);
        // $("#tabel_subtotal"+id_data).val(subtotal);
  }
  tb_no++;
  get_subtotal();
  /*if(termasuk_pajak==1 && $("input[name=termasuk_pajak]").is(":checked")==false){
        $("input[name=termasuk_pajak]").attr("checked", "checked").trigger("click");
      }else if(termasuk_pajak==1 && $("input[name=termasuk_pajak]").is(":checked")==true){
        get_ppn_subtotal($("input[name=termasuk_pajak]"));
      }else{
        get_subtotal();
      }*/
  $("#modal-form").modal('hide');
  }

})
 function tambah(id){      
  //  console.log(id);
      $('[name=popup_barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('penjualangrosir_get_barang')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        gudang:id
                        //results: data
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        results.push({
                          id:item.barang_id,
                          satuan_id:item.satuan_id,
                          satuan_nama:item.satuan_nama,
                          nama:item.barang_nama,
                          harga:item.harga,
                          kode:item.barang_kode,
                          text:item.barang_kode+' || '+item.barang_nama
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
  //$('[name=popup_barang]').find('option').remove().end();
  //var id_table    = $("#tabel_id"+id).val();
  var id_table    = $("#tabel_id_tabel"+id).val();
  var id_barang   = $("#tabel_idbarang"+id).val();
  var nama_barang = $("#tabel_namabarang"+id).val();
  var harga       = $("#tabel_harga"+id).val();
  var jumlah      = $("#tabel_jumlah"+id).val();
  var id_satuan   = $("#tabel_idsatuan"+id).val();
  var nama_satuan = $("#tabel_namasatuan"+id).val();
  var diskon      = $("#tabel_diskon"+id).val();
  var subtotal    = $("#tabel_subtotal"+id).val();
  var total       = $("#tabel_total"+id).val();
//  console.log(id_satuan);

  $("[name=popup_id_table]").val(id_table);
  // $("[name=popup_barang]").val(id_barang)/*.trigger('change')*/;
  $("[name=popup_barang]").html('<option value="'+id_barang+'">'+nama_barang+'</option>');    
  $("[name=popup_harga]").val(harga);
  $("[name=popup_jumlah]").val(jumlah);
  $("[name=popup_satuan]").val(id_satuan).trigger('change');
  // $("[name=popup_satuan]").html('<option value="'+id_satuan+'">'+nama_satuan+'</option>');
  /*$("[name=popup_id_satuan]").val(id_satuan);
  $("[name=popup_satuan]").val(nama_satuan).trigger('change');*/
  $("[name=popup_diskon]").val(diskon);
  $("[name=popup_total]").val(total);

  $("#help_popup_harga").text(accounting.formatMoney(harga));
  $("#help_popup_jumlah").text(format_angka(jumlah));
  $("#help_popup_diskon").text(accounting.formatMoney(diskon));
  $("#help_popup_total").text(accounting.formatMoney(total));
  get_jum();
  $("#modal-form").modal('show');
  tambah();
}

function get_edit(id){
  $.ajax({
      url: "{{ url('penjualangrosir_get_edit')}} ",
      type: 'post',
      data: {id : id,},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
        if(respon.length > 0){
          for(i in respon){
            table_barang.row.add(['<div><center></center></div>',
              '<div id="list_barang'+respon[i].id+'">'+respon[i].nama_barang+'</div>',
              '<div id="list_harga'+respon[i].id+'">'+accounting.formatMoney(respon[i].harga)+'</div>',
              '<div id="list_jumlah'+respon[i].id+'">'+format_angka(respon[i].jumlah)+'</div>',
              '<div id="list_satuan'+respon[i].id+'">'+respon[i].nama_satuan+'</div>',
              '<div id="list_total'+respon[i].id+'">'+accounting.formatMoney(respon[i].total)+'</div>',
              '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+respon[i].id+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
              '<input type="hidden" name="tabel_id[]" id="tabel_id'+respon[i].id+'" value="'+respon[i].id+'">'+
              '<input type="hidden" name="tabel_id_tabel[]" id="tabel_id_tabel'+respon[i].id+'" value="'+respon[i].id+'">'+
              '<input type="hidden" name="tabel_idlog[]" id="tabel_idlog'+respon[i].id+'" value="'+respon[i].id_log_stok+'">'+
              '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+respon[i].id+'" value="'+respon[i].id_barang+'">'+
              '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+respon[i].id+'" value="'+respon[i].nama_barang+'">'+
              '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+respon[i].id+'" value="'+respon[i].jumlah+'">'+
              '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+respon[i].id+'" value="'+respon[i].harga+'">'+
              '<input type="hidden" name="tabel_diskon[]" id="tabel_diskon'+respon[i].id+'" value="'+respon[i].potongan+'">'+
              '<input type="hidden" name="tabel_total[]" id="tabel_total'+respon[i].id+'" value="'+respon[i].total+'">'+
              '<input type="hidden" name="tabel_subtotal[]" id="tabel_subtotal'+respon[i].id+'" value="'+respon[i].subtotal+'">'+
              '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+respon[i].id+'" value="'+respon[i].id_satuan+'">'+
              '<input type="hidden" name="tabel_namasatuan[]" id="tabel_namasatuan'+respon[i].id+'" value="'+respon[i].nama_satuan+'">'
              ]).draw(false);
          }
          get_jum();
          get_subtotal();
          netto();
        }
        
      }
    })
}

function hapus(id){
  table_barang.row(id.parents('tr')).remove().draw();
    get_jum();
    get_subtotal();
    netto();
}

function get_jum(){
  var jumlah  = parseFloat($("[name=popup_jumlah]").val())  || 0;
  var harga   = parseFloat($("[name=popup_harga]").val())   || 0;
  var diskon  = parseFloat($("[name=popup_diskon]").val())  || 0;
  var total   = parseFloat((jumlah*harga)-diskon);
  $("[name=popup_total]").val(total);
  $("#help_popup_total").text(accounting.formatMoney(total));

}

function get_subtotal(this_){
  var jum = parseFloat(0);
  var val = parseFloat(0);
    $('input[id^="tabel_total"]').each(function() {
              // alert($(this).val());
              var val = ($(this).val());
              jum += parseFloat(val);
              
            });
   
    $("#td_total").text(accounting.formatMoney(jum));
    $("[name=td_total]").val(jum);
    // $("#help_popup_totaldiskon").text(accounting.formatMoney(diskon));
    // $("[name=td_diskon]").val(potongan);
    // $("#help_popup_totaldiskon").text(accounting.formatMoney(potongan));
    netto();
}

/*function get_ppn_subtotal(){  
  var is_checked = this_.is(":checked");
    var temp_tagihan = $("input[name=td_total]").val()
    var temp_sub_total = 0;

    if(is_checked){
      // temp_sub_total = (100/110)*temp_tagihan;
      temp_sub_total = 0;
      $('input[name^="tabel_jumlah[]"]').each(function(id){
        var input_jumlah = $(this);
        var input_harga = $('input[name^="tabel_harga[]"]');

        var jumlah = input_jumlah[0].value;
        var harga = (input_harga[id]).value;
        var temp_harga = (100/110)*harga;
        temp_sub_total += (parseFloat(temp_harga)*jumlah);
      });

      $("#td_total").text(accounting.formatMoney(temp_sub_total));
      $("input[name=td_total]").val(temp_sub_total);

      $("select[name=pajak]").val(1).change();
      netto();
    }else{
      // temp_sub_total = $("input[name=td_netto]").val();
      // $("#td_total").text(accounting.formatMoney(temp_sub_total));
      // $("input[name=td_total]").val(temp_sub_total);

      

      get_subtotal();

    }
}*/

function netto(){
  var total   = parseFloat($("[name=td_total]").val()) || 0;
  var diskon  = parseFloat($("[name=td_diskon]").val()) || 0;
  var ongkir  = parseFloat($("[name=td_ongkir]").val()) || 0;
  var netto   = parseFloat(0);
  var ppn     = parseFloat(0);
  var uangmuka= parseFloat($("[name=td_uangmuka]").val()) || 0;
  var bayar   = parseFloat($("[name=td_bayar]").val()) || 0;
  var carabayar = $("[name=carabayar]").val();

  if($('select[name=pajak]').val()==1){
      ppn = parseFloat((total)*(10/100));
    }
    // console.log(ppn);
  netto = (total+ppn+ongkir)-diskon-uangmuka;
  netto_bayar = netto-(bayar+diskon);
    $("[name=td_potongan]").val(diskon);
    $("#help_popup_potongan").text(accounting.formatMoney(diskon));

    $("[name=td_pajak]").val(ppn);
    $("#help_popup_pajak").text(accounting.formatMoney(ppn));

    $("[name=td_uangmuka]").val(uangmuka);
    $("#help_popup_uangmuka").text(accounting.formatMoney(uangmuka));

    $("[name=td_ongkir]").val(ongkir);
    $("#help_popup_ongkir").text(accounting.formatMoney(ongkir));

    if(carabayar == '1'){
    $("[name=td_bayar]").val(bayar);
    $("[name=td_netto]").val(netto_bayar);
    $("#td_netto").text(accounting.formatMoney(netto_bayar));
    }else{
    $("[name=td_netto]").val(netto);
    $("#td_netto").text(accounting.formatMoney(netto));
    }
    // console.log("carabayar= "+carabayar+" diskon= "+diskon+" ongkir= "+ongkir+" bayar= "+bayar+" netto= "+netto+" netto_bayar"+netto_bayar+" uangmuka= "+uangmuka+" subtotal= "+total);

};
</script>

@endsection

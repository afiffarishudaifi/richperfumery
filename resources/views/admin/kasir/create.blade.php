<?php $hal = "kasir"; ?>
@extends('layouts.admin.master')
@section('title', 'Penjualan')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('public/selectize/css/selectize.css')}}">
<link rel="stylesheet" href="{{asset('public/selectize/css/selectize.bootstrap3.css')}}">
<link rel="stylesheet" href="{{asset('public/admin/plugins/iCheck/all.css')}}">
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
    Penjualan
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
          <form role="form" class="form_belanja" action="{{url('kasir_simpan')}}" method="post">
            <input type="hidden" name="id_kasir" value="{{$data['data']['id_kasir']}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="lama_tanggalfaktur" value="{{($data['data']['tanggal_tempo'] == '') ? date('d-m-Y'):$data['data']['tanggal_tempo']}}">
            <input type="hidden" name="lama_nomor" value="{{($data['data']['nomor'] == '') ? $data['no_auto']:$data['data']['nomor']}}">
            <input type="hidden" name="lama_idgudang" value="{{$data['data']['id_gudang']}}">
            <div class="box-body">
              <div class="form-group col-md-12 " style="border:1px solid black;">
                <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="exampleInputPassword1">Tanggal Input</label>
                          <input class="form-control datepicker" type="text" name="tanggal" placeholder="Tanggal" value="{{($data['data']['tanggal'] == '') ? date('d-m-Y'):$data['data']['tanggal']}}" required readonly />
                      </div>
                      <div class="form-group">
                        <label>Tanggal Jatuh Tempo</label>
                        <input class="form-control datepicker" type="text" name="tanggal_tempo" placeholder="Tanggal Tempo" value="{{($data['data']['tanggal_tempo'] == '') ? date('d-m-Y'):$data['data']['tanggal_tempo']}}" required/>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Nama Pelanggan</label>
                          <select name="id_pelanggan" class="form-control selectize" style="width: 100%;" required="">
                              <option value=""> --- Pilih --- </option>
                              @foreach($data['pelanggan'] as $d)
                                  <option value="{{$d->id}}" {{($data['data']['id_pelanggan']==$d->id) ? "selected":""}}>{{$d->nama}}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Telp</label>
                        <input type="text"  name="telp_pelanggan" class="form-control number-only" value="{{$data['data']['telp_pelanggan']}}" placeholder="08xxxxxxxxx">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Alamat</label>
                        <input type="text"  name="alamat_pelanggan" class="form-control" value="{{$data['data']['alamat_pelanggan']}}" placeholder="JL. Example">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Status Pelanggan</label>
                        <select class="form-control select2" name="status_pelanggan" style="width: 100%; height: 100%;">
                           <option value="1">Biasa</option>
                           <option value="2">Member</option>
                           <option value="3">Karyawan</option>
                           <option value="4">Reseller</option>
                        </select>
                      </div>
                      <div class="form-group" id="tr_nomorpelanggan">
                        <label for="exampleInputPassword1">No. Member</label>
                        <input type="text"  name="nomor_pelanggan" class="form-control" value="{{$data['data']['telp_pelanggan']}}" placeholder="xxxxx xxxx xxxx">
                      </div>
                      
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                        <label>Tanggal Faktur</label>
                        <input class="form-control datepicker" type="text" name="tanggal_faktur" placeholder="Tanggal Faktur" value="{{($data['data']['tanggal_faktur'] == '') ? date('d-m-Y'):$data['data']['tanggal_faktur']}}" required/>
                      </div>
                      <div class="form-group" @php if($data['data']['id_kasir'] == '') { echo 'style="display: none;"'; } @endphp>
                        <label for="exampleInputPassword1">Nomor Faktur</label>
                        <input type="text"  name="nomor" class="form-control" value="{{($data['data']['nomor'] == '') ? $data['no_auto']:$data['data']['nomor']}}" {{(Auth::user()->group_id == 1)?"":"readonly"}}>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Gudang</label>
                        <select class="select2" name="gudang" style="width: 100%;"> 
                          @foreach($data['gudang'] as $d)
                            <option value="{{$d->id_gudang}}" nama="{{$d->nama_gudang}}" alamat="{{$d->alamat_gudang}}" {{($data['data']['id_gudang']==$d->id_gudang) ? "Selected":""}}>{{$d->nama_gudang}}</option>
                          @endforeach                     
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Keterangan</label>
                        <textarea class="form-control" rows="2" name="keterangan">{{$data['data']['keterangan']}}</textarea>
                      </div>
                  </div>
                </div><!-- end : row -->
              </div>

              <div class="form-group col-md-12" style="border:1px solid black;">
               <h3>Detail Produk</h3>
               <hr/>
               <a href="javascript:;" class="btn btn-sm btn-success btn_tambah" id="btn_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah Produk</a>
               <a href="javascript:;" class="btn btn-sm btn-primary btn_tambah_barang" id="btn_tambah_barang"><span class="glyphicon glyphicon-plus"></span> Barang Tambahan</a>
               <br/> <br/>
               <div class="table-responsive">
               <table class="table table-bordered table-hover table-striped table-barang" id="table_barang">
                <thead>
                  <tr>
                    <th width="3%">No.</th>
                    <th width="32%">Nama Produk</th>
                    <th class="20%">Harga</th>
                    <th class="15%">Jumlah</th>
                    <th class="20%">Total</th>
                    <th width="10%"></th>
                  </tr>
                </thead>
                <tbody>

                </tbody>
              </table>
            </div>

            </div>


        
        <div class=" col-md-6 col-sm-12 m-l-5">
          <div class="table-responsive">
           <table class="table table-bordered table-form table-totalbayar">
            <tbody>
              <tr class="active">
                <!-- <td>Paper Bag</td> -->
                <td>Total Penjualan</td>
                <td class="text-right">
                <label class="checkbox-inline checkbox-right">
                  
                </label>
                <!--<label id="wrapper-promo" style="display: none;"><input type="checkbox" class="flat-red" name="td_pilihpromo" {{($data['data']['td_statuspromo']=='YA')?'checked':''}}> <span class="label label-success">Paket Promo<span></label>-->
                <input type="hidden" name="td_statuspromo" value="{{$data['data']['td_statuspromo']}}">
                </td>
              </tr>
             <tr>
                <td width="30%">Sub Total</td>
                <td><input type="hidden" name="td_total" value="{{($data['data']['td_subtotal']== '') ? '0':$data['data']['td_subtotal']}}" readonly="">
                    <p class="help-block pull-right" id="td_total">Rp 0</p>
                </td>
              </tr>
              {{-- <tr>
                <td>Paket</td>
                <td>
                    <select class="form-control select2" name="viapromo" style="width: 100%;">
                      <option value="TIDAK">TIDAK</option>
                      <option value="YA">YA</option>
                    </select>
                </td>
              </tr> --}}
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
              <tr>
                <td id="row_metodebayar">Metode bayar</td>
                <td><select class="form-control select2" name="viabayar" style="width: 100%;">
                       <?php
                      foreach ($data['pembayaran'] as $d) {
                        $selected = ($data['data']['metodebayar'] == $d->id) ? "selected":'';
                        echo "<option value='".$d->id."' ".$selected."> ".$d->nama." </option>";
                      }?>
                    </select>
                    &nbsp;
                    <input type="text" class="form-control number-only text-right hidden" name="total_viabayar" id="total_viabayar">
                    <p class="help-block pull-right hidden" id="help_popup_ttlviabayar"></p>
                    <select class="form-control select hidden" name="viabayar_hide" style="width: 100%;">
                       <?php
                      foreach ($data['pembayaran'] as $d) {
                        $selected = ($data['data']['metodebayar2'] == $d->id) ? "selected":'';
                        echo "<option value='".$d->id."' ".$selected."> ".$d->nama." </option>";
                      }?>
                    </select>
                    <input type="text" class="form-control number-only text-right hidden" name="total_viabayar_hide">
                </td>
                <td width="2%" id="td_addmetodebayar"><button type="button" class="btn btn-xs btn-success" onclick="tambah_metodebayar()"><i class="fa fa-plus"></i></button></td>
              </tr>
              <!-- <tr>
                <td>Metode bayar</td>
                <td><select class="form-control select2" name="viabayar" style="width: 100%;">
                       <?php
                      foreach ($data['pembayaran'] as $d) {
                        $selected = ($data['data']['metodebayar'] == $d->id) ? "selected":'';
                        echo "<option value='".$d->id."' ".$selected."> ".$d->nama." </option>";
                      }?>
                    </select>
                </td>
              </tr> -->
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
          <a href="{{url('kasir')}}" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
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
@include('admin.kasir.form')
@endsection


@section('js')
<!-- iCheck -->
<script src="{{asset('public/admin/plugins/iCheck/icheck.min.js')}}"></script>
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="{{asset('public/js/touchspin.min.js')}}"></script>
<script src="{{asset('public/selectize/js/standalone/selectize.min.js')}}"></script>


<script type="text/javascript">

  var tb_no = parseInt(1000);
  var table_barang;
  var id_kasir = '{{$data['data']['id_kasir']}}';
  var id_pelanggan = '{{$data['data']['id_pelanggan']}}';
  var nama_pelanggan = '{{$data['data']['nama_pelanggan']}}';
  var id_gudang = '{{$data['data']['id_gudang']}}';
  var nama_gudang = '{{$data['data']['nama_gudang']}}';
  var metodebayar2 = '{{$data['data']['metodebayar2']}}';
  var total_metodebayar = '{{$data['data']['total_metodebayar']}}';
  var total_metodebayar2 = '{{$data['data']['total_metodebayar2']}}';

  $(document).ready(function () {
    table_barang = $("#table_barang").DataTable({
      "paging": false
    });
    table_barang.on( 'order.dt search.dt', function () {
            table_barang.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
    }).draw();

    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass   : 'iradio_flat-blue'
    })

    if(id_kasir != ''){
      if(id_pelanggan != ''){
        $("[name=id_pelanggan]").val(id_pelanggan).trigger('change');
        //$("[name=id_pelanggan]").html('<option value="'+id_pelanggan+'">'+nama_pelanggan+'</option>');
        /*var $popupelanggan = $("select[name=id_pelanggan]").selectize();
        var popupelanggan = $popupelanggan[0].selectize;
        popupelanggan.setValue(id_pelanggan);
        console.log(id_pelanggan);*/
      }
      /*if(metodebayar2 != ''){
        tambah_metodebayar();
      }*/

      if(metodebayar2 != ''){
      tambah_metodebayar();
      $("[name=total_viabayar]").val(parseFloat(total_metodebayar));
      $("[name=total_viabayar2]").val(parseFloat(total_metodebayar2));
      }else{
        hapusRow();
      }

      uangmuka();
      get_edit(id_kasir);
      get_subtotal();
      get_checkpromo();
      netto();
      
    }

    

    $("#tr_nomorpelanggan").hide();
        

    $(".touchspin-step").TouchSpin({
        min: 0,
        max: 100,
        step: 1
    });

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

    $('.selectize').selectize({
        create: true,
        sortField: 'text'
    });

 

  // $('[name=popup_produk]').select2({
  $('#popup_produk_select').select2({
    placeholder: "--- Pilih ---",
    ajax: {
        url: '{{url('kasir_get_produk')}}',
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
                  id:item.produk_id,
                  kode:item.produk_kode,
                  nama:item.produk_nama,
                  harga:item.produk_harga,
                  poin:item.produk_poin,
                  diskon_promo:item.diskon_promo,
                  text:item.produk_nama,
                  id_tipe: item.id_tipe
                });
              });
              return{
                results:results
              };
        },
        cache: true
      }        
  });

  $('[name=popup_barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('kasir_get_barang')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        var text_item = item.barang_kode+" || "+item.barang_nama+" || "+item.barang_alias;
                        if(item.barang_alias === null || item.barang_alias === "" || item.barang_alias === 0){
                          text_item = item.barang_kode+" || "+item.barang_nama;
                        }
                        results.push({
                          id:item.barang_id,
                          satuan_id:item.satuan_id,
                          satuan_nama:item.satuan_satuan,
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


$("#btn_tambah").click(function(){
    $('[name=popup_id_table]').val('');
    $('[name=popup_status]').val('1');
    $('[name=popup_produk]').html('<option></option>');
    $('[name=popup_barang]').html('<option></option>');
    $('[name=popup_harga]').val('');
    $('[name=popup_jumlah]').val('');
    $('[name=popup_total]').val('');
    $("[name=popup_satuan]").val('').trigger('change');
    $('#help_popup_jumlah').text('');
    $('#help_popup_harga').text('');
    $('#help_popup_total').text('');
    $('#group-barang').addClass('hide');
    $('#group-satuan').addClass('hide');
    $('#group-produk').removeClass('hide');
    $('[name=popup_poin]').val('0');

    /*$('#popup_namaproduk').addClass('hide');
    $('#popup_produk_input').addClass('hide');
    $('#popup_produk_select').removeClass('hide');*/
    $('#modal-form').modal('show');
    
});

$("#btn_tambah_barang").click(function(){
    $('[name=popup_id_table]').val('');
    $('[name=popup_status]').val('2');
    $('[name=popup_produk]').html('<option></option>');
    $('[name=popup_barang]').html('<option></option>');
    $('[name=popup_harga]').val('');
    $('[name=popup_jumlah]').val('');
    $('[name=popup_total]').val('');
    $("[name=popup_satuan]").val('').trigger('change');
    $('#help_popup_jumlah').text('');
    $('#help_popup_harga').text('');
    $('#help_popup_total').text('');
    $('#group-barang').removeClass('hide');
    $('#group-satuan').removeClass('hide');
    $('#group-produk').addClass('hide');
    $('[name=popup_poin]').val('0');
    $('#modal-form').modal('show');
});

$("#btn_tambah_pelanggan").click(function(){
    $('[name=popup_id_table_pelanggan]').val('');
    $('[name=pelanggan_nama]').val('');
    $('[name=pelanggan_telp]').val('');
    $('[name=pelanggan_alamat]').val('');
    $('#modal-form-pelanggan').modal('show');
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
$("[name=popup_produk]").on('change', function(){
  var d = $("[name=popup_produk]").select2('data')[0];
  var poin = parseFloat(d.poin) || 0;
  $("[name=popup_harga]").val(d.harga).trigger('keyup');
  $("[name=popup_poin]").val(poin);
});
$("[name=popup_barang]").on('change', function(){
  var d = $("[name=popup_barang]").select2('data')[0];
  $("[name=popup_harga]").val(parseFloat(0)).trigger('keyup');
  $("[name=popup_satuan]").val(d.satuan_id).trigger('change');
  $("[name=popup_poin]").val(parseFloat(0));
});
$("[name=carabayar]").on('change',function(){
  var d = $(this).val();
  if(d==2){
    $(".c_uangmuka").removeClass('hide')
  }else{
    $(".c_uangmuka").addClass('hide')
  }
  netto();
});

$("[name=td_diskon]").on('keyup',function(){
  var d = $(this).val();
  $("#help_popup_totaldiskon").text(accounting.formatMoney(d));
  netto();
});

$("[name=total_viabayar]").on('keyup',function(){
//   var d = $(this).val();
//   $("#help_popup_ttlviabayar").text(accounting.formatMoney(d));
  netto('1');
});

/*$("[name=total_viabayar2]").on('keyup',function(){
  var d = $(this).val();
  $("#help_popup_ttlviabayar2").text(accounting.formatMoney(d));
  netto();
});*/

$("[name=status_pelanggan]").on('change',function(){
  var d = $(this).val();
  if(d==2){
    $("#tr_nomorpelanggan").show();
  }else{
    $("#tr_nomorpelanggan").hide();
  }
});

$("[name=tanggal_faktur]").on('change',function(){
    var tanggal = $(this).val();
    var gudang  = $("[name=gudang]").val(); 
    var gudang_lama   = $("[name=lama_idgudang]").val();
    var tanggal_lama  = $("[name=lama_tanggalfaktur]").val();
    var nomor_lama    = $("[name=lama_nomor]").val(); 
    $.ajax({
      url: "{{ url('kasir_get_nota')}}",
      type: 'post',
      data: {tanggal:tanggal,gudang:gudang},
      headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success: function(respon){
        if(id_kasir != ''){
          if(tanggal == tanggal_lama && gudang == gudang_lama){
            $("[name=nomor]").val(nomor_lama);
          }else{
            $("[name=nomor]").val(respon).trigger("change");
          }
        }else{
          $("[name=nomor]").val(respon).trigger("change");        
        }
      }
    })
})

$("[name=gudang]").on('change',function(){
    var tanggal = $("[name=tanggal_faktur]").val();
    var gudang  = $(this).val();
    var gudang_lama   = $("[name=lama_idgudang]").val();
    var tanggal_lama  = $("[name=lama_tanggalfaktur]").val();
    var nomor_lama    = $("[name=lama_nomor]").val(); 
    $.ajax({
      url: "{{ url('kasir_get_nota')}}",
      type: 'post',
      data: {tanggal:tanggal,gudang:gudang},
      headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success: function(respon){
        // $("[name=nomor]").val(respon).trigger("change")
        if(id_kasir != ''){
          if(tanggal == tanggal_lama && gudang == gudang_lama){
            $("[name=nomor]").val(nomor_lama);
          }else{
            $("[name=nomor]").val(respon).trigger("change");
          }
        }else{
          $("[name=nomor]").val(respon).trigger("change");        
        }
      }
    })
})

$("[name=id_pelanggan]").on('change',function(){
  var id = $(this).val();
  $.ajax({
      url: "{{ url('kasir_attr_pelanggan')}} ",
      type: 'post',
      data: {id : id,},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
          var alamat = "";
          var telp = "";
          var nomor = "";
          var status = "";
          if(respon[0].alamat != ""){
            var alamat = respon[0].alamat;
          }
          if(respon[0].telp != ""){
            var telp = respon[0].telp;
          }
          if(respon[0].nomor != ""){
            var nomor = respon[0].nomor;
          }
          if(respon[0].status != ""){
            var status = respon[0].status;
          }
          $("[name=alamat_pelanggan]").val(alamat);
          $("[name=telp_pelanggan]").val(telp);
          $("[name=nomor_pelanggan]").val(nomor);
          $("[name=status_pelanggan]").val(status).trigger('change');  
             
      }

  })
 
})

$("[name=td_pilihpromo]").on('ifChanged', function(){
  if(this.checked){
    $("[name=td_statuspromo]").val('YA');
  }else{
    $("[name=td_statuspromo]").val('TIDAK');
  }
  get_subtotal();

})

$("#btn_popup_simpan").click(function(){
  var d = $("[name=popup_produk]").select2('data')[0];
  var b = $("[name=popup_barang]").select2('data')[0];
  var id_data     = $("[name=popup_id_table]").val();
  var store_id_data = $("[name=store_id_table]").val();
  var id_produk   = $("[name=popup_produk]").val();
  var id_barang   = $("[name=popup_barang]").val();
  var id_satuan   = $("[name=popup_satuan]").val();
  var nama_satuan = $("[name=popup_satuan] :selected").attr('satuan');
  var jumlah      = $("[name=popup_jumlah]").val();
  var harga       = $("[name=popup_harga]").val();
  var poin        = $("[name=popup_poin]").val();
  var total       = parseFloat(harga*jumlah);
  var idproduk_sebelum = $("[name=popup_produk_sebelum]").val();

  var status      = $("[name=popup_status]").val();
  if(id_data == ''){
    if(status == '1'){
      var id_tipe     = d.id_tipe;
      var barang_produk     = id_produk;
      var nama_produk       = d.nama;
      if(harga == 0){
        var potongan_satuan   = parseFloat(0);
      }else{
        var potongan_satuan   = d.diskon_promo;
      }
      // var potongan_total    = 0;
      // if (jumlah > 1) potongan_total = parseFloat(potongan_satuan*jumlah);
      var potongan_total      = parseFloat(potongan_satuan*jumlah);
    }else if(status == '2'){
      var barang_produk = id_barang;
      if(b.alias === null || b.alias === "" || b.alias === 0){
        var nama_barang = b.kode+" || "+b.nama;
        var kode_barang = b.kode;
        var alias_barang= b.alias;
      }else{
        /*var nama_barang = b.kode+" || "+b.nama+" || "+b.alias;*/
        var nama_barang = b.kode+" || "+b.nama;
        var kode_barang = b.kode;
        var alias_barang= b.alias;
      }
    }
  }else{
      if(status == '1'){
        var barang_produk = id_produk;
        var cek_produk = d.nama;
        if(cek_produk == undefined || cek_produk == 0 || cek_produk == '' ){
          var nama_produk       = $("[name=popup_produk] :selected").attr('nama');
          if(harga == 0){
              var potongan_satuan   = parseFloat(0);
          }else{
              var potongan_satuan   = $("[name=popup_produk] :selected").attr('diskon_promo');
          }
          id_tipe               = $("[name=popup_produk] :selected").attr('id_tipe');
        }else{
          var nama_produk       = d.nama;
          if(harga == 0){
            var potongan_satuan   = parseFloat(0);
          }else{
            var potongan_satuan   = d.diskon_promo;
          }
        }
        // var potongan_total    = 0;
        // if (jumlah > 1) potongan_total = parseFloat(potongan_satuan*jumlah);
        var potongan_total      = parseFloat(potongan_satuan*jumlah);
      }else{
        var cek_barang = b.nama;
        if(cek_barang == undefined || cek_barang == 0 || cek_barang == ''){
          var nama = $("[name=popup_barang] :selected").attr('nama');
          var kode = $("[name=popup_barang] :selected").attr('kode');
          var alias = $("[name=popup_barang] :selected").attr('alias');
          console.log(nama)
            if(alias === null || alias === "" || alias === 0){
              var nama_barang = kode+" || "+nama;
              var kode_barang = $("[name=popup_barang] :selected").attr('kode');
              var alias_barang = $("[name=popup_barang] :selected").attr('alias');
            }else{
              /*var nama_barang = kode+" || "+nama+" || "+alias;*/
              var nama_barang = kode+" || "+nama;
              var kode_barang = $("[name=popup_barang] :selected").attr('kode');
              var alias_barang = $("[name=popup_barang] :selected").attr('alias');
            }
        }else{
          var barang_produk = id_barang;
          if(b.alias === null || b.alias === "" || b.alias === 0){
            var nama_barang = b.kode+" || "+b.nama;
            var kode_barang = b.kode;
            var alias_barang= b.alias;
          }else{
            /*var nama_barang = b.kode+" || "+b.nama+" || "+b.alias;*/
            var nama_barang = b.kode+" || "+b.nama;
            var kode_barang = b.kode;
            var alias_barang= b.alias;
          }
        }
      }
  }

  if(barang_produk != '' && harga != '' && jumlah != ''){
  if(id_data == ''){
    if(status == '1'){
    table_barang.row.add(['<div><center></center></div>',
          '<div id="list_produk'+tb_no+'">'+nama_produk+'</div>',
          '<div id="list_harga'+tb_no+'" class="text-right">'+accounting.formatMoney(harga)+'</div>',
          '<div id="list_jumlah'+tb_no+'" class="text-right">'+format_angka(jumlah)+" Pcs"+'</div>',
          /*'<div id="list_satuan'+tb_no+'">'+nama_satuan+'</div>',*/
          '<div id="list_total'+tb_no+'" class="text-right">'+accounting.formatMoney(total)+'</div>',
          '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+tb_no+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
          '<input type="hidden" name="tabel_id[]" id="tabel_id'+tb_no+'" value="">'+
          '<input type="hidden" name="tabel_id_tabel[]" id="tabel_id_tabel'+tb_no+'" value="'+tb_no+'">'+
          '<input type="hidden" name="tabel_idproduk[]" id="tabel_idproduk'+tb_no+'" value="'+id_produk+'">'+
          '<input type="hidden" name="tabel_namaproduk[]" id="tabel_namaproduk'+tb_no+'" value="'+nama_produk+'">'+
          '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+tb_no+'" value="0">'+
          '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+tb_no+'" value="0">'+
          '<input type="hidden" name="tabel_kodebarang[]" id="tabel_kodebarang'+tb_no+'" value="0">'+
          '<input type="hidden" name="tabel_aliasbarang[]" id="tabel_aliasbarang'+tb_no+'" value="0">'+
          '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+tb_no+'" value="9">'+
          '<input type="hidden" name="tabel_namasatuan[]" id="tabel_namasatuan'+tb_no+'" value="Pcs">'+
          '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+tb_no+'" id_tipe="'+id_tipe+'" value="'+jumlah+'">'+
          '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+tb_no+'" value="'+harga+'">'+
          '<input type="hidden" name="tabel_total[]" id="tabel_total'+tb_no+'" value="'+total+'">'+
          '<input type="hidden" name="tabel_poin[]" id="tabel_poin'+tb_no+'" value="'+poin+'">'+
          '<input type="hidden" name="tabel_status[]" id="tabel_status'+tb_no+'" value="'+status+'">'+
          '<input type="hidden" name="tabel_potongan[]" id="tabel_potongan'+tb_no+'" value="'+potongan_satuan+'">'+
          '<input type="hidden" name="tabel_potongan_total[]" qty="'+jumlah+'" id_tipe="'+id_tipe+'" id="tabel_potongan_total'+tb_no+'" value="'+potongan_total+'">'+
          '<input type="hidden" name="tabel_tipe[]" id="tabel_tipe'+tb_no+'" value="'+id_tipe+'">'+
          '<input type="hidden" name="tabel_idproduk_sebelum[]" id="tabel_idproduk_sebelum'+tb_no+'" value="'+id_produk+'">']).draw(false);
    }else{
    table_barang.row.add(['<div><center></center></div>',
          '<div id="list_barang'+tb_no+'">'+nama_barang+'</div>',
          '<div id="list_harga'+tb_no+'" class="text-right">'+accounting.formatMoney(harga)+'</div>',
          '<div id="list_jumlah'+tb_no+'" class="text-right">'+format_angka(jumlah)+" "+nama_satuan+'</div>',
          '<div id="list_total'+tb_no+'" class="text-right">'+accounting.formatMoney(total)+'</div>',
          '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table_barang('+tb_no+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
          '<input type="hidden" name="tabel_id[]" id="tabel_id'+tb_no+'" value="">'+
          '<input type="hidden" name="tabel_id_tabel[]" id="tabel_id_tabel'+tb_no+'" value="'+tb_no+'">'+
          '<input type="hidden" name="tabel_idproduk[]" id="tabel_idproduk'+tb_no+'" value="0">'+
          '<input type="hidden" name="tabel_namaproduk[]" id="tabel_namaproduk'+tb_no+'" value="0">'+
          '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+tb_no+'" value="'+id_barang+'">'+
          '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+tb_no+'" value="'+nama_barang+'">'+
          '<input type="hidden" name="tabel_kodebarang[]" id="tabel_kodebarang'+tb_no+'" value="'+kode_barang+'">'+
          '<input type="hidden" name="tabel_aliasbarang[]" id="tabel_aliasbarang'+tb_no+'" value="'+alias_barang+'">'+
          '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+tb_no+'" value="'+id_satuan+'">'+
          '<input type="hidden" name="tabel_namasatuan[]" id="tabel_namasatuan'+tb_no+'" value="'+nama_satuan+'">'+
          '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+tb_no+'" id_tipe="" value="'+jumlah+'">'+
          '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+tb_no+'" value="'+harga+'">'+
          '<input type="hidden" name="tabel_total[]" id="tabel_total'+tb_no+'" value="'+total+'">'+
          '<input type="hidden" name="tabel_poin[]" id="tabel_poin'+tb_no+'" value="'+poin+'">'+
          '<input type="hidden" name="tabel_status[]" id="tabel_status'+tb_no+'" value="'+status+'">'+
          '<input type="hidden" name="tabel_potongan[]" id="tabel_potongan'+tb_no+'" value="0">'+
          '<input type="hidden" name="tabel_potongan_total[]" qty="'+jumlah+'" id_tipe="" id="tabel_potongan_total'+tb_no+'" value="0">'+
          '<input type="hidden" name="tabel_tipe[]" id="tabel_tipe'+tb_no+'" value="">'+
          '<input type="hidden" name="tabel_idproduk_sebelum[]" id="tabel_idproduk_sebelum'+tb_no+'" value="0">']).draw(false);
    }
  }else{
      if(status == '1'){
        $("#list_produk"+id_data).text(nama_produk);
        $("#list_harga"+id_data).text(accounting.formatMoney(harga));
        $("#list_jumlah"+id_data).text(format_angka(jumlah)+" Pcs");
        $("#list_total"+id_data).text(accounting.formatMoney(total));

        $("#tabel_id"+id_data).val(store_id_data);
        $("#tabel_id_tabel"+id_data).val(id_data);
        $("#tabel_idproduk"+id_data).val(id_produk);
        $("#tabel_namaproduk"+id_data).val(nama_produk);
        $("#tabel_idbarang"+id_data).val(id_barang);
        $("#tabel_namabarang"+id_data).val(nama_barang);
        $("#tabel_kodebarang"+id_data).val(kode_barang);
        $("#tabel_aliasbarang"+id_data).val(alias_barang);
        $("#tabel_harga"+id_data).val(harga);        
        $("#tabel_jumlah"+id_data).val(jumlah);
        $("#tabel_idsatuan"+id_data).val(id_satuan);
        $("#tabel_namasatuan"+id_data).val(nama_satuan);
        $("#tabel_total"+id_data).val(total);
        $("#tabel_poin"+id_data).val(poin);
        $("#tabel_idproduk_sebelum"+id_data).val(idproduk_sebelum);
        
        //Promo
        $("#tabel_potongan"+id_data).val(potongan_satuan);
        $("#tabel_potongan_total"+id_data).val(potongan_total);
        $("#tabel_potongan_total"+id_data).attr('qty',jumlah);
        $("#tabel_potongan_total"+id_data).attr('id_tipe',id_tipe);
        $("#tabel_tipe"+id_data).val(id_tipe);
      }else{
        $("#list_barang"+id_data).text(nama_barang);
        $("#list_harga"+id_data).text(accounting.formatMoney(harga));
        $("#list_jumlah"+id_data).text(format_angka(jumlah)+" "+nama_satuan);
        $("#list_total"+id_data).text(accounting.formatMoney(total));

        $("#tabel_id"+id_data).val(store_id_data);
        $("#tabel_id_tabel"+id_data).val(id_data);
        $("#tabel_idproduk"+id_data).val(id_produk);
        $("#tabel_namaproduk"+id_data).val(nama_produk);
        $("#tabel_idbarang"+id_data).val(id_barang);
        $("#tabel_namabarang"+id_data).val(nama_barang);
        $("#tabel_kodebarang"+id_data).val(kode_barang);
        $("#tabel_aliasbarang"+id_data).val(alias_barang);
        $("#tabel_harga"+id_data).val(harga);        
        $("#tabel_jumlah"+id_data).val(jumlah);
        $("#tabel_idsatuan"+id_data).val(id_satuan);
        $("#tabel_namasatuan"+id_data).val(nama_satuan);
        $("#tabel_total"+id_data).val(total);
        $("#tabel_poin"+id_data).val(poin);
        $("#tabel_idproduk_sebelum"+id_data).val(idproduk_sebelum);

        //promo
        $("#tabel_potongan"+id_data).val(0);
        $("#tabel_potongan_total"+id_data).val(0);
        $("#tabel_potongan_total"+id_data).attr('qty',jumlah);
        $("#tabel_potongan_total"+id_data).attr('id_tipe','');
        $("#tabel_jumlah"+id_data).attr('id_tipe','');
        $("#tabel_tipe"+id_data).val('');
      }
  }
  tb_no++;
  get_subtotal();
  get_checkpromo();
  $("#modal-form").modal('hide');
  }

})

function edit_table(id){
  var status = $("#tabel_status"+id).val();
  var cek_id    = $("#tabel_id_tabel"+id).val();
  if(cek_id == '' ){
  var id_table    = $("#tabel_id"+id).val();
  }else{
  var id_table    = cek_id;
  }
  var id_produk   = $("#tabel_idproduk"+id).val();
  var nama_produk = $("#tabel_namaproduk"+id).val();
  var nama_satuan = $("#tabel_namasatuan"+id).val();
  var harga       = $("#tabel_harga"+id).val();
  var jumlah      = $("#tabel_jumlah"+id).val();
  var total       = $("#tabel_total"+id).val();
  var poin        = $("#tabel_poin"+id).val();
  var idproduk_sebelum = $("#tabel_idproduk_sebelum"+id).val();

  //promo
  var potongan        = $("#tabel_potongan"+id).val();
  var potongan_total  = $("#tabel_potongan_total"+id).val();
  var id_tipe         = $("#tabel_tipe"+id).val();
  /*$('#popup_namaproduk').removeClass('hide');
  $('#popup_produk_input').removeClass('hide');
  $('#popup_produk_select').addClass('hide');*/

  $("[name=popup_id_table]").val(id_table);
  $("[name=store_id_table]").val($("#tabel_id"+id).val())
  $("[name=popup_produk]").html('<option value="'+id_produk+'" id_tipe="'+id_tipe+'" nama="'+nama_produk+'" diskon_promo="'+potongan+'">'+nama_produk+'</option>');
  $("[name=popup_harga]").val(harga);
  $("[name=popup_jumlah]").val(jumlah);
  $("[name=popup_total]").val(total); 
  $("[name=popup_poin]").val(poin);
  $("[name=popup_produk_sebelum]").val(idproduk_sebelum);

  $("#help_popup_harga").text(accounting.formatMoney(harga));
  $("#help_popup_jumlah").text(format_angka(jumlah));
  $("#help_popup_total").text(accounting.formatMoney(total));
  $('#group-barang').addClass('hide');
  $('#group-satuan').addClass('hide');
  $('#group-produk').removeClass('hide');
  $('[name=popup_status]').val(status);
  get_jum();
  $("#modal-form").modal('show');
}

function edit_table_barang(id){
  var status = $("#tabel_status"+id).val();
  var cek_id    = $("#tabel_id_tabel"+id).val();
  if(cek_id == '' ){
  var id_table    = $("#tabel_id"+id).val();
  }else{
  var id_table    = cek_id;
  }
  var id_barang   = $("#tabel_idbarang"+id).val();
  var nama_barang = $("#tabel_namabarang"+id).val();
  var kode_barang = $("#tabel_kodebarang"+id).val();
  var alias_barang= $("#tabel_aliasbarang"+id).val();
  var id_satuan   = $("#tabel_idsatuan"+id).val();
  var nama_satuan = $("#tabel_namasatuan"+id).val();
  var harga       = $("#tabel_harga"+id).val();
  var jumlah      = $("#tabel_jumlah"+id).val();
  var total       = $("#tabel_total"+id).val();
  var poin        = $("#tabel_poin"+id).val();
  var sebelum = $("#tabel_idproduk_sebelum"+id).val();

  $("[name=popup_id_table]").val(id_table);
  $("[name=store_id_table]").val($("#tabel_id"+id).val())
  $("[name=popup_barang]").html('<option value="'+id_barang+'" nama="'+nama_barang+'" kode="'+kode_barang+'" alias="'+alias_barang+'">'+nama_barang+'</option>');    
  $("[name=popup_harga]").val(harga);
  $("[name=popup_jumlah]").val(jumlah);
  $("[name=popup_satuan]").val(id_satuan).trigger('change');
  $("[name=popup_total]").val(total); 
  $("[name=popup_poin]").val(poin); 
  $("[name=popup_produk_sebelum]").val(sebelum);

  $("#help_popup_harga").text(accounting.formatMoney(harga));
  $("#help_popup_jumlah").text(format_angka(jumlah));
  $("#help_popup_total").text(accounting.formatMoney(total));
  $('#group-barang').removeClass('hide');
  $('#group-satuan').removeClass('hide');
  $('#group-produk').addClass('hide');
  $('[name=popup_status]').val(status);
  get_jum();
  $("#modal-form").modal('show');
}

function get_edit(id){
  $.ajax({
      url: "{{ url('kasir_get_edit')}} ",
      type: 'post',
      data: {id : id,},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
        if(respon.produk.length > 0){
          for(i in respon.produk){
            table_barang.row.add(['<div><center></center></div>',
              '<div id="list_produk'+respon.produk[i].id+'">'+respon.produk[i].nama_produk+'</div>',
              '<div id="list_harga'+respon.produk[i].id+'" class="text-right">'+accounting.formatMoney(respon.produk[i].harga)+'</div>',
              '<div id="list_jumlah'+respon.produk[i].id+'" class="text-right">'+format_angka(respon.produk[i].jumlah)+" "+respon.produk[i].nama_satuan+'</div>',
              '<div id="list_total'+respon.produk[i].id+'" class="text-right">'+accounting.formatMoney(respon.produk[i].total)+'</div>',
              '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+respon.produk[i].id+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
              '<input type="hidden" name="tabel_id[]" id="tabel_id'+respon.produk[i].id+'" value="'+respon.produk[i].id+'">'+
              '<input type="hidden" name="tabel_id_tabel[]" id="tabel_id_tabel'+respon.produk[i].id+'" value="'+respon.produk[i].id+'">'+
              '<input type="hidden" name="tabel_idlog[]" id="tabel_idlog'+respon.produk[i].id+'" value="'+respon.produk[i].id_log_stok+'">'+
              '<input type="hidden" name="tabel_idproduk[]" id="tabel_idproduk'+respon.produk[i].id+'" value="'+respon.produk[i].id_produk+'">'+
              '<input type="hidden" name="tabel_namaproduk[]" id="tabel_namaproduk'+respon.produk[i].id+'" value="'+respon.produk[i].nama_produk+'">'+
              '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+respon.produk[i].id+'" value="">'+
              '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+respon.produk[i].id+'" value="">'+
              '<input type="hidden" name="tabel_kodebarang[]" id="tabel_kodebarang'+respon.produk[i].id+'" value="">'+
              '<input type="hidden" name="tabel_aliasbarang[]" id="tabel_aliasbarang'+respon.produk[i].id+'" value="">'+
              '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+respon.produk[i].id+'" value="'+respon.produk[i].id_satuan+'">'+
              '<input type="hidden" name="tabel_namasatuan[]" id="tabel_namasatuan'+respon.produk[i].id+'" value="'+respon.produk[i].nama_satuan+'">'+
              '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+respon.produk[i].id+'" id_tipe="'+respon.produk[i].id_tipe+'" value="'+respon.produk[i].jumlah+'">'+
              '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+respon.produk[i].id+'" value="'+respon.produk[i].harga+'">'+
              '<input type="hidden" name="tabel_total[]" id="tabel_total'+respon.produk[i].id+'" value="'+respon.produk[i].total+'">'+
              '<input type="hidden" name="tabel_status[]" id="tabel_status'+respon.produk[i].id+'" value="'+respon.produk[i].status+'">'+
              '<input type="hidden" name="tabel_poin[]" id="tabel_poin'+respon.produk[i].id+'" value="'+respon.produk[i].poin+'">'+
              '<input type="hidden" name="tabel_potongan[]" id="tabel_potongan'+respon.produk[i].id+'" value="'+respon.produk[i].potongan+'">'+
              '<input type="hidden" name="tabel_potongan_total[]" qty="'+respon.produk[i].jumlah+'" id_tipe="'+respon.produk[i].id_tipe+'" id="tabel_potongan_total'+respon.produk[i].id+'" value="'+respon.produk[i].potongan_total+'">'+
              '<input type="hidden" name="tabel_tipe[]" id="tabel_tipe'+respon.produk[i].id+'" value="'+respon.produk[i].id_tipe+'">'+
              '<input type="hidden" name="tabel_idproduk_sebelum[]" id="tabel_idproduk_sebelum'+respon.produk[i].id+'" value="'+respon.produk[i].id_produk+'">'
              ]).draw(false);
          
          }


          // get_jum();
          // get_subtotal();
          // get_checkpromo()
          // netto();
        }
        if(respon.barang.length > 0){
          for(i in respon.barang){
            var nama  = respon.barang[i].nama_barang;
            var kode  = respon.barang[i].kode_barang;
            var alias = respon.barang[i].alias_barang;
            if(alias === null || alias === "" || alias === 0){
            var nama_barang = kode+" || "+nama;
            }else{
            var nama_barang = kode+" || "+nama+" || "+alias;
            }
            table_barang.row.add(['<div><center></center></div>',
              '<div id="list_barang'+respon.barang[i].id+'">'+nama_barang+'</div>',
              '<div id="list_harga'+respon.barang[i].id+'" class="text-right">'+accounting.formatMoney(respon.barang[i].harga)+'</div>',
              '<div id="list_jumlah'+respon.barang[i].id+'" class="text-right">'+format_angka(respon.barang[i].jumlah)+" "+respon.barang[i].nama_satuan+'</div>',
              '<div id="list_total'+respon.barang[i].id+'" class="text-right">'+accounting.formatMoney(respon.barang[i].total)+'</div>',
              '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table_barang('+respon.barang[i].id+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
              '<input type="hidden" name="tabel_id[]" id="tabel_id'+respon.barang[i].id+'" value="'+respon.barang[i].id+'">'+
              '<input type="hidden" name="tabel_id_tabel[]" id="tabel_id_tabel'+respon.barang[i].id+'" value="'+respon.barang[i].id+'">'+
              '<input type="hidden" name="tabel_idlog[]" id="tabel_idlog'+respon.barang[i].id+'" value="'+respon.barang[i].id_log_stok+'">'+
              '<input type="hidden" name="tabel_idproduk[]" id="tabel_idproduk'+respon.barang[i].id+'" value="">'+
              '<input type="hidden" name="tabel_namaproduk[]" id="tabel_namaproduk'+respon.barang[i].id+'" value="">'+
              '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+respon.barang[i].id+'" value="'+respon.barang[i].id_barang+'">'+
              '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+respon.barang[i].id+'" value="'+respon.barang[i].nama_barang+'">'+
              '<input type="hidden" name="tabel_kodebarang[]" id="tabel_kodebarang'+respon.barang[i].id+'" value="'+respon.barang[i].kode_barang+'">'+
              '<input type="hidden" name="tabel_aliasbarang[]" id="tabel_aliasbarang'+respon.barang[i].id+'" value="'+respon.barang[i].alias_barang+'">'+
              '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+respon.barang[i].id+'" value="'+respon.barang[i].id_satuan+'">'+
              '<input type="hidden" name="tabel_namasatuan[]" id="tabel_namasatuan'+respon.barang[i].id+'" value="'+respon.barang[i].nama_satuan+'">'+
              '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+respon.barang[i].id+'" id_tipe="" value="'+respon.barang[i].jumlah+'">'+
              '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+respon.barang[i].id+'" value="'+respon.barang[i].harga+'">'+
              '<input type="hidden" name="tabel_total[]" id="tabel_total'+respon.barang[i].id+'" value="'+respon.barang[i].total+'">'+
              '<input type="hidden" name="tabel_status[]" id="tabel_status'+respon.barang[i].id+'" value="'+respon.barang[i].status+'">'+
              '<input type="hidden" name="tabel_poin[]" id="tabel_poin'+respon.barang[i].id+'" value="'+respon.barang[i].poin+'">'+
              '<input type="hidden" name="tabel_potongan[]" id="tabel_potongan'+respon.barang[i].id+'" value="'+respon.barang[i].potongan+'">'+
              '<input type="hidden" name="tabel_potongan_total[]" qty="'+respon.barang[i].jumlah+'" id_tipe="" id="tabel_potongan_total'+respon.barang[i].id+'" value="'+respon.barang[i].potongan_total+'">'+
              '<input type="hidden" name="tabel_tipe[]" id="tabel_tipe'+respon.barang[i].id+'" value="">'+
              '<input type="hidden" name="tabel_idproduk_sebelum[]" id="tabel_idproduk_sebelum'+respon.barang[i].id+'" value="">'
              ]).draw(false);
          }

          
          // get_jum();
          // get_subtotal();
          // get_checkpromo();
          // netto();
        }

        get_jum();
        get_subtotal();
        get_checkpromo()
        netto();
        
      }
    })
}

function hapus(id){
  table_barang.row(id.parents('tr')).remove().draw();
    get_jum();
    get_subtotal();
    netto();
}

function uangmuka(){
  var d = $("[name=carabayar]").val();
  if(d==2){
    $(".c_uangmuka").removeClass('hide')
  }else{
    $(".c_uangmuka").addClass('hide')
  }
}

function get_jum(){
  var jumlah  = parseFloat($("[name=popup_jumlah]").val())  || 0;
  var harga   = parseFloat($("[name=popup_harga]").val())   || 0;
  var total   = parseFloat(jumlah*harga);
  $("[name=popup_total]").val(total);
  $("#help_popup_total").text(accounting.formatMoney(total));

}

function get_subtotal(this_){
  var jum = parseFloat(0);
    $('input[id^="tabel_total"]').each(function() {
              var val = ($(this).val());
              jum += parseFloat(val);
              
            });
  //promo
  var potongan = parseFloat(0);
  if($("[name=td_statuspromo]").val() == 'YA'){
    var potongan = parseFloat(0);

    var check_diskon = []
    $('input[id^="tabel_tipe"]').each(function(){
      var dd = $(this).val()
      if (dd != '') check_diskon.push(dd);
    })

    $('input[id^="tabel_potongan_total"]').each(function(){
      var val_pot = $(this).val();
      var qty = $(this).attr('qty');
      var id_tipe = $(this).attr('id_tipe');

      let arr_check = check_diskon.reduce((a, c) => (a[c] = (a[c] || 0) + 1, a), Object.create(null));
      // console.log(val_pot+' || '+ qty +' || '+ id_tipe)
      if (id_tipe != '') {
        if (arr_check[id_tipe] > 1 || qty > 1) potongan += parseFloat(val_pot);  
      }
    })
    $("[name=td_diskon]").val(potongan);
    $("#help_popup_totaldiskon").text(accounting.formatMoney(potongan));
  }else{
    $("[name=td_diskon]").val(0);
    $("#help_popup_totaldiskon").text(accounting.formatMoney(0));
  }
  
  // $("#td_total").text(accounting.formatMoney(parseFloat(jum-potongan)));
  // $("[name=td_total]").val(parseFloat(jum-potongan));
  $("#td_total").text(accounting.formatMoney(parseFloat(jum)));
  $("[name=td_total]").val(parseFloat(jum));
  netto();
}

function get_checkpromo(){
  var jum_item = parseFloat(0);
  var check_diskon = []
  $('input[id^="tabel_tipe"]').each(function(){
    var dd = $(this).val();
    if (dd != '') check_diskon.push(dd); 
  })
  $('input[id^="tabel_jumlah"]').each(function(){
    var val_item = $(this).val();
    var id_tipe = $(this).attr('id_tipe');
    let arr_check = check_diskon.reduce((a, c) => (a[c] = (a[c] || 0) + 1, a), Object.create(null));
    if (id_tipe != '') {
      if (val_item > 1) {
        jum_item += parseFloat(val_item); 
      }else{
        if (arr_check[id_tipe] > 1) {
          jum_item += parseFloat(val_item); 
        }
      }
    }
  })
  // console.log(jum_item);
  if(jum_item > 1){
    $("#wrapper-promo").show();
  }else{
    $("#wrapper-promo").hide();
  }
  netto();
}

function netto(cek_metode = '0'){
  var total   = parseFloat($("[name=td_total]").val()) || 0;
  var diskon  = parseFloat($("[name=td_diskon]").val()) || 0;
  var ongkir  = parseFloat($("[name=td_ongkir]").val()) || 0;
  var netto   = parseFloat(0);
  var ppn     = parseFloat(0);
  var uangmuka= parseFloat($("[name=td_uangmuka]").val()) || 0;
  var carabayar = $("[name=carabayar]").val();
  var total_metod = parseFloat($("[name=total_viabayar]").val()) || 0;
  var total_metod2= parseFloat($("[name=total_viabayar2]").val()) || 0;
  var netto_metode = parseFloat(0); 


  if($('select[name=pajak]').val()==1){
      ppn = parseFloat((total-diskon)*(10/100));
    }

  netto = total-(diskon-(ppn+ongkir))-uangmuka-(total_metod+total_metod2);
  netto_metode = total-(diskon-(ppn+ongkir))-uangmuka;

  
    $("[name=td_diskon]").val(diskon);
    $("#help_popup_totaldiskon").text(accounting.formatMoney(diskon));

    $("[name=td_pajak]").val(ppn);
    $("#help_popup_pajak").text(accounting.formatMoney(ppn));

    $("[name=td_uangmuka]").val(uangmuka);
    $("#help_popup_uangmuka").text(accounting.formatMoney(uangmuka));

    $("[name=td_ongkir]").val(ongkir);
    $("#help_popup_ongkir").text(accounting.formatMoney(ongkir));

    if(carabayar == '1'){
    $("[name=td_netto]").val(parseFloat(0));
    $("#td_netto").text(accounting.formatMoney(parseFloat(0)));
    }else{
    $("[name=td_netto]").val(netto);
    $("#td_netto").text(accounting.formatMoney(netto));
    }

    if(cek_metode == 1){
      $("[name=total_viabayar").val(total_metod);
      $("#help_popup_ttlviabayar").text(accounting.formatMoney(total_metod));
      $("[name=total_viabayar2").val(parseFloat(netto_metode-total_metod));
      $("#help_popup_ttlviabayar2").text(accounting.formatMoney(parseFloat(netto_metode-total_metod)));
    }else if(cek_metode == 2){
      $("[name=total_viabayar").val(parseFloat(netto_metode-total_metod2));
      $("#help_popup_ttlviabayar").text(accounting.formatMoney(parseFloat(netto_metode-total_metod2)));
      $("[name=total_viabayar2").val(total_metod2);
      $("#help_popup_ttlviabayar2").text(accounting.formatMoney(total_metod2));
    }else{
      $("[name=total_viabayar").val(total_metod);
      $("#help_popup_ttlviabayar").text(accounting.formatMoney(total_metod));
      $("[name=total_viabayar2").val(total_metod2);
      $("#help_popup_ttlviabayar2").text(accounting.formatMoney(total_metod2));

    }
};

function tambah_metodebayar(){
  var select = $("[name=viabayar_hide]").html();
  var input = $("[name=total_viabayar_hide]").html();
  var row = '<tr class="tr_metodebayar">'+
            '<td></td>'+
            '<td><select class="form-control select2" name="viabayar2" style="width: 100%;">'+select+'</select> &nbsp; <input type="text" class="form-control number-only text-right" name="total_viabayar2" id="total_viabayar2" onkeyup="get_totalmetod2($(this))"><p class="help-block pull-right" id="help_popup_ttlviabayar2"></p></td>'+
            '<td width="2%"><button type="button" class="btn btn-xs btn-default" id="hapusmetodebayar"><i class="fa fa-minus"></i></button></td>'+
            '</tr>';
  $('.table-totalbayar tr:eq(4)').after(row);
  $('select[name=viabayar2]').select2();
  $("#td_addmetodebayar").hide();

  $("[name=total_viabayar]").removeClass('hidden');
  $("#help_popup_ttlviabayar").removeClass('hidden');
  $("#help_popup_ttlviabayar2").removeClass('hidden');
  
  $("[name=total_viabayar2]").val(parseFloat(0));
  $("#help_popup_ttlviabayar2").text(accounting.formatMoney(parseFloat(0)));

  $("#hapusmetodebayar").on("click", hapusRow);

}

function hapusRow() {
      $("[name=total_viabayar]").val(parseFloat(0));
      $("#help_popup_ttlviabayar").text(accounting.formatMoney(parseFloat(0)));
      $("[name=total_viabayar2]").val(parseFloat(0));
      $("#help_popup_ttlviabayar2").text(accounting.formatMoney(parseFloat(0)));
      
      $(this).parents(".tr_metodebayar").remove();
      $("#td_addmetodebayar").show();
      $("[name=total_viabayar]").addClass('hidden');
      $("#help_popup_ttlviabayar").addClass('hidden');
};

function get_totalmetod2(jum){
//   var jum = jum.val();
//   $("#help_popup_ttlviabayar2").text(accounting.formatMoney(jum));
netto('2');
}

</script>

@endsection

<?php $hal = "kasirpembatalan"; ?>
@extends('layouts.admin.master')
@section('title', 'Detail Pembatalan')

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

<style type="text/css">

  .stepwizard-step p {
    margin-top: 0px;
    color:#666;
  }
  .stepwizard-row {
    display: table-row;
  }
  .stepwizard {
    display: table;
    width: 100%;
    position: relative;
  }
  .stepwizard-step button[disabled] {
    /*opacity: 1 !important;
    filter: alpha(opacity=100) !important;*/
  }
  .stepwizard .btn.disabled, .stepwizard .btn[disabled], .stepwizard fieldset[disabled] .btn {
    opacity:1 !important;
    color:#bbb;
  }
  .stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content:" ";
    width: 100%;
    height: 1px;
    background-color: #ccc;
    z-index: 0;
  }
  .stepwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
  }
  .btn-circle {
    width: 30px;
    height: 30px;
    text-align: center;
    padding: 6px 0;
    font-size: 12px;
    line-height: 1.428571429;
    border-radius: 15px;
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
          <h3 class="box-title">Detail Belanja Barang</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <form role="form" class="form_belanja" action="#" method="post">
            <input type="hidden" name="id_kasir" value="{{$data['data']['id_kasir']}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="box-body" style="margin-right: -25px;margin-left: -25px">


              <!-- WIZARD -->
              <div class="col-md-12">
                <div class="stepwizard">
                  <div class="stepwizard-row setup-panel">
                    <div class="stepwizard-step col-xs-4"> 
                      <a href="#step-1" type="button" class="btn btn-success btn-circle">1</a>
                      <p><small>Detail</small></p>
                    </div>
                    <div class="stepwizard-step col-xs-4"> 
                      <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                      <p><small>Produk</small></p>
                    </div>
                    <div class="stepwizard-step col-xs-4"> 
                      <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                      <p><small>Total</small></p>
                    </div>
                  </div>
                </div>

                <div class="panel panel-primary setup-content" id="step-1">
                  <div class="panel-heading">
                    <h3 class="panel-title">Detail Belanja</h3>
                  </div>
                  <div class="panel-body" style="margin-right: -20px;margin-left: -20px">

                    <div class="form-group col-md-12">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="exampleInputPassword1">Tanggal Input</label>
                            <input class="form-control datepicker" type="text" name="tanggal" placeholder="Tanggal" value="{{($data['data']['tanggal'] == '') ? date('d-m-Y'):$data['data']['tanggal']}}" required disabled=""/>
                          </div>
                          <div class="form-group">
                            <label>Tanggal Jatuh Tempo</label>
                            <input class="form-control datepicker" type="text" name="tanggal_tempo" placeholder="Tanggal Tempo" value="{{($data['data']['tanggal_tempo'] == '') ? date('d-m-Y'):$data['data']['tanggal_tempo']}}" required disabled="" />
                          </div>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Nama Pelanggan</label>
                            <select name="id_pelanggan" class="form-control selectize" style="width: 100%;" disabled="">
                              <option value=""> --- Pilih --- </option>
                              @foreach($data['pelanggan'] as $d)
                              <option value="{{$d->id}}" {{($data['data']['id_pelanggan']==$d->id) ? "selected":""}}>{{$d->nama}}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Telp</label>
                            <input type="text"  name="telp_pelanggan" class="form-control number-only" value="{{$data['data']['telp_pelanggan']}}" placeholder="08xxxxxxxxx" disabled="">
                          </div>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Alamat</label>
                            <input type="text"  name="alamat_pelanggan" class="form-control" value="{{$data['data']['alamat_pelanggan']}}" placeholder="JL. Example" disabled="">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Tanggal Faktur</label>
                            <input class="form-control datepicker" type="text" name="tanggal_faktur" placeholder="Tanggal Faktur" value="{{($data['data']['tanggal_faktur'] == '') ? date('d-m-Y'):$data['data']['tanggal_faktur']}}" required disabled=""/>
                          </div>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Nomor Faktur</label>
                            <input type="text"  name="nomor" class="form-control" value="{{($data['data']['nomor'] == '') ? $data['no_auto']:$data['data']['nomor']}}" disabled="">
                          </div>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Gudang</label>
                            <select class="select2" name="gudang" style="width: 100%;" disabled="">
                              @foreach($data['gudang'] as $d)
                              <option value="{{$d->id_gudang}}" nama="{{$d->nama_gudang}}" alamat="{{$d->alamat_gudang}}" {{($data['data']['id_gudang']==$d->id_gudang) ? "Selected":""}}>{{$d->nama_gudang}}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Keterangan</label>
                            <textarea class="form-control" rows="2" name="keterangan" disabled="">{{$data['data']['keterangan']}}</textarea>
                          </div>
                        </div>
                      </div><!-- end : row -->
                      <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
                    </div>

                  </div>
                </div>

                <div class="panel panel-primary setup-content" id="step-2">
                  <div class="panel-heading">
                    <h3 class="panel-title">Detail Produk</h3>
                  </div>
                  <div class="panel-body" style="margin-right: -5px;margin-left: -5px">

                    <div class="form-group col-md-12 table-responsive">
                      <!-- <h3>Detail Produk</h3>
                        <hr/> -->
                        <table class="table table-bordered table-hover table-striped table-barang" id="table_barang">
                          <thead>
                            <tr>
                              <!-- <th width="3%">No.</th> -->
                              <th width="32%">Nama Produk</th>
                              <th class="20%">Harga</th>
                              <th class="15%">Jumlah</th>
                              <th class="20%">Total</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>

                      <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
                    </div>
                  </div>

                  <div class="panel panel-primary setup-content" id="step-4">
                    <div class="panel-heading">
                      <h3 class="panel-title">Total Penjualan</h3>
                      <label id="wrapper-promo" style="display: none;margin-top:-15px;" class="pull-right">
                      <!-- @if($data['data']['td_statuspromo'] == 'YA')
                      <span class="label label-success">Pakai Paket Promo<span>
                      @endif -->
                      </label>
                      <input type="hidden" name="td_statuspromo" value="{{$data['data']['td_statuspromo']}}" disabled>
                    </div>
                    <div class="panel-body" style="margin-right: -20px;margin-left: -20px">

                      <div class=" col-md-6 col-sm-12 m-l-5">
                        <div class="table-responsive">
                          <table class="table table-bordered table-form">
                            <tbody>

                              <tr>
                                <td width="30%">Sub Total</td>
                                <td>
                                  <input type="hidden" name="td_total" value="{{($data['data']['td_subtotal']== '') ? '0':$data['data']['td_subtotal']}}" disabled="">
                                  <p class="help-block pull-right" id="td_total">Rp 0</p>
                                </td>
                              </tr>
                              <tr>
                                <td>Potongan</td>
                                <td><input type="text" name="td_diskon" class="form-control number-only text-right" value="{{($data['data']['td_potongan']== '') ? '0':$data['data']['td_potongan']}}" disabled="">
                                  <p class="help-block pull-right" id="help_popup_totaldiskon">Rp 0</p>
                                </td>
                              </tr>
                              <tr>
                                <td>Ongkos Kirim</td>
                                <td><input type="text" step="0.01" min="0" name="td_ongkir" id="td_ongkir" class="form-control text-right number-only" value="{{($data['data']['td_ongkir']== '') ? '0':$data['data']['td_ongkir']}}" disabled="">
                                  <p class="help-block pull-right" id="help_popup_ongkir">Rp 0</p>
                                </td>
                              </tr>
                              <!--<tr>
                                <td>Metode bayar</td>
                                <td>
                                  <select class="form-control select2" name="viabayar" style="width: 100%;" disabled="">
                                    <?php
                                    foreach ($data['pembayaran'] as $d) {
                                      $selected = ($data['data']['metodebayar'] == $d->id) ? "selected":'';
                                      echo "<option value='".$d->id."' ".$selected."> ".$d->nama." </option>";
                                    }?>
                                  </select>
                                </td>
                              </tr>-->
                              <tr>
                            <td>Metode bayar</td>
                            <td><select class="form-control select2" name="viabayar" style="width: 100%;" disabled="">
                                   <?php
                                  foreach ($data['pembayaran'] as $d) {
                                    $selected = ($data['data']['metodebayar'] == $d->id) ? "selected":'';
                                    echo "<option value='".$d->id."' ".$selected."> ".$d->nama." </option>";
                                  }?>
                                </select>
                                &nbsp;
                                <p class="help-block pull-right" id="help_popup_ttlviabayar"></p>
                            </td>
                          </tr>
                          <?php if($data['data']['metodebayar2'] != ''){?>
                            <tr>
                            <td>Metode bayar2</td>
                            <td><select class="form-control select2" name="viabayar2" style="width: 100%;" disabled="">
                                   <?php
                                  foreach ($data['pembayaran'] as $d) {
                                    $selected = ($data['data']['metodebayar2'] == $d->id) ? "selected":'';
                                    echo "<option value='".$d->id."' ".$selected."> ".$d->nama." </option>";
                                  }?>
                                </select>
                                &nbsp;
                                <p class="help-block pull-right" id="help_popup_ttlviabayar2"></p>
                            </td>
                          </tr>
                          <?php } ?>
                              <tr>
                                <td>Pembayaran</td>
                                <td>
                                  <select class="form-control select2" name="carabayar" style="width: 100%;" disabled="">
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

                      <!-- <button class="btn btn-success pull-right">Finish!</button> -->
                    </div>
                  </div>
                </div>
                <!-- WIZARD -->
              </div><!-- /.box-body -->

              <div class="box-footer">
               <a href="{{url('kasir_pembatalan')}}" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
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

    $(document).ready(function () {
      table_barang = $("#table_barang").DataTable({
        "paging": false,
        "ordering": false,//tambahan
        "pageLength": 10,//tambahan
        "lengthChange": false,//tambahan
        "searching": false//tambahan
      });
      table_barang.on( 'order.dt search.dt', function () {
        table_barang.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
          cell.innerHTML = i+1;
        } );
      }).draw();

      if(id_kasir != ''){
        if(id_pelanggan != ''){
          $("[name=id_pelanggan]").val(id_pelanggan).trigger('change');
          //$("[name=id_pelanggan]").html('<option value="'+id_pelanggan+'">'+nama_pelanggan+'</option>');
          /*var $popupelanggan = $("select[name=id_pelanggan]").selectize();
          var popupelanggan = $popupelanggan[0].selectize;
          popupelanggan.setValue(id_pelanggan);
          console.log(id_pelanggan);*/
        }
        uangmuka();
        get_edit(id_kasir);
        get_subtotal();
        get_checkpromo();
        netto();
      }

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



      $('[name=popup_produk]').select2({
        placeholder: "--- Pilih ---",
        ajax: {
          url: '{{url('kasir_pembatalan_get_produk')}}',
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
                text:item.produk_nama
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
         url: '{{url('kasir_pembatalan_get_barang')}}',
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
      $("[name=popup_harga]").val(d.harga).trigger('keyup');
    });
    $("[name=popup_barang]").on('change', function(){
      var d = $("[name=popup_barang]").select2('data')[0];
      $("[name=popup_harga]").val(parseFloat(0)).trigger('keyup');
      $("[name=popup_satuan]").val(d.satuan_id).trigger('change');
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
    })

    $("[name=id_pelanggan]").on('change',function(){
      var id = $(this).val();
      $.ajax({
        url: "{{ url('kasir_pembatalan_attr_pelanggan')}} ",
        type: 'post',
        data: {id : id,},
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(respon){
          var alamat = "";
          var telp = "";
          if(respon[0].alamat != ""){
            var alamat = respon[0].alamat;
          }
          if(respon[0].telp != ""){
            var telp = respon[0].telp;
          }
          $("[name=alamat_pelanggan]").val(alamat);
          $("[name=telp_pelanggan]").val(telp);

        }

      })

    })


function get_edit(id){
     $.ajax({
        url: "{{ url('kasir_pembatalan_get_edit')}} ",
        type: 'post',
        data: {id : id,},
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(respon){
          if(respon.produk.length > 0){
            for(i in respon.produk){
              table_barang.row.add([
                // '<div><center></center></div>',
                '<div id="list_produk'+respon.produk[i].id+'">'+respon.produk[i].nama_produk+'</div>',
                '<div id="list_harga'+respon.produk[i].id+'" class="text-right">'+accounting.formatMoney(respon.produk[i].harga)+'</div>',
                '<div id="list_jumlah'+respon.produk[i].id+'" class="text-right">'+format_angka(respon.produk[i].jumlah)+" "+respon.produk[i].nama_satuan+'</div>',
                '<div id="list_total'+respon.produk[i].id+'" class="text-right">'+accounting.formatMoney(respon.produk[i].total)+'</div>'+
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
                '<input type="hidden" name="tabel_potongan[]" id="tabel_potongan'+respon.produk[i].id+'" value="'+respon.produk[i].potongan+'">'+
                '<input type="hidden" name="tabel_potongan_total[]" qty="'+respon.produk[i].jumlah+'" id_tipe="'+respon.produk[i].id_tipe+'" id="tabel_potongan_total'+respon.produk[i].id+'" value="'+respon.produk[i].potongan_total+'">'+
                '<input type="hidden" name="tabel_tipe[]" id="tabel_tipe'+respon.produk[i].id+'" value="'+respon.produk[i].id_tipe+'">'+
                '<input type="hidden" name="tabel_status[]" id="tabel_status'+respon.produk[i].id+'" value="'+respon.produk[i].status+'">'
                ]).draw(false);

            }
            // get_jum();
            // get_subtotal();
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
             table_barang.row.add([
              // '<div><center></center></div>',
              '<div id="list_harga'+respon.barang[i].id+'">'+nama_barang+'</div>',
              '<div id="list_harga'+respon.barang[i].id+'" class="text-right">'+accounting.formatMoney(respon.barang[i].harga)+'</div>',
              '<div id="list_jumlah'+respon.barang[i].id+'" class="text-right">'+format_angka(respon.barang[i].jumlah)+" "+respon.barang[i].nama_satuan+'</div>',
              '<div id="list_total'+respon.barang[i].id+'" class="text-right">'+accounting.formatMoney(respon.barang[i].total)+'</div>'+
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
              '<input type="hidden" name="tabel_potongan[]" id="tabel_potongan'+respon.barang[i].id+'" value="'+respon.barang[i].potongan+'">'+
                '<input type="hidden" name="tabel_potongan_total[]" qty="'+respon.barang[i].jumlah+'" id_tipe="" id="tabel_potongan_total'+respon.produk[i].id+'" value="'+respon.produk[i].potongan_total+'">'+
              '<input type="hidden" name="tabel_tipe[]" id="tabel_tipe'+respon.barang[i].id+'" value="">'+
              '<input type="hidden" name="tabel_status[]" id="tabel_status'+respon.barang[i].id+'" value="'+respon.barang[i].status+'">'
              ]).draw(false);
           }


          //  get_jum();
          //  get_subtotal();
          //  netto();
         }
          get_jum();
          get_subtotal();
          get_checkpromo();
          netto();
       }
     })
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
        var dd = $(this).val();
        if (dd != '') check_diskon.push(dd);
    })

    $('input[id^="tabel_potongan_total"]').each(function(){
      var val_pot = $(this).val();
      var qty = $(this).attr('qty');
      var id_tipe = $(this).attr('id_tipe');

      let arr_check = check_diskon.reduce((a, c) => (a[c] = (a[c] || 0) + 1, a), Object.create(null));
      // console.log(arr_check)
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
  console.log(potongan);
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

function netto(){
  var total   = parseFloat($("[name=td_total]").val()) || 0;
  var diskon  = parseFloat($("[name=td_diskon]").val()) || 0;
  var ongkir  = parseFloat($("[name=td_ongkir]").val()) || 0;
  var netto   = parseFloat(0);
  var ppn     = parseFloat(0);
  var uangmuka= parseFloat($("[name=td_uangmuka]").val()) || 0;
  var carabayar = $("[name=carabayar]").val();
  var total_metodebayar = '{{$data['data']['total_metodebayar']}}';
  var total_metodebayar2 = '{{$data['data']['total_metodebayar2']}}';

  if($('select[name=pajak]').val()==1){
    ppn = parseFloat((total-diskon)*(10/100));
  }

  netto = total-(diskon-ppn+ongkir)-uangmuka;


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
  
  $("#help_popup_ttlviabayar").text(accounting.formatMoney(total_metodebayar));
  $("#help_popup_ttlviabayar2").text(accounting.formatMoney(total_metodebayar2));
};

</script>


<!-- SCRIPT TAB WIZARD -->
<script type="text/javascript">
  $(document).ready(function () {

    var navListItems = $('div.setup-panel div a'),
    allWells = $('.setup-content'),
    allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
      e.preventDefault();
      var $target = $($(this).attr('href')),
      $item = $(this);

      if (!$item.hasClass('disabled')) {
        navListItems.removeClass('btn-success').addClass('btn-default');
        $item.addClass('btn-success');
        allWells.hide();
        $target.show();
        // $target.find('input:eq(0)').focus();
      }
    });

    allNextBtn.click(function () {
      var curStep = $(this).closest(".setup-content"),
      curStepBtn = curStep.attr("id"),
      nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
      curInputs = curStep.find("input[type='text'],input[type='url']"),
      isValid = true;

      $(".form-group").removeClass("has-error");
      for (var i = 0; i < curInputs.length; i++) {
        if (!curInputs[i].validity.valid) {
          isValid = false;
          $(curInputs[i]).closest(".form-group").addClass("has-error");
        }
      }

      if (isValid) nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-success').trigger('click');
  });
</script>
@endsection

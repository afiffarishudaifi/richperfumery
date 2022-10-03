<?php $hal = "redeem"; ?>
@extends('layouts.admin.master')
@section('title', 'Redeem')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('public/selectize/css/selectize.css')}}">
<link rel="stylesheet" href="{{asset('public/selectize/css/selectize.bootstrap3.css')}}">
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
    Tukar Poin
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">

      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Form Tukar Poin</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <form id="regForm" role="form" class="form_belanja" action="{{url('redeem_simpan')}}" method="post">
            <input type="hidden" name="id_kasir" value="{{$data['data']['id_kasir']}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="box-body" style="margin-right: -25px;margin-left: -25px">


              <div class="col-md-12">
                <div class="stepwizard">
                  <div class="stepwizard-row setup-panel">
                    <div class="stepwizard-step col-xs-4">
                      <a href="#step-1" type="button" class="btn btn-success btn-circle">1</a>
                      <p><small>Belanja</small></p>
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
                    <h3 class="panel-title">Tambah Belanja Barang</h3>
                  </div>
                  <div class="panel-body">

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
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Tanggal Faktur</label>
                          <input class="form-control datepicker" type="text" name="tanggal_faktur" placeholder="Tanggal Faktur" value="{{($data['data']['tanggal_faktur'] == '') ? date('d-m-Y'):$data['data']['tanggal_faktur']}}" required/>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1">Nomor Faktur</label>
                          <input type="text"  name="nomor" class="form-control" value="{{($data['data']['nomor'] == '') ? $data['no_auto']:$data['data']['nomor']}}">
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
                        <div class="form-group" id="div_poinku">
                          <label for="examplePoin">Poin Pelanggan</label>
                          <input type="text" name="poin" id="poin" value="{{$data['data']['poin']}}" class="form-control" readonly="">
                        </div>
                      </div>
                    </div><!-- end : row -->

                    <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
                  </div>
                </div>

                <div class="panel panel-primary setup-content" id="step-2">
                  <div class="panel-heading">
                    <h3 class="panel-title">Detail Tukar Produk</h3>
                  </div>
                  <div class="panel-body" style="margin-right: -5px;margin-left: -5px">

                    <a href="javascript:;" class="btn btn-sm btn-success btn_tambah" id="btn_tambah"><span class="glyphicon glyphicon-plus"></span> Tukar Produk</a>
                    <!-- <a href="javascript:;" class="btn btn-sm btn-primary btn_tambah_barang" id="btn_tambah_barang"><span class="glyphicon glyphicon-plus"></span> Barang Tambahan</a> -->
                    <br/> <br/>
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover table-striped table-barang" id="table_barang">
                        <thead>
                          <tr>
                            <th width="3%">No.</th>
                            <th width="37%">Nama Produk</th>
                            <th width="15%">Poin</th>
                            <th width=15%">Jumlah</th>
                            <th width="15%">Total Poin</th>
                            <th width="15%">#</th>
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
                  </div>
                  <div class="panel-body">

                    <div class="table-responsive">
                      <table class="table table-bordered table-form table-totalbayar">
                       <tbody>
                         <tr class="active">
                           <!-- <td>Paper Bag</td> -->
                           <td>Total Penjualan</td>
                           <td class="text-right">
                           <label class="checkbox-inline checkbox-right">

                           </label>
                           </td>
                         </tr>
                         <tr>
                          <td width="30%">Poin</td>
                          <td><input type="hidden" name="td_poin" value="0"><p class="help_block_totalpoin pull-right" id="td_poin"></p></td>
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
                           </td>
                           <td width="2%" id="td_addmetodebayar"><button type="button" class="btn btn-xs btn-success" onclick="tambah_metodebayar()"><i class="fa fa-plus"></i></button></td>
                         </tr>
                         <tr id="div_pembayaran">
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
                         <tr id="div_tagihan">
                           <td>Tagihan</td>
                           <td><p class="help-block pull-right" id="td_netto">Rp 0</p>
                             <input type="hidden" name="td_bayar" id="td_bayar" value="{{$data['data']['td_tagihan']}}">
                             <input type="hidden" name="total_netto" id="total_netto" value="{{$data['data']['td_tagihan']}}">
                           </td>
                         </tr>
                       </tbody>
                      </table>
                    </div>

                    <button class="btn btn-success pull-right" type="submit">Finish!</button>
                  </div>
                </div>
              </div>



            </div>
          </form>
        </div>
      </div>



    </div>
    <!-- /.row (main row) -->



  </section>

  <!-- /.content -->
  @include('admin.redeem.form')
  @endsection


  @section('js')
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


      
      get_hide();
      if(id_kasir != ''){
        if(id_pelanggan != ''){
          $("[name=id_pelanggan]").val(id_pelanggan).trigger('change');
        }

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

    $('[name=popup_produk]').select2({
        placeholder: "--- Pilih ---",
        ajax: {
            url: "{{url('redeem_get_produk')}}",
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
                      harga:0,
                      poin:item.poin,
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
          url: "{{url('redeem_get_barang')}}",
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
                  harga:0,
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
      $(".btn_redeem_not").hide();
      $(".btn_redeem_check").hide();

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
      $('#group-harga').addClass('hide');
      $('#modal-form').modal('show');

  });


  $("#btn_tambah_pelanggan").click(function(){
      $('[name=popup_id_table_pelanggan]').val('');
      $('[name=pelanggan_nama]').val('');
      $('[name=pelanggan_telp]').val('');
      $('[name=pelanggan_alamat]').val('');
      $('#modal-form-pelanggan').modal('show');
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
  $("[name=popup_produk]").on('change', function(){
    var d = $("[name=popup_produk]").select2('data')[0];
    $("[name=popup_harga]").val(d.harga).trigger('keyup');
    $("[name=popup_harga_hidden]").val(d.harga);
    $("[name=popup_poin]").val(d.poin);
    $("#help_popup_poin").text(format_angka(d.poin,0));
    if(d != ''){
      $(".btn_redeem_not").show();
      $(".btn_redeem_check").hide();
    }
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

  $("[name=gudang]").on('change',function(){
    var tanggal = $("[name=tanggal_faktur]").val();
    var gudang  = $(this).val();
    $.ajax({
      url: "{{ url('redeem_get_nota')}}",
      type: 'post',
      data: {tanggal:tanggal,gudang:gudang},
      headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success: function(respon){
        $("[name=nomor]").val(respon).trigger("change")
      }
    })
  })

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
    $.ajax({
      url: "{{ url('redeem_get_nota')}}",
      type: 'post',
      data: {tanggal:tanggal,gudang:gudang},
      headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success: function(respon){
        $("[name=nomor]").val(respon).trigger("change")
      }
    })
  })

  $("[name=id_pelanggan]").on('change',function(){
    var id = $(this).val();
    $.ajax({
        url: "{{ url('redeem_attr_pelanggan')}} ",
        type: 'post',
        data: {id : id,},
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(respon){
            var alamat = "";
            var telp = "";
            var nomor = "";
            var status = "1";
            var poin = "0";
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
            if(respon[0].poin != "" ||  respon[0].poin > 0){
              var poin = respon[0].poin;
            }

            $("[name=alamat_pelanggan]").val(alamat);
            $("[name=telp_pelanggan]").val(telp);
            $("[name=nomor_pelanggan]").val(nomor);
            $("[name=status_pelanggan]").val(status).trigger('change');
            $("[name=poin]").val(format_angka(poin));

            if(id != ''){
              $("#div_poinku").show();
              $("[name=poin]").val(parseInt(0));
              if(respon[0].poin > 0){
                $("[name=poin]").val(format_angka(parseInt(poin),0));
              }
            }

        }

    })

  })

  $("#btn_popup_simpan").click(function(){
    var d = $("[name=popup_produk]").select2('data')[0];
    var id_data     = $("[name=popup_id_table]").val();
    var id_produk   = $("[name=popup_produk]").val();
    var id_satuan   = $("[name=popup_satuan]").val();
    var nama_satuan = $("[name=popup_satuan] :selected").attr('satuan');
    var jumlah      = $("[name=popup_jumlah]").val();
    var harga       = $("[name=popup_harga]").val();
    var total       = parseFloat(harga*jumlah);
    var poin        = $("[name=popup_poin]").val();
    var total_poin  = parseFloat(poin*jumlah);
    var status_redeem = $("[name=popup_status_redeem]").val();
    var status      = $("[name=popup_status]").val();
    var transaksi_poin = parseFloat(total_poin);
    var pelanggan_poin = parseFloat($("[name=poin]").val());
    if(id_data == ''){
        var barang_produk = id_produk;
        var nama_produk = d.nama;
    }else{
        var nama_produk = $("[name=popup_produk] :selected").attr('nama');
        
    }

    if(barang_produk != '' && harga != '' && jumlah != ''){
    if(id_data == ''){
      if(transaksi_poin <= pelanggan_poin ){
      table_barang.row.add(['<div><center></center></div>',
            '<div id="list_produk'+tb_no+'">'+nama_produk+'</div>',
            '<div id="list_poin'+tb_no+'" class="text-right">'+format_angka(poin,0)+' Poin</div>',
            /*'<div id="list_harga'+tb_no+'" class="text-right">'+accounting.formatMoney(harga)+'</div>',*/
            '<div id="list_jumlah'+tb_no+'" class="text-right">'+format_angka(jumlah)+" Pcs"+'</div>',
            /*'<div id="list_satuan'+tb_no+'">'+nama_satuan+'</div>',*/
            /*'<div id="list_total'+tb_no+'" class="text-right">'+accounting.formatMoney(total)+'</div>',*/
            '<div id="list_totalpoin'+tb_no+'" class="text-right">'+format_angka(total_poin,0)+" Poin"+'</div>',
            '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+tb_no+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))" data-id="'+tb_no+'"><i class="fa fa-trash"></i></button></div>'+
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
            '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+tb_no+'" value="'+jumlah+'">'+
            '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+tb_no+'" value="'+harga+'">'+
            '<input type="hidden" name="tabel_total[]" id="tabel_total'+tb_no+'" value="'+total+'">'+
            '<input type="hidden" name="tabel_status[]" id="tabel_status'+tb_no+'" value="'+status+'">'+
            '<input type="hidden" name="tabel_statusredeem[]" id="tabel_statusredeem'+tb_no+'" value="'+status_redeem+'">'+
            '<input type="hidden" name="tabel_poin_produk[]" id="tabel_poin_produk'+tb_no+'" value="'+poin+'">'+
            '<input type="hidden" name="tabel_total_poin_produk[]" id="tabel_total_poin_produk'+tb_no+'" value="'+total_poin+'">']).draw(false);
      // get_subtotal();
      get_poinsubtotal();
      }else{
        var kurang_poin = parseFloat(transaksi_poin-pelanggan_poin);
        alert('poin anda kurang '+kurang_poin+' Poin');
      }
    }else{
          $("#list_produk"+id_data).text(nama_produk);
          // $("#list_harga"+id_data).text(accounting.formatMoney(harga));
          $("#list_jumlah"+id_data).text(format_angka(jumlah)+" Pcs");
          // $("#list_total"+id_data).text(accounting.formatMoney(total));
          $("#list_poin"+id_data).text(format_angka(poin,0)+" Poin");
          $("#list_totalpoin"+id_data).text(format_angka(total,0)+" Poin");

          $("#tabel_id"+id_data).val(id_data);
          $("#tabel_id_tabel"+id_data).val(id_data);
          $("#tabel_idproduk"+id_data).val(id_produk);
          $("#tabel_namaproduk"+id_data).val(nama_produk);
          // $("#tabel_idbarang"+id_data).val(id_barang);
          // $("#tabel_namabarang"+id_data).val(nama_barang);
          // $("#tabel_kodebarang"+id_data).val(kode_barang);
          // $("#tabel_aliasbarang"+id_data).val(alias_barang);
          $("#tabel_harga"+id_data).val(harga);
          $("#tabel_jumlah"+id_data).val(jumlah);
          $("#tabel_idsatuan"+id_data).val(id_satuan);
          $("#tabel_namasatuan"+id_data).val(nama_satuan);
          $("#tabel_total"+id_data).val(total);
          $("#tabel_statusredeem"+id_data).val(status_redeem);
          $("#tabel_poin_produk"+id_data).val(poin);
          $("#tabel_total_poin_produk"+id_data).val(total_poin);
      // get_subtotal();
      get_poinsubtotal();
    }
    tb_no++;
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
    var poin        = $("#tabel_poin_produk"+id).val();
    var total_poin  = $("#tabel_total_poin_produk"+id).val();

    $("[name=popup_id_table]").val(id_table);
    $("[name=popup_produk]").html('<option value="'+id_produk+'" nama="'+nama_produk+'">'+nama_produk+'</option>');
    $("[name=popup_harga]").val(harga);
    $("[name=popup_jumlah]").val(jumlah);
    $("[name=popup_total]").val(total);
    $("[name=popup_poin]").val(poin); 
    $("[name=popup_total_poin]").val(total_poin);

    $("#help_popup_harga").text(accounting.formatMoney(harga));
    $("#help_popup_jumlah").text(format_angka(jumlah));
    $("#help_popup_total").text(accounting.formatMoney(total));
    $("#help_popup_totalpoin").text(format_angka(total_poin,0)+" Poin");
    $('#group-barang').addClass('hide');
    $('#group-satuan').addClass('hide');
    $('#group-produk').removeClass('hide');
    $('[name=popup_status]').val(status);
    get_jum();
    console.log(poin);
    $("#modal-form").modal('show');
  }

  function get_edit(id){
    $.ajax({
        url: "{{ url('redeem_get_edit')}} ",
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
                '<div id="list_poin'+respon.produk[i].id+'" class="text-right">'+format_angka(respon.produk[i].poin,0)+" Poin"+'</div>',
                '<div id="list_jumlah'+respon.produk[i].id+'" class="text-right">'+format_angka(respon.produk[i].jumlah)+" "+respon.produk[i].nama_satuan+'</div>',
                '<div id="list_totalpoin'+respon.produk[i].id+'" class="text-right">'+format_angka(respon.produk[i].total_poin,0)+" Poin"+'</div>',
                '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+respon.produk[i].id+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this),'+respon.produk[i].id+')""><i class="fa fa-trash"></i></button></div>'+
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
                '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+respon.produk[i].id+'" value="'+respon.produk[i].jumlah+'">'+
                '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+respon.produk[i].id+'" value="'+respon.produk[i].harga+'">'+
                '<input type="hidden" name="tabel_total[]" id="tabel_total'+respon.produk[i].id+'" value="'+respon.produk[i].total+'">'+
                '<input type="hidden" name="tabel_status[]" id="tabel_status'+respon.produk[i].id+'" value="'+respon.produk[i].status+'">'+
                '<input type="hidden" name="tabel_statusredeem[]" id="tabel_statusredeem'+respon.produk[i].id+'" value="'+respon.produk[i].status_redeem+'" >'+
                '<input type="hidden" name="tabel_poin_produk" id="tabel_poin_produk'+respon.produk[i].id+'" value="'+respon.produk[i].poin+'">'+
                '<input type="hidden" name="tabel_total_poin_produk" id="tabel_total_poin_produk'+respon.produk[i].id+'" value="'+respon.produk[i].total_poin+'">'
                ]).draw(false);

            }


            get_jum();
            get_subtotal();
            netto();
          }

        }
      })
  }

  function hapus(id, id_table){
    if(id_kasir == ''){
        netto_poin(id.attr('data-id'));
    }else{
        if(id_table){
          netto_poin(id_table);
        }else{
          netto_poin(id.attr('data-id'));
        }
    }
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
    var poin    = parseFloat($("[name=popup_poin]").val()) || 0;
    var total   = parseFloat(jumlah*harga);
    var total_poin = parseFloat(jumlah*poin);
    $("[name=popup_total]").val(total);
    $("#help_popup_total").text(accounting.formatMoney(total));

    $("[name=popup_total_poin]").val(total_poin);
    $("#help_popup_totalpoin").text(format_angka(total_poin,0)+" Poin");
  }

  function get_subtotal(this_){
    var poin = parseFloat(0);
  var pelanggan_poin = parseFloat($("[name=poin]").val());
  var produk_poin = parseFloat($("[name=popup_poin]").val());

  $('input[name^="tabel_total_poin_produk"]').each(function(){
    var val_poin = ($(this).val());
    poin += parseFloat(val_poin);
  })

  if(id_kasir == ''){
    pelanggan_kurang_poin = parseFloat(pelanggan_poin-poin);
  }else{
    pelanggan_kurang_poin = parseFloat(pelanggan_poin);
  }

  $("#td_poin").text(format_angka(parseFloat(poin),0)+" Poin");
  $("[name=td_poin]").val(poin);

  $("[name=poin]").val(pelanggan_kurang_poin);
  netto();
  }
  
  function get_poinsubtotal(this_){
  var pelanggan_poin = parseFloat($("[name=poin]").val());
  var poin = parseFloat(0);
  $('input[name^="tabel_total_poin_produk"]').each(function(){
    var val_poin = ($(this).val());
    poin += parseFloat(val_poin);
  })
  var popup_poin = parseFloat($("[name=popup_poin]").val());
  
  pelanggan_kurang_poin = parseFloat(pelanggan_poin-popup_poin);
  $("#td_poin").text(format_angka(parseFloat(poin),0)+" Poin");
  $("[name=td_poin]").val(poin);

  $("[name=poin]").val(pelanggan_kurang_poin);
  netto();
}

  function netto(){
    var carabayar = $("[name=carabayar]").val();
    var total_metod = parseFloat($("[name=total_viabayar]").val()) || 0;
    var total_metod2= parseFloat($("[name=total_viabayar2]").val()) || 0;
    var total_poin  = parseFloat($("[name=td_poin]").val()) || 0; 
    var netto = 0;

    if(carabayar == '1'){
    $("[name=td_netto]").val(parseFloat(0));
    $("#td_netto").text(accounting.formatMoney(parseFloat(0)));
    }else{
    $("[name=td_netto]").val(netto);
    $("#td_netto").text(accounting.formatMoney(netto));
    }

    $("[name=total_viabayar").val(total_metod);
    $("#help_popup_ttlviabayar").text(accounting.formatMoney(total_metod));
    $("[name=total_viabayar2").val(total_metod2);
    $("#help_popup_ttlviabayar2").text(accounting.formatMoney(total_metod2));

    $("[name=td_poin]").val(total_poin);
    $("#td_poin").text(format_angka(total_poin,0)+" Poin");
  };

function netto_poin(id){
  var poin = $("#tabel_total_poin_produk"+id).val();
  var pelanggan_poin = $("[name=poin]").val();
  var d_poin = parseFloat(parseFloat(poin)+parseFloat(pelanggan_poin));
  $("[name=poin]").val(d_poin);
}

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

    $("#hapusmetodebayar").on("click", hapusRow);

  }

  function hapusRow() {
        $(this).parents(".tr_metodebayar").remove();
        $("#td_addmetodebayar").show();
        $("[name=total_viabayar]").addClass('hidden');
        $("[name=help_popup_ttlviabayar]").addClass('hidden');
  };

  function get_totalmetod2(jum){
    var jum = jum.val();
    $("#help_popup_ttlviabayar2").text(accounting.formatMoney(jum));
  }

  function get_hide(){
    $("#div_poinku").hide();
    $("#div_pembayaran").hide();
  }
  
  function get_pelanggan(this_=''){
  // var id = $(this).val();
  var gudang = $("[name=gudang]").val();
  $.ajax({
      url: "{{ url('redeem_attr_get_pelanggan')}} ",
      type: 'post',
      data: {gudang : gudang},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
          var select = $('[name=id_pelanggan]')[0].selectize;
          if(respon.length > 0){
            for(i in respon){
              var selected = "";
              if(this_ != ''){
                var id = this_.val();
                if(respon[i].id_pelanggan == id){
                  var selected = "selected";
                }
              }

              var data = {
                'value':respon[i].id_pelanggan,
                'text':respon[i].nama_pelanggan,
              };
              
              select.addOption(data);
            }
          }

          // select.refreshOptions();
      }

  })
}

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

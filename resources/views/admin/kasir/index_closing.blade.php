<?php $hal = "kasirclosing"; ?>
@extends('layouts.admin.master')
@section('title', 'Closing Penjualan')

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
    Closing Penjualan
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Closing Penjualan</h3>
        </div>
        
        <!-- <a href="{{url('kasir_tambah')}}" style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a> -->
        <!-- <button class="btn btn-primary" style="margin-bottom:20px;margin-left:10px;" type="button" id="btn_posting"> <i class="fa  fa-check-o"></i>Closing</button> -->
        <!-- /.box-header -->

        <div class="box-body">
          <div class="row">
          <form role="form" class="form form_closing" action="javascript:;" method="post" autocomplete="off" >
            <div class="col-md-12"> 
            <div class="col-md-3">
              <div class="form-group">
                 <label>Gudang</label> 
                 <select class="form-control select2" name="gudang" style="width: 100%;">
                  @foreach($data['gudang'] as $d)
                    <option value="{{$d->id_gudang}}" nama="{{$d->nama_gudang}}" alamat="{{$d->alamat_gudang}}">{{$d->nama_gudang}}</option>
                  @endforeach  
                 </select>
                </div>
              </div> 
            <div class="col-md-3">
              <div class="form-group">
                  <label>Tanggal</label> 
                 <input type="text" class="form-control datepicker" name="tanggal">
                </div>
              </div>  
              <div class="col-md-6">          
                <div class="form-group"> 
                 <button type="submit" class="btn btn-success" style="margin-top: 25px;"><b><i class="fa fa-search"></i></b> Filter</button>
                 <!--<button class="btn btn-primary hide" type="button" id="btn_posting_check" style="margin-top: 25px;"><i class="fa fa-check-circle"></i> Closing</button>
                 @php if($data['group'] == 1){ @endphp
                 <button class="btn btn-warning hide" type="button" id="btn_posting_uncheck" style="margin-top: 25px;"><i class="fa fa-times-circle"></i> Closing</button>
                 @php } @endphp-->
                 <button class="btn btn-primary hide" type="button" id="btn_posting_check" style="margin-top: 25px;"><i class="fa fa-check-circle"></i> Closing</button>
                 <button class="btn btn-warning hide" type="button" id="btn_posting_uncheck" style="margin-top: 25px;"><i class="fa fa-times-circle"></i> Closing</button>
                 <button class="btn btn-primary hide" type="button" id="btn_posting_print" style="margin-top: 25px;"><i class="fa fa-print"></i> Print</button>
                </div>
              </div>
            </div>
          </form>
          </div>
          <div class="row">
            <form role="form" class="form form_closing_hidden" action="javascript:;" method="POST" autocomplete="off">
              <input type="hidden" name="gudang" id="gudang_hidden" value="{{isset($data['gudang'][0]->id_gudang)?$data['gudang'][0]->id_gudang:'9'}}">
              <input type="hidden" name="tanggal" id="tanggal_hidden" value="">
            </form>
          </div>
        </div>
        <hr>
        <div class="box-body">
          <div class="table-responsive">
            {{-- <table class="table table-bordered table-striped" style="padding-top: 0px;">
                  <thead>
                  <tr>
                    <th style="width:30%;vertical-align: middle;">Jumlah Nota</th>
                    <td style="width:50%" id="total" class="td_total"></td>
                    <td style="width:20%"><label class="label label-sm label-success" id="status"></label><input type="hidden" name="tanggal_bayar"></td>
                  </tr>
                  </thead>
            </table> --}}

            <table class="table table-bordered table-striped" id="table_nota" style="padding-top: 0px;">
              <thead>
                  <tr>
                    <th style="width:30%;vertical-align: middle;" rowspan="2">Jumlah Nota</th>
                    <td style="width:50%" id="total_closing" class="td_total_closing"></td>
                    <td style="width:20%"><label class="label label-sm label-success" id="status_closing"></label><input type="hidden" name="tanggal_bayar"></td>
                  </tr>
                  <tr>
                    <td style="width: 50%" id="total_unclosing" class="td_total_unclosing"></td>
                    <td style="width: 20%"><label class="label label-sm label-warning" id="status_unclosing"></label></td>
                  </tr>
                  </thead>
            </table>
          </div>
          <div class="table-responsive">
            <table id="datatable1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width:30%">Nama</th>
                  <th style="width:70%" >Nominal</th>                
                </tr>             
              </thead>
              <tbody>
              </tbody>
            
              <tr>
                  <th style="width:30%">Total:</th>
                  <th style="width:70%;" id="totall" class=" text-right">0</th>
              </tr>
              <tr>
                  <th style="width:30%">Total + Ongkos Kirim:</th>
                  <th style="width:70%;" id="totall_ongkir" class=" text-right">0</th>
              </tr>
            </table>
          </div>
          <div class="row" style="padding-top:5%;">
            <div class="col-md-12">
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a href="#tabs_produk" data-toggle="tab" aria-expanded="true">Penjualan Produk</a>
                  </li>
                  <li>
                    <a href="#tabs_barang" data-toggle="tab" aria-expanded="false">Penjualan Barang</a>
                  </li>
                  <li>
                    <a href="#tabs_produk_pernota" data-toggle="tab" aria-expanded="false">Penjualan PerNota & PerProduk</a>
                  </li>
                  <li>
                    <a href="#tabs_barang_pernota" data-toggle="tab" aria-expanded="false">Penjualan PerNota & PerBarang</a>
                  </li>
                  <li>
                    <a href="#tabs_botol" data-toggle="tab" aria-expanded="false">Penjualan Botol</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tabs_produk">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped no-margin table_tabs_produk" width="100%">
                        <thead>
                          <tr>
                            <th class="text-center" style="width:5%">No</th>
                            <th class="text-center" style="width:60%">Nama Produk</th>
                            <th class="text-center" style="width:10%">Jumlah</th>
                            <th class="text-center" style="width:25%">Nominal</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th colspan="2" class="text-left">Total</th>
                            <th class="text-center"></th>
                            <th class="text-right"></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="tabs_barang">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped no-margin table_tabs_barang" width="100%">
                        <thead>
                          <tr>
                            <th class="text-center" style="width:5%">No</th>
                            <th class="text-center" style="width:60%">Nama Barang</th>
                            <th class="text-center" style="width:10%">Jumlah</th>
                            <th class="text-center" style="width:25%">Nominal</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th colspan="2" class="text-left">Total</th>
                            <th class="text-center"></th>
                            <th class="text-right"></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="tabs_produk_pernota">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped no-margin table_tabs_produk_pernota" width="100%">
                        <thead>
                          <tr>
                            <th class="text-center" style="width:5%;">No</th>
                            <th class="text-center" style="width:10%;">No Faktur</th>
                            <th class="text-center" style="width:10%;">Tgl Faktur</th>
                            <th class="text-center" style="width:20%;">Nama Pelanggan</th>
                            <th class="text-center" style="width:30%;">Nama Produk</th>
                            <th class="text-center" style="width:10%;">Jumlah</th>
                            <th class="text-center" style="width:15%;">Nominal</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th colspan="5" class="text-left">Total</th>
                            <th class="text-center"></th>
                            <th class="text-right"></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="tabs_barang_pernota">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped no-margin table_tabs_barang_pernota" width="100%">
                        <thead>
                          <tr>
                            <th class="text-center" style="width:5%;">No</th>
                            <th class="text-center" style="width:10%;">No Faktur</th>
                            <th class="text-center" style="width:10%;">Tgl Faktur</th>
                            <th class="text-center" style="width:20%;">Nama Pelanggan</th>
                            <th class="text-center" style="width:30%;">Nama Barang</th>
                            <th class="text-center" style="width:10%;">Jumlah</th>
                            <th class="text-center" style="width:15%;">Nominal</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th colspan="5" class="text-left">Total</th>
                            <th class="text-center"></th>
                            <th class="text-right"></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="tabs_botol">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped no-margin table_tabs_botol" width="100%">
                        <thead>
                          <tr>
                            <th class="text-center" style="width:5%;">No</th>
                            <th class="text-center" style="width:30%;">Nama Barang</th>
                            <th class="text-center" style="width:10%;">Jumlah</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th colspan="2" class="text-left">Total</th>
                            <th class="text-center"></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  {{-- <div class="tab-pane" id="tabs_perpelanggan">
                    <div class="table-responsive">
                      
                    </div>
                  </div> --}}
                </div>
              </div>
            </div>
          </div>
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
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
  accounting.settings = {
          currency: {
              symbol: "Rp ",
              precision: 0,
              thousand: ".",
              decimal: ",",
              format: {
                  pos: '%s %v',
                  neg: '%s (%v)',
                  zero: '%s %v'
              },
          },
          number: {
              precision: 0,  // default precision on numbers is 0
              thousand: ".",
              decimal: ","
          }
  };
</script>
<script type="text/javascript">

var table, save_method;
var table_tabs_produk,table_tabs_barang,table_tabs_produk_pernota,table_tabs_barang_pernota,table_tabs_botol;
$(function(){
  table = $('#datatable1').DataTable({    
    "processing" : true,
    "searching":false,
    "ordering": false,
    "paginate": false,
    // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "lengthMenu": [[-1], ["All"]],
    "ajax" : {
      "url" : "{{url('kasirclosing_get_omset')}}",
      "type" : "GET",
      data: function(d) {
        // d.gudang  = $('.form_closing select[name=gudang]').val(); 
        // d.tanggal = $('.form_closing input[name=tanggal]').val();
        d.gudang  = $('.form_closing_hidden #gudang_hidden').val(); 
        d.tanggal = $('.form_closing_hidden #tanggal_hidden').val();
      }
    },
    columnDefs: [{
        targets: -1,
        className: 'text-right'       
        }]    
  });

  table_tabs_produk = $('.table_tabs_produk').DataTable({
    "processing" : true,
    "searching":true,
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "ajax" : {
      "url" : "{{url('kasirclosing_get_tabsproduk')}}",
      "type" : "GET",
      data: function(d) {
        d.gudang  = $('.form_closing_hidden #gudang_hidden').val(); 
        d.tanggal = $('.form_closing_hidden #tanggal_hidden').val();
      }
    },
    "columnDefs" : [
      {targets: [0,2], className: 'text-center'},
      {targets: 3, className: 'text-right'},
    ],
    "footerCallback" : function( tfoot, data, start, end, display ) {
        var api = this.api();
        var jumlah = 0;
        var nominal = 0;
        for (var i = 0; i < data.length; i++) {
          jumlah += parseFloat(data[i][2].replace('.',''));
          nominal += parseFloat(data[i][3].replace('Rp ','').replace('.',''));
        }
        $(api.column(2).footer()).html(format_angka_nodesimal(jumlah));
        $(api.column(3).footer()).html(format_uang(nominal));
    }  
  });

  table_tabs_barang = $('.table_tabs_barang').DataTable({
    "processing" : true,
    "searching":true,
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "ajax" : {
      "url" : "{{url('kasirclosing_get_tabsbarang')}}",
      "type" : "GET",
      data: function(d) {
        d.gudang  = $('.form_closing_hidden #gudang_hidden').val(); 
        d.tanggal = $('.form_closing_hidden #tanggal_hidden').val();
      }
    },
    "columnDefs" : [
      {targets: [0,2], className: 'text-center'},
      {targets: 3, className: 'text-right'},
    ],
    "footerCallback" : function( tfoot, data, start, end, display ) {
        var api = this.api();
        var jumlah = 0;
        var nominal = 0;
        for (var i = 0; i < data.length; i++) {
          jumlah += parseFloat(data[i][2].replace('.',''));
          nominal += parseFloat(data[i][3].replace('Rp ','').replace('.',''));
        }
        $(api.column(2).footer()).html(format_angka_nodesimal(jumlah));
        $(api.column(3).footer()).html(format_uang(nominal));
    }   
  });

  table_tabs_produk_pernota = $('.table_tabs_produk_pernota').DataTable({
    "processing" : true,
    "searching":true,
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "ajax" : {
      "url" : "{{url('kasirclosing_get_tabsprodukpernota')}}",
      "type" : "GET",
      data: function(d) {
        d.gudang  = $('.form_closing_hidden #gudang_hidden').val(); 
        d.tanggal = $('.form_closing_hidden #tanggal_hidden').val();
      }
    },
    "columnDefs" : [
      {targets: [0,2,5], className: 'text-center'},
      {targets: 6, className: 'text-right'},
    ],
    "footerCallback" : function( tfoot, data, start, end, display ) {
        var api = this.api();
        var jumlah = 0;
        var nominal = 0;
        for (var i = 0; i < data.length; i++) {
          jumlah += parseFloat(data[i][5].replace('.',''));
          nominal += parseFloat(data[i][6].replace('Rp ','').replace('.',''));
        }
        $(api.column(5).footer()).html(format_angka_nodesimal(jumlah));
        $(api.column(6).footer()).html(format_uang(nominal));
    }   
  });

  table_tabs_barang_pernota = $('.table_tabs_barang_pernota').DataTable({
    "processing" : true,
    "searching":true,
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "ajax" : {
      "url" : "{{url('kasirclosing_get_tabsbarangpernota')}}",
      "type" : "GET",
      data: function(d) {
        d.gudang  = $('.form_closing_hidden #gudang_hidden').val(); 
        d.tanggal = $('.form_closing_hidden #tanggal_hidden').val();
      }
    },
    "columnDefs" : [
      {targets: [0,2,5], className: 'text-center'},
      {targets: 6, className: 'text-right'},
    ],
    "footerCallback" : function( tfoot, data, start, end, display ) {
        var api = this.api();
        var jumlah = 0;
        var nominal = 0;
        for (var i = 0; i < data.length; i++) {
          jumlah += parseFloat(data[i][5].replace('.',''));
          nominal += parseFloat(data[i][6].replace('Rp ','').replace('.',''));
        }
        $(api.column(5).footer()).html(format_angka_nodesimal(jumlah));
        $(api.column(6).footer()).html(format_uang(nominal));
    }   
  });

  table_tabs_botol = $('.table_tabs_botol').DataTable({
    "processing" : true,
    "searching":true,
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "ajax" : {
      "url" : "{{url('kasirclosing_get_tabsbotol')}}",
      "type" : "GET",
      data: function(d) {
        d.gudang  = $('.form_closing_hidden #gudang_hidden').val(); 
        d.tanggal = $('.form_closing_hidden #tanggal_hidden').val();
      }
    },
    "columnDefs" : [
      {targets: [0,2], className: 'text-center'}
    ],
    "footerCallback" : function( tfoot, data, start, end, display ) {
        var api = this.api();
        var jumlah = 0;
        for (var i = 0; i < data.length; i++) {
          jumlah += parseFloat(data[i][2].replace('.',''));
        }
        $(api.column(2).footer()).html(format_angka_nodesimal(jumlah));
    }   
  });

  $.ajax({
      url: "{{url('kasirclosing_get_jumlahnota')}}",
      type: 'get',
      async: true,
      data: $('.form_closing_hidden').serialize(),
      dataType: "json",
      success: function (data) {
      /*var isi = data[0]['jumlah_nota'] ;
      var status = data[0]['status'];
      $("#status").text('');
        $('.td_total').html(parseFloat(isi));
        if(status == 1){
          $("#btn_posting_check").removeClass('hide');
          $("#btn_posting_uncheck").addClass('hide');
          $("#btn_posting_print").removeClass('hide');
        }else if(status == 2){
          $("#btn_posting_check").addClass('hide');
          $("#btn_posting_uncheck").removeClass('hide');
          $("#btn_posting_print").removeClass('hide');
          $("#status").text('Sudah Closing');
        }else{
          $("#btn_posting_check").addClass('hide');
          $("#btn_posting_uncheck").addClass('hide');
          $("#btn_posting_print").addClass('hide');
        }*/
      // var isi = data['data'][0]['jumlah_nota'];
      // var status = data['data'][0]['status'];
      // var today = new Date();
      // var a = new Date($('.form_closing input[name=tanggal]').datepicker('getDate'));
      // var group = data['group'];
      // $("#status").text('');
      // $('.td_total').html(parseFloat(isi));
      // status_closing(status,a,today,group);
      var group             = data['group'];
      if(data['nota'] != null){
      var jumlah_closing    = data['nota']['jumlah_nota_closing'];
      var jumlah_unclosing  = data['nota']['jumlah_nota_unclosing'];
      var status_closing    = data['nota']['status_closing'];
      var status_unclosing  = data['nota']['status_unclosing'];
          
      $("#status_closing").text("Sudah Closing");
      $("#status_unclosing").text("Belum Closing");
      $("#total_closing").text(jumlah_closing);
      $("#total_unclosing").text(jumlah_unclosing);
      if(jumlah_unclosing > 0){
        //   $("#btn_posting_check").removeClass('hide');
            if(group == 8){
              $("#btn_posting_check").addClass('hide');
            }else{
              $("#btn_posting_check").removeClass('hide');
            }
          if(group == '1' || group == '6'){
            if(jumlah_closing > 0){
              $("#btn_posting_uncheck").removeClass('hide');
            }else{
              $("#btn_posting_uncheck").addClass('hide');
            }
          }else{
            $("#btn_posting_uncheck").addClass('hide');
          }
          $("#btn_posting_print").addClass('hide');
      }else{
        if(group == '1' || group == '6'){
          $("#btn_posting_check").addClass('hide');
          $("#btn_posting_uncheck").removeClass('hide');
          $("#btn_posting_print").removeClass('hide');
        }else{
          $("#btn_posting_check").addClass('hide');
          $("#btn_posting_uncheck").addClass('hide');
          $("#btn_posting_print").removeClass('hide');
        }
      }
      }else{
        $("#status_closing").text("");
        $("#status_unclosing").text("");
        $("#total_closing").text('0');
        $("#total_unclosing").text('0');
        $("#btn_posting_check").addClass('hide');
        $("#btn_posting_uncheck").addClass('hide');
        $("#btn_posting_print").addClass('hide');
      }
      }
  });
  $.ajax({
      url: "{{url('kasirclosing_get_jumlahomset')}}",
      type: 'get',
      async: true,
      data: $('.form_closing_hidden').serialize(),
      dataType: "json",
      success: function (data) {
      var isi = data[0]['data'] ;
      var tanggal = data[0]['tanggal_bayar'];
        $('#totall').html(accounting.formatMoney(parseFloat(isi), "Rp. ", 2, ".", ","));
        $('[name=tanggal_bayar]').val(tanggal);
      }
  });
  $.ajax({
      url: "{{url('kasirclosing_get_jumlahomsetdanongkir')}}",
      type: 'get',
      async: true,
      data: $('.form_closing_hidden').serialize(),
      dataType: "json",
      success: function (data) {
        var isi = data[0]['data'] ;
        $('#totall_ongkir').html(accounting.formatMoney(parseFloat(isi), "Rp. ", 2, ".", ","));
      }
  });

});

$(document).on("submit","form.form_closing",function(e){
    $("form.form_closing_hidden input[name=gudang]").val($("form.form_closing select[name=gudang]").val());
    $("form.form_closing_hidden input[name=tanggal]").val($("form.form_closing input[name=tanggal]").val());
    table.ajax.reload();
    table_tabs_produk.ajax.reload();
    table_tabs_barang.ajax.reload();
    table_tabs_produk_pernota.ajax.reload();
    table_tabs_barang_pernota.ajax.reload();
    table_tabs_botol.ajax.reload();
    
    // var jum_tabs_produk = $("table_tabs_produk").dataTable().column(2).data().sum();
    // $('#sum_jumlah_tabs_produk').text(jum_tabs_produk);
      $.ajax({
        url: "{{url('kasirclosing_get_jumlahnota')}}",
        type: 'get',
        async: true,
        data: $('.form_closing_hidden').serialize(),
        dataType: "json",
        success: function (data) {
        /*var isi = data[0]['jumlah_nota'] ;
        var status = data[0]['status'];
        $("#status").text('');
          $('.td_total').html(isi);
          if(status == 1){
            $("#btn_posting_check").removeClass('hide');
            $("#btn_posting_uncheck").addClass('hide');
            $("#btn_posting_print").removeClass('hide');
          }else if(status == 2){
            $("#btn_posting_check").addClass('hide');
            $("#btn_posting_uncheck").removeClass('hide');
            $("#btn_posting_print").removeClass('hide');
            $("#status").text('Sudah Closing');
          }else{
            $("#btn_posting_check").addClass('hide');
            $("#btn_posting_uncheck").addClass('hide');
            $("#btn_posting_print").addClass('hide');
          }*/
        // var isi = data['data'][0]['jumlah_nota'] ;
        // var status = data['data'][0]['status'];
        // var today = new Date();
        // var a = new Date($('.form_closing input[name=tanggal]').datepicker('getDate'));
        // var group = data['group'];
        // $("#status").text('');
        // $('.td_total').html(isi);
        // status_closing(status,a,today,group);
        
        
        var group             = data['group'];
        if(data['nota'] != null){
        var jumlah_closing    = data['nota']['jumlah_nota_closing'] || 0;
        var jumlah_unclosing  = data['nota']['jumlah_nota_unclosing'] || 0;
        var status_closing    = data['nota']['status_closing'];
        var status_unclosing  = data['nota']['status_unclosing'];
        
        $("#status_closing").text("Sudah Closing");
        $("#status_unclosing").text("Belum Closing");
        $("#total_closing").text(jumlah_closing);
        $("#total_unclosing").text(jumlah_unclosing);
        // console.log($data['nota'].length());
        if(jumlah_unclosing > 0){
            $("#btn_posting_check").removeClass('hide');
            if(group == '1' || group == '6'){
              if(jumlah_closing > 0){
                $("#btn_posting_uncheck").removeClass('hide');
              }else{
                $("#btn_posting_uncheck").addClass('hide');
              }
            }else{
              $("#btn_posting_uncheck").addClass('hide');
            }
            $("#btn_posting_print").addClass('hide');
        }else{
          if(group == '1' || group == '6'){
            $("#btn_posting_check").addClass('hide');
            $("#btn_posting_uncheck").removeClass('hide');
            $("#btn_posting_print").removeClass('hide');
          }else{
            $("#btn_posting_check").addClass('hide');
            $("#btn_posting_uncheck").addClass('hide');
            $("#btn_posting_print").removeClass('hide');
          }
        }
        }else{
          $("#status_closing").text("");
          $("#status_unclosing").text("");
          $("#total_closing").text('0');
          $("#total_unclosing").text('0');
          $("#btn_posting_check").addClass('hide');
          $("#btn_posting_uncheck").addClass('hide');
          $("#btn_posting_print").addClass('hide');
        }

        }
      });
      $.ajax({
        url: "{{url('kasirclosing_get_jumlahomset')}}",
        type: 'get',
        async: true,
        data: $('.form_closing_hidden').serialize(),
        dataType: "json",
        success: function (data) {
          var isi = data[0]['data'];
          var tanggal = data[0]['tanggal_bayar'];
          $('#totall').html(accounting.formatMoney(parseFloat(isi), "Rp. ", 2, ".", ","));
          $('[name=tanggal_bayar]').val(tanggal);
        }
      });
      $.ajax({
        url: "{{url('kasirclosing_get_jumlahomsetdanongkir')}}",
        type: 'get',
        async: true,
        data: $('.form_closing_hidden').serialize(),
        dataType: "json",
        success: function (data) {
          var isi = data[0]['data'];
          $('#totall_ongkir').html(accounting.formatMoney(parseFloat(isi), "Rp. ", 2, ".", ","));
        }
      });    
});
$(document).on("click","#btn_posting_check",function(e){
  if(confirm("Apakah yakin data akan di closing?")){
  $.ajax({
        url: "{{url('kasirclosing_get_checkclosing')}}",
        type: 'get',
        async: true,
        data: $('.form_closing_hidden').serialize(),
        dataType: "json",
        success: function(data) {
          /*if(data==1){
            $("#btn_posting_check").addClass('hide');
            $("#btn_posting_uncheck").removeClass('hide');
            $("#status").text('Sudah Closing');
          }else{
            $("#btn_posting_check").removeClass('hide');
            $("#btn_posting_uncheck").addClass('hide');
            $("#status").text('');
          }*/
          $("#total_closing").text(data['jumlah_nota_closing']);
          $("#total_unclosing").text(data['jumlah_nota_unclosing']);
          if(data['closing'] == 0){
            $("#btn_posting_check").addClass('hide');
          }else{
            $("#btn_posting_check").removeClass('hide');
          }
          if(data['unclosing'] == 0){
            $("#btn_posting_uncheck").addClass('hide');
          }else{
            $("#btn_posting_uncheck").removeClass('hide');
          }
          if(data['print'] == 0){
            $("#btn_posting_print").addClass('hide');
          }else{
            $("#btn_posting_print").removeClass('hide');
          }
        }
      });
  }
});
$(document).on("click","#btn_posting_uncheck",function(e){
  if(confirm("Apakah yakin data akan dibatalkan closing?")){
  $.ajax({
        url: "{{url('kasirclosing_get_uncheckclosing')}}",
        type: 'get',
        async: true,
        data: $('.form_closing_hidden').serialize(),
        dataType: "json",
        success: function(data) {
          /*if(data==1){
            $("#btn_posting_check").removeClass('hide');
            $("#btn_posting_uncheck").addClass('hide');
          }else{
            $("#btn_posting_check").addClass('hide');
            $("#btn_posting_uncheck").removeClass('hide');
          }*/
          $("#total_closing").text(data['jumlah_nota_closing']);
          $("#total_unclosing").text(data['jumlah_nota_unclosing']);
          if(data['closing'] == 0){
            $("#btn_posting_check").addClass('hide');
          }else{
            $("#btn_posting_check").removeClass('hide');
          }
          if(data['unclosing'] == 0){
            $("#btn_posting_uncheck").addClass('hide');
          }else{
            $("#btn_posting_uncheck").removeClass('hide');
          }
          if(data['print'] == 0){
            $("#btn_posting_print").addClass('hide');
          }else{
            $("#btn_posting_print").removeClass('hide');
          }
        }
      });
  }
});
$(document).on("click","#btn_posting_print",function(e){
  /*if(confirm("Apakah yakin data akan dibatalkan closing?")){
  $.ajax({
        url: "{{url('kasirclosing_get_uncheckclosing')}}",
        type: 'get',
        async: true,
        data: $('.form_closing').serialize(),
        dataType: "json",
        success: function(data) {
          if(data==1){
            $("#btn_posting_check").removeClass('hide');
            $("#btn_posting_uncheck").addClass('hide');
          }else{
            $("#btn_posting_check").addClass('hide');
            $("#btn_posting_uncheck").removeClass('hide');
          }
        }
      });      
  }*/
    // var id_gudang  = $('.form_closing select[name=gudang]').val();
    // var tanggal = $('.form_closing input[name=tanggal]').val();
    var id_gudang  = $('.form_closing_hidden input[name=gudang]').val();
    var tanggal = $('.form_closing_hidden input[name=tanggal]').val();
    var url = "<?= url('kasirclosing_get_print')?>"+"/"+id_gudang+"/"+tanggal;
    window.open(url,'_blank');

});




$("#checkAll").click(function () {
     $('input:checkbox').not(this).prop('checked', this.checked);
 });
  
function deleteData(id){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "kasir_hapus",
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

$("#btn_posting").click(function(){
    var rows_selected = [];
    var jenis_selected = [];

    var rowcollection =  table.$("#check_verifikasi:checked", {"page": "all"});
    rowcollection.each(function(index,elem){
        var checkbox_value = $(elem).val();

        rows_selected.push(checkbox_value);
    
    })
    if(rows_selected.length > 0){
      if(confirm("Anda yakin akan memverifikasi data ini?")){    
        kirim_data(rows_selected);  
      }    
    }else{
        alert("Tidak Ada Data Terpilih!");
    }

  })

  function kirim_data(val){
    $.ajax({
      url: '{{ url('kasirposting_simpan') }}',
      type: 'post',
      dataType: 'json',
      data: {id : val},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
        table.ajax.reload();
      },
      error : function(){
          alert("Tidak dapat menyimpan data!");
      }
    })
  }
  
  function status_closing(status,tgl,today,group){
      if(status == 1){
        $("#btn_posting_check").removeClass('hide');
        $("#btn_posting_uncheck").addClass('hide');
        $("#btn_posting_print").removeClass('hide');
      }else if(status == 2){
        $("#btn_posting_check").addClass('hide');
        $("#btn_posting_print").removeClass('hide');
        // $("#btn_posting_uncheck").removeClass('hide');
        if(group == 1 || group == 6){
          $("#btn_posting_uncheck").removeClass('hide');
        }
        // else{
        //   if(tgl.getMonth() == today.getMonth() && tgl.getFullYear() == today.getFullYear()){
        //   $("#btn_posting_uncheck").removeClass('hide');
        //   }else{
        //   $("#btn_posting_uncheck").addClass('hide');  
        //   }
        // }
        $("#status").text('Sudah Closing');
      }else{
        $("#btn_posting_check").addClass('hide');
        $("#btn_posting_uncheck").addClass('hide');
        $("#btn_posting_print").addClass('hide');
      }
      
  }
</script>

@endsection

<?php $hal = "index"; ?>
@extends('layouts.admin.master')
@section('title', 'Admin-Index')

@section('css')

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
    Beranda
    <small>it all starts here</small>
  </h1>
</section>
<!-- Main content -->
<section class="content">

  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Filter</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
        title="Collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <form enctype="" class="form form_filter" action="javascript:;">
        <div class="col-md-12 div_filter">
          <div class="col-md-3">
            <div class="form-group">
              <label>Gudang</label>
              <select name="gudang" id="" class="form-control select2" style="width=100%" >
                <option value="">pilih</option>
                @foreach ($data['gudang'] as $item)
                <option value="<?php echo $item->id ?>"> <?php echo $item->nama ?></option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Tanggal</label>
              <input type="text" class="form-control" name="range" id="range" readonly>
            </div>
          </div>
          {{-- <div class="col-md-3">
            <div class="form-group">
              <label>Kecamatan</label>
              <select name="gudang" id="" class="form-control" style="width=100%" >
                <option value="">f</option>
              </select>
            </div>
          </div>  --}}
          <div class="col-md-3">
            <div class="form-group">
              <a href="javascript:;" style="margin-top: 25px;"  class="btn btn-danger  btn_batal_ganti "><b><i class="fa fa-reload"></i></b> Reset</a>
              <button type="submit" style="margin-top: 25px;" class="btn btn-success"><b><i class="fa fa-search"></i></b> Filter</button>
            </div>
          </div>
        </div>
      </form>
    </div>
    <!-- /.box-body -->

    <!-- /.box-footer-->
  </div>


  <!-- WIZARD -->
  <div class="col-md-12">
    <div class="stepwizard">
      <div class="stepwizard-row setup-panel">
        <div class="stepwizard-step col-xs-3">
          <a href="#step-1" type="button" class="btn btn-success btn-circle">1</a>
          <p><small>Omset</small></p>
        </div>
        <!-- <div class="stepwizard-step col-xs-2">
          <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
          <p><small>Pilihan</small></p>
        </div> -->
        <div class="stepwizard-step col-xs-2">
          <!-- <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a> -->
          <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
          <p><small>Produk</small></p>
        </div>
        <div class="stepwizard-step col-xs-2">
          <!-- <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a> -->
          <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
          <p><small>Barang</small></p>
        </div>
        <div class="stepwizard-step col-xs-3">
          <!-- <a href="#step-5" type="button" class="btn btn-default btn-circle" disabled="disabled">5</a> -->
          <a href="#step-5" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
          <p><small>Pelanggan</small></p>
        </div>
      </div>
    </div>

    <div class="panel panel-primary setup-content" id="step-1">
      <div class="panel-heading">
        <h3 class="panel-title">Jumlah Omset</h3>
      </div>
      <div class="panel-body" style="margin-left: -20px;margin-right: -20px">

        <div class="col-md-3"></div>
        <div class="col-md-6">

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
              <th style="width:30%">Total + Ongkir:</th>
              <th style="width:70%;" id="totall_ongkir" class=" text-right">0</th>
            </tr>
          </table>

          <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
        </div>
        <div class="col-md-3"></div>

      </div>
    </div>

    <div class="panel panel-primary setup-content" id="step-2">
      <div class="panel-heading">
        <h3 class="panel-title">Penjualan barang pilihan</h3>
      </div>
      <div class="panel-body" style="margin-left: -20px;margin-right: -20px">

        <div class="col-md-3"></div>
        <div class="col-md-6">

          <div class="table-responsive">
            <table class="table table-bordered table-striped table_barang_pilihan" id="table_barang_pilihan" style="width: 100%">
              <thead>
                <tr>
                  <th style="width: 10%">No</th>
                  <th style="width: 10%">Nama barang</th>
                  <th style="width: 10%">Terjual</th>
                  <th style="width: 70%">Stok Awal</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>

          <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
        </div>
        <div class="col-md-3"></div>

      </div>
    </div>

    <div class="panel panel-primary setup-content" id="step-3">
      <div class="panel-heading">
        <h3 class="panel-title">Penjualan Produk</h3>
      </div>
      <div class="panel-body" style="margin-left: -20px;margin-right: -20px">

        <div class="col-md-3"></div>
        <div class="col-md-6">

          <div class="table-responsive">
            <table class="table table-bordered table-striped" style="width: 100%;">
              <thead>
                <tr>
                  <th style="width:30%">Jumlah Nota</th>
                  <td style="width:70%" id="total" class="sss"></td>
                </tr>
              </thead>
            </table>
            <table class="table table-bordered table-striped table_produk" style="width: 100%;">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Produk</th>
                  <th>Qty</th>
                  <th class="text-right">Jumlah</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>

          <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
        </div>
        <div class="col-md-3"></div>

      </div>
    </div>

    <div class="panel panel-primary setup-content" id="step-4">
      <div class="panel-heading">
        <h3 class="panel-title">Penjualan Barang</h3>
      </div>
      <div class="panel-body" style="margin-left: -20px;margin-right: -20px">

        <div class="col-md-3"></div>
        <div class="col-md-6">

          <div class="table-responsive">
            <table class="table table-bordered table-striped table_barang" id="table_barang" width="100%">
              <thead>
                <tr>
                  <th style="width: 20%">#</th>
                  <th style="width: 20%">No</th>
                  <th style="width: 60%">Nama barang</th>
                  <!-- <th style="width: 20%">Jumlah</th> -->
                  <!-- <th style="width: 20%">Stok</th> -->
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
        </div>
        <div class="col-md-3"></div>

      </div>
    </div>

    <div class="panel panel-primary setup-content" id="step-5">
      <div class="panel-heading">
        <h3 class="panel-title">Pembelian Pelanggan Terbanyak</h3>
      </div>
      <div class="panel-body" style="margin-left: -20px;margin-right: -20px">

        <div class="col-md-3"></div>
        <div class="col-md-6">

          <div class="table-responsive">
            <table class="table table-bordered table-striped table_pelanggan" id="table_pelanggan" width="100%">
              <thead>
                <tr>
                  <th style="width: 20%">#</th>
                  <th style="width: 10%">No</th>
                  <th style="width: 50%">Pelanggan</th>
                  <!-- <th style="width: 20%">Produk</th> -->
                  <!-- <th style="width: 20%">Qty</th> -->
                  <th style="width: 20%">Jumlah</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>

          <!-- <button class="btn btn-primary nextBtn pull-right" type="button">Next</button> -->
        </div>
        <div class="col-md-3"></div>

      </div>
    </div>
  </div>
  <!-- WIZARD -->

  <!-- /.box -->
  <div class=" box-body">

  </div>
</section>
<!-- /.content -->
@endsection


@section('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
{{-- <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script> --}}
<script type="text/javascript">
var total = 0;
$(document).ready(function() {
  $.ajax({
    url: 'getjumlahnota',
    type: 'get',
    async: true,
    data: $('.form_filter').serialize(),
    dataType: "json",
    success: function (data) {
      var isi = data[0]['jumlah_nota'] ;
      // console.log(isi);
      $('.sss').html(parseFloat(isi));
    }
  });
  $.ajax({
    url: 'getjumlahomset',
    type: 'get',
    async: true,
    data: $('.form_filter').serialize(),
    dataType: "json",
    success: function (data) {
      var isi = data[0]['data'] ;
      // console.log(isi);
      $('#totall').html(accounting.formatMoney(isi, "Rp. ", 2, ".", ","));
    }
  });
  $.ajax({
    url: 'getjumlahomsetdanongkir',
    type: 'get',
    async: true,
    data: $('.form_filter').serialize(),
    dataType: "json",
    success: function (data) {
      var isi = data[0]['data'] ;
      // console.log(isi);
      $('#totall_ongkir').html(accounting.formatMoney(isi, "Rp. ", 2, ".", ","));
    }
  });
  var form = $('.form_filter').serialize();
  table1 = $('.table_produk').DataTable({
    "processing" : true,
    "searching":false,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "info": false,
    "sDom": '<"row view-filter"<"col-md-6-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-md-6-12"<"text-center"ip>>>',
    // "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
    "ajax" : {
      "url" : "gettabelproduk",
      "type" : "GET",
      data: function(d) {
        d.gudang=$('.form_filter select[name=gudang]').val();
        d.range=$('.form_filter input[name=range]').val();
      }
    }
  });
  table2 = $('.table_pelanggan').DataTable({
    "processing" : true,
    "searching":true,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "info": false,
    "sDom": '<"row view-filter"<"col-md-6-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-md-6-12"<"text-center"ip>>>',
    // "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
    "ajax" : {
      "url" : "gettabelpelanggan",
      "type" : "GET",
      data: function(d) {
        d.gudang=$('.form_filter select[name=gudang]').val();
        d.range=$('.form_filter input[name=range]').val();
      }
    },
    "columns": [
      {
        "className":      'details-control',
        "orderable":      false,
        "data":           null,
        "defaultContent": ''
      },
      { "data": "no" },
      { "data": "nama_pelanggan" },
      { "data": "jumlah_total" },
      // { "data": "alamat" },
      // { "data": "gudang" },
      // { "data": "status" },
      // { "data": "aksi" }
    ],
  });

  /* Formatting function for row details - modify as you need */
  function format ( d ) {
    // `d` is the original data object for the row
    return '<table id="show_tabel">'+
    '<tr>'+
    '<td>Nama</td>'+
    '<td> : '+d.nama_pelanggan+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Barang</td>'+
    '<td> : '+d.nama+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Jumlah</td>'+
    '<td> : '+d.jumlah_produk+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Total</td>'+
    '<td> : '+d.jumlah_total+'</td>'+
    '</tr>'+
    '</table>';
  }
  // Add event listener for opening and closing details
  $('#table_pelanggan tbody').on('click', 'td.details-control', function () {
    var tabelnya = $('#table_pelanggan').DataTable();
    var tr = $(this).closest('tr');
    var row = tabelnya.row( tr );

    if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }else {
      // Open this row
      row.child( format(row.data()) ).show();
      tr.addClass('shown');
    }
  });


  table = $('.table_barang').DataTable({
    "processing" : true,
    "searching":true,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "info": false,
    "sDom": '<"row view-filter"<"col-md-6-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-md-6-12"<"text-center"ip>>>',
    // "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
    "ajax" : {
      "url" : "getbarangbanyak",
      "type" : "GET",
      data: function(d) {
        d.gudang=$('.form_filter select[name=gudang]').val();
        d.range=$('.form_filter input[name=range]').val();
      }
    },
    "columns": [
      {
        "className":      'details-control',
        "orderable":      false,
        "data":           null,
        "defaultContent": ''
      },
      { "data": "no" },
      { "data": "barang_nama" },
      // { "data": "jumlah" },
      // { "data": "alamat" },
      // { "data": "gudang" },
      // { "data": "status" },
      // { "data": "aksi" }
    ],
  });

  /* Formatting function for row details - modify as you need */
  function format_brg_banyak ( d ) {
    // `d` is the original data object for the row
    return '<table>'+
    '<tr>'+
    '<td>Nama Barang</td>'+
    '<td> : '+d.barang_nama+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Jumlah</td>'+
    '<td> : '+d.jumlah+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Stok</td>'+
    '<td> : '+d.stok+'</td>'+
    '</tr>'+
    '</table>';
  }
  // Add event listener for opening and closing details
  $('#table_barang tbody').on('click', 'td.details-control', function () {
    var tabelnya = $('#table_barang').DataTable();
    var tr = $(this).closest('tr');
    var row = tabelnya.row( tr );

    if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }else {
      // Open this row
      row.child( format_brg_banyak(row.data()) ).show();
      tr.addClass('shown');
    }
  });



  table8 = $('.table_barang_pilihan').DataTable({

    "processing" : true,
    "searching":true,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "info": false,
    "sDom": '<"row view-filter"<"col-md-6-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-md-6-12"<"text-center"ip>>>',
    // "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
    "ajax" : {
      "url" : "getbarangpilihan",
      "type" : "GET",
      data: function(d) {
        d.gudang=$('.form_filter select[name=gudang]').val();
        d.range=$('.form_filter input[name=range]').val();
      }
    }
  });
  table3 = $('#datatable1').DataTable({

    "processing" : true,
    "searching":false,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    "paginate":false,
    "info": false,
    "sDom": '<"row view-filter"<"col-md-6-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-md-6-12"<"text-center"ip>>>',
    // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "ajax" : {
      "url" : "get_omset",
      "type" : "GET",
      data: function(d) {
        //  console.log(d);
        d.gudang=$('.form_filter select[name=gudang]').val();
        d.range=$('.form_filter input[name=range]').val();
      }
    },
    columnDefs: [{
      targets: -1,
      className: 'text-right'
    }]
  });
  $('input[name="range"]').daterangepicker({
    locale: {
      format: 'DD/MM/YYYY'
    }
  });
  $('.btn_batal_ganti').on('click',function(e){
    location.reload();
  });

  $(document).on("submit","form.form_filter",function(e){
    table1.ajax.reload();
    table2.ajax.reload();
    table3.ajax.reload();
    table.ajax.reload();
    table8.ajax.reload();


    $.ajax({
      url: 'getjumlahnota',
      type: 'get',
      async: true,
      data: $('.form_filter').serialize(),
      dataType: "json",
      success: function (data) {
        var isi = data[0]['jumlah_nota'] ;
        // console.log(isi);
        $('.sss').html(isi);
      }
    });
    $.ajax({
      url: 'getjumlahomset',
      type: 'get',
      async: true,
      data: $('.form_filter').serialize(),
      dataType: "json",
      success: function (data) {
        var isi = data[0]['data'] ;
        console.log(isi);
        $('#totall').html(accounting.formatMoney(parseFloat(isi), "Rp. ", 2, ".", ","));
      }
    });
    $.ajax({
      url: 'getjumlahomsetdanongkir',
      type: 'get',
      async: true,
      data: $('.form_filter').serialize(),
      dataType: "json",
      success: function (data) {
        var isi = data[0]['data'] ;
        console.log(isi);
        $('#totall_ongkir').html(accounting.formatMoney(parseFloat(isi), "Rp. ", 2, ".", ","));
      }
    });
  });

  function  visitorData (data) {
    Highcharts.chart('container', {
      chart: {
        type: 'column'
      },
      title: {
        text: 'Jumlah omset'
      },
      subtitle: {
        text: ' '
      },
      credits: {
        enabled: false
      },
      xAxis: {
        type: 'category'
      },
      yAxis: {
        title: {
          text: 'Jumlah Rp'
        }

      },
      legend: {
        enabled: false
      },
      plotOptions: {
        series: {
          borderWidth: 0,
          dataLabels: {
            enabled: true,
            format: '{point.y:.1f}'
          }
        }
      },

      tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b> of total<br/>'
      },
      series:[{
        name: "Browsers",
        colorByPoint: true,
        data: data,
      }],

    });
  }
});
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

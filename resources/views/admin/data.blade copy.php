<?php $hal = "index"; ?>
@extends('layouts.admin.master')
@section('title', 'Admin-Index')

@section('css')
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
      {{-- <h3 class="box-title">Title</h3> --}}

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
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
             <input type="text" class="form-control" name="range" id="range">
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
    <div class="col-md-12">
        <div class="col-md-6">
          <div class="box box-info">
            <div class="box-header with-border">
          <h3 class="box-title">Jumlah Omset</h3><br>
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:30%">Nama</th>
                <th style="width:70%">Nominal</th>                
              </tr>             
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        </div>
        </div>
        <div class="col-md-6">
            <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Penjualan Produk</h3><br>
            
            </div>
            <table class="table table-bordered table-striped">
              <thead>
              <tr>
                <th style="width:30%">Jumlah Nota</th>
                <td style="width:70%" id="total" class="sss"></td>
              </tr>
              </thead>
            </table>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin table_produk">
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
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            
            <!-- /.box-footer -->
          </div>
        </div>
        <div class="col-md-12">
           <div class="col-md-6">
            <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Penjualan Barang</h3><br>
            
            </div>
            
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin table_barang">
                  <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama barang</th>
                    <th class="text-right">Jumlah</th> 
                  </tr>
                  </thead>
                  <tbody>
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            
            <!-- /.box-footer -->
          </div>
        </div>
        <div class="col-md-6">
            <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Pembelian Pelanggan Terbanyak</h3><br>
            
            </div>
            
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin table_pelanggan">
                  <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama pelanggan</th>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th class="text-right">Jumlah</th> 
                  </tr>
                  </thead>
                  <tbody>
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            
            <!-- /.box-footer -->
          </div>
        </div>
      </div>
       
      </div>
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
          $('.sss').html(isi);
        }
      });
    });
  var form = $('.form_filter').serialize();
   table1 = $('.table_produk').DataTable({
    "processing" : true,
    "searching":false,
    "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
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
    "searching":false,
    "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
    "ajax" : {
      "url" : "gettabelpelanggan",
      "type" : "GET",
      data: function(d) {
       d.gudang=$('.form_filter select[name=gudang]').val();
       d.range=$('.form_filter input[name=range]').val();
      }
    }
  });
   table = $('.table_barang').DataTable({
    "processing" : true,
    "searching":false,
    "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
    "ajax" : {
      "url" : "getbarangbanyak",
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
    "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
    "ajax" : {
      "url" : "get_omset",
      "type" : "GET",
      data: function(d) {
       d.gudang=$('.form_filter select[name=gudang]').val();
       d.range=$('.form_filter input[name=range]').val();
      }
    }
  });
  $('input[name="range"]').daterangepicker({
   locale: {
      format: 'DD/MM/YYYY'
    }
  });
  $('.btn_batal_ganti').on('click',function(e){
    location.reload();
  });
  // 
$(document).on("submit","form.form_filter",function(e){
       table1.ajax.reload();
       table2.ajax.reload();
       table.ajax.reload();
     
      
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
      url: 'get_omset',
      type: 'get',
      async: true,
      data: $('.form_filter').serialize(),
      dataType: "json",
      success: function (data) {
          // visitorData(data);
          var tunai = data[0]['y'];
          var debet = data[1]['y'];
          var flazz = data[2]['y'];
          var kredit = data[3]['y'];
          var transfer = data[4]['y'];
          var ovo = data[5]['y'];
          var hutang = data[6]['y'];
          var total = tunai+debet+flazz+kredit+transfer+ovo+hutang;
           $('#tunai').html(accounting.formatMoney(tunai));
          $('#debet').html(accounting.formatMoney(debet));
          $('#flazz').html(accounting.formatMoney(flazz));
          $('#kredit').html(accounting.formatMoney(kredit));
          $('#transfer').html(accounting.formatMoney(transfer));
          $('#hutang').html(accounting.formatMoney(hutang));
          $('#ovo').html(accounting.formatMoney(ovo));
          $('#total').html(accounting.formatMoney(total));
          // console.log(data[0]);
      }
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
</script>
@endsection

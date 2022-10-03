<?php $hal = "laporanpengiriman"; ?>
@extends('layouts.admin.master')
@section('title', 'Laporan Pengiriman')

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

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
    color: #000;
    }
  </style>

@endsection

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Laporan Pengiriman
    <small>it all starts here</small>
  </h1>
</section>
<!-- Main content -->
<section class="content col-md-6">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title"></h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <form enctype="multipart/form-data" class="form form_filter form_laporanpembelian" target="_blank" action="{{url('laporanpengiriman_cetak')}}" autocomplete="off">
        <div class="col-md-12 div_filter">
          <div class="col-md-12">
            <div class="form-group">
            <label>Gudang</label>
              <select name="gudang[]" id="gudang" class="select2_multiple" style="width:100%;" required="">
                <!-- <option value="all">Semua Gudang</option>
                <option value="toko">Semua Toko</option> -->
                <option value="all">Semua Toko</option>
                @foreach ($data['gudang'] as $item)
                <option value="<?php echo $item->id_gudang ?>"> <?php echo $item->nama_gudang?></option>
               @endforeach
              </select>
            </div>
          </div> 
           <div class="col-md-12">
            <div class="form-group">
            <label>Tanggal Dari</label>
             <input type="text" class="form-control datepicker" name="tanggalAwal" value="{{date('d-m-Y')}}">
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
            <label>Tanggal Ke</label>
             <input type="text" class="form-control datepicker" name="tanggalAkhir" value="{{date('d-m-Y')}}">
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
            <label>Barang</label>
             <select class="form-control" name="barang" style="width: 100%;">
               <option value="0"> Semua Barang </option>
             </select>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
            <label>Kategori</label>
             <select class="form-control" name="kategori" style="width: 100%;">
               <option value="1">Rekap Pengiriman</option>
               <option value="2">Per-Nota</option>
             </select>
            </div>
          </div>
           <div class="col-md-8">
             <div class="form-group">
              <button type="button" style="margin-top: 25px; width: 75px;" class="btn btn-warning" id="btn_filter"><b><i class="fa  fa-search"></i></b> Filter</button>
             <button type="submit" style="margin-top: 25px; width: 75px;" class="btn btn-success" id="btn_cetak"><b><i class="fa fa-file-pdf-o"></i></b> PDF</button>
             <button type="button" style="margin-top: 25px; width: 75px;" class="btn btn-primary" id="btn_excel"><b><i class="fa  fa-file-excel-o"></i></b> Excel</button>
           </div>                    
           </div>                    
         </div>
      </form>

      <div class="col-md-12" id="cek_filter">
        
      </div>
    
    </div>
    <!-- /.box-body -->
    
    <!-- /.box-footer-->
  </div>
</section>
@endsection

@section('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript">
$(document).ready(function() {
  $('input[name="range"]').daterangepicker({
   locale: {
      format: 'DD/MM/YYYY'
    }
  });
  $(".select2_multiple").select2({
    multiple: true,
  });

  // $('#div-barang').hide();
  $('[name=barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('laporanpengiriman_get_barang')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                      var results = [];
                      results = [{"id": 0,"text": " Semua Barang "}];
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
 $('.btn_batal_ganti').on('click',function(e){
    location.reload();
  });
$('#btn_excel').validator().on('click', function(e){
    if(!e.isDefaultPrevented()){
      var gudang = $('#gudang').val();
      var tanggal = $('[name=tanggalAwal]').val();
      var tanggal2 = $('[name=tanggalAkhir]').val();
      var kategori = $('[name=kategori]').val();
      var barang = $('[name=barang]').val();
      
      var where = [gudang,tanggal,tanggal2,kategori,barang];
      
      var url_where = where.join("/");

      // window.open("{{url('laporanpengiriman_excel')}}/"+gudang+"/"+tanggal+"/"+tanggal2,'_blank');
      window.open("{{url('laporanpengiriman_excel')}}/"+url_where,'_blank');
    }
  });
$('#btn_filter').validator().on('click', function(e){
    if(!e.isDefaultPrevented()){
      var gudang = $('#gudang').val();
      var tanggal = $('[name=tanggalAwal]').val();
      var tanggal2 = $('[name=tanggalAkhir]').val();
      var kategori = $('[name=kategori]').val();
      var barang = $('[name=barang]').val();
      
      var where = [gudang,tanggal,tanggal2,kategori,barang];
      
      var url_where = where.join("/");
      
      // window.open("{{url('laporanpengiriman_hasil')}}/"+gudang+"/"+tanggal+"/"+tanggal2,'_blank');
      window.open("{{url('laporanpengiriman_hasil')}}/"+url_where,'_blank');
    }
  });
/*$('[name=kategori]').on('change',function(){
  var id = $(this).val();
  if(id == 2){
      $('#div-barang').show();
  }else{
      $('#div-barang').hide();
  }
})*/
</script>
@endsection

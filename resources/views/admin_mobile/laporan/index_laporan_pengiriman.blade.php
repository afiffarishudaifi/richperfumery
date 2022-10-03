<?php $hal = "laporanpenjualan"; ?>
@extends('layouts.admin.master')
@section('title', 'Laporan Penjualan')

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
    Laporan Penjulan
    <small>it all starts here</small>
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">&nbsp;</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <form enctype="" class="form form_filter" target="_blank" action="{{url('cetaklaporan')}}" autocomplete="off">
        <div class="col-md-12 div_filter">
          <div class="col-md-3">
            <div class="form-group">
            <label>Gudang</label>
              <select name="gudang" id="" class="form-control select2" style="width=100%">
                @foreach ($data['gudang'] as $item)
                <option value="<?php echo $item->id_gudang ?>"> <?php echo $item->nama_gudang?></option>
               @endforeach
              </select>
            </div>
          </div> 
           <div class="col-md-3">
            <div class="form-group">
            <label>Tanggal</label>
             <input type="text" class="form-control datepicker" name="tanggal" value="{{date('d-m-Y')}}">
            </div>
          </div>
           <div class="col-md-3">
             <div class="form-group">
             <button type="submit" style="margin-top: 25px;" class="btn btn-success"><b><i class="fa fa-save"></i></b> Cetak</button>
           </div>                    
           </div>                    
         </div>
      </form>
    
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

});
 $('.btn_batal_ganti').on('click',function(e){
    location.reload();
  });
//  $('.form_filter').validator().on('submit', function(e){
//     if(!e.isDefaultPrevented()){
//       var id = $('#id').val();

//       $.ajax({
//         url : "cetaklaporan",
//         type : "POST",
//         data : $('.form_filter').serialize(),
//         success : function(data){
//           table.ajax.reload();
//           // location.reload();
//         },
//         error : function(){
//           alert("Tidak dapat menyimpan data!");
//         }
//       });
//       return false;
//     }
//   });
</script>
@endsection

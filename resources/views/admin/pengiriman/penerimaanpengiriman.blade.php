<?php $hal = "penerimaanpengiriman"; ?>
@extends('layouts.admin.master')
@section('title', 'PENERIMAAN PENGIRIMAN')

@section('css')
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

/* table, th {
  border: 0.1px solid black !important;
} */

/* td {
  border: 0.1px solid black !important;
} */
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">PENERIMAAN PENGIRIMAN</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-toggle="collapse" title="Search" data-target="#form-search"> <i class="fa fa-search"></i> Search</button>
          </div>
        </div>
        <div class="box-body collapse" id="form-search">
          <form class="form-horizontal form-tanggal-search" method="POST" autocomplete="off">
            <div class="col-md-6">
              <div class="form-group">
                <label class="col-md-2">Tanggal</label>
                <div class="col-md-5">
                  <input type="text" name="search_tanggal" class="form-control" value="{{date('d-m-Y')}}">
                </div>
                <div class="col-md-4">
                  <a href="javascript:;" style="margin-bottom:20px;" class="card-body-title btn-search-tanggal"><button class="btn btn-success" type="button"><i class="fa fa-search"></i> Search</button></a>
                  <a href="javascript:;" style="margin-bottom:20px;" class="card-body-title btn-reset-tanggal"><button class="btn btn-warning" type="button"><i class="fa fa-undo"></i> Reset</button></a>
                </div>
              </div>
            </div>
          </form>
      </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped" width="100%">
            <thead>
              <tr>
                <th style="width:10%">No #</th>
                <th style="width:16%">No Pengiriman </th>
                <th style="width:16%">Gudang Awal</th>
                <th style="width:10%">tujuan</th>
                <th style="width:10%">Tanggal</th>
                <th style="width:10%">Status</th>
                <th style="width:10%">Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
    </div>
  </section>
@endsection
@section('js')
<link rel="stylesheet" type="text/css" href="{{asset('public/js/daterangepicker/daterangepicker.css')}}" />
<script type="text/javascript" src="{{asset('public/js/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/js/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
var table, table_search;
  $(function () {
    $('.tanggal').datepicker({
      format:'yyyy-mm-dd',
      autoclose:true
    });
    $('.js-example-basic-single').select2({
      dropdownParent: $(".modal")
    });

    $('input[name=search_tanggal]').daterangepicker({
     locale: {
        format: 'DD-MM-YYYY',
        separator: "  s.d. ",
      }
    });

    table = $('#datatable1').DataTable({
      "processing" : true,
      "ajax" : {
        "url" : "{{ route('penpengirimanlistData') }}",
        "type" : "GET"
      }
    });
  $('.btn_tambah').on('click',function(e){
    // alert('as');
    $('.form_connectio1n')[0].reset();
    $('.d').html('<h4 class="modal-title">Tambah Gudang</h4>');
    $('#pengiriman').val('').trigger('change.select2');
    $('#crud').val('tambah');
    $('#modal-form').modal('show');
  });
  $(document).on("click","#btn_edit",function() {
    $('.d').html('<h4 class="modal-title">Edit Pengiriman</h4>');
    $('#crud').val('edit');
    var id = $(this).data('id');
    var gudang = $(this).data('gudang_awal');
    var tujuan = $(this).data('gudang_tujuan');
    var kode = $(this).data('kode_pengirimana');
    var tanggal = $(this).data('tanggal');
    var id_pengiriman = $(this).data('id_pengiriman');
    var pengirim = $(this).data('pengirim');
    var pengiriman = '<option value="'+id_pengiriman+'">'+pengirim+'</option>';
    console.log(pengiriman);
    $('#id').val(id);
    $('#tanggal').val(tanggal);
    $('#kode').val(kode);
    $('#pengiriman').html(pengiriman);
    $('#gudang_awal').val(gudang).trigger('change');
    $('#tujuan').val(tujuan).trigger('change');

    $('#modal-form').modal('show');
  });
   $('#pengiriman').select2({
            placeholder: "Pilih...",
            // minimumInputLength: 2,
            ajax: {
                url: 'select2_pengiriman',
                dataType: 'json',
                data: function (params) {
                  // console.log();
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
                          nohp:item.nohp,
                          text:item.nama
                        });
                      });
                      return{
                        results:results
                      };
                        
                },
                cache: true
            }
    
  });
  $(document).on("click","#btn_hapus",function() {
    var id = $(this).data('id');
    if(confirm("Apakah yakin data akan dihapus? ini akan menghapus pengiriman yang ada di pesedian barang!")){
      $.ajax({
        url : "pengiriman/"+id,
        type : "POST",
        data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
        success : function(data){
          table.ajax.reload();
           location.reload();
        },
        error : function(){
          alert("Tidak dapat menghapus data!");
        }
      });
    }
  });

  $('#modal-form form').validator().on('submit', function(e){
    if(!e.isDefaultPrevented()){
      var id = $('#id').val();

      $.ajax({
        url : "{{ route('pengiriman.store') }}",
        type : "POST",
        data : $('#modal-form form').serialize(),
        success : function(data){
          $('#modal-form').modal('hide');
          // table.ajax.reload();
          location.reload();
        },
        error : function(){
          alert("Tidak dapat menyimpan data!");
        }
      });
      return false;
    }
  });

  });

$(".btn-search-tanggal").on("click",function(){
    var tanggal = $("[name=search_tanggal]").val();
    var url = '<?=$hal?>_searchtanggal';
    $('#datatable1').DataTable().clear();
    $('#datatable1').DataTable().destroy();
    table_search = $('#datatable1').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{url('penerimaanpengiriman_searchtanggal')}}",
      "data" : {tanggal:tanggal}
    }
  });

});

$(".btn-reset-tanggal").on("click",function(){
    $('#datatable1').DataTable().clear();
    $('#datatable1').DataTable().destroy();
    table = $('#datatable1').DataTable({
      "processing" : true,
      "ajax" : {
        "url" : "{{ url('penpengirimanlistData') }}",
        "type" : "GET"
      }
    });
});

  </script>
@endsection

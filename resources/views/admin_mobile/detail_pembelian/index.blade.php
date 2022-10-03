<?php $hal = "pembelian"; ?>
@extends('layouts.admin.master')
@section('title', 'Pembelian')

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
    Pembelian
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box box-default">
        <form class="" action="{{route('pembelian.store')}}" method="post">
            {{ csrf_field() }} {{ method_field('POST') }}
        <div class="box-header with-border">
          <h3 class="box-title">Tambah Pembelian</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Penyedia</label>
                <select name="supplier_id" class="form-control select2" style="width: 100%;">
                  <option selected="selected">--</option>
                  @foreach ($supplier as $item)
                  <option value="{{$item->supplier_id}}">{{$item->supplier_nama}}</option>
                @endforeach

                </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label>No Transaksi</label>
              <input type="text" name="pembelian_no_faktur" class="form-control">
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Pajak</label>
                <select name="pembelian_ppn_status" id="pembelian_ppn_status" class="form-control select2" style="width: 100%;">
                  <option value="ya" selected="selected">PPN</option>
                  <option value="tidak">Non PPN</option>
                </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label>Tgl. Transaksi</label>
                <input type="text" name="pembelian_tanggal" value="{{ \Carbon\Carbon::now()->toDateString() }}" class="form-control" >
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          <div class="col-md-2">
          <div class="form-group">
            <label>Total</label>
            <input type="hidden" id="pembelian_total"  name="pembelian_total" class="form-control">
            {{-- <input type="text" id="total_barang"  name="total_barang" class="form-control"> --}}
            <p id="total_barang" class="total_barang">Rp. </p>
          </div>
          </div>
          <div class="col-md-2">
          <div class="form-group">
            <label>Onkir</label>
            <input type="text" class="form-control" id="pembelian_ongkir" value="0" name="pembelian_ongkir">
          </div>
          </div>
          <div class="col-md-2">
          <div class="form-group">
            <label>Pembayaran</label>
            <select lass="form-control select2" style="width: 100%;" name="pembelian_cara_bayar">
              <option selected="selected" value="tunai">Tunai</option>
              <option value="kredit">Kredit</option>
            </select>
          </div>
          </div>
          {{-- <div class="col-md-2">
          <div class="form-group">
            <label>Kekurangan</label>
            <input type="text" class="form-control" placeholder="Enter ...">
          </div>
          </div>
          <div class="col-md-2">
          <div class="form-group">
            <label>Bayar</label>
            <input type="text" class="form-control" name="pembelian_uang_muka">
          </div>
          </div> --}}
          <div style="float:right; margin-top:5%">
            <button  type="submit" name="button" class="btn btn-success">Simpan</button>
          </div>


          </form>
        </div>


      </div>
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Detail Barang</h3>
        </div>
        <a onclick="addForm()" style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah Barang</button></a>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:35%">Nama Barang </th>
                <th style="width:15%">Harga</th>
                <th style="width:5%">Qty</th>
                <th style="width:15%">Total</th>
                <th style="width:30%">Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
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
@include('admin.pembelian.form')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>


<script type="text/javascript">
function convertToRupiah(angka)
{
 var rupiah = '';
 var angkarev = angka.toString().split('').reverse().join('');
 for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
 return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
};
</script>
<script type="text/javascript">
var table, save_method;
var id = $('#id').val();
var ongkir = $('#pembelian_ongkir').val();
var pajak = $('#pembelian_ppn_status').val();

 function total(){
  $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
  $.ajax({
    url : "{{url('update_total_pembelian')}}",
    // url : "{{route('total_pembelian')}}",
    type : "POST",
    data : {term : id,ongkir,pajak},
    success : function(data){
      $('.total_barang').html("<h4>"+convertToRupiah(data)+"</h4>");
      $('#pembelian_total').val(data);
    },
    error : function(){
      alert("Tidak dapat menyimpan data!");
    }
  });
};

$(function(){
  table = $('.table').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ route('detail_data_pembelian') }}",
      "type" : "GET"
    }
  });
  function total(){
   $.ajaxSetup({
   headers: {
     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   }
 });
   $.ajax({
     url : "{{url('update_total_pembelian')}}",
     // url : "{{route('total_pembelian')}}",
     type : "POST",
     data : {term : id,ongkir,pajak},
     success : function(data){
       // $("p").html("<h4>Rp."+rupiah+"</h4>");
       // $('#total_barang').val(convertToRupiah(data));
       $('.total_barang').html("<h4>"+convertToRupiah(data)+"</h4>");
       $('#pembelian_total').val(data);

     },
     error : function(){
       alert("Tidak dapat menyimpan data!");
     }
   });
 };

  $('#modal-form form').validator().on('submit', function(e){
    if(!e.isDefaultPrevented()){
      var id = $('#id').val();
      var ongkir = $('#pembelian_ongkir').val();
      var pajak = $('#pembelian_ppn_status').val();

      if(save_method == "add") url = "{{ route('detail_pembelian.store') }}";
      else url = "detail_pembelian/"+id;
      $.ajax({
        url : url,
        type : "POST",
        data : $('#modal-form form').serialize(),
        success : function(data){
          $('#modal-form').modal('hide');
          table.ajax.reload();
          total();

        },
        error : function(){
          alert("Tidak dapat menyimpan data!");
        }
      });
      return false;
    }
  });
});



function addForm(){
  save_method = "add";
  $('input[name=_method]').val('POST');
  $('#modal-form').modal('show');
  $('#modal-form form')[0].reset();
  $('.modal-title').text('Tambah Data Barang');
}
function editForm(id){
  save_method = "edit";
  $('input[name=_method]').val('PATCH');
  $('#modal-form form')[0].reset();
  $.ajax({
    url : 'detail_pembelian/'+id+'/edit',
    type : "GET",
    dataType : "JSON",
    success : function(data){
      $('#modal-form').modal('show');
      $('.modal-title').text('Edit Data Barang');
      $('#id').val(data.pembelian_detail_id);
      $('#barang_id').val(data.barang_id);
      $('#pembelian_detail_harga').val(data.pembelian_detail_harga);
      $('#pembelian_detail_qty').val(data.pembelian_detail_qty);
      $('#pembelian_detail_diskon').val(data.pembelian_detail_diskon);
      $('#pembelian_detail_jumlah').val(data.pembelian_detail_jumlah);
      let	number_string = data.pembelian_detail_jumlah.toString(),
      	sisa 	= number_string.length % 3,
      	rupiah 	= number_string.substr(0, sisa),
      	ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
      if (ribuan) {
      	separator = sisa ? '.' : '';
      	rupiah += separator +ribuan.join('.');
      }
      $("p").html("<h4>Rp."+rupiah+"</h4>");
    },
    error : function(){
      alert("Tidak dapat menampilkan data !!!");
    }
  });
}
function deleteData(id){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "detail_pembelian/"+id,
      type : "POST",
      data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
      success : function(data){
        table.ajax.reload();
        total();


      },
      error : function(){
        alert("Tidak dapat menghapus data!");
      }
    });
  }
}

</script>

<script>
$(function () {
  $('#example1').DataTable()
  $('#example2').DataTable({
    'paging'      : true,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false
  })
})
</script>
<script>
  // $(function () {
  //   //Initialize Select2 Elements
  //   $('.select2').select2()
  // })
  $(document).ready(function() {
  $('.js-example-basic-single').select2({
    dropdownParent: $(".modal")
  });
});
</script>
<script>
    $('#barang_id').on('change', function(e){
        var state_id = e.target.value;

        $.get('{{ url('get_data_barang') }}'+ '/' + state_id, function(data) {
            $('#pembelian_detail_harga').empty();
            // $.each(data, function(index,subCatObj){
            //     $('#barang_harga').val(''+subCatObj.log_stok_saldo_harga+'');
            // });
                $('#pembelian_detail_harga').val(''+data.log_stok_saldo_harga+'');
        });
    });
</script>

<script type="text/javascript">

function hitung_total(){
  var qty = $("#pembelian_detail_qty").val();
  var harga = $("#pembelian_detail_harga").val();
  var total = $("#jumlah").val()
  var diskon = $("#pembelian_detail_diskon").val()

// hitung diskon
if (diskon == 0) {
  var hasil = qty * harga;
}else if(diskon > 0 ) {
  var hasil = (qty * harga)-((qty * harga) * (diskon/100));
}else {
  var hasil = 0;
}

$("#pembelian_detail_jumlah").val(hasil);

var	number_string = hasil.toString(),
	sisa 	= number_string.length % 3,
	rupiah 	= number_string.substr(0, sisa),
	ribuan 	= number_string.substr(sisa).match(/\d{3}/g);

if (ribuan) {
	separator = sisa ? '.' : '';
	rupiah += separator + ribuan.join('.');
}
var jml= document.getElementById("jumlah")
 $("#jumlah").html("<h4>Rp."+rupiah+"</h4>");
  // $("#jumlah").val("Rp."+rupiah);
}
</script>

{{-- Format Rupiah --}}
<script type="text/javascript">
  // var jmlhh = $('#jumlahCount').val();
  var jmlhh = document.getElementById("jumlahCount");
  $('#pembelian_total').val(jmlhh);
</script>

{{-- Hitung Total --}}
<script type="text/javascript">




</script>

@endsection

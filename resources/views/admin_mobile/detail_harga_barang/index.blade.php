<?php $hal = "barang"; ?>
@extends('layouts.admin.master')
@section('title', 'Master Barang')

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
    Detail Harga Barang
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{$nama_barang}}</h3>
        </div>
        <a onclick="addForm()"  style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:10%">No #</th>
                <th style="width:50%">Tanggal</th>
                <th style="width:30%">Harga Jual</th>
                <th style="width:10%">Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
          <a href="{{route('barang.index')}}"  style="margin-bottom:20px;" class="card-body-title"><button class="btn btn-warning">Kembali</button></a>

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
@include('admin.detail_harga_barang.form')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script type="text/javascript">
var table, save_method;
$(function(){
  table = $('.table').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ url('data_detail_harga_barang') }}/{{$barang_id}}",
      "type" : "GET"
    }
  });
  $('#modal-form form').validator().on('submit', function(e){
    if(!e.isDefaultPrevented()){
      var id = $('#id').val();
      if(save_method == "add") url = "{{ route('detail_harga_barang.store') }}";
      else url = ""+id;
      $.ajax({
        url : url,
        type : "POST",
        data : $('#modal-form form').serialize(),
        success : function(data){
          $('#modal-form').modal('hide');
          table.ajax.reload();
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
  $('.modal-title').text('Tambah Harga');
}
function editForm(id){
  save_method = "edit";
  $('input[name=_method]').val('PATCH');
  $('#modal-form form')[0].reset();
  $.ajax({
    url : ""+id+"/edit",
    type : "GET",
    dataType : "JSON",
    success : function(data){
      $('#modal-form').modal('show');
      $('.modal-title').text('Edit Harga');
      $('#id').val(data.detail_harga_barang_id);
      $('#detail_harga_barang_tanggal').val(data.detail_harga_barang_tanggal);
      $('#detail_harga_barang_harga_jual').val(data.detail_harga_barang_harga_jual);
    },
    error : function(){
      alert("Tidak dapat menampilkan data !!!");
    }
  });
}
function deleteData(id){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : ""+id,
      type : "POST",
      data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
      success : function(data){
        table.ajax.reload();
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
function hanyaAngka(evt) {
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57))

  return false;
  return true;
}
</script>

<script type="text/javascript">
var detail_harga_barang_harga_jual = document.getElementById("detail_harga_barang_harga_jual");
// var rupiah = document.getElementById("rupiah");
var rupiah = document.getElementById("rupiah");
detail_harga_barang_harga_jual.addEventListener("keyup", function(e) {
  // tambahkan 'Rp.' pada saat form di ketik
  // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
  // rupiah.value = formatRupiah(this.value, "Rp. ");
  rupiah.innerHTML = formatRupiah(this.value, "Rp. ");
  $('#rupiah').val('coba');
  console.log(rupiah);
});

/* Fungsi formatRupiah */
function formatRupiah(angka, prefix) {
  var number_string = angka.replace(/[^,\d]/g, "").toString(),
  split = number_string.split(","),
  sisa = split[0].length % 3,
  detail_harga_barang_harga_jual = split[0].substr(0, sisa),
  ribuan = split[0].substr(sisa).match(/\d{3}/gi);

  // tambahkan titik jika yang di input sudah menjadi angka ribuan
  if (ribuan) {
    separator = sisa ? "." : "";
    detail_harga_barang_harga_jual += separator + ribuan.join(".");
  }

  detail_harga_barang_harga_jual = split[1] != undefined ? detail_harga_barang_harga_jual + "," + split[1] : detail_harga_barang_harga_jual;
  return prefix == undefined ? detail_harga_barang_harga_jual : detail_harga_barang_harga_jual ? "Rp. " + detail_harga_barang_harga_jual : "";
}
</script>

@endsection

<?php $hal = "kasirposting"; ?>
@extends('layouts.admin.master')
@section('title', 'closing Penjualan')

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
        <button class="btn btn-primary" style="margin-bottom:20px;margin-left:10px;" type="button" id="btn_posting"> <i class="fa  fa-check-o"></i>Closing</button>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:3%">No <input type="checkbox" id="checkAll" ></th>
                <th>tanggal</th>
                <th>No.Faktur</th>
                <th>Pelanggan</th>
                <th>Gudang</th>
                <th>Total Penjualan</th>
                <th style="width:10%">Posting</th>
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
      "url" : "{{ route('kasirposting_data') }}",
      "type" : "GET"
    },
    "columnDefs": [
      { targets: 0, orderable: false, searchable: false, }
    ]
    
  });  
  
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


@endsection

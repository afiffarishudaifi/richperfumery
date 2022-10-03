<?php $hal = "persetujuanopname"; ?>
@extends('layouts.admin.master')
@section('title', 'Persetujuan Stok Opname')

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
    Persetujuan Stok Opname
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Persetujuan Stok Opname</h3>
        </div>
        <!-- <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <button class="btn btn-primary" type="button" id="btn_verifikasi">Verifikasi</button>
              </div>
        </div>
        </div>
      </div> -->
      <button class="btn btn-primary" style="margin-bottom:20px;margin-left:10px;" type="button" id="btn_verifikasi"><i class="fa  fa-check-o"></i> Verifikasi</button>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
                  <tr>
                      <th width="3%">No <input type="checkbox" id="checkAll" ></th>
                      <th width="10%">tanggal</th>
                      <th width="22%">Nama Barang</th>
                      <th width="10%">Gudang</th>
                      <th width="10%">Stok</th>
                      <th width="10%">Fisik</th>  
                      <th width="10%">Selisih</th>
                      <th width="15%">Ket.</th> 
                      <th width="10%">Status</th>
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
var table, save_method, table_barang;
$(function(){
  table = $("#datatable1").DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ url('persetujuanopname_data') }}',
          columns: [
              {data: 'nomor', name: 'nomor'},
              {data: 'tanggal', name: 'tanggal'},
              {data: 'nama_barang', name: 'nama_barang'},
              {data: 'nama_gudang', name: 'nama_gudang'},
              {data: 'stok', name: 'stok'},
              {data: 'fisik', name: 'fisik'},
              {data: 'selisih', name: 'selisih'},
              {data: 'keterangan', name: 'keterangan'},
              {data: 'status', name: 'status', orderable: false, searchable: false}
          ],
          "columnDefs": [
            { targets: 0, orderable: false, searchable: false, }
          ],
    });

  
});

$("#checkAll").click(function () {
     $('input:checkbox').not(this).prop('checked', this.checked);
 });

$("#btn_verifikasi").click(function(){
    var rows_selected = [];
    var jenis_selected = [];

    var rowcollection =  table.$("#check_verifikasi:checked", {"page": "all"});
    rowcollection.each(function(index,elem){
        var checkbox_value = $(elem).val();
        rows_selected.push(checkbox_value);    
    });
    if(rows_selected.length > 0){
      if(confirm("Anda yakin akan memverifikasi data ini?")){    
        kirim_data(rows_selected);  
      }    
    }else{
        alert("Tidak Ada Data Terpilih!");
    }
  });

  function kirim_data(val, gudang){
    $.ajax({
      url: '{{ url('persetujuanopname_simpanmulti') }}',
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


@endsection

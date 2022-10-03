<?php $hal = "produk"; ?>
@extends('layouts.admin.master')
@section('title', 'Master Produk')

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
          <h3 class="box-title">Produk</h3>
        </div>
        <a href="produk_tambah"  style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                 <table id="datatable1" class="table table-bordered table-striped" width="100%">
            <thead>
              <tr>
                <th style="width:10%">No #</th>
                <th style="width:16%">Kode Produk </th>
                <th style="width:16%">Nama</th>
                <th style="width:10%">Harga</th>
                <th style="width:10%">Action</th>
              </tr>
            </thead>
            <tbody>
                <tr>
                  
                </tr>
            </tbody>
          </table>
            </div>
         
        </div>
        <!-- /.box-body -->
      </div>
    </div>
    </div>
  </section>
  
@endsection
@section('js')
<script type="text/javascript">
  $(function () {
     table = $('#datatable1').DataTable({
      "processing" : true,
      "ajax" : {
        "url" : "{{ route('produk_listdata') }}",
        "type" : "GET"
      }
    });

  });

function deleteData(id){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "produk_hapus",
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
</script>
@endsection
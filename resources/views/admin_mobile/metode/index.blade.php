<?php $hal = "metode"; ?>
@extends('layouts.admin.master')
@section('title', 'Metode')

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
          <h3 class="box-title">Metode</h3>
        </div>
        <a href="#"   style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsiv">
                 <table id="datatable1" class="table table-bordered table-striped" width="100%">
            <thead>
              <tr>
                <th style="width:5%">No #</th>
                <th style="width:80%">Nama</th>
                <th style="width:15%">Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
            </div>
         
        </div>
        <!-- /.box-body -->
      </div>
    </div>
    </div>
  </section>
  @include('admin.metode.form')
@endsection
@section('js')
<script type="text/javascript">
  $(function () {
    $('.btn_tambah').on('click',function(){
        $('.form_connectio1n')[0].reset();
        $('.d').html('<h4 class="modal-title">Tambah Metode</h4>');
        $('#crud').val('tambah');
        $('#modal-form').modal();
    });
    table = $('.table').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ url('metodelihatdata') }}",
      "type" : "GET"
            }
    });
     $(document).on("click",".btn_edit",function(e) {
        $('.d').html('<h4 class="modal-title">Edit Metode</h4>');
        $('#crud').val('edit');
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var telp = $(this).data('telp');
        var inisial = $(this).data('inisial');
        var alamat = $(this).data('alamat');
        var jenis_outlet = $(this).data('jenis_outlet');
        $('#id').val(id);
        $('#nama').val(nama);
        $('#telp').val(telp);
        $('#inisial').val(inisial);
        $('#alamat').val(alamat);
         $('#jenis').val(jenis_outlet).trigger('change');
        $('#modal-form').modal();
    });
     $(document).on("click",".btn_hapus",function() {
    var id = $(this).data('id');
    if(confirm("Apakah yakin data akan dihapus?")){
      $.ajax({
        url : "metode/"+id,
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
        url : "{{route('metodesimpandata')}}",
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
</script>
@endsection
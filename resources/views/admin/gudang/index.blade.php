<?php $hal = "refgudang"; ?>
@extends('layouts.admin.master')
@section('title', 'Master Gudang')

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
          <h3 class="box-title">Master Gudang</h3>
        </div>
        <a  style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="datatable1" class="table table-bordered table-striped" width="100%">
            <thead>
              <tr>
                <th style="width:10%">No #</th>
                <th style="width:16%">Profil</th>
                <th style="width:16%">Nama</th>
                <th style="width:16%">Alamat</th>
                <th style="width:10%">Status</th>
                <th style="width:10%">Kode</th>
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
  @include('admin.gudang.form1')
@endsection


@section('js')
  <script type="text/javascript">
  $(function () {
    $('.js-example-basic-single').select2({
    dropdownParent: $(".modal")
  });
    table = $('#datatable1').DataTable({
      "processing" : true,
      "ajax" : {
        "url" : "{{ route('data_refgudang') }}", 
        "type" : "GET"
      }
    });
  $('.btn_tambah').on('click',function(e){
    // alert('as');
    $('.form_connectio1n')[0].reset();
    $('.d').html('<h4 class="modal-title">Tambah Gudang</h4>');
    $('#crud').val('tambah');
    $('#id_profil').val('').trigger('change');
    $('#modal-form').modal('show');
  });
  $(document).on("click","#btn_edit",function() {
    $('.d').html('<h4 class="modal-title">Edit Program</h4>');
    $('#crud').val('edit');
    var nama_profil = $(this).data('nama_profil');
    var id = $(this).data('id');
    var id_profil = $(this).data('id_profil');
    var alamat = $(this).data('alamat');
    var nama = $(this).data('nama');
    var kode = $(this).data('kode');
    var isi = '<option value="'+id_profil+'">'+nama_profil+'</option>';
    $('#id_profil').html(isi);
    $('#id').val(id);
    $('#alamat').val(alamat);
    $('#nama').val(nama);
    $('#kode').val(kode);

    $('#modal-form').modal('show');
  });
  $('#id_profil').select2({
            placeholder: "Pilih...",
            // minimumInputLength: 2,
            ajax: {
                url: 'select2profil',
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
                          alamat:item.alamat,
                          jenis_outlet:item.jenis_outlet,
                          nama:item.nama,
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
    if(confirm("Apakah yakin data akan dihapus?")){
      $.ajax({
        url : "refgudang/"+id,
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
        url : "{{ route('refgudang.store') }}",
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

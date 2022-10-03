<?php $hal = "pengiriman"; ?>
@extends('layouts.admin.master')
@section('title', 'PENGIRIMAN')

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
          <h3 class="box-title">PENGIRIMAN</h3>
        </div>
        <a  style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a>
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
  @include('admin.pengiriman.form1')
@endsection
@section('js')
  <script type="text/javascript">
  $(function () {
    $('.tanggal').datepicker({
      //format:'yyyy-mm-dd',
      format:'dd-mm-yyyy',
      autoclose:true
    });
    $('.js-example-basic-single').select2({
    dropdownParent: $(".modal")
  });
    table = $('#datatable1').DataTable({
      "processing" : true,
      "ajax" : {
        "url" : "{{ route('data_pengiriman') }}",
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
          // $('#modal-form').modal('hide');
          // table.ajax.reload();
          // location.reload();
          if(data.status == 1){
            swal({
                title:"Data Sukses Simpan",
                text:"Data berhasil disimpan",
                type:"success",
                confirmButtonText:"Okay"
            },function(){
                table.ajax.reload();
                $('#modal-form').modal('hide');
            });
          }else if(data.status == 2){
            swal({
                title:"Data Sukses Update",
                text:"Data berhasil diupdate",
                type:"success",
                confirmButtonText:"Okay"
            },function(){
                table.ajax.reload();
                $('#modal-form').modal('hide');
            });
          }else{
            swal({
                title:"Data Gagal Simpan",
                text:"Data gagal disimpan",
                type:"error",
                confirmButtonText:"Okay"
            },function(){
                table.ajax.reload();
                $('#modal-form').modal('hide');
            });
          }
        },
        error : function(){
          // alert("Tidak dapat menyimpan data!");
          swal({
                title:"Data Gagal Simpan",
                text:"Data gagal disimpan",
                type:"error",
                confirmButtonText:"Okay"
            },function(){
                table.ajax.reload();
                $('#modal-form').modal('hide');
            });
        }
      });
      return false;
    }
  });

  });

  </script>
@endsection

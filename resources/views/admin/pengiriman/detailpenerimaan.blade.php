<?php $hal = "detail_penerimaanpengiriman"; ?>
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
      <a href="{{url('penerimaanpengiriman')}}" style="margin-left: 10px;margin-top: 10px;" class="btn btn-xs btn-primary">Kembali</a>
        <div class="box-header">
          <h3 class="box-title">Detail Barang</h3>
        </div>
       
        @php 
        if($gudang['status'] == 1){
           echo '<a  style="margin-bottom:20px;margin-left:10px;" id="btn_semua" class="card-body-title btn btn-success btn_semua"><i class="fa  fa-check"></i> Semua Barang Diterima</a>';
          // echo'';
        }
        @endphp
       
        <!-- /.box-header -->
        <div class="box-body">
          <table id="datatable1" class="table table-bordered table-striped" width="100%">
            <thead>
              <tr>
                <th style="width:10%">No #</th>
                <th style="width:16%">Nama Barang</th>
                <th style="width:16%">Satuan</th>
                {{-- <th style="width:16%">Diterima</th> --}}
                <th style="width:16%">Jumlah</th>
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
  @include('admin.pengiriman.formdetail')
  @include('admin.pengiriman.formterima')
@endsection

@section('js')
<script type="text/javascript">
$(document).on("click","#btn_a",function() {
   $('.d').html('<h4 class="modal-title">Diterima</h4>');
   var gudang = "{{$gudang['tujuan']}}";
   var nama = $(this).data('nama');
   var satuan = $(this).data('satuan');
   var kode = $(this).data('kode');
   var jumlah = $(this).data('jumlah');
   var id_barang = $(this).data('id_barang');
   var id_detail_pengiriman = $(this).data('id_detail_pengiriman');
   $(' .form_connectio1n #status').val(0);
   $(' .form_connectio1n #gudang').val(gudang);
   $(' .form_connectio1n #id_barang').val(id_barang);
   $(' .form_connectio1n #diterima').val('');
   $(' .form_connectio1n #dikembalikan').val('');
   $(' .form_connectio1n #nama').val(nama);
   $('.form_connectio1n #satuan').val(satuan);
   $('.form_connectio1n #kode').val(kode);
   $('.form_connectio1n #jumlah').val(jumlah);
   $('.form_connectio1n #id_detail_pengiriman').val(id_detail_pengiriman);
   $('#diterima').on('keyup',function(){
          var a = $(this).val();
          $('#dikembalikan').val(jumlah-a);

        });
     $('#dikembalikan').on('keyup',function(){
          var a = $(this).val();
          $('#diterima').val(jumlah-a);

        });
   $('#modal-diterima').modal('show');
  // alert('aa');
});
  $(function () {
    var id = "{{$gudang['id']}}";
    var gudang = "{{$gudang['gudang']}}";
    $(document).on('click','#btn_kirim_se',function(){
      if(confirm("Apakah yakin data akan mengirim semua barang? notice= ketika barang sudah dikirim tidak bisa diedit")){
      $.ajax({
        url : "detailpengiriman_kirimbarang",
        type : "POST",
        data : {'id' : id, '_token' : $('meta[name=csrf-token]').attr('content')},
        success : function(data){
          table.ajax.reload();
           location.reload();
        },
        error : function(){
          alert("Tidak dapat mengirim barang!");
        }
      });
    }
    });
     $(document).on('click','#btn_semua',function(){
      if(confirm("Apakah yakin data menerima semua barang?")){
      $.ajax({
        url : "detailpengiriman_terima",
        type : "GET",
        data : {'id' : id, 'gudang': gudang, '_token' : $('meta[name=csrf-token]').attr('content')},
        // data : {'id' : id, '_token' : $('meta[name=csrf-token]').attr('content')},
        success : function(data){
          // table.ajax.reload();
           location.reload();
        },
        error : function(){
          alert("Tidak dapat mengirim barang!");
          //  location.reload();
        }
      });
    }
    });
    $('#status').on('change',function(){
      var a = $(this).val();
      if(a == 1){
        $('#diterima').attr('readonly',false);
        $('#dikembalikan').attr('readonly',true);
      }else if(a==2){
        $('#diterima').attr('readonly',true);
        $('#dikembalikan').attr('readonly',false);

      }
      
    });
    
    // $('.tanggal').datepicker({
    //   format:'yyyy-mm-dd',
    //   autoclose:true
    // });
    // console.log(gudang);
    var gudang = "{{$gudang['gudang']}}";
    $('.barang_select2').select2({
            placeholder: "Pilih...",
            // minimumInputLength: 2,
            ajax: {
                url: 'select2barang',
                dataType: 'json',
                data: function (params) {
                  // console.log();
                    return {
                        q: $.trim(params.term),
                        gudang:gudang
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        results.push({
                          id:item.barang_id,
                          id_log_stok:item.log_stok_id,
                          satuan:item.satuan_satuan,
                          satuan_nama:item.satuan_nama,
                          id_satuan:item.id_satuan,
                          nama:item.barang_nama,
                          text:item.barang_kode+' || '+item.barang_nama,
                          jumlah_keluar:item.jumlah_keluar,
                          jumlah_masuk:item.jumlah_masuk
                        });
                      });
                      return{
                        results:results
                      };
                        
                },
                cache: true
            }
    
  });
    //modal : ketika barang dipilih
      $('#barang').on('change',function(e){
        // var satuan = $('[name=barang] :selected').attr('satuan');
        var nilai = $("#barang").select2('data')[0];
        // var keluar = nilai
        var id_satuan = nilai.id_satuan ;
        var satuan_nama = nilai.satuan_nama ;
        var option = '<option value="'+id_satuan+'">'+satuan_nama+'</option>';
        $('#nama').val(nilai.nama);
        $('#id_log_stok').val(nilai.id_log_stok);
        $('#satuan').val(nilai.satuan_nama);
        $('#id_satuan').val(nilai.id_satuan);
        $('#kode').val(nilai.text);
        $('#tersedia').val(nilai.jumlah_masuk - nilai.jumlah_keluar);
        // console.log(nilai);
      });
     
      
    table = $('#datatable1').DataTable({
      "processing" : true,
      "ajax" : {
        "url" : "penerimaanpengiriman/"+id,
        "type" : "GET"

      }
    });
  $('.btn_tambah').on('click',function(e){
    // alert('as');
    $('.form_connectio1n')[0].reset();
    $('.d').html('<h4 class="modal-title">Tambah Barang</h4>');
    $('#gudang').val(gudang);
    $('#barang').val('').trigger('change.select2');
    
    
    $('#crud').val('tambah');
    $('#modal-form').modal('show');
  });
  $(document).on("click","#btn_edit",function() {
    $('.d').html('<h4 class="modal-title">Edit Program</h4>');
    $('#crud').val('edit');
    var barang = $(this).data('id_barang');
    var id = $(this).data('id');
    var satuan = $(this).data('satuan');
    var nama1 = $(this).data('nama');
    var Jumlah = $(this).data('jumlah');
    var kode = $(this).data('kode');
    var isi = '<option value="'+barang+'">'+kode+'</option>'
    // $('#barang').select2();
    // $('#barang').select2('data', {id: barang, a_key: 'nama1'});
    // console.log( $('#barang').select2('data', {id: barang, a_key: barang}));
    $('#barang').html(isi);
   
    $('#id').val(id);
    $('#jumlah').val(Jumlah);
    $('#satuan').val(satuan);
    $('#nama').val(nama1);

    $('#modal-form').modal('show');
  });
   
  $(document).on("click","#btn_hapus",function() {
    var id = $(this).data('id');
    if(confirm("Apakah yakin data akan dihapus?")){
      $.ajax({
        url : "detailpengiriman/"+id,
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
        url : "{{ route('detailpengiriman.store') }}",
        type : "POST",
        data : $('#modal-form form').serialize(),
        success : function(data){
          $('#modal-form').modal('hide');
          table.ajax.reload();
          location.reload();
        },
        error : function(){
          alert("Tidak dapat menyimpan data!");
        }
      });
      return false;
    }
  });
   $('#modal-diterima form').validator().on('submit', function(e){
    if(!e.isDefaultPrevented()){
      // var id = $('#id').val();

      $.ajax({
        url : "{{ route('simpanpersetujuan') }}",
        type : "POST",
        data : $('#modal-diterima form').serialize(),
        success : function(data){
          console.log(data);
          // $('#modal-diterima').modal('hide');
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
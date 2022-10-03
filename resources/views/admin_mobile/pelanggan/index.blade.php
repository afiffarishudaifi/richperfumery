<?php $hal = "pelanggan"; ?>
@extends('layouts.admin.master')
@section('title', 'Pelanggan')

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
          <h3 class="box-title">Pelanggan</h3>
        </div>
        <a href="#"   style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
           <table id="datatable1" class="table table-bordered table-striped" width="100%">
            <thead>
              <tr>
                <th style="width:5%"></th>
                <th style="width:5%">No</th>
                <th style="width:16%">Nama</th>
                <!-- <th style="width:16%">No Telephone</th> -->
                <!-- <th style="width:10%">Alamat</th> -->
                <!-- <th style="width:10%">Gudang</th> -->
                <!-- <th style="width:10%">Status</th> -->
                <th style="width:10%">Action</th>
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
@include('admin.pelanggan.form')
@endsection
@section('js')
<script type="text/javascript">
  var table;
  $(function () {
    $('#tgl_lahir1').datepicker({
      format:'yyyy-mm-dd',
      autoclose:true
    });

    $('#tanggal_awal').on('change',function(){
      var tanggal = new Date($(this).data('datepicker').getFormattedDate('yyyy-mm-dd'));
      var Year = tanggal.getFullYear()+2;
      var month = tanggal.getMonth()+1;
      var day = tanggal.getDate()-1;

      if(month<10){
        month='0'+month;
      }
      if(day<10){
        day='0'+day;
      }
      var expired = (day)+"-"+(month)+"-"+(Year); 
      $("#tanggal_akhir").val(expired);
      var tanggal2 = (Year)+"-"+(month)+"-"+(day); 
      expired_date(tanggal,tanggal2);
    });

    $('#tanggal_akhir').on('change',function(){
      var tgl_awal = new Date($("#tanggal_awal").data('datepicker').getFormattedDate('yyyy-mm-dd'))
      var tgl_akhir = new Date($(this).data('datepicker').getFormattedDate('yyyy-mm-dd'));
      expired_date(tgl_awal,tgl_akhir);
      /*var d_expired = tgl_awal.getTime() - tgl_akhir.getTime();
      var expired = parseInt(d_expired);
      if(expired < 0){
        $('#status_aktif').val('2');
      }else{
        $('#status_aktif').val('1');
      }*/
    });

    function expired_date(tanggal, tanggal2){
      var tgl_awal = new Date(tanggal)
      var tgl_akhir = new Date(tanggal2);
      var tgl_sekarang = new Date();
      /*var d_expired = tgl_awal.getTime() - tgl_akhir.getTime();
      var expired = parseInt(d_expired);
      if(expired < 0){
        $('#status_aktif').val('2');
      }else{
        $('#status_aktif').val('1');
      }*/
      var d_expired2 = tgl_sekarang.getTime() - tgl_akhir.getTime();
      var expired = parseInt(d_expired2);

      var status = $("[name=status]").val();
      if(status == 2){
        if(tgl_akhir <= tgl_sekarang){
          $('#status_aktif').val('2');
        }else{ 
          $('#status_aktif').val('1');
        }
      }else{
        $('#status_aktif').val('1');
      }

    }


    $('#status').on('change',function(e){
      var nilai = $(this).val();      
      // alert(nilai);
      if (nilai==1) {
        $("#no_member").addClass('hidden');
        $("#tgl_lahir").addClass('hidden');
        $("#tempat").addClass('hidden');
        $("#email").addClass('hidden');
        $("#email").addClass('hidden');
        $("#tanggal_member").addClass('hidden');
      }else if(nilai==2){
        // console.log(nilai);
        $("#no_member").removeClass('hidden');
        $("#tgl_lahir").removeClass('hidden');
        $("#tempat").removeClass('hidden');
        $("#email").removeClass('hidden');
        $("#email").removeClass('hidden');
        $("#tanggal_member").removeClass('hidden');
      // $("#no_member").show(); 
    }else if(nilai==3){
      $("#no_member").addClass('hidden');
      $("#tgl_lahir").addClass('hidden');
      $("#tempat").addClass('hidden');
      $("#email").addClass('hidden');
      $("#email").addClass('hidden');
      $("#tanggal_member").addClass('hidden');
    }else if(nilai==4){
      $("#no_member").addClass('hidden');
      $("#tgl_lahir").addClass('hidden');
      $("#tempat").addClass('hidden');
      $("#email").addClass('hidden');
      $("#email").addClass('hidden');
      $("#tanggal_member").addClass('hidden');
    }

  });
    $('.btn_tambah').on('click',function(){
      $('.form_connectio1n')[0].reset();
      $('.d').html('<h4 class="modal-title">Tambah Pelanggan</h4>');
      $('#crud').val('tambah');
      $('#modal-form').modal();
    });
    table = $('.table').DataTable({
      "processing" : true,
      "ordering": false,//tambahan
      "pageLength": 10,//tambahan
      "lengthChange": false,//tambahan
      "searching": true,//tambahan
      "ajax" : {
        "url" : "{{ url('pelangganlihatdata') }}",
        "type" : "GET"
      },
      "columns": [
      {
        "className":      'details-control',
        "orderable":      false,
        "data":           null,
        "defaultContent": ''
      },
      { "data": "no" },
      { "data": "nama" },
      // { "data": "telp" },
      // { "data": "alamat" },
      // { "data": "gudang" },
      // { "data": "status" },
      { "data": "aksi" }
      ],
    });
    /* Formatting function for row details - modify as you need */
    function format ( d ) {
      // `d` is the original data object for the row
      return '<table>'+
      '<tr>'+
      '<td>Nama:</td>'+
      '<td>'+d.nama+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>No. Telepon:</td>'+
      '<td>'+d.telp+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Alamat:</td>'+
      '<td>'+d.alamat+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Gudang:</td>'+
      '<td>'+d.gudang+'</td>'+
      '</tr>'+
      '<tr>'+
      '<td>Status:</td>'+
      '<td>'+d.status+'</td>'+
      '</tr>'+
      '</table>';
    }
    // Add event listener for opening and closing details
    $('.table tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = table.row( tr );

      if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
      }else {
        // Open this row
        row.child( format(row.data()) ).show();
        tr.addClass('shown');
      }
    });


    $(document).on("click",".btn_edit",function(e) {
      $('.d').html('<h4 class="modal-title">Edit Pelanggan</h4>');
      $('#crud').val('edit');
      var id = $(this).data('id');
      var nama = $(this).data('nama');
      var telp = $(this).data('telp');
      var alamat = $(this).data('alamat');
      var status = $(this).data('status');
      var no_member = $(this).data('no_member');
      var tempat = $(this).data('tempat');
      var tanggal_lahir = $(this).data('tanggal_lahir');
      var email = $(this).data('email');
      var jenis = $(this).data('jenis');
      var gudang = $(this).data('gudang');
      var tanggal_awal = $(this).data('tanggal_awal');
      var tanggal_akhir = $(this).data('tanggal_akhir');
      var status_aktif = $(this).data('statis_aktif');
      expired_date(tanggal_awal,tanggal_akhir);
      var status_aktif = $("[name=status_aktif]").val();
      if (status==2) {
        $("#no_member").removeClass('hidden');
        $("#tgl_lahir").removeClass('hidden');
        $("#tempat").removeClass('hidden');
        $("#email").removeClass('hidden');
        $("#email").removeClass('hidden');
        $("#tanggal_member").removeClass('hidden');
      }
      $('#status').val(status).trigger('change');
      $('#id').val(id);
      $('#nama').val(nama);
      $('#nomember').val(no_member);
      $('#tempat1').val(tempat);
      $('#tgl_lahir1').val(tanggal_lahir);
      $('#email1').val(email);
      $('#jenis').val(jenis);
      $('#telp').val(telp);
      $('#alamat').val(alamat);
      $('#gudang').val(gudang).trigger('change');
      $('#tanggal_awal').val(tanggal_awal);
      $('#tanggal_akhir').val(tanggal_akhir);
      $('#status_aktif').val(status_aktif);
      $('#modal-form').modal();
    });
    $(document).on("click",".btn_hapus",function() {
      var id = $(this).data('id');
      if(confirm("Apakah yakin data akan dihapus?")){
        $.ajax({
          url : "pelanggan/"+id,
          type : "POST",
          data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
          success : function(data){
            table.ajax.reload();
            //location.reload();
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
          url : "{{route('pelanggansimpandata')}}",
          type : "POST",
          data : $('#modal-form form').serialize(),
          success : function(data){
            $('#modal-form').modal('hide');
          table.ajax.reload();
          // location.reload();
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
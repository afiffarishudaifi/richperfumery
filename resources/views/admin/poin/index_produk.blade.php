<?php $hal = "produkpoin"; ?>
@extends('layouts.admin.master')
@section('title', 'Master Poin')

@section('css')
<!-- DataTables -->
<!-- <link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}"> -->

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
    Poin Produk
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Poin Produk</h3>
        </div>
        <a onclick="addForm()"  style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
          <table id="datatable_group" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:5%">No #</th>
                <th style="width:35%">Nama Produk</th>
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
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->
@include('admin.poin.form_produk2')
@endsection

@section('js')
<!-- DataTables -->
<!-- <script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script> -->

<script type="text/javascript">
var table_group, save_method;
$(document).ready(function(){

  table_group = $('#datatable_group').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    searchDelay: 2000,
    ajax: {
      "url" : "{{ url('produkpoin_get_data_group') }}",
      "type" : "GET"
    },
    columns: [        
        {data: 'nama', name: 'nama', orderable: false, searchable: false, render : function(data, type, row, meta){
            return meta.row+1;
        }}, 
        {data: 'nama', name: 'nama'},
        {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
    ],
    /*createdRow: function( row, data, dataIndex ) {
       $( row ).find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').addClass('text-center');
    }*/

  })

  $('[name=popup_produk]').select2({
    placeholder: "--- Pilih ---",
    ajax: {
        url: '{{url('produkpoin_get_produk')}}',
        dataType: 'json',
        data: function (params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function (data) {
              var results = [];
              $.each(data, function(index,item){
                results.push({
                  id:item.id_produk,
                  kode:item.kode_produk,
                  nama:item.nama_produk,
                  harga:item.harga_produk,
                  text:item.nama_produk
                });
              });
              return{
                results:results
              };
        },
        cache: true
      }        
  });

  $("[name=popup_kategori]").on('change',function(){
      var kategori = $(this).val();
      if(kategori == 1){
        $('[name=popup_tanggal]').val(null).trigger('change');
        $('#div_hari').show();
        $('#div_tanggal').hide();
      }else if(kategori == 2){
        $('[name=popup_hari]').val('').trigger('change');
        $('#div_hari').hide();
        $('#div_tanggal').show();
      }else{
        $('[name=popup_hari]').val('').trigger('change');
        $('#div_hari').hide();
        $('#div_tanggal').hide();
      }
  })

  $("[name=popup_poin]").on('keyup',function(){
      var jum = $(this).val();
      $("#help_block_poin").text(format_angka(parseInt(jum),0));
  })

  $("#btn_popup_simpan").validator().on('click',function(){
      $.ajax({
        url : "{{ url('produkpoin_simpan') }}",
        type : "POST",
        data : $('#modal-form form').serialize(),
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function(data){
          $('#modal-form').modal('hide');
          // table.ajax.reload();
          table_group.ajax.reload();
        },
        error : function(){
          alert("Tidak dapat menyimpan data!");
        }
      });
  })
});

function addForm(){
  save_method = "add";
  $('input[name=_method]').val('POST');
  $('.modal-title').text('Tambah Data Satuan');
  //$('#modal-form form')[0].reset();

  $("#div_tanggal").hide();
  $("[name=popup_id]").val('').trigger('change');
  $("[name=popup_produk]").html('<option></option>')
  $("[name=popup_kategori]").val('1').trigger('chage');
  $("[name=popup_hari]").val('').trigger('change');
  $("[name=tanggal]").val('').trigger('change');
  $("[name=poin]").val('0');
  $('#modal-form').modal('show');
}

</script>
@endsection

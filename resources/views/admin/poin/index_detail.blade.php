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
          <h3 class="box-title">Detail Poin Produk {{$data['nama_produk']}}</h3>
          <a href="{{url('produkpoin')}}" class="card-body-title text-right pull-right"><button class="btn btn-primary"><i class="fa  fa-arrow-left"></i> Kembali</button></a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:5%">No #</th>
                <th style="width:35%">Nama Produk</th>
                <th style="width:15%">Kategori</th>
                <th style="width:15%">Hari</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:15%">Poin</th>
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
var table, save_method;
var id_produk = "{{$data['id_produk']}}";
$(document).ready(function(){
  table = $('#datatable1').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    searchDelay: 2000,
    ajax: {
      "url" : "{{ url('produkpoin_get_data') }}",
      "type" : "GET",
      data : function(d){
          d.id_produk   = id_produk;
      }
    },
    columns: [        
        {data: 'nama_produk', name: 'nama_produk', orderable: false, searchable: false, render : function(data, type, row, meta){
            return meta.row+1;
        }}, 
        {data: 'nama_produk', name: 'nama_produk'},
        {data: 'kategori', name:'kategori'},
        {data: 'd_hari', name:'d_hari'},
        {data: 'd_tanggal', name:'d_tanggal'},
        {data: 'poin', name:'poin'},
        {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
    ],
  });

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


  // $('#modal-form form').validator().on('submit', function(e){
  //   if(!e.isDefaultPrevented()){
  //     var url = "{{ url('produkpoin_simpan') }}";
  //     $.ajax({
  //       url : url,
  //       type : "POST",
  //       data : $('#modal-form form').serialize(),
  //       headers : {
  //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //       },
  //       success : function(data){
  //         $('#modal-form').modal('hide');
  //         table.ajax.reload();
  //       },
  //       error : function(){
  //         alert("Tidak dapat menyimpan data!");
  //       }
  //     });
  //     return false;
  //   }
  // });

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
          table.ajax.reload();
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
function editForm(id){
  save_method = "edit";
  $('input[name=_method]').val('PATCH');
  $('#modal-form form')[0].reset();

  var kategori    = $("#table_kategori"+id).val();
  var id_produk   = $("#table_idproduk"+id).val();
  var kode_produk = $("#table_kodeproduk"+id).val();
  var nama_produk = $("#table_namaproduk"+id).val(); 
  var poin        = parseFloat($("#table_poin"+id).val());

  if(id != ''){
    $("#popup_id").val($("#table_id"+id).val());
    $("#popup_produk").html("<option value='"+id_produk+"'>"+nama_produk+"</option>");
    // $("#popup_produk").val($("#table_idproduk"+id).val());
    $("#popup_kategori").val($("#table_kategori"+id).val()).trigger('change');
    if(kategori == 1){
      $("#div_hari").show();
      $("#div_tanggal").hide();
      $("#popup_hari").val($("#table_hari"+id).val()).trigger('change');
      $("#popup_tanggal").val(null).trigger('change');
    }else if(kategori == 2){
      $("#div_hari").hide();
      $("#div_tanggal").show();
      $("#popup_hari").val('').trigger('change');
      $("#popup_tanggal").val($("#table_tanggal"+id).val());
    }else{
      $("#div_hari").hide();
      $("#div_tanggal").hide();
      $("#popup_hari").val('').trigger('change');
      $("#popup_tanggal").val(null).trigger('change');
    }
    $("#popup_poin").val(poin);
    $("#help_block_poin").text(format_angka(poin,0));
  }else{
    alert("Tidak dapat menampilkan data !!!");
  }

  $('#modal-form').modal('show');
}


function deleteData(id,produk){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "{{url('produkpoin_hapus')}}",
      type : "POST",
      data : {'id':id,'produk':produk},
      headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success : function(respon){
        if(respon.status == 1){
          if(respon.data == 0){
            window.locations.href = "{{url('produkpoin')}}";
          }else{
            table.ajax.reload();
          }
        }else{
          alert("Tidak dapat menghapus data!");
        }
      },
      error : function(){
        alert("Tidak dapat menghapus data!");
      }
    });
  }
}
</script>
@endsection

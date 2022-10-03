<?php $hal = "master_user"; ?>
@extends('layouts.admin.master')
@section('title', 'Master Users')

@section('css')
<!-- DataTables --> <!-- TODO ALL-->
{{-- <link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}"> --}}
<link rel="stylesheet" href="{{asset('public/admin/bower_components/select2/dist/css/select2.min.css')}}">
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
    Master Users
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Master Users</h3>
        </div>
        <a onclick="addForm()"  style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:5%">No #</th>
                <th style="width:16%">Profil </th>
                <th style="width:16%">Nama </th>
                <th style="width:16%">Group</th>
                <th style="width:16%;">Email</th>
                <th style="width:16%;">Username</th>
                <th style="width:16%;">Password</th>
                <th style="width:15%">Action</th>
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
@include('admin.master_user.form')
@endsection


@section('js')
<!-- DataTables -->

<script type="text/javascript">
var table, save_method;
$(function(){
  table = $('.table').DataTable({
    "processing" : true,
    responsive: true,
    "ajax" : {
      "url" : "{{ url('master_user/get_data') }}",
      "type" : "GET"
    }
    
  });
  
  $('#modal-form form').validator().on('submit', function(e){
    if(!e.isDefaultPrevented()){
      var id = $('#id').val();
      if(save_method == "add") url = "{{ url('master_user/store') }}";
      else url = "master_user/"+id;
      $.ajax({
        url : url,
        type : "POST",
        data : $('#modal-form form').serialize(),
        success : function(data){
          $('#modal-form').modal('hide');
          table.ajax.reload();
        },
        error : function(){
          alert("Tidak dapat menyimpan data!");
        }
      });
      return false;
    }
  });
});
function addForm(){
  save_method = "add";
  $('input[name=_method]').val('POST');
  $('#modal-form').modal('show');
  $('#modal-form form')[0].reset();
  $('.modal-title').text('Tambah Data Users');
}
function editForm(id){
  save_method = "edit";
  $('input[name=_method]').val('PATCH');
  $('#modal-form form')[0].reset();
  var nama_profil =$(this).data('nama_profil');
  $.ajax({
    url : "{{url('master_user/edit')}}/"+id,
    type : "GET",
    dataType : "JSON",
    success : function(data){
      var isi = '<option value="'+data.id_profil+'">'+data.nama+'</option>';
      $('#id_profil').html(isi);
      $('#modal-form').modal('show');
      $('.modal-title').text('Edit Data Users');
      $('#id').val(data.id);
      $('#group_id').val(data.group_id).trigger('change');
      $('#name').val(data.name);
      $('#email').val(data.email);
      $('#users_email').val(data.users_email);
    },
    error : function(){
      alert("Tidak dapat menampilkan data !!!");
    }
  });
}
function deleteData(id){
  if(confirm("Apakah yakin data akan dihapus?")){
    $.ajax({
      url : "master_user/"+id,
      type : "POST",
      data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
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

  $(document).ready(function() {
  $('.js-example-basic-single').select2({
    dropdownParent: $(".modal")
  });
  //  $('#id_profil').select2({
  //           placeholder: "Pilih...",
  //           // minimumInputLength: 2,
  //           ajax: {
  //               url: 'select2profil',
  //               dataType: 'json',
  //               data: function (params) {
  //                 // console.log();
  //                   return {
  //                       q: $.trim(params.term)
  //                   };
  //               },
  //               processResults: function (data) {
  //                     var results = [];
  //                     $.each(data, function(index,item){
  //                       results.push({
  //                         id:item.id,
  //                         alamat:item.alamat,
  //                         jenis_outlet:item.jenis_outlet,
  //                         nama:item.nama,
  //                         text:item.nama
  //                       });
  //                     });
  //                     return{
  //                       results:results
  //                     };
                        
  //               },
  //               cache: true
  //           }
    
  //  });
  $('#id_profil').select2({
    placeholder: "Pilih...",
    ajax: {
        url: "{{url('master_user/get_profil')}}",
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
  var select2_cek_group       = "{{$session->group_id}}";
  var select2_cek_idprofil    = "{{$session->id_profil}}";
  var select2_cek_namaprofil  = "{{$namaprofil}}";
  if(select2_cek_group == 5){
    $("#id_profil").html("<option value='"+select2_cek_idprofil+"'>"+select2_cek_namaprofil+"</option>");
  }
});

$(' #confirm_password').on('keyup', function () {
  if ($('#password').val() == $('#confirm_password').val()) {
    $('#message').html('').css('color', 'green');
  } else
    $('#message').html('Password tidak cocok').css('color', 'red');
});


$('#confirm_password').keyup(function(){
    var pass    =   $('#password').val();
    var cpass   =   $('#confirm_password').val();
    if(pass!=cpass){
        $('#submit').attr({disabled:true});
    }
    else{
        $('#submit').attr({disabled:false});
    }
});



</script>


@endsection

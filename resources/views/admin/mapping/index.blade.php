<?php $hal = "mapping"; ?>
@extends('layouts.admin.master')
@section('title', 'Mapping')

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
  </style>

@endsection

@section('content')

<section class="content-header">
  <h1>
    Mapping
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">MAPPING</h3>
        </div>
        <a  style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:10%">No #</th>
                <th style="width:16%">Akuntansi</th>
                <th style="width:16%">Program</th>
                <th style="width:10%">Action</th>
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
@include('admin.mapping.form1')

@endsection
@section('js')
<script src="{{asset('public/js/bootstrap_multiselect.js')}}"></script>
<script type="text/javascript">
$(function () {
  $('.js-example-basic-single').select2({
    dropdownParent: $(".modal")
  });
  $('.btn_tambah').on('click',function(e){
    // alert('as');
    $('.form_connectio1n')[0].reset();
    $('.d').html('<h4 class="modal-title">Tambah Program</h4>');
    $('#crud').val('tambah');
    $.get("{{ route('lihat') }}", function(data){
      $('#id_skpd').empty();

      // $.each(JSON.parse(data), function(index, catOBj){
      //   $('#id_skpd').html('<option value="'+catOBj.id+'">'+catOBj.nama+'</option>');
      // });
      $('#id_skpd').val('').change();
    });
    $('#modal-form').modal('show');
  });


});
</script>
@endsection

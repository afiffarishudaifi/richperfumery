<?php $hal = "poin"; ?>
@extends('layouts.admin.master')
@section('title', 'Master Poin')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('public/admin/plugins/sweetalert2/sweetalert2.css')}}">
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
    Master Poin
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Master Poin</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <form action="{{url('poin_simpan')}}" autocomplete="off" method="POST" id="form_input_poin">
                {{ csrf_field() }}
                <input type="hidden" name="id">
                <div class="form-group row">
                    <label class="col-form-label col-md-3 col-lg-3">Nominal</label>
                    <div class="col-md-9 col-lg-9">
                        <input type="text" name="nominal" class="form-control number-only pull-right" >
                        <p class="help-block pull-right" id="help_popup_nominal"></p>
                    </div>
                </div>
                <div class="footer">
                    <div class="pull-left">
                        <p>*) Jumlah nominal dalam 1 poin</p>
                    </div>
                    <button class="pull-right btn btn-primary" type="button" id="btn_simpan_poin"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </form>
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
@endsection

@section('js')
<script type="text/javascript" src="{{asset('public/admin/plugins/sweetalert2/sweetalert2.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        get_data();

        $('#btn_simpan_poin').click(function() {
            var menit = $("[name=menit]").val();
            if (menit != '') {
                $.ajax({
                    url: "{{ url('poin_simpan') }}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $("#form_input_poin").serialize(),
                    success: function(respon) {
                        if (respon.status == 0) { 
                            Swal.fire({
                              icon: 'error',
                              title: 'Data Tidak Berhasil Disimpan',
                              showConfirmButton: false,
                              timer: 1500
                            })                    
                        }else{
                            Swal.fire({
                              icon: 'success',
                              title: 'Data Berhasil Disimpan',
                              showConfirmButton: false,
                              timer: 1500
                            })                        
                        }                                                
                    }
                })
            } else {
                swal("", "Field Menit tidak boleh kosong", "warning");
            }
        })

        $('[name=nominal]').on("keyup",function(){
            var jum = $(this).val();
            $("#help_popup_nominal").text("Rp. "+format_angka(jum));
        })
    })

    function get_data() {
        $.ajax({
            url: "{{ url('poin_get_data')}} ",
            type: 'get',
            dataType: 'json',
            success: function(respon) {                
                $('[name=id]').val(respon.id);
                $('[name=nominal]').val(respon.nominal);
                $('#help_popup_nominal').text("Rp. "+format_angka(respon.nominal));
            }
        });
    }
</script>




@endsection

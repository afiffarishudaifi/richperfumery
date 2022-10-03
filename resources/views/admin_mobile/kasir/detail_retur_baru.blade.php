<?php $hal = "kasirretur"; ?>
@extends('layouts.admin.master')
@section('title', 'Retur Penjualan')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('public/selectize/css/selectize.css')}}">
<link rel="stylesheet" href="{{asset('public/selectize/css/selectize.bootstrap3.css')}}">
<link rel="stylesheet" href="{{asset('public/admin/plugins/iCheck/all.css')}}">
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

<style type="text/css">

  .stepwizard-step p {
    margin-top: 0px;
    color:#666;
  }
  .stepwizard-row {
    display: table-row;
  }
  .stepwizard {
    display: table;
    width: 100%;
    position: relative;
  }
  .stepwizard-step button[disabled] {
    /*opacity: 1 !important;
    filter: alpha(opacity=100) !important;*/
  }
  .stepwizard .btn.disabled, .stepwizard .btn[disabled], .stepwizard fieldset[disabled] .btn {
    opacity:1 !important;
    color:#bbb;
  }
  .stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content:" ";
    width: 100%;
    height: 1px;
    background-color: #ccc;
    z-index: 0;
  }
  .stepwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
  }
  .btn-circle {
    width: 30px;
    height: 30px;
    text-align: center;
    padding: 6px 0;
    font-size: 12px;
    line-height: 1.428571429;
    border-radius: 15px;
  }
</style>
@endsection


@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Penjualan
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">


    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Detail Retur Penjualan</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <form role="form" class="form_belanja" action="#" method="post">
            <input type="hidden" name="id_retur" value="{{$data['data']['id_retur']}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="box-body" style="margin-right: -25px;margin-left: -25px">


              <!-- WIZARD -->
              <div class="col-md-12">
                <div class="stepwizard">
                  <div class="stepwizard-row setup-panel">
                    <div class="stepwizard-step col-xs-4"> 
                      <a href="#step-1" type="button" class="btn btn-success btn-circle">1</a>
                      <p><small>Detail Penjualan</small></p>
                    </div>
                    <div class="stepwizard-step col-xs-4"> 
                      <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                      <p><small>Detail Retur</small></p>
                    </div>
                    <div class="stepwizard-step col-xs-4"> 
                      <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                      <p><small>Form Retur</small></p>
                    </div>
                  </div>
                </div>

                <div class="panel panel-primary setup-content" id="step-1">
                  <div class="panel-heading">
                    <h3 class="panel-title">Detail Penjualan</h3>
                  </div>
                  <div class="panel-body" style="margin-right: -20px;margin-left: -20px">

                    <div class="form-group col-md-12">
                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label>Tanggal Input</label>
                                  {{-- name="PARAM1[tanggal]" --}}
                                  <input class="form-control" type="text"
                                      placeholder="Tanggal"
                                      value="{{ $data['data']['tanggal'] == '' ? date('d-m-Y') : $data['data']['tanggal'] }}"
                                      readonly />
                              </div>
                              <div class="form-group">
                                  <label>Tanggal Jatuh Tempo</label>
                                  {{-- name="PARAM1[tanggal_tempo]" --}}
                                  <input class="form-control" type="text"
                                      placeholder="Tanggal Tempo"
                                      value="{{ $data['data']['tanggal_tempo'] == '' ? date('d-m-Y') : $data['data']['tanggal_tempo'] }}"
                                      readonly />
                              </div>
                              <div class="form-group">
                                  <label>Nama Pelanggan</label>
                                  <input class="form-control" style="width: 100%;"
                                      value="{{ $data['data']['nama_pelanggan'] }}" disabled />
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label>Tanggal Faktur</label>
                                  {{-- name="PARAM1[tanggal_faktur]" --}}
                                  <input class="form-control" type="text"
                                      placeholder="Tanggal Faktur"
                                      value="{{ $data['data']['tanggal_faktur'] == '' ? date('d-m-Y') : $data['data']['tanggal_faktur'] }}"
                                      readonly />
                              </div>
                              <div class="form-group">
                                  <label>Nomor Faktur</label>
                                  <input type="text" name="PARAM1[no_faktur]" class="form-control"
                                      value="{{ $data['data']['nomor'] == '' ? $data['no_auto'] : $data['data']['nomor'] }}"
                                      readonly>
                              </div>
                              <div class="form-group">
                                  <label>Gudang</label>
                                  <input class="form-control" style="width: 100%;"
                                      value="{{ $data['data']['nama_gudang'] }}" disabled />
                              </div>
                          </div>
                      </div>
                      <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
                    </div>

                  </div>
                </div>

                <div class="panel panel-primary setup-content" id="step-2">
                  <div class="panel-heading">
                    <h3 class="panel-title">Detail Retur</h3>
                  </div>
                  <div class="panel-body" style="margin-right: -5px;margin-left: -5px">

                    <div class="form-group col-md-12 table-responsive">
                      <!-- <h3>Detail Produk</h3>
                        <hr/> -->
                        <table class="table table-bordered table-hover table-striped table-barang" id="table_barang">
                          <thead>
                              <tr>
                                {{-- <th width="3%">No.</th> --}}
                                <th width="35%">Nama Barang / Produk</th>
                                <th width="15%">Jumlah Retur</th>
                                <th width="15%">Satuan</th>
                              </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                      </div>

                      <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
                    </div>
                  </div>

                  <div class="panel panel-primary setup-content" id="step-4">
                    <div class="panel-heading">
                      <h3 class="panel-title">Form Retur</h3>
                    </div>
                    <div class="panel-body" style="margin-right: -20px;margin-left: -20px">

                      <div class=" col-md-6 col-sm-12 m-l-5">
                        <div class="table-responsive">
                          <table class="table table-bordered table-form">
                              <tbody>
                                  <tr class="active">
                                      <td colspan="2">Form Retur</td>
                                  </tr>
                                  <tr>
                                      <td>Kode Retur</td>
                                      <td>
                                          <input type="text" name="PARAM1[kode_retur]" class="form-control"
                                              required disabled value="{{ $data['data']['kode_retur'] }}">
                                      </td>
                                  </tr>
                                  <tr>
                                      <td>Tanggal Retur</td>
                                      <td>
                                          <input class="form-control datepicker" type="text"
                                              name="PARAM1[tanggal]" placeholder="Tanggal Retur" value="{{ date('d-m-Y') }}"
                                              required disabled />
                                      </td>
                                  </tr>
                                  <tr>
                                      <td>Alasan</td>
                                      <td>
                                          <textarea class="form-control" rows="2" name="PARAM1[keterangan]" required disabled>{{ $data['data']['keterangan'] }}</textarea>
                                      </td>
                                  </tr>
                              </tbody>
                          </table>
                        </div>
                      </div>

                      <!-- <button class="btn btn-success pull-right">Finish!</button> -->
                    </div>
                  </div>
                </div>
                <!-- WIZARD -->
              </div><!-- /.box-body -->

              <div class="box-footer">
               <a href="{{url('kasirreturbaru')}}" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
             </div>
           </form>
           <!-- /.box-body -->
         </div>
         <!-- /.box -->
       </div>
     </div>
     <!-- /.row (main row) -->



   </section>

   <!-- /.content -->

   @endsection


   @section('js')
  <!-- iCheck -->
  <script src="{{asset('public/admin/plugins/iCheck/icheck.min.js')}}"></script>
   <!-- DataTables -->
   <script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
   <script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
   <script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
   <script src="{{asset('public/js/touchspin.min.js')}}"></script>
   <script src="{{asset('public/selectize/js/standalone/selectize.min.js')}}"></script>


   <script type="text/javascript">

    var tb_no = parseInt(1000);
    var table_barang;
    var id_retur = '{{$data['data']['id_retur']}}';
    var id_pelanggan = '{{$data['data']['id_pelanggan']}}';
    var nama_pelanggan = '{{$data['data']['nama_pelanggan']}}';
    var id_gudang = '{{$data['data']['id_gudang']}}';
    var nama_gudang = '{{$data['data']['nama_gudang']}}';

    $(document).ready(function () {
      table_barang = $("#table_barang").DataTable({
        "paging": false,
        "ordering": false,//tambahan
        "pageLength": 10,//tambahan
        "lengthChange": false,//tambahan
        "searching": false//tambahan
      });

      table_barang.on('order.dt search.dt', function() {
          table_barang.column(0, {
              search: 'applied',
              order: 'applied'
          }).nodes().each(function(cell, i) {
              cell.innerHTML = i + 1;
          });
      }).draw();

      if(id_retur != ''){
        if(id_pelanggan != ''){
          $("[name=id_pelanggan]").val(id_pelanggan).trigger('change');
        }
        get_edit(id_retur);
      }

      $(".touchspin-step").TouchSpin({
        min: 0,
        max: 100,
        step: 1
      });

      accounting.settings = {
        currency: {
          symbol: "",
          precision: 2,
          thousand: ".",
          decimal : ",",
          format: {
            pos : '%s %v',
            neg : '%s (%v)',
            zero : '%s %v'
          },
        },
        number: {
          precision : 0,  // default precision on numbers is 0
          thousand: ".",
          decimal : ","
        }
      };

      $('.selectize').selectize({
        create: true,
        sortField: 'text'
      });
    });

    $("[name=td_diskon]").on('keyup',function(){
      var d = $(this).val();
      $("#help_popup_totaldiskon").text(accounting.formatMoney(d));
      netto();
    })


function get_edit(id){
     $.ajax({
        url: "{{ url('kasirreturbaru_get_edit')}} ",
        type: 'post',
        data: {id : id,},
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(respon){
          if(respon.produk.length > 0){
            for(i in respon.produk){
              table_barang.row.add([
                  // `<div class="text-center"></div>`,
                  `<div>${respon.produk[i].nama_produk}</div>`,
                  `<div class="text-right">${format_angka(respon.produk[i].jumlah)}</div>`,
                  `<div>${respon.produk[i].nama_satuan}</div>`,
                ]).draw(false);

            }
          }

          if(respon.barang.length > 0){
           for(i in respon.barang){
             var nama  = respon.barang[i].nama_barang;
             var kode  = respon.barang[i].kode_barang;
             var alias = respon.barang[i].alias_barang;
             if(alias === null || alias === "" || alias === 0){
               var nama_barang = kode+" || "+nama;
             }else{
               var nama_barang = kode+" || "+nama+" || "+alias;
             }
             table_barang.row.add([
                // `<div class="text-center"></div>`,
                `<div>${nama_barang}</div>`,
                `<div class="text-right">${format_angka(respon.barang[i].jumlah)}</div>`,
                `<div>${respon.barang[i].nama_satuan}</div>`,
              ]).draw(false);
           }
         }
       }
     })
}
</script>


<!-- SCRIPT TAB WIZARD -->
<script type="text/javascript">
  $(document).ready(function () {

    var navListItems = $('div.setup-panel div a'),
    allWells = $('.setup-content'),
    allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
      e.preventDefault();
      var $target = $($(this).attr('href')),
      $item = $(this);

      if (!$item.hasClass('disabled')) {
        navListItems.removeClass('btn-success').addClass('btn-default');
        $item.addClass('btn-success');
        allWells.hide();
        $target.show();
        // $target.find('input:eq(0)').focus();
      }
    });

    allNextBtn.click(function () {
      var curStep = $(this).closest(".setup-content"),
      curStepBtn = curStep.attr("id"),
      nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
      curInputs = curStep.find("input[type='text'],input[type='url']"),
      isValid = true;

      $(".form-group").removeClass("has-error");
      for (var i = 0; i < curInputs.length; i++) {
        if (!curInputs[i].validity.valid) {
          isValid = false;
          $(curInputs[i]).closest(".form-group").addClass("has-error");
        }
      }

      if (isValid) nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-success').trigger('click');
  });
</script>
@endsection

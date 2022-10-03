<?php $hal = "penerimaanbarang"; ?>
@extends('layouts.admin.master')
@section('title', 'Penerimaan')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">

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
    Penerimaan
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">


    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Tambah Penerimaan Barang</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <form role="form" class="form_belanja" action="{{url('penerimaan_simpan')}}" method="post">
              <!-- {{ csrf_field() }} {{ method_field('POST') }} -->
            <input type="hidden" name="id_pembelian" value="{{$data['data']['id_pembelian']}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="status_bayar" value="{{$data['data']['status_bayar']}}">
            <div class="box-body">
              <div class="row">
              <div class="col-md-12 form-horizontal form-group" style="border:1px solid black;">
                <br>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputPassword1" class="col-md-4">No. Faktur Pembelian</label>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="no_faktur_beli" placeholder="Tanggal" value="{{$data['data']['nomor']}}" required readonly/>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1" class="col-md-4">Tanggal Input</label>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="tanggal_beli" placeholder="Tanggal" value="{{$data['data']['tanggal']}}" required readonly/>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1" class="col-md-4">Tanggal Faktur</label>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="tanggal_faktur_beli" placeholder="Tanggal" value="{{$data['data']['tanggal_faktur']}}" required readonly/>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1" class="col-md-4">Penyedia</label>
                    <div class="col-md-8">
                      <select class="form-control" name="penyedia_beli" readonly>
                        <option value="{{$data['data']['id_penyedia']}}" selected="">{{$data['data']['nama_penyedia']}}</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputPassword1" class="col-md-4">Tanggal Penerimaan</label>
                    <div class="col-md-8">
                      <input class="form-control datepicker" type="text" name="tanggal_penerimaan" placeholder="Tanggal" value="{{($data['data']['tanggal_penerimaan'] == '') ? date('d-m-Y'):$data['data']['tanggal_penerimaan']}}" required readonly/>
                    </div>
                  </div>
                  <!-- <div class="form-group">
                    <label for="exampleInputPassword1" class="col-md-4">No faktur</label>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="no_faktur_penerimaan" placeholder="Nomor Faktur" value="{{$data['data']['nomor_penerimaan']}}"/>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1" class="col-md-4">No Surat Jalan</label>
                    <div class="col-md-8">
                      <input class="form-control datepicker" type="text" name="no_surat_penerimaan" placeholder="Nomor Surat" value="{{$data['data']['nomor_surat']}}"/>
                    </div>
                  </div> -->
                  <div class="form-group">
                    <label for="exampleInputPassword1" class="col-md-4">Gudang</label>
                    <div class="col-md-8">
                      <select class="form-control" name="gudang" style="width: 100%;">
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1" class="col-md-4">Status Penerimaan</label>
                    <div class="col-md-8">
                      <select class="form-control select2" name="status_penerimaan" style="width: 100%;">
                        <?php
                          foreach($data['status'] as $key => $value){
                            if($key == $data['data']['status_penerimaan']){
                              $selected = 'selected';
                            }else{
                              $selected = '';
                            }
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

                  <div class="form-group col-md-12 table-responsive" style="border:1px solid black;">
                   <h3>Detail Barang</h3>
                   <table class="table table-bordered table-hover table-striped table-barang" id="table_barang">
                    <thead>
                      <tr>
                        <th width="3%">No.</th>
                        <th>Nama Barang</th>
                        <th width="15%">Jumlah</th>
                        <th width="15%">Satuan</th>
                        <th width="15%">Jumlah Terima</th>
                      </tr>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                </div>

              </div>
                
        </div>

        <!-- /.box-body -->
        <div class="box-footer">
          <a href="{{url('penerimaanbarang')}}" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
          <button type="submit" class="btn bg-blue btn-md pull-right"><span class="glyphicon glyphicon-floppy-disk"></span> Simpan</button>
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
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>


<script type="text/javascript">

  var formBarang = $(".form_barang");
  var formBelanja = $(".form_belanja");
  var selectPajak = formBelanja.find("select[name=pajak]");
  var inputOngkir = formBelanja.find("input[name=status_ongkir]");
  var inputNominalOngkir = formBelanja.find("input[name=nominal_ongkir]");
  var tb_no = parseInt(1000);
  var table_barang;
  var id_pembelian = '{{$data['data']['id_pembelian']}}';
  var id_penyedia = '{{$data['data']['id_penyedia']}}';
  var nama_penyedia = '{{$data['data']['nama_penyedia']}}';
  var id_gudang = '{{$data['data']['id_gudang']}}';
  var nama_gudang = '{{$data['data']['nama_gudang']}}';

  $(document).ready(function () {
    table_barang = $("#table_barang").DataTable({
      "paging": false
    });
    table_barang.on( 'order.dt search.dt', function () {
            table_barang.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
    }).draw();

    if(id_pembelian != ''){
      if(id_penyedia != ''){
        $("[name=id_penyedia]").html('<option value="'+id_penyedia+'">'+nama_penyedia+'</option>')
      }

      if(id_gudang != ''){
        $("[name=gudang]").html('<option value="'+id_gudang+'">'+nama_gudang+'</option>')
      }
      get_edit(id_pembelian);
    }

    $('[name=gudang]').select2({
            placeholder: "Pilih...",
            //minimumInputLength: 2,
            ajax: {
                url: '{{url('penerimaan_get_gudang')}}',
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
                          text:item.nama,
                        });
                      });
                      return{
                        results:results
                      };
                },
                cache: true
            }   
  }); 

    
      
});

function get_edit(id){
  $.ajax({
      url: "{{ url('penerimaan_get_edit')}} ",
      type: 'post',
      data: {id : id,},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
        if(respon.length > 0){
          for(i in respon){
            var id_penerimaan = "";
            
            table_barang.row.add(['<div><center></center></div>',
              '<div id="list_barang'+respon[i].id+'">'+respon[i].nama_barang+'</div>',
              '<div id="list_jumlah'+respon[i].id+'" class="text-right">'+format_angka(respon[i].jumlah)+'</div>',
              '<div id="list_satuan'+respon[i].id+'">'+respon[i].nama_satuan+'</div>',
              '<div id="list_jumlah_terima'+respon[i].id+'"> <input type="text" class="form-control text-right" id="popup_jumlah_terima'+respon[i].id+'" name="popup_jumlah_terima" onkeyup="keyup_jumterima('+this.value+')"> <p class="text-right" id="help_popup_jumlahterima'+respon[i].id+'"></p></div>'+
              '<input type="hidden" name="tabel_id[]" id="tabel_id'+respon[i].id+'" value="'+respon[i].id+'">'+
              '<input type="hidden" name="tabel_idlog[]" id="tabel_idlog'+respon[i].id+'" value="'+respon[i].id_log_stok+'">'+
              '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+respon[i].id+'" value="'+respon[i].id_barang+'">'+
              '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+respon[i].id+'" value="'+respon[i].nama_barang+'">'+
              '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+respon[i].id+'" value="'+respon[i].jumlah+'">'+
              '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+respon[i].id+'" value="'+respon[i].harga+'">'+
              '<input type="hidden" name="tabel_diskon[]" id="tabel_diskon'+respon[i].id+'" value="'+respon[i].potongan+'">'+
              '<input type="hidden" name="tabel_total[]" id="tabel_total'+respon[i].id+'" value="'+respon[i].total+'">'+
              '<input type="hidden" name="tabel_subtotal[]" id="tabel_subtotal'+respon[i].id+'" value="'+respon[i].subtotal+'">'+
              '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+respon[i].id+'" value="'+respon[i].id_satuan+'">'+
              '<input type="hidden" name="tabel_namasatuan[]" id="tabel_namasatuan'+respon[i].id+'" value="'+respon[i].nama_satuan+'">'
              ]).draw(false);
          }
        }
        
      }
    })
}

function hapus(id){
  table_barang.row(id.parents('tr')).remove().draw();
    get_jum();
    get_subtotal();
    netto();
}
$("[name=status_penerimaan]").on('change',function(){
  var id = $(this).val();
  if(id==1){
    $('[name=gudang]').html("<option value='0'></option>");
  }
});

function keyup_jumterima(value){
  //$("#help_popup_jumlahterima"+id).val(accounting.formatMoney(value));
  console.log(value);
}


</script>

@endsection

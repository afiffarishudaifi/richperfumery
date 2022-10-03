<?php $hal = "stok"; ?>
@extends('layouts.admin.master')
@section('title', 'Stok Opname')

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
    Stok Opname
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">


    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Tambah Stok Opname</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <form role="form" class="form_belanja" action="{{url('persetujuanopname_simpan')}}" method="post">
              <input type="hidden" name="id_stokopname" value="{{$data['data']['id_stokopname']}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <!-- <input type="hidden" name="status" value="{{$data['data']['status']}}"> -->
            <div class="box-body">
              <div class="form-group col-md-12 " style="border:1px solid black;">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputPassword1">Tanggal</label>
                          <input class="form-control set_datepicker" type="text" name="tanggal" placeholder="Tanggal" value="{{($data['data']['tanggal']=='') ? date('d-m-Y'):$data['data']['tanggal']}}" required readonly />
                    </div>
                    <div class="form-group">
                      <label>Gudang</label>
                      <select name="gudang" class="form-control" required>
                      </select>
                    </div>
                    <div class="form-group">
                    <label>Keterangan</label>
                      <textarea class="form-control" rows="2" name="keterangan">{{$data['data']['keterangan']}}</textarea>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Status</label>
                      <select class="form-control select2" name="status" style="width: 100%;">
                        <?php
                          foreach($data['status'] as $key => $value){
                            if($key == $data['data']['status']){
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
               <!-- end : row -->
              </div>

              <div class="form-group col-md-12" style="border:1px solid black;">
               <h3>Detail Barang</h3>
               <hr/>
               <br/> <br/>
               <div class="table-responsive">
               <table class="table table-bordered table-hover table-striped table-barang">
                <thead>
                  <tr>
                      <th>No</th>
                      <th>Nama Barang</th>
                      <th>Gudang</th>
                      <th>Stok</th>
                      <th>Fisik</th>  
                      <th>Selisih</th>
                      <th>Ket.</th>
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
          <a href="{{url('stokopname')}}" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
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
<script src="{{asset('public/js/accounting.js')}}" charset="utf-8"></script>

<script type="text/javascript">
  var table;
  var tb_no = parseInt(1000);
  var id_stokopname = '{{$data['data']['id_stokopname']}}';
  var id_gudang     = '{{$data['data']['id_gudang']}}';
  var nama_gudang   = '{{$data['data']['nama_gudang']}}';
  $(document).ready(function () {
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

  table = $(".table-barang").DataTable({
    "paging" : false,
  });
  table.on('order.dt search.dt', function(){
    table.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i){
       cell.innerHTML = i+1;
    });
  }).draw();
  if(id_stokopname != ''){
    get_edit(id_stokopname);
    $("[name=gudang]").html('<option value="'+id_gudang+'" nama="'+nama_gudang+'" edit="1">'+nama_gudang+'</option>');
  } 


});

function get_edit(id){
  $.ajax({
      url: "{{url('persetujuanopname_get_edit')}} ",
      type: 'post',
      data: {id : id},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
        if(respon.length > 0){
          for(i in respon){
            table.row.add(['<div><center></center></div>',
              '<div id="list_barang'+respon[i].id+'">'+respon[i].kode_barang+" || "+respon[i].nama_barang+" || "+respon[i].alias_barang+'</div>',
              '<div id="list_gudang'+respon[i].id+'">'+respon[i].nama_gudang+'</div>',
              '<div id="list_jumStok'+respon[i].id+'">'+format_angka(respon[i].jumlah_stok)+'</div>',
              '<div id="list_jumFisik'+respon[i].id+'">'+format_angka(respon[i].jumlah_fisik)+'</div>',
              '<div id="list_selisih'+respon[i].id+'">'+format_angka(respon[i].selisih)+'</div>',
              '<div id="list_keterangan'+respon[i].id+'">'+respon[i].keterangan+'</div>'+
              '<input type="hidden" name="tabel_id[]" id="tabel_id'+respon[i].id+'" value="'+respon[i].id+'">'+
              '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+respon[i].id+'" value="'+respon[i].id_barang+'">'+
              '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+respon[i].id+'" value="'+respon[i].nama_barang+'">'+
              '<input type="hidden" name="tabel_kodebarang[]" id="tabel_kodebarang'+respon[i].id+'" value="'+respon[i].kode_barang+'">'+
              '<input type="hidden" name="tabel_aliasbarang[]" id="tabel_aliasbarang'+respon[i].id+'" value="'+respon[i].alias_barang+'">'+
              '<input type="hidden" name="tabel_idgudang[]" id="tabel_idgudang'+respon[i].id+'" value="'+respon[i].id_gudang+'">'+
              '<input type="hidden" name="tabel_namagudang[]" id="tabel_namagudang'+respon[i].id+'" value="'+respon[i].nama_barang+'">'+
              '<input type="hidden" name="tabel_jumStok[]" id="tabel_jumStok'+respon[i].id+'" value="'+respon[i].jumlah_stok+'">'+
              '<input type="hidden" name="tabel_jumFisik[]" id="tabel_jumFisik'+respon[i].id+'" value="'+respon[i].jumlah_fisik+'">'+
              '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+respon[i].id+'" value="'+respon[i].id_satuan+'">'+
              '<input type="hidden" name="tabel_selisih[]" id="tabel_selisih'+respon[i].id+'" value="'+respon[i].selisih+'">'+
              '<input type="hidden" name="tabel_idlog_stok[]" id="tabel_idlog_stok'+respon[i].id+'" value="'+respon[i].id_log_stok+'">'+
              '<input type="hidden" name="tabel_keterangan[]" id="tabel_keterangan'+respon[i].id+'" value="'+respon[i].keterangan+'">'
              ]).draw(false);
          }
          
        }
        
      }
    });
}



</script>

@endsection

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
    Penerimaan Barang
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Penerimaan Barang</h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">

                <div class="form-group">
                  <div class="col-md-8">
                      <select class="form-control" name="gudang">
                        <option value=""> --- Pilih --- </option>
                      </select>
                  </div>
                      <button class="btn btn-primary col-md-4" type="button" id="btn_verifikasi">Verifikasi</button>
                </div>

            </div>
        </div>
        </div>
      </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th width="3%">No</th>
                <th width="15%">No. Pembelian</th>
                <th width="15%">Gudang</th>
                <th width="10%">Tanggal</th>
                <th>Penyedia</th>
                <th width="15%">Status Penerimaan</th> 
                <th class="text-center" width="10%">Aksi</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>

        <!-- <div class="box-body table-responsive">
          <table class="dataTable display">
            <thead>
              <tr>
                <th width="3%">No</th>
                <th width="15%">No. Pembelian</th>
                <th width="15%">Gudang</th>
                <th width="10%">Tanggal</th>
                <th>Penyedia</th>
                <th width="15%">Status Penerimaan</th> 
                <th class="text-center" width="10%">Aksi</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div> -->

        
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
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script type="text/javascript">
var table, save_method, table_barang;
$(function(){
  table = $('#datatable1').DataTable({
    "processing" : true,
    "ajax" : {
      "url" : "{{ url('penerimaan_data') }}",
      "type" : "GET"
    }
  })

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

/*$(document).ready(function(){
    table_barang = $(".dataTable").DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ url('penerimaan_get_data') }}',
          columns: [
              {data: 'rownum', name: 'rownum', orderable: false, searchable: false, render : function(data, type, row, meta){
            var check = '';
                if(row.status_id == 1){
                  check = '<input type="checkbox" id="check_verifikasi" class="styled" value="'+row.id_pembelian+'">';
                }
            return meta.row+1 + ' ' + row.checkbox;

            
          }, className: 'text-center'},
              {data: 'nomor', name: 'nomor'},
              {data: 'nama_gudang', name: 'nama_gudang'},
              {data: 'tanggal', name: 'tanggal'},
              {data: 'nama_supplier', name: 'nama_supplier'},
              {data: 'status', name: 'status'},
              {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
          ],
          "order": [[ 3, "desc" ]]
    });
  })*/

$("#btn_verifikasi").click(function(){
    var rows_selected = [];
    var jenis_selected = [];
    var gudang_selected = [];

    var rowcollection =  table.$("#check_verifikasi:checked", {"page": "all"});
    rowcollection.each(function(index,elem){
        var checkbox_value = $(elem).val();
        var gudang = $("[name=gudang]").val();

        rows_selected.push(checkbox_value);
        gudang_selected.push(gudang);
    
    })
    if(rows_selected.length > 0){
      if(confirm("Anda yakin akan memverifikasi data ini?")){    
        kirim_data(rows_selected, gudang_selected);  
      }    
    }else{
        alert("Tidak Ada Data Terpilih!");
    }

  })

  function kirim_data(val, gudang){
    $.ajax({
      url: '{{ url('penerimaan_simpanmulti') }}',
      type: 'post',
      dataType: 'json',
      data: {id : val, gudang : gudang},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
        table.ajax.reload();
      },
      error : function(){
          alert("Tidak dapat menyimpan data!");
      }
    })
  }
  
</script>


@endsection

<?php $hal = "Penerimaan"; ?>
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
var table, save_method;
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


$("#btn_verifikasi").click(function(){
    var rows_selected = [];
    var jenis_selected = [];

    var rowcollection =  table.$("#check_verifikasi:checked", {"page": "all"});
    rowcollection.each(function(index,elem){
        var checkbox_value = $(elem).val();
        var jenis = $(elem).attr('jenis');

        rows_selected.push(checkbox_value);
        jenis_selected.push(jenis);
    
    })
    var gudang_selected = $("[name=gudang]").val();
    if(rows_selected.length > 0){
      if(confirm("Anda yakin akan memverifikasi data ini?")){    
        //kirim_data(rows_selected, jenis_selected, gudang_selected);  
        console.log(rows_selected);
      }    
    }else{
        alert("Tidak Ada Data Terpilih!");
    }

  })

  function kirim_data(val, jenis, gudang){
    $.ajax({
      url: '{{ url('penerimaan_simpanmulti') }}',
      type: 'post',
      dataType: 'json',
      data: {id : val, jenis : jenis, gudang : gudang},
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
  // $(function () {
  //   //Initialize Select2 Elements
  //   $('.select2').select2()
  // })
  $(document).ready(function() {
  $('.js-example-basic-single').select2({
    dropdownParent: $(".modal")
  });
});
</script>


@endsection

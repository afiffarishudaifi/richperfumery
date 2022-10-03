<?php $hal = "barang"; ?>
@extends('layouts.admin.master')
@section('title', 'Master Barang')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('public/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
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
    Kasir
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
  <div class="col-md-12">
    <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Kasir</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <form class="form-horizotal" method="">
            <div class="col-md-8">

            <div class="col-md-6">

            <div class="form-group">
              <label class="control-label col-md-4">Tanggal</label>
              <div class="input-group col-md-8">
                <input type="text" name="tanggal" class="form-control datepicker" value="{{date('d-m-Y')}}">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4">Kode Kasir</label>
              <div class="input-group col-md-8">
                <input type="text" name="kode_kasir" class="form-control" >
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4">Outlet</label>
              <div class="input-group col-md-8">
                <input type="text" name="outlet" class="form-control" >
              </div>
            </div>
            <!-- <div class="form-group"><br></div>  -->
            <div class="form-group">
              <label class="control-label col-md-4">Kode Barang</label>
              <div class="input-group col-md-8">
                <select class="form-control col-md-12" style="width: 100%; height: 100%;" name="kode_barang">
                  
                </select>
                
              </div>
            </div>          

          </div>

            <div class="col-md-6">
              <input type="hidden" name="id_table_barang" value="">
            <div class="form-group">
              <label class="control-label col-md-4">Nama Barang</label>
              <div class="input-group col-md-8">
                <input type="text" name="nama_barang" class="form-control">
              </div>
            </div>
              <div class="form-group">
                <label class="control-label col-md-4">Jumlah</label>
                <div class="input-group col-md-8">
                  <input type="text" name="jumlah_barang" class="form-control number-only">
                  <p class="help-block text-right" id="help_popup_jumlah"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-4">Harga</label>
                <div class="input-group col-md-8">
                  <input type="text" name="harga_barang" class="form-control number-only">
                  <p class="help-block text-right" id="help_popup_harga"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-4">SubTotal</label>
                <div class="input-group col-md-8">
                  <input type="text" name="subtotal" class="form-control" readonly="">
                  <p class="help-block text-right" id="help_popup_subtotal"></p>
                </div>
              </div>
            </div>

            <div class="col-md-12">
            <button type="button" class="btn btn-success col-md-12" id="btn_tambah">Tambah</button>
            </div>
      </div> <!-- col 8 -->

            <div class="col-md-4">
              
              <div class="form-group">
              <label class="control-label col-md-4">Total</label>
              <div class="input-group col-md-8">
                <input type="text" name="total" id="total" class="form-control" readonly="">
                <p class="help-block text-right" id="help_popup_total"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4">Bayar</label>
              <div class="input-group col-md-8">
                <input type="text" name="bayar" class="form-control">
                <p class="help-block text-right" id="help_popup_bayar"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4">Kembali</label>
              <div class="input-group col-md-8">
                <input type="text" name="kembali" class="form-control" readonly="">
                <p class="help-block text-right" id="help_popup_kembali"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4">Pembayaran</label>
              <div class="input-group col-md-8">
                <select class="form-control" name="pembayaran">
                      <option value="tunai" selected>Penjualan Tunai</option>
                      <option value="debet">Penjualan Debet</option>
                      <option value="flazz">Flazz</option>
                      <option value="kredit">Penjualan Kredit</option>
                      <option value="transfer">Penjualan Transfer</option>
                      <option value="ovo">Penjualan OVO</option>
                      <option value="hutang">Penjualan Hutang</option>
                    </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4">Rek.</label>
              <div class="input-group col-md-8">
                <select class="form-control" name="pembayaran">
                      <option value="tunai" selected>BCA</option>
                      <option value="kredit">MANDIRI</option>
                    </select>
              </div>
            </div>
            <button class="btn btn-primary col-md-12" type="submit" id="btn_simpan">Simpan</button>


            </div>

            <div class="col-md-12">

              <hr>

            </div>
            <div class="col-sm-12 form-group">
            <div class="table-responsive">
            <table id="datatable1" class="table table-bordered table-striped">
              <thead>
                <tr>
                <th width="3%">No.</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th></th>
              </tr>
              </thead>
              <tbody>
                
              </tbody>
            </table>
            </div>
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

<script type="text/javascript">
var table, save_method;
var tb_no = parseInt(10000);
$(document).ready(function(){
  table = $('#datatable1').DataTable({
    "processing" : true,
    /*"ajax" : {
      "url" : "{{ route('data_barang') }}",
      "type" : "GET"
    },*/
    scrollY       : 400,
    scrollCollapse: true,
    paging        : false
  });
table.on('order.dt search.dt', function(){
  table.column(0, {search:'applied', order:'applied'}).nodes().each( function(cell, i){
    cell.innerHTML = i+1;
  });
}).draw();

$('[name=kode_barang]').select2({
            placeholder: "Pilih...",
            //minimumInputLength: 2,
            ajax: {
                url: 'select2barang_kasir',
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
                          id:item.barang_id,
                          satuan:item.satuan_satuan,
                          nama:item.barang_nama,
                          text:item.barang_kode,
                          harga:item.harga,
                          kode:item.barang_kode,
                        });
                      });
                      return{
                        results:results
                      };
                },
                cache: true
            }   
  });

/*$("[name=kode_barang]").select2({
        minimumInputLength: 3,
        multiple: false,
        allowClear: true,
        ajax: {
            url: urlFetchCity,
            dataType: 'json',
            type: "POST",
            data: function (term, page) { return { city: term }; },
            results: function (data, page) {
                return {
                    return {results: data};
                };
            }
        },
        initSelection: function (item, callback) {
            var id = item.val();
            var text = item.data('option');
            var data = { id: id, text: text };
            callback(data);
        },
        formatResult: function (item) { return ('<div>' + item.id + ' - ' + item.text + '</div>'); },
        formatSelection: function (item) { return (item.text); },
        escapeMarkup: function (m) { return m; }
    });*/



  $("[name=kode_barang]").on('change',function(){
    var d = $('[name=kode_barang]').select2("data")[0];
    $("[name=nama_barang]").val(d.nama);
    $("[name=harga_barang]").val(d.harga);
    $("#help_popup_harga").text(accounting.formatMoney(d.harga));
  });

  $("[name=jumlah_barang]").on('keyup',function(){
    var jumlah = $(this).val();
    var harga = $("[name=harga_barang]").val();
    var subtotal = parseInt(jumlah*harga);
    $("[name=subtotal]").val(subtotal);
    $("#help_popup_jumlah").text(format_angka(jumlah));
    $("#help_popup_subtotal").text(accounting.formatMoney(subtotal));
  });

  $("[name=harga_barang]").on('keyup',function(){
    var jumlah = $("[name=jumlah_barang]").val();
    var harga = $(this).val();
    var subtotal = parseInt(jumlah*harga);
    $("[name=subtotal]").val(subtotal);
    $("#help_popup_harga").text(accounting.formatMoney(harga));
    $("#help_popup_subtotal").text(accounting.formatMoney(subtotal));
  });


  $('#modal-form form').validator().on('submit', function(e){
    if(!e.isDefaultPrevented()){
      var id = $('#id').val();
      if(save_method == "add") url = "{{ route('barang.store') }}";
      else url = "barang/"+id;
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


$("#btn_tambah").click(function(){
    var id_data = $("[name=id_table_barang]").val();
    var kode    = $("[name=kode_barang]").select2("data")[0];
    var nama    = $("[name=nama_barang]").val();
    var jumlah  = $("[name=jumlah_barang]").val();
    var harga   = $("[name=harga_barang]").val();
    
    if(kode != '' || jumlah != '' || harga != ''){
    if(id_data == ''){
      table.row.add(['<div><center></center></div>',
          '<div id="list_kode'+tb_no+'">'+kode.kode+'</div>',
          '<div id="list_nama'+tb_no+'">'+nama+'</div>',
          '<div id="list_jumlah'+tb_no+'">'+jumlah+'</div>',
          '<div id="list_harga'+tb_no+'">'+harga+'</div>',
          '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+tb_no+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
              '<input type="hidden" id="tabel_idtable'+tb_no+'" name="tabel_idtable[]" value="">'+
              '<input type="hidden" name="tabel_id[]" class="tabel_id" id="tabel_id'+tb_no+'" value="'+tb_no+'">'+
              '<input type="hidden" name="tabel_kodeid[]" id="tabel_kodeid'+tb_no+'" value="'+kode.id+'">'+
              '<input type="hidden" name="tabel_kodenama[]" id="tabel_kodenama'+tb_no+'" value="'+kode.kode+'">'+
              '<input type="hidden" name="tabel_nama[]" id="tabel_nama'+tb_no+'" value="'+nama+'">'+
              '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+tb_no+'" value="'+jumlah+'">'+
              '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+tb_no+'" value="'+harga+'">'+
              '<input type="hidden" name="tabel_total[]" id="tabel_total'+tb_no+'" value="'+parseFloat(jumlah*harga)+'">'
        ]).draw(false);
        tb_no++;
    }else{
        $("#list_kode"+id_data).text(kode);
        $("#list_nama"+id_data).text(nama);
        $("#list_jumlah"+id_data).text(jumlah);
        $("#list_harga"+id_data).text(harga);

        $("#tabel_id"+id_data).val(id_data);
        $("#tabel_kodeid"+id_data).val(kode.id);
        $("#tabel_kodenama"+id_data).val(kode.kode);
        $("#tabel_nama"+id_data).val(nama);
        $("#tabel_jumlah"+id_data).val(jumlah);
        $("#tabel_harga"+id_data).val(harga);
    }


    $("[name=id_table_barang]").val("");
    $("[name=nama_barang]").val("");
    $("[name=jumlah_barang]").val("");
    $("[name=harga_barang]").val("");
    $("[name=subtotal]").val("");
    $("#help_popup_jumlah").text("");
    $("#help_popup_harga").text("");
    $("#help_popup_subtotal").text("");
    //$("[name=kode_barang]").select2("val", "");
    $("[name=kode_barang]").val(null).trigger('change');

    jum_total(); 
    }
});

function edit_table(id){
  var id_table = $("#tabel_id"+id).val();
  var kode = $("#tabel_kodeid"+id).val();
  var nama = $("#tabel_nama"+id).val();
  var jumlah = $("#tabel_jumlah"+id).val();
  var harga = $("#tabel_harga"+id).val();
  var total = $("#tabel_total"+id).val();

  $("[name=id_table_barang]").val(id_table);
  $("[name=kode_barang]").val(kode).trigger('change');
  $("[name=nama_barang]").val(nama);
  $("[name=jumlah_barang]").val(jumlah);
  $("[name=harga_barang]").val(harga);
  $("[name=subtotal]").val(total);
  $("#help_popup_jumlah").text(format_angka(jumlah));
  $("#help_popup_harga").text(accounting.formatMoney(harga));
  $("#help_popup_subtotal").text(accounting.formatMoney(total));

  //console.log(id_table+'=id,'+kode+'=kode,'+nama+'=nama'+jumlah+'=jumlah'+harga);
}

function hapus(id){
  table.row(id.parents('tr')).remove().draw();
}

function jum_total(){
    var jum = parseFloat(0);
    $('input[id^="tabel_total"]').each(function() {
              // alert($(this).val());
              var val = ($(this).val());
              jum += parseFloat(val);
              
          });
    $("#help_popup_total").text(accounting.formatMoney(jum));
    $("[name=total]").val(jum);
    //netto();
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

<?php $hal = "kasirpembayaran"; ?>
@extends('layouts.admin.master')
@section('title', 'Pembayaran Penjualan')

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
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Pembayaran Penjualan</h3>
        </div>

        <!-- <a href="{{url('kasir_tambah')}}" style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a>
        <button class="btn btn-primary" style="margin-bottom:20px;margin-left:10px;" type="button" id="btn_posting"> <i class="fa  fa-check-o"></i>Pembayaran</button>-->
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:3%">#</th>
                <th style="width:3%">No</th>
                <!-- <th style="width:10%;">Tanggal</th> -->
                <!-- <th style="width:10%;">No. Faktur</th> -->
                <th style="width:25%;">Pelanggan</th>
                <!-- <th style="width:10%">Gudang</th> -->
                <!-- <th style="width:10%">Jatuh Tempo</th> -->
                <!-- <th style="width:12%">Total Penjualan</th> -->
                <th style="width:10%">Status</th>
                <th style="width:5%">Aksi</th>
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
@include('admin_mobile.pembayaran.form_kasir')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script type="text/javascript">
var table, table_barang, save_method;
$(function(){
  table = $("#datatable1").DataTable({
    processing: true,
    serverSide: true,
    "ordering": false,//tambahan
    "pageLength": 10,//tambahan
    "lengthChange": false,//tambahan
    ajax: '{{ url('kasirpembayaran_data') }}',
    columns: [
      {data: 'nomor', name: 'nomor'},
      {data: 'tanggal', name: 'tanggal'},
      {data: 'no_faktur', name: 'no_faktur'},
      {data: 'pelanggan', name: 'pelanggan'},
      {data: 'gudang', name: 'gudang'},
      {data: 'tempo', name: 'tempo'},
      {data: 'tagihan', name: 'tagihan'},
      {data: 'status', name: 'status', orderable: false, searchable: false},
      {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
    ],
    "columnDefs": [
      { targets: 0, orderable: false, searchable: false, }
    ],
    createdRow: function(row, data, index){
      $('td', row).eq(6).attr('align','right'); // 6 is index of column
    },
    "columns": [
      {
        "className":      'details-control',
        "orderable":      false,
        "data":           null,
        "defaultContent": ''
      },
      { "data": "nomor" },
      // { "data": "no_faktur" },
      { "data": "pelanggan" },
      { "data": "status" },
      { "data": "aksi" }
    ],
  });

  /* Formatting function for row details - modify as you need */
  function format_brg_banyak ( d ) {
    // `d` is the original data object for the row

    return '<table>'+
    '<tr>'+
    '<td>Tanggal</td>'+
    '<td> : '+d.tanggal+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Faktur</td>'+
    '<td> : '+d.no_faktur+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Pelanggan</td>'+
    '<td> : '+d.pelanggan+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Gudang</td>'+
    '<td> : '+d.gudang+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Tempo</td>'+
    '<td> : '+d.tempo+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Tagihan</td>'+
    '<td> : '+d.tagihan+'</td>'+
    '</tr>'+
    '<tr>'+
    '<td>Status</td>'+
    '<td> : '+d.status+'</td>'+
    '</tr>'+
    '</table>';
  }
  // Add event listener for opening and closing details
  $('#datatable1 tbody').on('click', 'td.details-control', function () {
    var tabelnya = $('#datatable1').DataTable();
    var tr = $(this).closest('tr');
    var row = tabelnya.row( tr );

    if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }else {
      // Open this row
      row.child( format_brg_banyak(row.data()) ).show();
      tr.addClass('shown');
    }
  });

  table_barang = $("#table_barang").DataTable({
    paging : false,
    "ordering": false,//tambahan
    "lengthChange": false,//tambahan
    "bInfo" : true
  });
  table_barang.on( 'order.dt search.dt', function () {
    table_barang.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
      cell.innerHTML = i+1;
    } );
  } ).draw();

});

function edit_table(id){
  var id_table        = $("#table_id"+id).val();
  var nomor           = $("#table_nofaktur"+id).val();
  var nama_pelanggan  = $("#table_namapelanggan"+id).val();
  var nama_gudang     = $("#table_namagudang"+id).val();
  var tanggal         = $("#table_tanggal"+id).val();
  var tanggal_faktur  = $("#table_tanggal_faktur"+id).val();
  var tanggal_tempo   = $("#table_tanggal_tempo"+id).val();
  var tanggal_bayar   = $("#table_tanggal_bayar"+id).val();
  var total_tagihan   = $("#table_total_tagihan"+id).val();
  var total_uangmuka  = $("#table_total_uangmuka"+id).val();
  var total_ongkir    = $("#table_total_ongkir"+id).val();
  var total_bayar     = $("#table_total_bayar"+id).val();
  var total_potongan  = $("#table_total_potongan"+id).val();
  var keterangan      = $("#table_keterangan"+id).val();
  var carabayar       = $("#table_carabayar"+id).val();
  var status          = $("#table_status"+id).val();

  $("#popup_id_table").val(id_table);
  $("#popup_pelanggan").text(nama_pelanggan);
  $("#popup_nomor").text(nomor);
  $("#popup_gudang").text(nama_gudang);
  $("#popup_tanggal").text(tanggal);
  $("#popup_tanggal_faktur").text(tanggal_faktur);
  $("#popup_tanggal_tempo").text(tanggal_tempo);
  $("#popup_tanggal_bayar").val(tanggal_bayar);
  $("#popup_carabayar").text(carabayar);
  //$("#popup_tagihan").text(accounting.formatMoney(total_tagihan));
  $("#td_uangmuka").val(total_uangmuka);
  $("#td_potongan").val(total_potongan);
  $("#td_ongkir").val(total_ongkir);
  $("#td_bayar").val(total_bayar);
  $("#popup_keterangan").text(keterangan);
  $("[name=popup_status]").val(status).trigger('change');

  table_barang.clear();
  $.ajax({
    url: "{{ url('kasirpembayaran_get_detail')}} ",
    type: 'post',
    data: {id : id,},
    headers : {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(respon){
      if(respon.produk.length > 0){
        for(i in respon.produk){
          table_barang.row.add(['<div width="3%"><center></center></div>',
          '<div id="list_produk'+respon.produk[i].id+'" width="42%">'+respon.produk[i].nama_produk+'</div>',
          '<div id="list_harga'+respon.produk[i].id+'" class="text-right" width="20%">'+accounting.formatMoney(respon.produk[i].harga)+'</div>',
          '<div id="list_jumlah'+respon.produk[i].id+'" class="text-right" width="15%">'+format_angka(respon.produk[i].jumlah)+" "+respon.produk[i].nama_satuan+'</div>',
          '<div id="list_total'+respon.produk[i].id+'" class="text-right" width="42%">'+accounting.formatMoney(respon.produk[i].total)+'</div>'+
          '<input type="hidden" name="tabel_total[]" id="tabel_total'+respon.produk[i].id+'" value="'+respon.produk[i].total+'">'
        ]).draw(false);
      }
      /*get_jum();*/
      get_subtotal();
      netto();
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
        table_barang.row.add(['<div width="3%"><center></center></div>',
        '<div id="list_produk'+respon.barang[i].id+'" width="42%">'+nama_barang+'</div>',
        '<div id="list_harga'+respon.barang[i].id+'" class="text-right" width="20%">'+accounting.formatMoney(respon.barang[i].harga)+'</div>',
        '<div id="list_jumlah'+respon.barang[i].id+'" class="text-right" width="15%">'+format_angka(respon.barang[i].jumlah)+" "+respon.barang[i].nama_satuan+'</div>',
        '<div id="list_total'+respon.barang[i].id+'" class="text-right" width="20%">'+accounting.formatMoney(respon.barang[i].total)+'</div>'+
        '<input type="hidden" name="tabel_total[]" id="tabel_total'+respon.barang[i].id+'" value="'+respon.barang[i].total+'">'
      ]).draw(false);
    }


    /*get_jum();*/
    get_subtotal();
    netto();
  }

}
})

$("#modal-form").modal('show');
}


$("#btn_popup_simpan").click(function(){
  var id = $("[name=popup_id_table]").val();
  var status = $("[name=popup_status]").val();
  if(id != '' && status != ''){
    $.ajax({
      url: "{{ url('kasirpembayaran_simpan') }}",
      type: 'post',
      dataType: 'json',
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: $("#form_pembayaran").serialize(),
      success: function(respon){
        table.ajax.reload();
        $('#modal-form').modal('hide');
      }
    })
  }
})

$("[name=popup_status]").on('change',function(){
  netto();
});

function get_subtotal(this_){
  var jum = parseFloat(0);
  $('input[id^="tabel_total"]').each(function() {
    var val = ($(this).val());
    jum += parseFloat(val);

  });
  $("#td_subtotal").text(accounting.formatMoney(jum));
  $("[name=td_subtotal]").val(jum);
  netto();
}

function netto(){
  var total   = parseFloat($("[name=td_subtotal]").val()) || 0;
  var diskon  = parseFloat($("[name=td_potongan]").val()) || 0;
  var ongkir  = parseFloat($("[name=td_ongkir]").val()) || 0;
  var netto   = parseFloat(0);
  var uangmuka= parseFloat($("[name=td_uangmuka]").val()) || 0;
  var carabayar = $("[name=popup_carabayar]").val();
  var status  = $("[name=popup_status]").val();
  var sisa    = parseFloat(0);
  var bayar   = parseFloat(0);

  netto = total-(diskon+ongkir)-uangmuka;

  if(status == 1){
    bayar = parseFloat(0);
  }else if(status == 2){
    bayar = netto;
  }

  sisa = netto-bayar;

  $("[name=td_potongan]").val(diskon);
  $("[name=td_uangmuka]").val(uangmuka);
  $("[name=td_ongkir]").val(ongkir);
  $("#popup_tagihan").text(accounting.formatMoney(netto));
  $("#popup_bayar").text(accounting.formatMoney(bayar));
  $("#popup_sisa").text(accounting.formatMoney(sisa));
}



</script>

@endsection

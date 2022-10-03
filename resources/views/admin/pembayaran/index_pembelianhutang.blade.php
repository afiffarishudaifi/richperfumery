<?php $hal = "pembelianpembayaran"; ?>
@extends('layouts.admin.master')
@section('title', 'Pembayaran Hutang')

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
    Pembayaran Hutang
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Data Pembayaran Hutang</h3>
        </div>
        
        <!-- <a href="{{url('kasir_tambah')}}" style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa  fa-plus-square-o"></i> Tambah</button></a> 
        <button class="btn btn-primary" style="margin-bottom:20px;margin-left:10px;" type="button" id="btn_posting"> <i class="fa  fa-check-o"></i>Pembayaran</button>-->
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:3%">No</th>
                <th style="width:10%;">tanggal</th>
                <th style="width:10%;">No. Faktur</th>
                <th style="width:25%;">Supplier</th>
                <th style="width:10%">Gudang</th>
                <th style="width:10%">Jatuh Tempo</th>
                <th style="width:12%">Total Hutang</th>
                <th style="width:10%">Status</th>
                <th style="width:5%"></th>
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
@include('admin.pembayaran.form_pembelianhutang')
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
          ajax: '{{ url('pembelianpembayaran_data') }}',
          columns: [
              {data: 'nomor', name: 'nomor'},
              {data: 'tanggal', name: 'tanggal'},
              {data: 'no_faktur', name: 'no_faktur'},
              {data: 'supplier', name: 'supplier'},
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
    });

  table_barang = $("#table_barang").DataTable({
      paging : false,
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
  var nama_supplier   = $("#table_namasupplier"+id).val();
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
  var total_pajak     = $("#table_total_pajak"+id).val();
  var total_subtotal  = $("#table_subtotal"+id).val();
  var keterangan      = $("#table_keterangan"+id).val();
  var carabayar       = $("#table_carabayar"+id).val();
  var status          = $("#table_status"+id).val();

  $("#popup_id_table").val(id_table);
  $("#popup_supplier").text(nama_supplier); 
  $("#popup_nomor").text(nomor);   
  $("#popup_gudang").text(nama_gudang);
  $("#popup_tanggal").text(tanggal);
  $("#popup_tanggal_faktur").text(tanggal_faktur);
  $("#popup_tanggal_tempo").text(tanggal_tempo);
  $("#popup_tanggal_bayar").val(tanggal_bayar);
  $("#popup_carabayar").val(carabayar);
  //$("#popup_tagihan").text(accounting.formatMoney(total_tagihan)); 
  $("#td_uangmuka").val(total_uangmuka);
  $("#td_ongkir").val(total_ongkir);
  $("#td_bayar").val(total_bayar);
  $("#td_pajak").val(total_pajak);
  $("#td_potongan").val(total_potongan);
  $("#td_subtotal").val(total_subtotal);
  $("#popup_keterangan").text(keterangan); 
  $("[name=popup_status]").val(status).trigger('change');

  table_barang.clear();
  $.ajax({
      url: "{{ url('pembelianpembayaran_get_detail')}} ",
      type: 'post',
      data: {id : id,},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
        if(respon.length > 0){
          for(i in respon){
            table_barang.row.add(['<div width="3%"><center></center></div>',
              '<div id="list_barang'+respon[i].id_pembelian+'" width="42%">'+respon[i].nama_barang+'</div>',
              '<div id="list_harga'+respon[i].id_pembelian+'" class="text-right" width="20%">'+accounting.formatMoney(respon[i].harga)+'</div>',
              '<div id="list_jumlah'+respon[i].id_pembelian+'" class="text-right" width="15%">'+format_angka(respon[i].jumlah)+" "+respon[i].nama_satuan+'</div>',
              '<div id="list_total'+respon[i].id_pembelian+'" class="text-right" width="42%">'+accounting.formatMoney(respon[i].total)+'</div>'+
              '<input type="hidden" name="tabel_total[]" id="tabel_total'+respon[i].id_pembelian+'" value="'+respon[i].total+'">'
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
        url: "{{ url('pembelianpembayaran_simpan') }}",
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
    //$("#td_subtotal").text(accounting.formatMoney(jum));
    $("[name=td_subtotal]").val(jum);
    netto();
}

function netto(){
  var total   = parseFloat($("[name=td_subtotal]").val()) || 0;
  var diskon  = parseFloat($("[name=td_potongan]").val()) || 0;
  var ongkir  = parseFloat($("[name=td_ongkir]").val()) || 0;
  var pajak   = parseFloat($("[name=td_pajak]").val()) || 0;
  var uangmuka= parseFloat($("[name=td_uangmuka]").val()) || 0;
  var carabayar = $("[name=popup_carabayar]").val();
  var status  = $("[name=popup_status]").val(); 
  var sisa    = parseFloat(0);
  var bayar   = parseFloat(0);
  var ppn     = parseFloat(0);
  var netto   = parseFloat(0);
  var d_uangmuka = parseFloat(0);

  if(pajak==1){
      ppn = parseFloat((total-diskon)*(10/100));
    }

  netto = total-(diskon-ppn+ongkir);

  if(status == 3){
      d_uangmuka = parseFloat(0);
      bayar = parseFloat(0);
      total_bayar = parseFloat(0);
  }else if(status == 2){
      d_uangmuka=uangmuka;
      bayar = parseFloat(0);
      total_bayar = parseFloat(0);
  }else if(status == 1){
      d_uangmuka=parseFloat(0);
      bayar = netto;
      total_bayar = bayar-uangmuka;
  }

  sisa = netto-bayar-d_uangmuka;

  $("[name=td_diskon]").val(diskon);
  $("[name=td_uangmuka]").val(uangmuka);
  $("[name=td_ongkir]").val(ongkir);
  $("[name=td_pajak]").val(pajak);
  $("#popup_tagihan").text(accounting.formatMoney(netto));  
  $("#popup_bayar").text(accounting.formatMoney(total_bayar));
  $("#popup_sisa").text(accounting.formatMoney(sisa));
  $("#popup_uangmuka").text(accounting.formatMoney(uangmuka));
  //console.log(netto+"/"+ppn+"/"+pajak+"/"+total+"/"+diskon);
}




</script>

@endsection

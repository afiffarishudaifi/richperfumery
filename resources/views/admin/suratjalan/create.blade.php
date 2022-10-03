<?php $hal = "suratjalan"; ?>
@extends('layouts.admin.master')
@section('title', 'Surat Jalan')

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
    Surat Jalan
    <!-- <small>Data barang</small> -->
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">


    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Tambah Belanja Barang</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          
          <form role="form" class="form_belanja" action="{{url('suratjalan_simpan')}}" method="post">
            <input type="hidden" name="id_pembelian" value="{{$data['data']['id_pembelian']}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="jenis" value="{{$data['data']['jenis']}}">
            <input type="hidden" name="status_jenis" value="{{$data['data']['status_jenis']}}">
            <div class="box-body">
              <div class="form-group col-md-12 " style="border:1px solid black;">
                <div class="row">
                    <div class="form-group col-md-6">
                      <label for="exampleInputPassword1">Tanggal Input</label>
                          <input class="form-control datepicker" type="text" name="tanggal" placeholder="Tanggal" value="{{($data['data']['tanggal'] == '') ? date('d-m-Y'):$data['data']['tanggal']}}" required readonly />
                    </div>
                    <div class="form-group col-md-6">
                      <label>Tanggal Faktur</label>
                          <input class="form-control datepicker" type="text" name="tanggal_faktur" placeholder="Tanggal Faktur" value="{{($data['data']['tanggal_faktur'] == '') ? date('d-m-Y'):$data['data']['tanggal_faktur']}}" required/>
                    </div>
                    <div class="form-group col-md-6">
                    <label for="exampleInputPassword1">Supplier</label>
                    <select name="id_penyedia" class="form-control" style="width: 100%;" required="">  
                    </select>
                  </div>
                    <div class="form-group  col-md-6">
                      <label for="exampleInputPassword1">Nomor Faktur</label>
                      <input type="text"  name="nomor" class="form-control" value="{{$data['data']['nomor']}}" required="">
                    </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-6">
                    <label>Tanggal Jatuh Tempo</label>
                        <input class="form-control datepicker" type="text" name="tanggal_tempo" placeholder="Tanggal Tempo" value="{{($data['data']['tanggal_tempo'] == '') ? date('d-m-Y'):$data['data']['tanggal_tempo']}}" required/>
                    </div>
                  <div class="form-group col-md-6">
                    <label for="exampleInputPassword1">Keterangan</label>
                    <textarea class="form-control" rows="2" name="keterangan">{{$data['data']['keterangan']}}</textarea>
                  </div>

                </div> <!-- end : row -->
              </div>

              <div class="form-group col-md-12" style="border:1px solid black;">
               <h3>Detail Barang</h3>
               <hr/>

               <a href="javascript:;" class="btn btn-sm btn-success btn_tambah" id="btn_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah Barang</a>
               <br/> <br/>
               <div class="table-responsive">
               <table class="table table-bordered table-hover table-striped table-barang" id="table_barang">
                <thead>
                  <tr>
                    <th width="3%">No.</th>
                    <th>Nama Barang</th>
                    <!-- <th>Harga</th> -->
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <!-- <th>Total</th> -->
                    <th></th>
                  </tr>
                </thead>
                <tbody>

                </tbody>                
              </table>
            </div>

            </div>


        
        <div class=" col-md-6 col-sm-12 m-l-5 hide">
          <div class="table-responsive">
           <table class="table table-bordered table-form">
            <tbody>
              <tr class="active">
                <td>Total Pembelian</td>
                <td class="text-right">
                </td>
              </tr>
             <tr>
                <td width="30%">Sub Total</td>
                <td><input type="hidden" name="td_total" value="{{($data['data']['td_subtotal']== '') ? '0':$data['data']['td_subtotal']}}" readonly="">
                    <p class="help-block pull-right" id="td_total">Rp 0</p>
                </td>
              </tr>
              <tr>
                <td>Potongan</td>
                <td><input type="text" name="td_diskon" class="form-control number-only text-right" value="{{($data['data']['td_potongan']== '') ? '0':$data['data']['td_potongan']}}" readonly="">
                    <p class="help-block pull-right" id="help_popup_totaldiskon">Rp 0</p>
                </td>
              </tr> 
              <tr>
                <td>PPN</td>
                  <td><select class="form-control select-search" name="td_pajak">
                      <option value="0" {{($data['data']['pajak'] == 0) ? 'selected':''}}>Tidak</option>
                      <option value="1" {{($data['data']['pajak'] == 1) ? 'selected':''}}>Iya</option>
                      </select>
                      <input type="hidden" name="td_jmlPajak" class="form-control number-only text-right" value="0">
                      <p class="help-block pull-right fmt-nominal" id="help_popup_pajak">Rp 0</p> 
                  </td>
              </tr>
              <tr>
                <td>Ongkos Kirim</td>
                <td><input type="text" step="0.01" min="0" name="td_ongkir" id="td_ongkir" class="form-control text-right number-only" value="{{($data['data']['td_ongkir']== '') ? '0':$data['data']['td_ongkir']}}" readonly="">
                    <p class="help-block pull-right" id="help_popup_ongkir">Rp 0</p>
                </td>
              </tr>
              <tr>
                <td>Pembayaran</td>
                <td><select class="form-control select2" name="carabayar" style="width: 100%;">
                       <?php
                      foreach ($data['carabayar'] as $key => $value) {
                        $selected = ($data['data']['carabayar'] == $key) ? "selected":'';
                        echo "<option value='".$key."' ".$selected."> ".$value." </option>";
                      }?>
                    </select>
                </td>
              </tr>
              <tr>
                <td>Uang Muka</td>
                <td><input type="text" name="td_uangmuka" class="form-control text-right number-only" value="{{$data['data']['uang_muka']}}" readonly="">
                  <p class="help-block pull-right" id="help_popup_uangmuka">{{$data['data']['uang_muka']}}</p>
                </td>
              </tr>
              <tr>
                <td>Tagihan</td>
                <td><p class="help-block pull-right" id="td_netto">Rp 0</p>
                  <input type="hidden" name="td_netto" value="{{$data['data']['td_tagihan']}}">
                </td>
              </tr>
            </tbody>
           </table>

          </div>
        </div>
        
        </div>

        <!-- /.box-body -->
        <div class="box-footer">
          <a href="{{url('suratjalan')}}" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
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
@include('admin.suratjalan.form')
@endsection


@section('js')
<!-- DataTables -->
<script src="{{asset('public/admin/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>


<script type="text/javascript">

  var tb_no = parseInt(1000);
  var table_barang;
  var id_pembelian = '{{$data['data']['id_pembelian']}}';
  var id_penyedia = '{{$data['data']['id_penyedia']}}';
  var nama_penyedia = '{{$data['data']['nama_penyedia']}}';
  

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
      get_edit(id_pembelian);
      get_subtotal();
      netto();
    }

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

    $('[name=id_penyedia]').select2({
            placeholder: "Pilih...",
            //minimumInputLength: 2,
            ajax: {
                url: '{{url('suratjalan_get_penyedia')}}',
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
                          id:item.supplier_id,
                          nama:item.supplier_nama,
                          alamat:item.supplier_alamat,
                          telp:item.supplier_telp,
                          top:item.supplier_tempo,
                          text:item.supplier_nama,
                        });
                      });
                      return{
                        results:results
                      };
                },
                cache: true
            }   
  }); 

      $('[name=popup_barang]').select2({
            placeholder: "--- Pilih ---",
            ajax: {
                url: '{{url('suratjalan_get_barang')}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                        //results: data
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        var text_item = item.barang_nama+" || ("+item.barang_alias+")";
                        if(item.barang_alias === null || item.barang_alias === "" || item.barang_alias === 0){
                          text_item = item.barang_nama;
                        }
                        results.push({
                          id:item.barang_id,
                          satuan_id:item.satuan_id,
                          satuan_nama:item.satuan_satuan,
                          nama:item.barang_nama,
                          harga:item.harga,
                          kode:item.barang_kode,
                          alias:item.barang_alias,
                          text:text_item
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


$("#btn_tambah").click(function(){
    $('[name=popup_id_table]').val('');
    $('[name=popup_barang]').find('option').remove().end();
    $('[name=popup_harga]').val('');
    $('[name=popup_jumlah]').val('');
    $('[name=popup_satuan]').val('').trigger('change');
    $('#help_popup_jumlah').text('');
    $('#modal-form').modal('show');
});

$("[name=td_ongkir]").on('keyup', function(){
  var ongkir = $(this).val();
  $("#help_popup_ongkir").text(accounting.formatMoney(ongkir));
  get_jum();
  netto();
});
$("[name=td_uangmuka]").on('keyup', function(){
  var uangmuka = $(this).val();
  $("#help_popup_uangmuka").text(accounting.formatMoney(uangmuka));
  get_jum();
  netto();
});
$("[name=popup_jumlah]").on('keyup', function(){
  var jumlah = $(this).val();
  $("#help_popup_jumlah").text(format_angka(jumlah));
  get_jum();
});
/*$("[name=popup_harga]").on('keyup', function(){
  var harga = $(this).val();
  var jumlah = $("[name=popup_jumlah]").val();
  $("#help_popup_harga").text(accounting.formatMoney(harga));
  if(jumlah > 0){
  get_jum();
  }
});*/
$("[name=popup_barang]").on('change', function(){
  var d = $("[name=popup_barang]").select2('data')[0];
  $("[name=popup_satuan]").val(d.satuan_id).trigger('change');
});
$("[name=td_pajak]").on('change',function(){
  if($(this).val() == 0){
    $('[name=td_jmlPajak]').val(0);
    $('#help_popup_pajak').text(accounting.formatMoney(0))
  }
  netto();
});

$("[name=td_diskon]").on('keyup',function(){
  var diskon = $(this).val();
  $("#help_popup_totaldiskon").text(accounting.formatMoney(diskon));
  netto();
})

$("[name=id_penyedia]").on('change',function(){
  var d = $("[name=id_penyedia]").select2('data')[0];
  var addDay = parseInt(d.top)||30;
  var _date  = new Date();
      _date.setDate(_date.getDate()+addDay);

  var dd = _date.getDate();
  var mm = _date.getMonth()+1;
  var yyyy = _date.getFullYear();
    if(mm < 10) mm = '0'+mm;
    if(dd < 10) dd = '0'+dd;

  var set = [dd, mm, yyyy];
  $("[name=tanggal_tempo]").val(set.join("-"));
});

$("#btn_popup_simpan").click(function(){
  var d = $("[name=popup_barang]").select2('data')[0];
  var id_data   = $("[name=popup_id_table]").val();
  var id_barang = $("[name=popup_barang]").val();
  if(id_data == ""){
    if(d.alias === null || d.alias === "" || d.alias === 0){
      var nama_barang = d.nama;
    }else{
      var nama_barang = d.nama+" || ("+d.alias+")";
    }
  }else{
      var nama_barang = $("[name=popup_barang] :selected").attr('nama');
  }

  
  var id_satuan = $("[name=popup_satuan]").val();
  var nama_satuan = $("[name=popup_satuan] :selected").attr('nama');
  var jumlah    = $("[name=popup_jumlah]").val();
  /*var harga     = $("[name=popup_harga]").val();
  var total     = parseFloat(harga*jumlah);*/
  var harga     = parseFloat(0);
  var total     = parseFloat(0);

  if(id_barang != '' && id_satuan != '' && jumlah != ''){
  if(id_data == ''){
    table_barang.row.add(['<div><center></center></div>',
          '<div id="list_barang'+tb_no+'">'+nama_barang+'</div>',
          /*'<div id="list_harga'+tb_no+'">'+accounting.formatMoney(harga)+'</div>',*/
          '<div id="list_jumlah'+tb_no+'">'+format_angka(jumlah)+'</div>',
          '<div id="list_satuan'+tb_no+'">'+nama_satuan+'</div>',
          /*'<div id="list_total'+tb_no+'">'+accounting.formatMoney(total)+'</div>',*/
          '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+tb_no+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
          '<input type="hidden" name="tabel_id[]" id="tabel_id'+tb_no+'" value="">'+
          '<input type="hidden" name="tabel_id_tabel[]" id="tabel_id_tabel'+tb_no+'" value="'+tb_no+'">'+
          '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+tb_no+'" value="'+id_barang+'">'+
          '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+tb_no+'" value="'+nama_barang+'">'+
          '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+tb_no+'" value="'+jumlah+'">'+
          '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+tb_no+'" value="'+harga+'">'+
          '<input type="hidden" name="tabel_total[]" id="tabel_total'+tb_no+'" value="'+total+'">'+
          '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+tb_no+'" value="'+id_satuan+'">'+
          '<input type="hidden" name="tabel_namasatuan[]" id="tabel_namasatuan'+tb_no+'" value="'+nama_satuan+'">']).draw(false);
  }else{
        $("#list_barang"+id_data).text(nama_barang);
        /*$("#list_harga"+id_data).text(accounting.formatMoney(harga));*/
        $("#list_jumlah"+id_data).text(format_angka(jumlah));
        $("#list_satuan"+id_data).text(nama_satuan);
        /*$("#list_total"+id_data).text(accounting.formatMoney(total));*/
        console.log(nama_barang+'=barang'+id_barang+"=idbarang");

        $("#tabel_id"+id_data).val(id_data);
        $("#tabel_id_tabel"+id_data).val(id_data);
        $("#tabel_idbarang"+id_data).val(id_barang);
        $("#tabel_namabarang"+id_data).val(nama_barang);
        $("#tabel_harga"+id_data).val(harga);        
        $("#tabel_jumlah"+id_data).val(jumlah);
        $("#tabel_idsatuan"+id_data).val(id_satuan);
        $("#tabel_namasatuan"+id_data).val(nama_satuan);
        $("#tabel_total"+id_data).val(total);
  }
  tb_no++;
  get_subtotal();
  $("#modal-form").modal('hide');
  }

})

function edit_table(id){
  var cek_id      = $("#tabel_id_tabel"+id).val();
    if(cek_id == '' ){
    var id_table    = $("#tabel_id"+id).val();
    }else{
    var id_table    = cek_id;
    }
  var id_barang   = $("#tabel_idbarang"+id).val();
  var nama_barang = $("#tabel_namabarang"+id).val();
  var harga       = $("#tabel_harga"+id).val();
  var jumlah      = $("#tabel_jumlah"+id).val();
  var id_satuan   = $("#tabel_idsatuan"+id).val();
  var nama_satuan = $("#tabel_namasatuan"+id).val();
  var total       = $("#tabel_total"+id).val();


  $("[name=popup_id_table]").val(id_table);
  
  $("[name=popup_barang]").html('<option value="'+id_barang+'" nama="'+nama_barang+'">'+nama_barang+'</option>');    
  $("[name=popup_namabarang]").val(nama_barang);
  /*$("[name=popup_harga]").val(harga);*/
  $("[name=popup_jumlah]").val(jumlah);
  /*$("[name=popup_satuan]").html('<option value="'+id_satuan+'">'+nama_satuan+'</option>');*/
  $("[name=popup_satuan]").val(id_satuan).trigger('change');
  /*$("[name=popup_total]").val(total);*/

  /*$("#help_popup_harga").text(accounting.formatMoney(harga))*/;
  $("#help_popup_jumlah").text(format_angka(jumlah));
  /*$("#help_popup_total").text(accounting.formatMoney(total));*/
  get_jum();
  $("#modal-form").modal('show');
}

function get_edit(id){
  $.ajax({
      url: "{{ url('suratjalan_get_edit')}} ",
      type: 'post',
      data: {id : id,},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(respon){
        if(respon.length > 0){
          for(i in respon){
            table_barang.row.add(['<div><center></center></div>',
              '<div id="list_barang'+respon[i].id+'">'+respon[i].nama_barang+'</div>',
              /*'<div id="list_harga'+respon[i].id+'">'+accounting.formatMoney(respon[i].harga)+'</div>',*/
              '<div id="list_jumlah'+respon[i].id+'">'+format_angka(respon[i].jumlah)+'</div>',
              '<div id="list_satuan'+respon[i].id+'">'+respon[i].nama_satuan2+'</div>',
              /*'<div id="list_total'+respon[i].id+'">'+accounting.formatMoney(respon[i].total)+'</div>',*/
              '<div><button type="button" class="btn btn-xs btn-warning" onclick="edit_table('+respon[i].id+')"><i class="fa fa-edit"></i> </button><button class="btn btn-xs btn-danger" type="button"  onclick="hapus($(this))""><i class="fa fa-trash"></i></button></div>'+
              '<input type="hidden" name="tabel_id[]" id="tabel_id'+respon[i].id+'" value="'+respon[i].id+'">'+
              '<input type="hidden" name="tabel_id_tabel[]" id="tabel_id_tabel'+respon[i].id+'" value="'+respon[i].id+'">'+
              '<input type="hidden" name="tabel_idlog_stok[]" id="tabel_idlog_stok'+respon[i].id+'" value="'+respon[i].id_log_stok+'">'+
              '<input type="hidden" name="tabel_idbarang[]" id="tabel_idbarang'+respon[i].id+'" value="'+respon[i].id_barang+'">'+
              '<input type="hidden" name="tabel_namabarang[]" id="tabel_namabarang'+respon[i].id+'" value="'+respon[i].nama_barang+'">'+
              '<input type="hidden" name="tabel_jumlah[]" id="tabel_jumlah'+respon[i].id+'" value="'+respon[i].jumlah+'">'+
              '<input type="hidden" name="tabel_harga[]" id="tabel_harga'+respon[i].id+'" value="'+parseFloat(0)+'">'+
              '<input type="hidden" name="tabel_total[]" id="tabel_total'+respon[i].id+'" value="'+parseFloat(0)+'">'+
              '<input type="hidden" name="tabel_idsatuan[]" id="tabel_idsatuan'+respon[i].id+'" value="'+respon[i].id_satuan2+'">'+
              '<input type="hidden" name="tabel_namasatuan[]" id="tabel_namasatuan'+respon[i].id+'" value="'+respon[i].nama_satuan2+'">'
              ]).draw(false);
          }
          get_jum();
          get_subtotal();
          netto();
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

function get_jum(){
  var jumlah  = parseFloat($("[name=popup_jumlah]").val())  || 0;
  var harga   = parseFloat($("[name=popup_harga]").val())   || 0;
  var total   = parseFloat(jumlah*harga);
  $("[name=popup_total]").val(total);
  $("#help_popup_total").text(accounting.formatMoney(total));

}

function get_subtotal(this_){
  var jum = parseFloat(0);
    $('input[id^="tabel_total"]').each(function() {
              // alert($(this).val());
              var val = ($(this).val());
              jum += parseFloat(val);
              
            });
    $("#td_total").text(accounting.formatMoney(jum));
    $("[name=td_total]").val(jum);
    netto();
}


function netto(){
  var total   = parseFloat($("[name=td_total]").val()) || 0;
  var diskon  = parseFloat($("[name=td_diskon]").val()) || 0;
  var ongkir  = parseFloat($("[name=td_ongkir]").val()) || 0;
  var netto   = parseFloat(0);
  var ppn     = parseFloat(0);
  var uangmuka= parseFloat($("[name=td_uangmuka]").val()) || 0;
  
  if($('select[name=td_pajak]').val()==1){
      ppn = parseFloat((total-diskon)*(10/100));
    }

  netto = total-(diskon-ppn+ongkir)-uangmuka;
    $("[name=td_diskon]").val(diskon);
    $("#help_popup_totaldiskon").text(accounting.formatMoney(diskon));

    $("[name=td_jmlpajak]").val(ppn);
    $("#help_popup_pajak").text(accounting.formatMoney(ppn));

    $("[name=td_uangmuka]").val(uangmuka);
    $("#help_popup_uangmuka").text(accounting.formatMoney(uangmuka));

    $("[name=td_ongkir]").val(ongkir);
    $("#help_popup_ongkir").text(accounting.formatMoney(ongkir));

    $("[name=td_netto]").val(netto);
    $("#td_netto").text(accounting.formatMoney(netto));

};
</script>

@endsection

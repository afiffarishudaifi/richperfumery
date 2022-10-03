<?php $hal = "pengiriman"; ?>
@extends('layouts.admin.master')
@section('title', 'PENGIRIMAN')

@section('css')
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

/* table, th {
  border: 0.1px solid black !important;
} */

/* td {
  border: 0.1px solid black !important;
} */
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
      <a href="{{url('pengiriman')}}" style="margin-left: 10px;margin-top: 10px;" class="btn btn-xs btn-primary">Kembali</a>
        <div class="box-header">
          <h3 class="box-title">Detail Barang</h3>
        </div>

        <a style="margin-bottom:20px;margin-left:10px;display:none;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</a>
        <a style="margin-bottom:20px;margin-left:10px;display:none;" id="btn_kirim_se" class="card-body-title btn btn-success  btn_kirim_se"><i class="fa  fa-check"></i> Kirim Semua Barang</a>
       
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table id="datatable1" class="table table-bordered table-striped" width="100%">
            <thead>
              <?php  if($gudang['user_group']==1||$gudang['user_group']==6){?>
              <tr>
                <th style="width:10%">No #</th>
                <th style="width:16%">Nama Barang</th>
                <th style="width:16%">Satuan</th>
                {{-- <th style="width:16%">Diterima</th> --}}
                <th style="width:13%">Jumlah</th>
                <th style="width:13%">Harga</th>
                <th style="width:10%">Status</th>
                <th style="width:10%">Action</th>
              </tr>
              <?php  }else{ ?>
                <tr>
                <th style="width:10%">No #</th>
                <th style="width:16%">Nama Barang</th>
                <th style="width:16%">Satuan</th>
                <th style="width:16%">Jumlah</th>
                <th style="width:10%">Status</th>
                <th style="width:10%">Action</th>
              </tr>

              <?php } ?>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
    </div>
  </section>
  @include('admin.pengiriman.formdetail')
  @include('admin.pengiriman.formterima')
  @include('admin.pengiriman.formdetail_detail')
@endsection

@section('js')
<script type="text/javascript">
$("[name=jumlah]").on('keyup',function(){
    var jml = $(this).val();
    $("#help_detail_jumlah").text(format_angka(jml));
});
$("[name=harga]").on('keyup',function(){
    var jml = $(this).val();
    $("#help_detail_harga").text(accounting.formatMoney(jml));
});

$(function () {
    var id = "{{$gudang['id']}}";
    var gudang = "{{$gudang['gudang']}}";
    var s_group_aktif = "{{$gudang['tombol_create']}}";
    var s_group_user  = "{{$gudang['user_group']}}";
    var s_status      = "{{$gudang['status']}}";

    if(s_group_aktif == 2){
      if(s_group_user == 1){
        $(".btn_tambah").show();
        if(s_status == 0){
          $(".btn_kirim_se").show();
        }
      }else{
        $(".btn_tambah").show();
        if(s_status == 0){
          $(".btn_kirim_se").show();
        }
      }
    }
    $(document).on('click','#btn_semua',function(){
      if(confirm("Apakah yakin data menerima semua barang?")){
        $.ajax({
          url : "detailpengiriman_terima",
          type : "GET",
          data : {'id' : id, 'gudang': gudang, '_token' : $('meta[name=csrf-token]').attr('content')},
          // data : {'id' : id, '_token' : $('meta[name=csrf-token]').attr('content')},
          success : function(data){
            // table.ajax.reload();
             location.reload();
          },
          error : function(){
            alert("Tidak dapat mengirim barang!");
            //  location.reload();
          }
        });
      }
    });
    $('#status').on('change',function(){
      var a = $(this).val();
      if(a == 1){
        $('#diterima').attr('readonly',false);
        $('#dikembalikan').attr('readonly',true);
      }else if(a==2){
        $('#diterima').attr('readonly',true);
        $('#dikembalikan').attr('readonly',false);

      }
      
    });
    
    var gudang = "{{$gudang['gudang']}}";
    var id_pengiriman = "{{$gudang['id']}}";
    $('.barang_select2').select2({
            placeholder: "Pilih...",
            // minimumInputLength: 2,
            ajax: {
                url: 'select2barang',
                dataType: 'json',
                data: function (params) {
                  // console.log();
                    return {
                        q: $.trim(params.term),
                        gudang:gudang
                    };
                },
                processResults: function (data) {
                      var results = [];
                      $.each(data, function(index,item){
                        results.push({
                          id:item.barang_id+'.'+item.satuan_nama,
                          id_log_stok:item.log_stok_id,
                          satuan:item.satuan_satuan,
                          satuan_nama:item.satuan_nama,
                          id_satuan:item.id_satuan,
                          nama:item.barang_nama,
                          text:item.barang_kode+' || '+item.barang_nama+ ' || '+item.satuan_nama,
                          jumlah_keluar:item.jumlah_keluar,
                          jumlah_masuk:item.jumlah_masuk
                        });
                      });
                      return{
                        results:results
                      };
                        
                },
                cache: true
            }
    
    });

  //modal : ketika barang dipilih
  $('#barang').on('change',function(e){
    var nilai = $("#barang").select2('data')[0];
    var id_satuan = nilai.id_satuan;
    var satuan_nama = nilai.satuan_nama;
    var option = '<option value="'+id_satuan+'">'+satuan_nama+'</option>';

    $('#stok').show();

    $('#nama').val(nilai.nama);
    // $('#id_log_stok').val(nilai.id_log_stok);
    $('#satuan').val(nilai.satuan_nama);
    $('#id_satuan').val(nilai.id_satuan);
    $('#kode').val(nilai.text);
    $('#tersedia').val(nilai.jumlah_masuk - nilai.jumlah_keluar);
  });

  table = $('#datatable1').DataTable({
      serverSide : true,
			processing : true,
      "ajax" : {
        "url" : "{{url('detailpengiriman/show')}}",
        data : (data) => {
          data.id_pengiriman = id
        }
      },
      columns:arr_colomns(`{{$gudang['user_group']}}`),
      columnDefs: [{targets: 3,className: 'text-right'},{targets: 4,className: 'text-right'}]
  });

  $('.btn_tambah').on('click',function(e){
    // alert('as');
    $('.form_connectio1n')[0].reset();
    $('.d').html('<h4 class="modal-title">Tambah Barang</h4>');
    $('#gudang').val(gudang);
    $('#barang').val('').trigger('change.select2');
    var id = "{{$gudang['id']}}";
    $('#id').val(id);
    $("#id_detail").val('');
    $('#id_log_stok').val('');
    $('#id_log_stok_penerimaan').val('');
    $('#help_detail_harga').text('');
    $('#help_detail_jumlah').text('');
    $('#stok').show();
    
    $('#crud').val('tambah');
    $('#modal-form').modal('show');
  });
  
  $(document).on("click","#btn_edit",function() {
    $('.form_connectio1n')[0].reset();
    $('.d').html('<h4 class="modal-title">Edit Program</h4>');
    $('#crud').val('edit');
    var barang = $(this).data('id_barang');
    var id = $(this).data('id_pengiriman');
    var id_satuan = $(this).data('id_satuan');
    var satuan = $(this).data('satuan');
    var id_detail = $(this).data('id_detail_pengiriman');
    var nama1 = $(this).data('nama');
    var Jumlah = $(this).data('jumlah');
    var harga = $(this).data('harga');
    var kode = $(this).data('kode');
    var isi = '<option value="'+barang+'.'+satuan+'">'+kode+' || '+nama1+' || '+satuan+'</option>';
    var keterangan = $(this).data('keterangan');
    var id_log_stok = $(this).data('id_log_stok');
    var id_log_stok_penerimaan = $(this).data('id_log_stok_penerimaan');

    var nama_barang = kode+" || "+nama1;
    $('#barang').html(isi);
    $('#id').val(id);
    $("#id_detail").val(id_detail);
    $('#jumlah').val(Jumlah);
    $('#harga').val(harga);
    $('#satuan').val(satuan);
    $("#id_satuan").val(id_satuan)
    $('#nama').val(nama1);
    $('#keterangan').val(keterangan);
    $('#id_log_stok').val(id_log_stok);
    $('#id_log_stok_penerimaan').val(id_log_stok_penerimaan);

    $("#help_detail_jumlah").text(format_angka(Jumlah));
    $("#help_detail_harga").text(accounting.formatMoney(harga));
    $("#stok").hide();

    $('#modal-form').modal('show');
  });

  $(document).on("click","#btn_detail",function() {
    $('.d').html('<h4 class="modal-title">Detail Barang Pengiriman</h4>');
    $('#crud').val('detail');
    var barang  = $(this).data('id_barang');
    var id      = $(this).data('id_detail_pengiriman');
    var satuan  = $(this).data('satuan');
    var nama1   = $(this).data('nama');
    var Jumlah  = $(this).data('jumlah');
    var harga   = $(this).data('harga');
    var kode    = $(this).data('kode');
    var isi     = '<option value="'+barang+'">'+kode+'</option>';
    var keterangan = $(this).data('keterangan');
    var id_log_stok = $(this).data('id_log_stok');
    var id_log_stok_penerimaan = $(this).data('id_log_stok_penerimaan');

    $('#detail_barang').val(kode+" || "+nama1);   
    $('#detail_id').val(id);
    $('#detail_jumlah').val(Jumlah);
    $('#detail_harga').val(harga);
    $('#detail_satuan').val(satuan);
    $('#detail_nama').val(nama1);
    $('#detail_keterangan').val(keterangan);
    $('#detail_idlogstok').val(id_log_stok);
    $('#detail_idlogstokpenerimaan').val(id_log_stok_penerimaan);

    $("#detail_help_detail_jumlah").text(format_angka(Jumlah));
    $("#detail_help_detail_harga").text(accounting.formatMoney(harga));
    $("#detail_tersedia").removeClass('hide');

    $('#modal-form_detail').modal('show');
  });
   
  $(document).on("click","#btn_hapus",function() {
    var id = $(this).data('id');
    swal({
          title: "Hapus data?",
          text: "Anda akan menghapus data ini",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-warning",
          confirmButtonText: 'Ya',
          cancelButtonText: "Tidak",
      },function(){      
          $.ajax({
              // url: "{{ url('detailpengiriman_hapus')}} ",
              url: "{{ url('detailpengiriman/hapus_detail')}} ",
              type: 'post',
              data: {id : id},
              headers : {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(respon){
                if(respon.status == 1){
                  swal("", "Data berhasil dihapus", "success");
                }else{
                  swal("", "Data gagal dihapus", "error");
                }
                table.ajax.reload();
            }
          })
      })
  });

  $(document).on('click','#btn_kirim_se',function(){
    if(confirm("Apakah yakin data akan mengirim semua barang? notice= ketika barang sudah dikirim tidak bisa diedit")){
      $.ajax({
        url : "{{url('detailpengiriman/kirimbarang_detail')}}",
        type : "POST",
        data : {'id' : id, '_token' : $('meta[name=csrf-token]').attr('content')},
        success : function(data){
          if(data.status){
            swal({
                title:"Data Sukses Terkirim",
                text:"Data berhasil dikirimkan",
                type:"success",
                confirmButtonText:"Okay"
            },function(){
                table.ajax.reload();
                $(".btn_kirim_se").hide();
                $('#modal-form').modal('hide');
            });
          }else{
            swal({
              title:"Data Gagal Terkirim",
              text:"Data gagal dikirimkan",
              type:"error",
              confirmButtonText:"Okay"
            },function(){
              table.ajax.reload();
              $('#modal-form').modal('hide');
            });
          }
        },
        error : function(){
          alert("Tidak dapat mengirim barang!");
        }
      });
    }
  });

  $(document).on("click","#btn_a",function() {
    var id_detail_pengiriman = $(this).data('id_detail_pengiriman');
    $.ajax({
      url     : "{{url('detailpengiriman_kirimbarang_peritem')}}",
      data    : {id : id_detail_pengiriman},
      type    : 'POST',
      headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success : function(data){
          if(data.status == 1){
              swal({
                  title:"Data Sukses Terkirim",
                  text:"Data berhasil dikirimkan",
                  type:"success",
                  timer: 1000
              },function(){
                  table.ajax.reload();
              });
          }else{
              swal({
                  title:"Data Gagal Terkirim",
                  text:"Data gagal dikirimkan",
                  type:"error",
                  confirmButtonText:"Okay",
                  timer: 1000
              },function(){
                  table.ajax.reload();
              });
          }
      },
      error : function(){
          swal({
              title:"Data Gagal Terkirim",
              text:"Data gagal dikirimkan",
              type:"error",
              confirmButtonText:"Okay",
              timer: 1000
          },function(){
              table.ajax.reload();
          });
      }
    })
  });

  $('#modal-form form').validator().on('submit', function(e){
    if(!e.isDefaultPrevented()){
      var id = $('#id').val();
      $.ajax({
        url : "{{ url('detailpengiriman/simpan_detail') }}",
        type : "POST",
        data : $('#modal-form form').serialize(),
        headers : {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function(data){
          if(data.status == 1){
            swal({
                title:"Data Sukses Simpan",
                text:"Data berhasil disimpan",
                type:"success",
                confirmButtonText:"Okay"
            },function(){
                table.ajax.reload();
                $('#modal-form').modal('hide');
            });
          }else if(data.status == 2){
            swal({
                title:"Data Sukses Update",
                text:"Data berhasil terupdate",
                type:"success",
                confirmButtonText:"Okay"
            },function(){
                table.ajax.reload();
                $('#modal-form').modal('hide');
            });
          }else{
            swal({
                title:"Data Gagal Simpan",
                text:"Data gagal disimpan",
                type:"error",
                confirmButtonText:"Okay"
            },function(){
                table.ajax.reload();
                $('#modal-form').modal('hide');
            });
          }
        },
        error : function(){
            swal({
                title:"Data Gagal Simpan",
                text:"Data gagal disimpan",
                type:"success",
                confirmButtonText:"Okay"
            },function(){
                table.ajax.reload();
                $('#modal-form').modal('hide');
            });
        }
      });
      return false;
    }
  });

  $('#modal-diterima form').validator().on('submit', function(e){
    if(!e.isDefaultPrevented()){
      // var id = $('#id').val();

      $.ajax({
        url : "{{ route('simpanpersetujuan') }}",
        type : "POST",
        data : $('#modal-diterima form').serialize(),
        success : function(data){
          console.log(data);
          // $('#modal-diterima').modal('hide');
          // table.ajax.reload();
          location.reload();
        },
        error : function(){
          alert("Tidak dapat menyimpan data!");
        }
      });
      return false;
    }
  });

  function arr_colomns($kondisi){
    if($kondisi == 1 || $kondisi == 6){
      var arr_columns = [
        {data:'id_detail_pengiriman', name:'pd.id',orderable:false, searchable:false, render : function(data, type, full, meta){
			  	return meta.row+1;
			  }},
        {data:'nama_barang',name:'tb.barang_nama'},
        {data:'nama_satuan',name:'ts.satuan_satuan'},
        {data:'jumlah',name:'pd.jumlah'},
        {data:'harga',name:'pd.harga'},
        {data:'status',name:'pd.status',orderable:false},
        {data:'action',name:'action',orderable:false, searchable:false}
      ];
    }else{
      var arr_columns = [
        {data:'id_detail_pengiriman', name:'pd.id',orderable:false, searchable:false, render : function(data, type, full, meta){
			  	return meta.row+1;
			  }},
        {data:'nama_barang',name:'tb.barang_nama'},
        {data:'nama_satuan',name:'ts.satuan_satuan'},
        {data:'jumlah',name:'pd.jumlah'},
        {data:'status',name:'pd.status',orderable:false},
        {data:'action',name:'action',orderlabel:false,searchabel:false}
      ];
    }
    return arr_columns;
  }

});
   



</script>
@endsection
<?php $hal = "produk"; ?>
@extends('layouts.admin.master')
@section('title', 'Produk')

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

<div class="content">
	<div class="panel panel-flat">
		<a href="{{url('produk')}}" class="btn btn-xs btn-primary" style="float:left">Kembali</a><br>
		<div class="panel-heading">
			<h5 class="panel-title">Form Input Produk</h5>
			<div class="heading-elements">
				<ul class="icons-list">
		      		{{-- <li><a data-action="collapse"></a></li> --}}
		      	</ul>
	       	</div>
	    </div>
	    <div class="panel-body">
	    	<form class="form-horizontal" method="post" action="{{ url('produk_simpan_detail') }}" autocomplete="off" >
	    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	    	<input type="hidden" name="id" id="id_a" value="{{$data['m_produk'][0]->id}}">
	    	<input type="hidden" name="crud" value="edit">
        	
        	<div class="col-sm-6">
        		<div class="form-group">
        			<label class="control-label col-sm-4">Kode Produk</label>
        			<div class="col-sm-8">
					<input type="text" name="kode" class="form-control" value="{{$data['m_produk'][0]->kode_produk}}" required>
        			</div>
        		</div>
        		<div class="form-group">
        			<label class="control-label col-sm-4">Nama Produk</label>
        			<div class="col-sm-8">
        				<input type="text" name="nama" class="form-control" value="{{$data['m_produk'][0]->nama}}" required>
        			</div>
        		</div>
				<div class="form-group">
        			<label class="control-label col-sm-4">Tipe</label>
        			<div class="col-sm-8">
        				<select name="tipe" id="tipe" class="form-control">
							<option value="">-- Pilih Tipe --</option>
							<option value="1">30 ml</option>
							<option value="2">55 ml</option>
							<option value="3">100 ml</option>
							<option value="">Lainnya</option>
						</select>
        			</div>
        		</div>
        		<div class="form-group">
        			<label class="control-label col-sm-4">Harga</label>
        			<div class="col-sm-8">
        				<input type="text" name="jumlah_komposisi" value="{{$data['m_produk'][0]->harga}}" class="form-control harga number-only" value="" required>
        			</div>
				</div>
				<p class="help-block pull-right" id="help_popup_ongkir">Rp 0</p>
        		
        		
        		
        	</div>

        <div class="col-sm-12 form-group">
        	<h5 class="panel-title">Detail Barang</h5>
        	<br><a href="#" class="btn btn-success" style="margin-bottom: 1%;" id="btn_tambah_item">Tambah</a>
        	{{-- <div class="col-sm-12 ">  --}}
				<div class="table-responsive">
					<table class="table table-stripped" id="table_barang">
					<thead>
						<tr>
							<th>No</th>
							<th>Kode Barang</th>
							<th>Nama Barang</th>
							<th>Satuan</th>
							<th>Jumlah</th>
							<th class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
					</table>
				</div>
        	
	    	<div class="form-group pull-right">
             	<button class="btn btn-primary" style="margin-right: 30px;" type="submit">Simpan</button>
            </div>
		</div>
	    </form>
	</div>
</div>
</div>


<!---Modal--->
 @include('admin.produk.formmodal')


@endsection
@section('js')
  <script type="text/javascript">
 	var table_barang;
	var tb_no = parseInt(10000);
	
  	$("#btn_tambah_item").click(function(){
        $('.form_connectio1n')[0].reset();
		$('.d').html('<h4 class="modal-title">Tambah Barang</h4>');
        $('#barang').val('').trigger('change.select2');
		$("#kode_a").val('');
		$("#kode").val('');
		// $("[name=popup_barang]").val(null).trigger('change');
		// $("[name=popup_jumlah]").val('');
		// $("#div_konversi").addClass('hide');

		$("#modal-form").modal('show');
	});
    $(function () {
	 $('#crud').val('tambah');
	 
     $('.barang_select2').select2({
            placeholder: "Pilih...",
            // minimumInputLength: 2,
            allowClear: true,
            ajax: {
                url: 'select2barang2',
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
						  satuan:item.satuan_satuan,
                          text:item.barang_kode+' || '+item.barang_nama
                        });
                      });
                      return{
                        results:results
                      };
                        
                },
                cache: true
            }
    
  });
  $('.harga').on('keyup',function(e){
  var harga = $(this).val();
  $("#help_popup_ongkir").text(accounting.formatMoney(harga));
  });
  $('#barang').on('change',function(e){
        // var satuan = $('[name=barang] :selected').attr('satuan');
        var nilai = $("#barang").select2('data')[0];
        $('#nama').val(nilai.nama);
        $('#id_barang').val(nilai.id);
        $('#satuan').val(nilai.satuan);
        $('#kode').val(nilai.text);
        // console.log(nilai);
      });
      
	
	$(document).ready(function(){
		$('#tipe').val("{{ $data['m_produk'][0]->id_type_ukuran }}").trigger('change');
		var h = $('.harga').val();
	 $("#help_popup_ongkir").text(accounting.formatMoney(h));
		var id_a = $('#id_a').val();
		table_barang = $("#table_barang").DataTable({
			"paging": false,
			"responsive": true
		});
		table_barang.on( 'order.dt search.dt', function () {
            table_barang.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
		get_edit(id_a);
		// 
		// get_satuan();
	});
	//edit detail barang 
	// var m_detail_produk = "{{ $data['m_detail_produk']}}" ; 
	// console.log(m_detail_produk);
	//  $.each( m_detail_produk, function( key, value ) {
	// 	var a = key+1;
	//  });
      $("#btn_popup_simpan").click(function(){
		var id_barang = $("#barang").val();
		var nama = $("#nama").val();
		var kode = $("#kode").val();
		var satuan = $("#satuan").val();
		var kode_a = $("#kode_a").val();
		var jumlah = $("#jumlah").val();
		// console.log(id_barang);
		if(kode_a==''){
		table_barang.row.add(['<div><center><input type="hidden" name="tabel_id[]" id="tabel_id'+tb_no+'" value=""></center></div>',
					'<input type="text" readonly style="border:0" name="list_kode[]" id="list_kode'+tb_no+'" value="'+kode+'" placeloader="'+kode+'">',
					'<input type="text" readonly style="border:0" readonly style="border:0" name="list_nama[]" id="list_nama'+tb_no+'" value="'+nama+'" placeloader="'+kode+'">',
					'<input type="text" readonly style="border:0" readonly style="border:0" name="list_satuan[]" id="list_satuan'+tb_no+'" value="'+satuan+'" placeloader="'+satuan+'">',
					'<input type="text" readonly style="border:0" name="list_jumlah[]" id="list_jumlah'+tb_no+'" value="'+jumlah+'" placeloader="'+jumlah+'">',
					'<div><button type="button" data-kode="'+kode+'" data-nama="'+nama+'" data-jumlah="'+jumlah+'" class="btn btn-xs btn-warning" onclick="edit_table('+tb_no+')"><i class="fa fa-pencil"></i> </button> <button class="btn btn-xs btn-danger" type="button" onclick="hapus($(this))"><i class="fa fa-eraser"></i></button></div><input type="hidden" name="list_kode_id[]" id="list_kode_id'+tb_no+'" value="'+id_barang+'">'
					
					]).draw(false);
		}else{
			// console.log(kode_a);
			var kode1 = $("#barang").val();
			// console.log(kode1);
			//  table_barang.ajax.reload();
			$("#list_kode"+kode_a+"").val(kode1);
			$("#list_nama"+kode_a+"").val(nama);
			$("#list_jumlah"+kode_a+"").val(jumlah);
			// alert($("#list_jumlah"+kode_a+"").val());
		}
		
		
			$("#modal-form").modal('hide');
			tb_no++;
	});
	
	
    });

	function get_edit(id){
	$.ajax({
      url: "{{ url('produk_getedit')}} ",
      type: 'post',
      data: {id : id,},
      headers : {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
	  success: function(respon){
	   if(respon.length > 0){
		for(i in respon){
			table_barang.row.add([
			'<div><center><input type="hidden" name="tabel_id[]" id="tabel_id'+tb_no+'" value=""></center></div>',
					'<input type="text" readonly style="border:0" name="list_kode[]" id="list_kode'+respon[i].id+'" value="'+respon[i].barang_kode+'" placeloader="">',
					'<input type="text" readonly style="border:0" readonly style="border:0" name="list_nama[]" id="list_nama'+respon[i].id+'" value="'+respon[i].barang_nama+'" placeloader="'+respon[i].barang_nama+'">','<input type="text" readonly style="border:0" readonly style="border:0" name="list_satuan[]" id="list_satuan'+respon[i].id+'" value="'+respon[i].satuan+'" placeloader="'+respon[i].satuan+'">',
					'<input type="text" readonly style="border:0" name="list_jumlah[]" id="list_jumlah'+respon[i].id+'" value="'+respon[i].jumlah+'" placeloader="'+respon[i].jumlah+'">',
					'<div><button type="button" data-kode="'+respon[i].barang_kode+'" data-nama="'+respon[i].barang_nama+'" data-jumlah="'+respon[i].jumlah+'" class="btn btn-xs btn-warning" onclick="edit_table('+respon[i].id+')"><i class="fa fa-pencil"></i> </button> <button class="btn btn-xs btn-danger" type="button" onclick="hapus($(this))"><i class="fa fa-eraser"></i></button></div><input type="hidden" name="list_kode_id[]" id="list_kode_id'+respon[i].id+'" value="'+respon[i].id_barang+'">'
			]).draw(false);
		}
		// get_subtotal();
	   }
	  }
	});
	}
	function hapus(d){
	table_barang.row(d.parents('tr')).remove().draw();
	
	}
	function edit_table(id){
		$('.d').html('<h4 class="modal-title">Edit Barang</h4>');
		// var kode = $(this).data('kode');
		var tabel_id = $("#tabel_id"+id+"").val();
		var id_barang = $("#list_kode"+id+"").val();
		var nama = $("#list_nama"+id+"").val();
		var satuan = $("#list_satuan"+id+"").val();
		var jumlah = $("#list_jumlah"+id+"").val();
		// var jumlah_konv = $("#list_jumlah"+id+"").val();
		console.log(id_barang);
		var op = '<option value='+id_barang+'>'+id_barang+'</option>'
		$("#kode_a").val(id);
		$("#barang").html(op);
		$("#nama").val(nama);
		$("#satuan").val(satuan);
		$("#jumlah").val(jumlah);
		$("#modal-form").modal();
	}
  </script>
@endsection

<?php $hal = "promo"; ?>
@extends('layouts.admin.master')
@section('title', 'Promo')

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
          <div class="box-header">
            <h3 class="box-title">Promo</h3>
          </div>
          {{-- <button id="btn_tambah"  style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn btn-primary btn_tambah"><i class="fa  fa-plus-square-o"></i> Tambah</button> --}}
          <div class="box-body">
              <div class="table-responsive">
                   <table id="datatable1" class="table table-bordered table-striped" width="100%">
              <thead>
                <tr>
                  <th style="width:10%">No #</th>
                  <th style="width:16%">Tipe Ukuran </th>
                  <th style="width:10%">Harga</th>
                  <th style="width:10%">Action</th>
                </tr>
              </thead>
              <tbody>
                  <tr>
                    
                  </tr>
              </tbody>
            </table>
              </div>
           
          </div>
          <!-- /.box-body -->
        </div>
      </div>
      </div>
    </section>

    <div class="modal fade" id="modal_tambah_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="modal_tambah_dataLabel">Promo</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <form class="form-horizontal" id="form_tambah">
                  @csrf
                      <input type="hidden" name="id">
                      <div class="card-body">
                          <div class="form-group row">
                              <label class="col-sm-4 col-form-label  text-secondary">Tipe Ukuran</label>
                              <div class="col-sm-8">
                                  <input type="text" class="form-control" name="id_type_ukuran" placeholder="Tipe Ukuran">
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-sm-4 col-form-label  text-secondary">Harga</label>
                              <div class="col-sm-8">
                                  <input type="text" class="form-control" name="harga" placeholder="Harga">
                              </div>
                          </div>
                      </div>
              </div>
              <div class="modal-footer">
                  <button type="button" id="btn_close" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" id="btn_simpan" class="btn btn-primary">Save</button>
              </div>
                  </form>
          </div>
      </div>
  </div>
@endsection
@section('js')
<script type="text/javascript">
  $(document).ready(function () {
    var tb = $('#datatable1').DataTable({
      processing: true,
      serverSide: true,
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      ajax: "{{ url('promo') }}",
      columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex'},
          {data: 'id_type_ukuran', name: 'id_type_ukuran'},
          {data: 'harga', name: 'harga'},
          {data: 'action', name: 'action', orderable: true, searchable: true
          },
      ],
      // columnDefs: [
      //     { className: 'text-right', targets: [] },
      //     { className: 'text-center', targets: [4] },
      // ],
    });
    $("#btn_tambah").click(function(){
        $("#modal_tambah_data").modal("show");
  });
  $('body').on('click', '#btn_edit', function () {
    var id = $(this).data('id');
    $.get("{{ url('promo_edit') }}"+'/'+id, function (data) {
        $("#modal_tambah_data").modal("show");
        $('[name=id]').val(data.id);
        $('[name=id_type_ukuran]').val(data.id_type_ukuran);
        $('[name=harga]').val(data.harga);
    })
  });
  $("#btn_simpan").click(function(){
        $.ajax({
            url: "{{ url('promo_simpan')}} ",
            type:'POST',
            data: $("#form_tambah").serialize(),
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        });
        $("#modal_tambah_data").modal("hide");
        tb.ajax.reload();
    });
  })

  
</script>
@endsection
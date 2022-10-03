<div class="modal fade slide-up disable-scroll" id="modal-form_detail" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
          <div class="d">

            <!-- <h4 class="modal-title">Default Modal</h4> -->
          </div>
        </div>
        <form class="form-horizontal form_connectio1n" autocomplete="off" data-toggle="validator" method="post">
          {{ csrf_field() }} {{ method_field('POST') }}
          <div class="modal-body">
          <input type="hidden" id="id" name="id" value="{{$gudang['id']}}">
            <input type="hidden" id="detail_crud" name="crud" value="">
            <input type="hidden" id="detail_kode" name="kode" value="">
            <input type="hidden" id="detail_gudang" name="gudang" value="">
			      <input type="hidden" id="detail_id_satuan" name="id_satuan" value="">
            <input type="hidden" id="detail_tanggal" name="tanggal" value="{{$gudang['tanggal']}}">
            <!-- <input type="hidden" id="detail_id_log_stok" name="id_log_stok" value=""> -->
              <!-- /.form-group -->

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Barang</label>
                <div class="col-sm-8">
                  <input type="text" name="detail_barang" id="detail_barang" class="form-control" readonly="">
                </div>
              </div>
              
              <!-- /.form-group -->
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama</label>
              <div class="col-sm-8">
                <input type="text" name="nama" id="detail_nama" class="form-control" readonly >
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Satuan</label>

              <div class="col-sm-8">
              <input type="text" name="satuan" readonly autocomplete="off" value="" class="form-control " id="detail_satuan"> 
              
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Jumlah</label>

              <div class="col-sm-8">
              <input type="text" id="detail_jumlah" name="jumlah" value="" class="form-control number-only text-right" readonly="">
              <p class="text-right" id="detail_help_detail_jumlah"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Harga</label>
              <div class="col-sm-8">
              <input type="text" id="detail_harga" name="harga" class="form-control number-only text-right" readonly="">
              <p class="text-right" id="detail_help_detail_harga"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Keterangan</label>
              <div class="col-sm-8">
                <textarea class="form-control" rows="3" style="width: 100%;" name="keterangan" id="detail_keterangan" disabled=""></textarea>
              </div>
            </div>
            <!-- /.form-group -->



          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
          </div>
        </div>

      </form>

      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
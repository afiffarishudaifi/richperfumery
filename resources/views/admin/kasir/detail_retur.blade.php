<div class="modal fade" id="modal-form_detail"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Barang</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" autocomplete="off" id="form_retur">
              <input type="hidden" name="popup_detail_id_table">
              <input type="hidden" name="popup_detail_idlog_stok">
              <div class="form-group">
                <label class="col-sm-3 control-label">Kode Retur</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_kode" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Tanggal</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_tanggal" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Dari Pelanggan</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_pelanggan" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Ke Gudang</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_gudang" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Barang</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_barang" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Jumlah</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_jumlah" class="form-control number-only text-right" onkeyup="maksimal($(this))" min="0" disabled="">
                  <p class="help-block text-right" id="help_popup_detail_jumlah"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Satuan</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_satuan" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Keterangan</label>
                <div class="col-sm-9">
                  <textarea class="form-control" rows="3" name="popup_detail_ket" disabled=""></textarea>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
          </div>
      </div>
  </div>
</div>

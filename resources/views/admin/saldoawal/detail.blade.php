<div class="modal fade" id="modal-form_detail"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Barang</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" autocomplete="off" id="form_saldoawal_detail">
              <input type="hidden" name="popup_detail_id_table">
              <input type="hidden" name="popup_detail_namabarang">
              <input type="hidden" name="popup_detail_tanggal2">
              <input type="hidden" name="popup_detail_idlog_stok">
              <div class="form-group">
                <label class="col-sm-3">Tanggal</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_tanggal" class="form-control" value="<?=date('d-m-Y')?>" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Barang</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_barang" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Gudang </label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_gudang" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Jumlah</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_jumlah" class="form-control number-only" disabled="">
                  <p class="help-block text-right" id="help_popup_detail_jumlah"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Satuan</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_satuan" class="form-control" disabled="">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal">Batal</button>
          </div>
      </div>
  </div>
</div>

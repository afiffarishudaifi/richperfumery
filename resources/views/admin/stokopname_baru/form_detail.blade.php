<div class="modal fade" id="modal-form_detail_edit"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Barang</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" autocomplete="off" id="form_stokopname_detail">
              <input type="hidden" name="popup_detail_id_table">
              <input type="hidden" name="popup_detail_idlog_stok">
              <input type="hidden" name="popup_detail_satuan">
              <input type="hidden" name="popup_detail_status">
              <div class="form-group">
                <label class="col-sm-3">Tanggal</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_tanggal" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Gudang</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_gudang" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Barang</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_barang" class="form-control" disabled="">
                </div>
              </div>              
              <div class="form-group hide">
                <label class="col-sm-3">Jumlah Stok</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_stok" class="form-control text-right number-only" disabled="">
                  <p class="help-block text-right" id="help_popup_detail_stok"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Jumlah Fisik</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_fisik" class="form-control text-right number-only" disabled="">
                  <p class="help-block text-right" id="help_popup_detail_fisik"></p>
                </div>
              </div>
              <div class="form-group hide">
                <label class="col-sm-3">Selisih</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_detail_selisih" class="form-control text-right number-only" disabled="">
                  <p class="help-block pull-right" id="help_popup_detail_selisih"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Keterangan</label>
                <div class="col-sm-9">
                  <textarea class="form-control" rows="2" name="popup_detail_keterangan" disabled=""></textarea>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-arrow-left"></span> Batal</button>
          </div>
      </div>
  </div>
</div>

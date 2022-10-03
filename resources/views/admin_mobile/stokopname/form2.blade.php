<div class="modal fade" id="modal-form"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Barang</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" autocomplete="off">
              <input type="hidden" name="popup_id_table">
              <input type="hidden" name="popup_satuan">
              <div class="form-group">
                <label class="col-sm-3">Barang</label>
                <div class="col-sm-9">
                  <select class="form-control" name="popup_barang" style="width: 100%;">

                  </select>
                </div>
              </div>
              <!-- <div class="form-group">
                <label class="col-sm-3">Gudang</label>
                <div class="col-sm-9">
                  <select class="form-control" name="popup_gudang" style="width: 100%;">

                  </select>
                </div>
              </div> -->
              <div class="form-group">
                <label class="col-sm-3">Jumlah Stok</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_jumStok" class="form-control text-right number-only" readonly="">
                  <p class="help-block text-right" id="help_popup_jumStok"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Jumlah Fisik</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_jumFisik" class="form-control text-right number-only">
                  <p class="help-block text-right" id="help_popup_jumFisik"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Selisih</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_selisih" class="form-control text-right number-only" readonly="">
                  <p class="help-block pull-right" id="help_popup_selisih"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Keterangan</label>
                <div class="col-sm-9">
                  <textarea class="form-control" rows="2" name="popup_keterangan"></textarea>
                  <!-- <input type="text" name="popup_keterangan" class="form-control"> -->
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="btn_popup_simpan">Simpan</button>
            <!-- <button type="submit" class="btn btn-success pull-right">Simpan</button> -->
          </div>
      </div>
  </div>
</div>

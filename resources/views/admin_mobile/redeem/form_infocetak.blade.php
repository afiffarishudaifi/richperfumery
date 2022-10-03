<div class="modal fade" id="modal-forminfo"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">KONFIRMASI CETAK!! </h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal form_infocetak" id="form_infocetak" autocomplete="off">
              <input type="hidden" name="popupinfo_iduser" id="popupinfo_iduser">
              <input type="hidden" name="popupinfo_idkasir" id="popupinfo_idkasir">
              <div class="form-group">
                <label class="col-sm-3">Keterangan</label>
                <div class="col-sm-9">
                  <textarea class="form-control" rows="5" name="popupinfo_keterangan"></textarea>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Batal</button>
            <button type="button" class="btn btn-primary" id="btn_popupinfo_simpan"><i class="fa fa-print"></i> Cetak</button>
            <!-- <button type="submit" class="btn btn-success pull-right">Simpan</button> -->
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="modal-form-pelanggan"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Pelanggan</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" autocomplete="off" id="form_supplier">
              <input type="hidden" name="popup_id_table_pelanggan">
              <div class="form-group">
                <label for="pelanggan_nama" class="col-sm-3 control-label">Nama pelanggan</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" required name="pelanggan_nama" id="pelanggan_nama" placeholder="Example: PT. Abc" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label for="pelanggan_alamat" class="col-sm-3 control-label">Alamat</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" required name="pelanggan_alamat" id="pelanggan_alamat" placeholder="Example: Jl. Soekarno Hatta" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <!-- /.form-group -->


            <div class="form-group">
              <label for="pelanggan_telp" class="col-sm-3 control-label">No. Telp</label>

              <div class="col-sm-9">
                <input type="text" class="form-control" required name="pelanggan_telp" id="pelanggan_telp" placeholder="Example: 080888777666" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="btn_popup_simpan_pelanggan">Simpan</button>
            <!-- <button type="submit" class="btn btn-success pull-right">Simpan</button> -->
          </div>
      </div>
  </div>
</div>

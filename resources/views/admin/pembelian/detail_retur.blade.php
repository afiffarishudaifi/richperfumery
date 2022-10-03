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
              <input type="hidden" name="popup_edit_id_table">
              <input type="hidden" name="popup_edit_idlog_stok">
              <div class="form-group">
                <label class="col-sm-3 control-label">Kode Retur</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_edit_kode" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Tanggal</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_edit_tanggal" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Dari Gudang</label>
                <div class="col-sm-9">
                  <!-- <select class="form-control select2" name="popup_edit_gudang" style="width: 100%;" disabled="">
                  <?php  foreach($data['gudang'] as $key => $list){ ?>
                    <option value="{{$list->id}}">{{$list->nama}}</option>
                  <?php } ?>
                  </select> -->
                  <input type="text" name="popup_edit_gudang" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Barang</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_edit_barang" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Ke Supplier</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_edit_penyedia" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Total Stok</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_edit_stok" class="form-control number-only text-right" disabled="">
                  <p class="help-block pull-left label label-warning">Jumlah Stok</p>
                  <p class="help-block text-right" id="help_popup_edit_stok"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Jumlah</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_edit_jumlah" class="form-control number-only text-right" onkeyup="maksimal($(this))" min="0" disabled="">
                  <p class="help-block text-right" id="help_popup_edit_jumlah"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Satuan</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_edit_satuan" class="form-control" disabled="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Keterangan</label>
                <div class="col-sm-9">
                  <textarea class="form-control" rows="3" name="popup_edit_ket" disabled=""></textarea>
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

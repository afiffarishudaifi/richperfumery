<div class="modal fade" id="modal-form"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Produk Poin</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" data-toogle="validator" autocomplete="off">
              <input type="hidden" id="popup_id" name="popup_id">
              <div class="form-group">
                <label for="produk" class="col-sm-2 control-label">Produk</label>
                <div class="col-sm-8">
                  <select class="form-control select2" name="popup_produk" id="popup_produk" style="width: 100%;">
                    
                  </select>
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <div class="form-group">
                <label for="kategori" class="col-sm-2 control-label">Kategori</label>
                <div class="col-sm-8">
                    <select class="form-control" name="popup_kategori" id="popup_kategori" style="width: 100%;">
                      <option value="">--- Piih --- </option>
                      <option value="1">Rutin</option>
                      <option value="2">Khusus</option>
                      <option value="3">Keseluruhan</option>
                    </select>
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <div class="form-group" id="div_hari">
               <label for="satuan_satuan" class="col-sm-2 control-label">Hari</label>
               <div class="col-sm-8">
                  <select class="form-control select2" name="popup_hari" id="popup_hari" style="width: 100%;">
                    <option value="">--- Pilih ---</option>
                    @foreach($data['hari'] as $key => $value)
                      <option value="{{$key}}" >{{$value}}</option>
                    @endforeach
                  </select>
                  <span class="help-block with-errors"></span>
               </div>
              </div>
              <div class="form-group" id="div_tanggal">
              <label for="satuan_satuan" class="col-sm-2 control-label">Tanggal</label>
              <div class="col-sm-8">
                <input type="text" name="popup_tanggal" id="popup_tanggal" class="form-control datepicker" style="width: 100%;">
                <span class="help-block with-errors"></span>
              </div>
              </div>
              <div class="form-group">
              <label for="satuan_satuan" class="col-sm-2 control-label">Poin</label>
              <div class="col-sm-8">
                <input type="text" class="form-control number-only pull-right" name="popup_poin" id="popup_poin" value="0">
                <p class="help-block pull-right" id="help_block_poin">0</span>
                <span class="help-block with-errors"></span>
              </div>
              </div>
            <!-- /.form-group -->
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

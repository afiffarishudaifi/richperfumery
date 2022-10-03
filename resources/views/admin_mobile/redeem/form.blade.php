<div class="modal fade" id="modal-form"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Produk</h4>
            <p id="div_popup_poinku">Poin <b><span class="badge badge-success" id="popup_poinku">0</span></b></p>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" autocomplete="off">
              <input type="hidden" name="popup_id_table">
              <input type="hidden" name="popup_status">
              <input type="hidden" name="popup_idlog_stok">
              <input type="hidden" name="popup_status_redeem">
              <input type="hidden" name="popup_poin_produk">
              <div class="form-group" id="group-produk">
                <label class="col-sm-3">Produk</label>
                <div class="col-sm-9">
                  <select class="form-control select-search" name="popup_produk" style="width: 100%;" id="popup_produk_select">
                    
                  </select>
                  <p>Poin <b><span class="badge badge-success" id="help_poin_produk">0</span></b></p>
                  <button type="button" class="btn btn-default btn_redeem_not pull-right" id="btn_redeem_not" style="margin-top: -20px;">Redeem</button>
                  <button type="button" class="btn btn-primary btn_redeem_check pull-right" id="btn_redeem_check" style="margin-top: -20px;">Not Redeem</button>
                </div>
              </div>
              <div class="form-group" id="group-barang">
                <label class="col-sm-3">Barang</label>
                <div class="col-sm-9">
                  <select class="form-control select-search" name="popup_barang" style="width: 100%;">
                    
                  </select>
                  <!-- <button type="button" class="btn btn-default btn_redeem_not" id="btn_redeem_not">Redeem</button>
                  <button type="button" class="btn btn-primary btn_redeem_check" id="btn_redeem_check">Not Redeem</button> -->
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Jumlah</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_jumlah" class="form-control number-only">
                  <p class="help-block text-right" id="help_popup_jumlah"></p>
                </div>
              </div>
              <div class="form-group" id="group-satuan">
                <label class="col-sm-3">Satuan</label>
                <div class="col-sm-9">
                  <select name="popup_satuan" class="form-control select2" style="width: 100%;">
                    <option value="">-- Pilih --</option>
                    @foreach($data['satuan'] as $d)
                    <option value="{{$d->satuan_id}}" nama="{{$d->satuan_nama}}" satuan="{{$d->satuan_satuan}}">{{$d->satuan_nama." (".$d->satuan_satuan.")"}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Harga Jual</label>
                <div class="col-sm-9">
                  <input type="hidden" name="popup_harga_hidden" id="popup_harga_hidden">
                  <input type="text" name="popup_harga" class="form-control text-right number-only">
                  <p class="help-block pull-right" id="help_popup_harga"></p>
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

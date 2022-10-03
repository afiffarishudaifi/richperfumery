<div class="modal fade" id="modal-form"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Produk</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" autocomplete="off">
              <input type="hidden" name="popup_id_table">
              <input type="hidden" name="popup_status">
              <input type="hidden" name="popup_idlog_stok">
              <input type="hidden" name="popup_poin">
              <input type="hidden" name="popup_produk_sebelum">
              <input type="hidden" name="popup_hargapromo">
              <div class="form-group" id="group-produk">
                <label class="col-sm-3">Produk</label>
                <div class="col-sm-9">
                  <select class="form-control select-search" name="popup_produk" style="width: 100%;">
                    
                  </select>
                </div>
              </div>
              <div class="form-group" id="group-barang">
                <label class="col-sm-3">Barang</label>
                <div class="col-sm-9">
                  <select class="form-control select-search" name="popup_barang" style="width: 100%;">
                    
                  </select>
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
                  <input type="text" name="popup_harga" class="form-control text-right number-only">
                  <p class="help-block pull-right" id="help_popup_harga"></p>
                </div>
              </div>
              <div id="wrapper-promo">
                <div class="form-group">
                  <label class="col-sm-3 blink"><span class="btn btn-success btn-xs">PAKET</span></label>
                  <div class="col-sm-9">
                    {{-- <input type="checkbox" class="form-check-input" id="check_promo" name="check_promo">
                    <label class="form-check-label" for="exampleCheck1">Pakai Promo!</label> --}}
                    <select name="popup_promo" class="form-control select2" style="width: 100%;" id="popup_promo">
                      <option value="TIDAK">TIDAK</option>
                      <option value="YA">YA</option>
                    </select>
                    <small class="text-green">Promo Berhasil Ditemukan!</small>
                  </div>
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

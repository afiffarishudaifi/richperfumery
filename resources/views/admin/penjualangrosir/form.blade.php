<div class="modal fade" id="modal-form"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Barang</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal formgudang" autocomplete="off">
              <input type="hidden" name="popup_id_table">
              <input type="hidden" name="gudang" id="id_gudang">
              <input type="hidden" name="satuan_awal" id="satuan_awal">
               <input type="hidden"  name="popup_id_satuan" id="popup_id_satuan">
               <input type="hidden"  name="popup_nama_satuan" id="popup_nama_satuan">
              <div class="form-group">
                <label class="col-sm-3">Barang</label>
                <div class="col-sm-9">
                  <select class="form-control select-search" name="popup_barang" style="width: 100%;">
                    
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Jumlah</label>
                <div class="col-sm-7">
                  <input type="text" name="popup_jumlah" class="form-control number-only">
                  <p class="help-block text-right" id="help_popup_jumlah"></p>
                </div>
                <div class="col-sm-2 pull-right">
                  <input type="text" name="popup_stok" class="form-control" readonly>
                  <p class="help-block pull-left label label-warning">Jumlah Stok</p>
                  <p class="help-block text-right" id="popup_stok"></p>
                </div> 
              </div>
              <div class="form-group">
                <label class="col-sm-3">Satuan</label>
                <div class="col-sm-9">
                 <select name="popup_satuan" id="popup_satuan" class="form-control select2" style="width: 100%;" disabled> 
                  <option value="">Pilih</option> 
                  @foreach($data['satuan'] as $key => $list)
                  <option value="{{$list->satuan_id}}">{{$list->satuan_nama}}</option>
                  @endforeach
                  </select> 
                  <input type="hidden" name="popup_satuan" value="">
                  {{-- <input type="text" readonly name="popup_satuan" id="popup_satuan" class="form-control"> --}}
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Harga Jual</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_harga" class="form-control text-right number-only">
                  <p class="help-block pull-right" id="help_popup_harga"></p>
                </div>
              </div>
              <!-- <div class="form-group">
                <label class="col-sm-3">Potongan Satuan</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_diskon" class="form-control text-right number-only">
                  <p class="help-block pull-right" id="help_popup_diskon"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Total</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_total" class="form-control text-right number-only" readonly="">
                  <p class="help-block pull-right" id="help_popup_total"></p>
                </div>
              </div> -->
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

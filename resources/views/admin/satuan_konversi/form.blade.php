<div class="modal fade" id="modal-form"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Konversi</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" autocomplete="off" id="form_satuankonversi">
              <input type="hidden" name="popup_id_table">
              <input type="hidden" name="popup_jumlah_bagi">
              <div class="form-group">
                <label class="col-sm-3">Satuan Awal</label>
                <div class="col-sm-9">
                  <select class="form-control select2" name="popup_satuan_awal" style="width: 100%;">
                    <option value="">-- Pilih --</option>
                    @foreach($data['satuan'] as $d)
                    <option value="{{$d->satuan_id}}" nama="{{$d->satuan_nama}}">{{$d->satuan_nama}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Jumlah Awal</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_jumlah_awal" class="form-control number-only">
                  <p class="help-block text-right" id="help_popup_jumlahawal"></p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Satuan Akhir</label>
                <div class="col-sm-9">
                  <select name="popup_satuan_akhir" class="form-control select2" style="width: 100%;">
                    <option value="">-- Pilih --</option>
                    @foreach($data['satuan'] as $d)
                    <option value="{{$d->satuan_id}}" nama="{{$d->satuan_nama}}">{{$d->satuan_nama}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Jumlah Akhir</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_jumlah_akhir" class="form-control number-only">
                  <p class="help-block text-right" id="help_popup_jumlahakhir"></p>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="btn_popup_simpan">Simpan</button>
          </div>
      </div>
  </div>
</div>

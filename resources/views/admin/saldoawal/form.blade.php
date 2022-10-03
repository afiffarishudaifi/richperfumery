<div class="modal fade" id="modal-form"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Barang</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" autocomplete="off" id="form_saldoawal">
              <input type="hidden" name="popup_id_table">
              <input type="hidden" name="popup_namabarang">
              <input type="hidden" name="popup_tanggal2">
              <input type="hidden" name="popup_idlog_stok">
              <div class="form-group">
                <label class="col-sm-3">Tanggal</label>
                <div class="col-sm-9">
                  <input type="text" name="popup_tanggal" class="form-control datepicker" value="<?=date('d-m-Y')?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Barang</label>
                <div class="col-sm-9">
                  <select class="form-control select-search" name="popup_barang" style="width: 100%;">
                    
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3">Gudang </label>
                <div class="col-sm-9">
                  <select class="form-control select2" name="popup_gudang" style="width: 100%;">
                  <?php  foreach($data['gudang'] as $key => $list){ ?>
                    <option value="{{$list->id}}">{{$list->nama}}</option>
                  <?php } ?>
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
              <div class="form-group">
                <label class="col-sm-3">Satuan</label>
                <div class="col-sm-9">
                  <select name="popup_satuan" class="form-control select2" style="width: 100%;" disabled>
                    <option value="">-- Pilih --</option>
                    @foreach($data['satuan'] as $d)
                    <option value="{{$d->satuan_id}}" nama="{{$d->satuan_nama}}">{{$d->satuan_nama}}</option>
                    @endforeach
                  </select>
                  <input type="hidden" name="popup_satuan" value="">
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

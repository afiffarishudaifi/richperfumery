<!-- <div class="modal fade" id="modal-form2" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" action="javascript:;" method="post" class="form_barang">
        <input type="hidden" value="" name="crud" >
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Barang</h4>
          </div>
          <div class="modal-body">

            <div class="box-body">
            <input type="hidden" name="popup_id_table">
              <div class="form-group">
               <label for="exampleInputPassword1">Nama Barang</label>
               <select name="popup_barang" id="popup_barang" class="form-control js-example-basic-single" style="width: 100%;">
              </select>
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Harga</label>
              <input type="text" class="form-control" id="popup_harga" name="popup_harga" value="">
            </div>

            <div class="row">

            <div class="form-group col-md-6">
              <label for="exampleInputPassword1">jumlah</label>
              <input type="text" class="form-control text-right number-only" name="popup_jumlah">
              <label class="fmt-nominal pull-right" id="help_block_jumlah"></label>
            </div>
            <div class="form-group col-md-6">
              <label for="exampleInputPassword1">Diskon</label>
              <input type="text" class="form-control text-right number-only" name="popup_diskon">
              <label class="fmt-nominal pull-right" id="help_block_diskon"></label>
            </div>
            <div class="form-group col-md-6">
              <label for="exampleInputPassword1">Total</label>
              <input type="text" class="form-control text-right number-only" readonly="" name="popup_total" >
              <label class="fmt-nominal pull-right" id="help_block_total"></label>
            </div>

            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning pull-left" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success pull-right">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div> -->

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
              <div class="form-group">
                <label class="col-sm-3">Produk</label>
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
              <div class="form-group">
                <label class="col-sm-3">Satuan</label>
                <div class="col-sm-9">
                  <select name="popup_satuan" class="form-control select2" style="width: 100%;">
                    <option value="">-- Pilih --</option>
                    @foreach($data['satuan'] as $d)
                    <option value="{{$d->satuan_id}}" nama="{{$d->satuan_nama}}">{{$d->satuan_nama}}</option>
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

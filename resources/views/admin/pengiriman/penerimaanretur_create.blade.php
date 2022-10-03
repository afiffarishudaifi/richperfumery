<div class="modal fade slide-up disable-scroll" id="modal-penerimaanretur" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
          <div class="d">

            <!-- <h4 class="modal-title">Default Modal</h4> -->
          </div>
        </div>
        <form class="form-horizontal form_connectio1n" autocomplete="off" data-toggle="validator" method="post" id="form_penerimaanretur">
          {{ csrf_field() }} {{ method_field('POST') }}
          <div class="modal-body">
            <input type="hidden" id="popup_id_table" name="popup_id_table">
            <input type="hidden" id="popup_idsatuan" name="popup_idsatuan">
            <input type="hidden" id="popup_idbarang" name="popup_idbarang">
            <input type="hidden" id="popup_idgudang_outlet" name="popup_idgudang_outlet">
            <input type="hidden" id="popup_idgudang_pusat" name="popup_idgudang_pusat">
            <input type="hidden" id="popup_idretur" name="popup_idretur">
            <!-- <input type="hidden" id="popup_status" name="popup_status"> -->
            <input type="hidden" id="popup_idlog_stok" name="popup_idlog_stok">
            <input type="hidden" id="popup_idlog_stok_penerimaan" name="popup_idlog_stok_penerimaan">
            <input type="hidden" id="popup_status_hidden" name="popup_status_hidden">
            
              <!-- /.form-group -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Tanggal</label>

              <div class="col-sm-8">
              <input type="text" name="popup_tanggal"  autocomplete="off" value="" class="form-control datepicker" id="popup_tanggal">
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Kode Retur</label>
              <div class="col-sm-8">
                <input type="text" name="popup_kode" id="popup_kode" class="form-control" value="" readonly="">
              </div>
            </div>
             <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Outlet</label>
              <div class="col-sm-8">
                <input type="text" name="popup_gudang_outlet" id="popup_gudang_outlet" class="form-control" readonly="">
              </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Pusat</label>
                <div class="col-sm-8">
                  <input type="text" name="popup_gudang_pusat" id="popup_gudang_pusat" class="form-control" readonly="">
                </div>
              </div>
              <!-- /.form-group -->
           
            <div class="form-group">
              <label class="col-sm-2 control-label">Barang</label>
              <div class="col-sm-8">
              <input type="text" name="popup_barang" id="popup_barang" class="form-control" readonly="">
             </div>
            </div>             
            <div class="form-group">
              <label class="col-sm-2 control-label">Jumlah</label>
              <div class="col-sm-8">
              <input type="text" id="popup_jumlah"  name="popup_jumlah" value="" class="form-control" readonly="">
              <p class="help-block text-right" id="help_block_jumlah"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Jumlah Terima</label>
              <div class="col-sm-8">
              <input type="text" id="popup_jumlahterima"  name="popup_jumlahterima" value="" class="form-control">
              <p class="help-block text-right" id="help_block_jumlahterima"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Keterangan</label>
              <div class="col-sm-8">
              <textarea class="form-control" rows="3" name="popup_keterangan" id="popup_keterangan"></textarea>
              </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Status</label>
                <div class="col-sm-8">
                  <select class="form-control select2" name="popup_status" style="width: 100%;">
                     <?php
                          foreach($data['status'] as $key => $value){
                            echo '<option value="'.$key.'">'.$value.'</option>';
                          }
                      ?>
                  </select>
                </div>
            </div>
            <!-- /.form-group -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
            <button type="button" class="btn btn-primary btn_popup_simpan_baru"><i class="fa fa-floppy-o"></i> Simpan </button>
          </div>
        </div>

      </form>

      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

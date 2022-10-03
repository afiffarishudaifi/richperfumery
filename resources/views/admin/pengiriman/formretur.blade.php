<div class="modal fade slide-up disable-scroll" id="modal-retur" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
          <div class="d">

            <!-- <h4 class="modal-title">Default Modal</h4> -->
          </div>
        </div>
        <form class="form-horizontal form_connectio1n" autocomplete="off" data-toggle="validator" method="post">
          {{ csrf_field() }} {{ method_field('POST') }}
          <div class="modal-body">
            <input type="hidden" id="crud" name="crud" value="">
            <input type="hidden" id="status" name="status">
            <input type="hidden" id="id_satuan" name="id_satuan">
            <input type="hidden" id="id_retur" name="id_retur">
            <input type="hidden" id="id_log_stok" name="id_log_stok">
            
            <!-- /.form-group -->
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Kode Retur</label>
              <div class="col-sm-8">
                <input type="text" name="kode" id="kode" class="form-control" readonly="" onchange="get_noauto($(this))">
              </div>
            </div>
             <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Outlet</label>

              <div class="col-sm-8">
                <select name="id_gudang_outlet" id="id_gudang_outlet" class="form-control js-example-basic-single" style="width: 100%; height:100%">
                  <!-- <option value=""> Pilih </option> -->
                  @foreach($gudang['outlet'] as $list)
                  <option  value="{{ $list->id }}" > {{ $list->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Pusat</label>

                <div class="col-sm-8">
                  <!-- <span class="addon"><i class="fa  fa-user"></i></span> -->
                  <select name="id_gudang_pusat" id="id_gudang_pusat" class="form-control js-example-basic-single" style="width: 100%; height:100%">
                    <option value=""> Pilih </option>
                    @foreach($gudang['gudang'] as $list)
                      <option  value="{{ $list->id }}" {{($list->id == 8)?'selected':''}}> {{ $list->nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <!-- /.form-group -->

            <div class="form-group">
              <label class="col-sm-2 control-label">Barang</label>
              <div class="col-sm-8">
              <select class="form-control js-example-basic-single" style="width: 100%; height:100%" name="barang" id="barang">
               
              </select>

             </div>
            </div> 
            <div class="form-group" id="stok">
              <label class="col-sm-2 control-label">Total stok</label>

              <div class="col-sm-8">
              <input type="text" name="total" readonly autocomplete="off" value="" class="form-control total " id="total">
                <label class="label label-warning">jumlah stok di outlet</label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Tanggal</label>

              <div class="col-sm-8">
              <input type="text" name="tanggal"  autocomplete="off" value="" class="form-control datepicker" id="tanggal">

              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Jumlah</label>

              <div class="col-sm-8">
              <input type="text" id="jumlah"  name="jumlah" value="" class="form-control number-only" onkeyup="maksimal($(this))">
              <p class="help-block text-right" id="help_popup_jumlah"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Keterangan</label>

              <div class="col-sm-8">
              <textarea class="form-control" rows="3" name="keterangan" id="keterangan"></textarea>

              </div>
            </div>
            <!-- /.form-group -->



          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
            <button type="submit" class="btn btn-primary btn-save"><i class="fa fa-floppy-o"></i> Simpan </button>
          </div>
        </div>

      </form>

      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

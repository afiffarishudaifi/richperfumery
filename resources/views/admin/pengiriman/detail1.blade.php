<div class="modal fade slide-up disable-scroll" id="modal-form_detail" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">


    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
          <div class="detail_d">

            <!-- <h4 class="modal-title">Default Modal</h4> -->
          </div>
        </div>
        <form class="form-horizontal form_connectio1n" autocomplete="off" data-toggle="validator" method="post">
          {{ csrf_field() }} {{ method_field('POST') }}
          <div class="modal-body">
            <input type="hidden" id="detail_id" name="detail_id">
            <input type="hidden" id="detail_crud" name="detail_crud" value="">
              <!-- /.form-group -->

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">No Pengiriman</label>

                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" class="form-control"  name="detail_kode" id="detail_kode" placeholder="kode" disabled="">
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Pusat</label>

                <div class="input-group col-sm-8">
                  <input type="text" name="detail_gudang_awal" id="detail_gudang_awal" class="form-control" disabled="">
                </div>
              </div>
              <!-- /.form-group -->


            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Outlet</label>

              <div class="input-group col-sm-8">
                <input type="text" name="detail_tujuan" id="detail_tujuan" class="form-control" disabled="">
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Pengirim</label>

              <div class="input-group col-sm-8">
                <input type="text" name="detail_pengiriman" id="detail_pengiriman" class="form-control" disabled="">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Tanggal Pengiriman</label>

              <div class="input-group col-sm-8">
              <input type="text" name="detail_tanggal" autocomplete="off" value="" class="form-control" id="detail_tanggal" disabled="">

              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Keterangan</label>

              <div class="input-group col-sm-8">
              <textarea class="form-control" rows="3" style="width: 100%;" name="detail_keterangan" id="detail_keterangan" disabled=""></textarea>

              </div>
            </div>
            {{-- <div class="form-group">
              <label class="col-sm-2 control-label">Jumlah</label>

              <div class="input-group col-sm-8">
              <input type="text" name="jumlah" value="" class="form-control">

              </div>
            </div> --}}
            <!-- /.form-group -->



          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
          </div>
        </div>

      </form>

      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

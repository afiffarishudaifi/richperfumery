<div class="modal" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">


    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
          <h4 class="modal-title">Default Modal</h4>
        </div>
        <form class="form-horizontal" data-toggle="validator" method="post">
          {{ csrf_field() }} {{ method_field('POST') }}
          <div class="modal-body">
            <input type="hidden" id="id" name="id">

            <input type="hidden" name="barang_id" id="barang_id" value="{{$barang_id}}">



              <div class="form-group">
                <label for="detail_harga_barang_tanggal" class="col-sm-2 control-label">Tanggal</label>

                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="date" class="form-control" required name="detail_harga_barang_tanggal" id="detail_harga_barang_tanggal" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <!-- /.form-group -->


            <div class="form-group">
              <label for="detail_harga_barang_harga_jual" class="col-sm-2 control-label">Harga Jual (Rp.)</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" onkeypress="return hanyaAngka(event)" class="form-control" required name="detail_harga_barang_harga_jual" id="detail_harga_barang_harga_jual" placeholder="Example: 150000" >
                <b><span id="rupiah" class="rupiah">Rp. 0</span></b> 
                <span class="help-block with-errors"></span>
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

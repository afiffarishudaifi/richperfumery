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
          <input type="hidden" id="id_pembelian" value="{{$id_pembelian}}" name="id_pembelian">
          
          <div class="row">
            <div class="form-group">
              <label class="col-sm-2 control-label">Nama Barang</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <select id="barang_id" required name="barang_id" class="form-control" style="width: 100%;">
                  @foreach($barang as $list)
                  <option value="{{ $list->barang_id }}"> {{ $list->barang_nama }}</option>
                  @endforeach
                </select>
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Harga</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" class="form-control" name="pembelian_detail_harga" readonly id="pembelian_detail_harga" placeholder="Rp.500">
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label">Qty</label>

                <div class="input-group col-sm-6">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" onkeyup="hitung_total()" class="form-control" required name="pembelian_detail_qty" id="pembelian_detail_qty">

                </div>
              </div>
              <!-- /.form-group -->
            </div>
            <div class="col-md-6">

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label">Discount %</label>
                <div class="input-group col-sm-4">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" onkeyup="hitung_total()" class="form-control" name="pembelian_detail_diskon" id="pembelian_detail_diskon" value="0">
                  <span class="help-block with-errors"></span>
                </div>
              </div>
            </div>

            <div class="col-md-6">

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label">Total</label>
                <div class="input-group col-sm-6">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="hidden" class="form-control" name="pembelian_detail_jumlah" id="pembelian_detail_jumlah" readonly>
                  <p id="jumlah"></p>
                  {{-- <input type="text" class="form-control" name="jumlah" id="jumlah" readonly> --}}
                  <span class="help-block with-errors"></span>
                </div>
              </div>
            </div>
            <!-- /.form-group -->

            <!-- /.form-group -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
            <button type="submit" id="submit" class="btn btn-primary btn-save"><i class="fa fa-floppy-o"></i> Simpan </button>
          </div>
        </div>

      </form>

      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
</div>

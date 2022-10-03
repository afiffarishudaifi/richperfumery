<div class="modal fade slide-up disable-scroll" id="modal-form" role="dialog" aria-hidden="true" data-backdrop="static">
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
          <input type="hidden" id="id" name="id" value="{{$gudang['id']}}">
            <input type="hidden" id="id_detail" name="id_detail" value="">
            <input type="hidden" id="crud" name="crud" value="">
            <input type="hidden" id="kode" name="kode" value="">
            <input type="hidden" id="gudang" name="gudang" value="">
			      <input type="hidden" id="id_satuan" name="id_satuan" value="">
            <input type="hidden" id="tanggal" name="tanggal" value="{{$gudang['tanggal']}}">
            <input type="hidden" id="id_log_stok" name="id_log_stok" value="">
            <input type="hidden" id="id_log_stok_penerimaan"name="id_log_stok_penerimaan" value="">
            <!-- <input type="hidden" id="id_log_stok" name="id_log_stok" value=""> -->
              <!-- /.form-group -->
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Barang</label>
                <div class="col-sm-8">
                  <!-- <span class="-addon"><i class="fa  fa-user"></i></span> -->
                  <select name="barang" id="barang" class="form-control barang_select2 js-example-basic-single" style="width: 100%; height:100%">                  
                  </select>
                </div>
              </div>              
              <!-- /.form-group -->
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama</label>

              <div class="col-sm-8">
                <input type="text" name="nama" id="nama" class="form-control" readonly >
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Satuan</label>
              <div class="col-sm-8">
              <input type="text" name="satuan" readonly autocomplete="off" value="" class="form-control " id="satuan">
              </div>
            </div>
             <div class="form-group" id="stok">
              <label class="col-sm-2 control-label">Stok</label>

              <div class="col-sm-8">
              <input type="text" name="tersedia" readonly autocomplete="off" value="" class="form-control " id="tersedia">

              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Jumlah</label>
              <div class="col-sm-8">
              <input type="text" id="jumlah" name="jumlah" value="" class="form-control number-only text-right">
              <p class="text-right" id="help_detail_jumlah"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Harga</label>
              <div class="col-sm-8">
              <input type="text" id="harga" name="harga" class="form-control number-only text-right">
              <p class="text-right" id="help_detail_harga"></p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Keterangan</label>
              <div class="col-sm-8">
                <textarea class="form-control" rows="3" style="width: 100%;" name="keterangan" id="keterangan"></textarea>
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
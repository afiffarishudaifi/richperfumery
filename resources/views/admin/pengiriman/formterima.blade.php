<div class="modal fade slide-up disable-scroll" id="modal-diterima" role="dialog" aria-hidden="true" data-backdrop="static">
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
            <input type="hidden" id="crud" name="crud" value="">
            <input type="hidden" id="kode" name="kode" value="">
            <input type="hidden" id="id_detail_pengiriman" name="id_detail_pengiriman" value="">
            <input type="hidden" id="gudang" name="gudang" value="">
            <input type="hidden" id="id_barang" name="id_barang" value="">
              <!-- /.form-group -->

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">kode barang</label>
                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                 <input type="text" readonly value="" id="kode" class="form-control">
                </div>
              </div>
              
              <!-- /.form-group -->


            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama</label>

              <div class="input-group col-sm-8">
                <input type="text" name="nama" id="nama" class="form-control" readonly >
              </div>
            </div>
            {{-- <div class="form-group">
              <label class="col-sm-2 control-label">Status</label>

              <div class="input-group col-sm-8">
              <select class="form-control" name="status" id="status">
                <option value="0">pilih</option>
                <option value="1">Diterima</option>
                <option value="2">Dikembalikan</option>
              </select>

              </div>
            </div> --}}
            <div class="form-group">
              <label class="col-sm-2 control-label">Diterima</label>

              <div class="input-group col-sm-8">
              <input type="text" name="diterima"  autocomplete="off" value="" class="form-control " id="diterima">

              </div>
            </div>
            {{-- <div class="form-group">
              <label class="col-sm-2 control-label">Dikembalikan</label>

              <div class="input-group col-sm-8">
              <input type="text" name="dikembalikan"  autocomplete="off" value="" class="form-control " id="dikembalikan">

              </div>
            </div> --}}
            <div class="form-group">
              <label class="col-sm-2 control-label">Jumlah</label>

              <div class="input-group col-sm-8">
              <input type="text" id="jumlah" readonly name="jumlah" value="" class="form-control">

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

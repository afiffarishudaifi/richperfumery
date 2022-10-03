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
            <input type="hidden" id="id" name="id">
            <input type="hidden" id="crud" name="crud" value="">
              <!-- /.form-group -->

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">No Pengiriman</label>

                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" class="form-control"  name="kode" id="kode" placeholder="kode" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Pusat</label>

                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <select name="gudang_awal" id="gudang_awal" class="form-control js-example-basic-single" style="width: 100%; height:100%">
                    @foreach($gudang['gudang'] as $list)
                      <option  value="{{ $list->id }}"> {{ $list->nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <!-- /.form-group -->


            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Outlet</label>

              <div class="input-group col-sm-8">
                <select name="tujuan" id="tujuan" class="form-control js-example-basic-single" style="width: 100%; height:100%">
                  @foreach($gudang['outlet'] as $list)
                  <option  value="{{ $list->id }}"> {{ $list->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Pengirim</label>

              <div class="input-group col-sm-8">
                <select name="pengiriman" id="pengiriman" class="form-control js-example-basic-single" style="width: 100%; height:100%">
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Tanggal Pengiriman</label>

              <div class="input-group col-sm-8">
              <input type="text" name="tanggal" autocomplete="off" value="" class="form-control tanggal" id="tanggal">

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
            <button type="submit" class="btn btn-primary btn-save"><i class="fa fa-floppy-o"></i> Simpan </button>
          </div>
        </div>

      </form>

      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

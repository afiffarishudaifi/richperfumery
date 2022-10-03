<div class="modal" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">


    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
          <h4 class="modal-title">Default Modal</h4>
        </div>
        <form class="form-horizontal" autocomplete="off">
          {{ csrf_field() }} {{ method_field('POST') }}
          <div class="modal-body">
            <input type="hidden" id="id" name="id">

              <div class="form-group">
                <label for="supplier_nama" class="col-sm-3 control-label">Nama Supplier</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" required name="supplier_nama" id="supplier_nama" placeholder="Example: PT. Abc" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label for="supplier_alamat" class="col-sm-3 control-label">Alamat</label>

                <div class="col-sm-9">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" class="form-control" required name="supplier_alamat" id="supplier_alamat" placeholder="Example: Jl. Soekarno Hatta" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <!-- /.form-group -->


            <div class="form-group">
              <label for="supplier_telp" class="col-sm-3 control-label">No. Telp</label>

              <div class="col-sm-9">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" class="form-control" required name="supplier_telp" id="supplier_telp" placeholder="Example: 080888777666" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->


            <div class="form-group">
              <label for="supplier_telp" class="col-sm-3 control-label">Term of payment</label>
              <div class="col-sm-8">
                <input type="text" class="form-control number-only" required name="supplier_tempo" id="supplier_tempo">
              </div>
              <p class="help-block pull-left">Hari</p>
              <span class="help-block with-errors"></span>
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

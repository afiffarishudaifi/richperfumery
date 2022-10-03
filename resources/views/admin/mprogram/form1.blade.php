<div class="modal fade slide-up disable-scroll" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">


    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
          <div class="d">

            <!-- <h4 class="modal-title">Default Modal</h4> -->
          </div>
        </div>
        <form class="form-horizontal form_connectio1n" autocomplete="false" data-toggle="validator" method="post">
          {{ csrf_field() }} {{ method_field('POST') }}
          <div class="modal-body">
            <input type="hidden" id="id" name="id">
            <input type="hidden" id="crud" name="crud" value="">



              <!-- /.form-group -->

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Kode</label>

                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" class="form-control" required name="kode" id="kode" placeholder="Kode program" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <!-- /.form-group -->


            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama Program</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" class="form-control" autocomplete="false" required name="nama" id="nama" placeholder="Nama Program" >
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

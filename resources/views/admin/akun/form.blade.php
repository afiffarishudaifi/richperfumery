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


            <div class="form-group">
              <label class="col-sm-2 control-label">Parent</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <select  id="akun_id_parent" name="akun_id_parent" class="form-control js-example-basic-single" style="width: 100%;">
                  <option value="0" >--</option>
                  @foreach($akun as $list)
                  <option  value="{{ $list->akun_id }}"> {{ $list->akun_nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <!-- /.form-group -->



            <div class="form-group">
              <label for="akun_kode" class="col-sm-2 control-label">Kode Perkiraan</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" class="form-control" required name="akun_kode" id="akun_kode" placeholder="Kode Perkiraan" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->

            <div class="form-group">
              <label for="akun_nama" class="col-sm-2 control-label">Nama Perkiraan</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" class="form-control" required name="akun_nama" id="akun_nama" placeholder="Nama Perkiraan" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->

            <div class="form-group">
              <label class="col-sm-2 control-label">Saldo Normal</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <select  id="akun_saldo_normal" name="akun_saldo_normal" class="form-control" style="width: 100%;">
                  <option value="0" >--Pilih--</option>
                  <option  value="D"> Debet </option>
                  <option  value="K"> Kredit </option>
                </select>
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

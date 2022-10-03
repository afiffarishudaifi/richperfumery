<div class="modal fade slide-up disable-scroll" id="modal-form"  role="dialog" aria-hidden="true" data-backdrop="static">
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
              <label for="inputEmail3" class="col-sm-2 control-label">Profil</label>

              <div class="input-group col-sm-8">
                <select name="id_profil" id="id_profil" class="form-control js-example-basic-single" style="width: 100%; height:100%">
                </select>
              </div>
            </div> 
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Nama Gudang</label>

                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" class="form-control"  name="nama" id="nama" placeholder="nama" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Alamat</label>

                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" class="form-control"  name="alamat" id="alamat" placeholder="alamat" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <!-- /.form-group -->            

            {{-- <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Jenis Gudang</label>

              <div class="input-group col-sm-8">
                <select name="jenis_gudang" id="jenis_gudang" class="form-control js-example-basic-single" style="width: 100%; height:100%">
                  <option value="1">Gudang Pusat</option>
                  <option value="2">Outlet</option>
                </select>
              </div>
            </div> --}}
            <div class="form-group">
              <label class="col-sm-2 control-label">Status</label>

              <div class="input-group col-sm-8">
                <select  id="status" name="status" class="form-control js-example-basic-single" style="width: 100%;">
                  <option value="1" >Aktif</option>
                  <option value="2" >Tidak Aktif</option>
                </select>
                  
              </div>
            </div>
            <!-- /.form-group -->
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Kode</label>
                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" class="form-control"  name="kode" id="kode" placeholder="kode" >
                  <span class="help-block with-errors"></span>
                </div>
              </div> 



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

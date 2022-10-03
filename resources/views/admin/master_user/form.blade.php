<div class="modal" id="modal-form" role="dialog" aria-hidden="true" data-backdrop="static">
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
              <label class="col-sm-2 control-label">Profil</label>
              <div class="col-sm-8">
              <select name="id_profil" id="id_profil" class="form-control js-example-basic-single" style="width: 100%; height:100%">
                </select>
                <span class="help-block with-errors"></span>
              </div>
            </div>

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama</label>

              <div class="col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" class="form-control" required name="name" id="name" placeholder="Nama Users" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->


            <div class="form-group">
              <label class="col-sm-2 control-label"> Group</label>

              <div class="col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <select  id="group_id" required name="group_id" class="form-control select2" style="width: 100%;">
                  @foreach($group as $list)
                  <option  value="{{ $list->group_id }}"> {{ $list->group_nama }}</option>
                  @endforeach
                </select>
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Email</label>

              <div class="col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="email" class="form-control" required name="users_email" id="users_email" placeholder="Nama Users" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Username</label>

              <div class="col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" class="form-control" required name="email" id="email" placeholder="Username" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Password</label>

              <div class="col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="password"   class="form-control" name="password" id="password" placeholder="Isikan Password" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->

            {{-- <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Confirm Password</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="password"  class="form-control" name="confirm_password" id="confirm_password" placeholder="Ulangi Password" >
                <span id='message'></span>
              </div>
            </div> --}}
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
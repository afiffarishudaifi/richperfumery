<div class="modal fade slide-up disable-scroll" id="modal-form" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
          
        </div>       
        
      <form class="form-horizontal form_connectio1n" autocomplete="off" data-toggle="validator" method="post">
          {{ csrf_field() }} {{ method_field('POST') }}
            <div class="modal-body">
                    <div class="col-md-12">
            <input type="hidden" id="id" name="id" value="">
            <input type="hidden" id="crud" name="crud" value="">
              <!-- /.form-group -->
        
              <!-- /.form-group -->
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama</label>
              <div class="input-group col-sm-8">
                <input type="text" name="nama" id="nama" class="form-control"  >
              </div>
            </div>
            <!-- /.form-group -->
             </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
            <button type="submit" class="btn btn-primary" id="btn_popup_simpan">Simpan</button>
          </div>
    </form>
</div>
    </div>  
  </div>

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
        
        <form class="form-horizontal form_connectio1n" autocomplete="off">
            {{-- {{ csrf_field() }} {{ method_field('POST') }} --}}
            <div class="modal-body">
                    <div class="col-md-12">
            <input type="hidden" id="id" name="id" value="">
            <input type="hidden" id="crud" name="crud" value="">
            <input type="hidden" id="kode" name="kode" value="">
            <input type="hidden" id="kode_a" name="kode_a" value="">
              <!-- /.form-group -->
       
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Barang</label>
                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <select name="barang" id="barang" class="form-control barang_select2 js-example-basic-single" style="width: 100%; height:100%">                  
                  </select>
                </div>
              </div>
              
              <!-- /.form-group -->


            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama</label>

              <div class="input-group col-sm-8">
                <input type="text" name="nama" id="nama" class="form-control" readonly >
              </div>
            </div>
             <div class="form-group">
              <label class="col-sm-2 control-label">satuan</label>

              <div class="input-group col-sm-8">
              <input type="text" id="satuan" readonly name="satuan" value="" class="form-control">

              </div>
            </div>
           
            <div class="form-group">
              <label class="col-sm-2 control-label">Jumlah</label>

              <div class="input-group col-sm-8">
              <input type="text" id="jumlah" name="jumlah" value="" class="form-control">

              </div>
            </div>
            <!-- /.form-group -->



             </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
            <button type="button" class="btn btn-primary" id="btn_popup_simpan">Simpan</button>
          </div>
    </form>
</div>


      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  
  </div>

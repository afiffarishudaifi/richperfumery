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
          <div class="col-md-12">
            <input type="hidden" id="id" name="id" value="">
            <input type="hidden" id="crud" name="crud" value="">
            <input type="hidden" id="status_aktif" name="status_aktif" value="">
        
            <!-- /.form-group -->
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama</label>
              <div class="col-sm-8">
                <input type="text" name="nama" id="nama" class="form-control"  >
              </div>
            </div>
             <div class="form-group">
              <label class="col-sm-2 control-label">Telephone</label>
              <div class="col-sm-8">
              <input type="text" id="telp" name="telp" value="" class="form-control">
              </div>
            </div>
             <div class="form-group">
              <label class="col-sm-2 control-label">Status</label>

              <div class="col-sm-8">
              {{-- <input type="status" id="telp" name="status" value="" class="form-control">
               --}}
               <select name="status" id="status" class="form-control select2" style="width: 100%; height:100%">
                 <option value="1">Biasa</option>
                 <option value="2">Member</option>
                 <option value="3">Karyawan</option>
                 <option value="4">Reseller</option>
               </select>

              </div>
            </div>
             <div class="form-group hidden" id="no_member">
              <label class="col-sm-2 control-label">No Member</label>

              <div class="col-sm-8">
              <input type="text" id="nomember" name="nomember" value="" class="form-control">

              </div>
            </div>
            <div class="form-group hidden" id="tempat">
              <label class="col-sm-2 control-label">Tempat Lahir</label>

              <div class="col-sm-8">
              <input type="text" id="tempat1" name="tempat1" value="" class="form-control">

              </div>
            </div>
            <div class="form-group hidden" id="tgl_lahir">
              <label class="col-sm-2 control-label">Tanggal Lahir</label>

              <div class="col-sm-8">
              <input type="text" id="tgl_lahir1" name="tgl_lahir1" value="" class="form-control">

              </div>
            </div>
             <div class="form-group hidden" id="email">
              <label class="col-sm-2 control-label">Email</label>

              <div class="col-sm-8">
              <input type="email" id="email1" name="email1" value="" class="form-control">

              </div>
            </div>
            <div class="form-group hidden" id="jenis">
              <label class="col-sm-2 control-label">Jenis Kelamin</label>

              <div class="col-sm-8">
              {{-- <input type="text" id="email" name="email" value="" class="form-control"> --}}
                <select name="jenis" id="jenis" class="form-control select2" style="width: 100%; height:100%">
                <option value="1">Laki-Laki</option>
                <option value="2">Perempuan</option>
                </select>
              </div>
            </div>
           
            <div class="form-group">
              <label class="col-sm-2 control-label">Alamat</label>
              <div class="col-sm-8">
              <textarea type="text" id="alamat" name="alamat" value="" class="form-control"></textarea>
              </div>
            </div>
            <div class="form-group hidden" id="tanggal_member">
              <label class="col-sm-2 control-label">Member Sejak</label>
              <div class="col-sm-3">
              <input type="text" name="tanggal_awal" id="tanggal_awal" class="form-control tgl_awal datepicker">
              </div>
              <label class="col-sm-1 control-label">Ke</label>
              <div class="col-sm-3">
              <input type="text" name="tanggal_akhir" id="tanggal_akhir" class="form-control tgl_akhir datepicker">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Gudang</label>
              <div class="col-sm-8">
              <select class="form-control select2" id="gudang" name="gudang" style="width: 100%;">
                  <?php  foreach($data['gudang'] as $key => $list){ ?>
                    <option value="{{$list->id}}">{{$list->nama}}</option>
                  <?php } ?>
              </select>
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


      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  
  </div>

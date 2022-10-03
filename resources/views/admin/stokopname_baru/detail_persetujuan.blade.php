<div class="modal fade" id="modal-form-detail"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Data Stokopname</h4>
          </div>
          <div class="modal-body">
            <!-- <button class="btn btn-primary" style="margin-bottom:20px;margin-left:10px;" type="button" id="btn_verifikasi"><i class="fa  fa-check-o"></i> Verifikasi</button> -->
            <?php echo $tombol_create; ?>
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-striped table-barang" id="table_stokopnamedetail">
                    <thead>
                      <tr>
                          <th width="3%">No <input type="checkbox" id="checkAll" ></th>
                          <th width="10%">tanggal</th>
                          <th width="20$">Nama Barang</th>
                          <th width="15%">Gudang</th>
                          <th width="10%">Fisik</th>
                          <th width="10%">Selisih</th>
                          <th width="15%">Ket.</th> 
                          <th width="7%">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>

                    </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-arrow-left"></span> Batal</button>
          </div>
      </div>
  </div>
</div>

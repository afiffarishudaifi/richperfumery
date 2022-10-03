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
                <label class="col-sm-2 control-label">Kategori</label>

                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <select  id="barang_id_parent" name="barang_id_parent" class="form-control js-example-basic-single" style="width: 100%;">
                    <option value="0" >--</option>
                    @foreach($data['barang'] as $list)
                      <option  value="{{ $list->barang_id }}">{{$list->barang_nama}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Kode Barang</label>

                <div class="input-group col-sm-8">
                  <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                  <input type="text" class="form-control" required name="barang_kode" id="barang_kode" placeholder="Kode Barang" >
                  <span class="help-block with-errors"></span>
                </div>
              </div>
              <!-- /.form-group -->


            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama Barang</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" class="form-control" required name="barang_nama" id="barang_nama" placeholder="Nama Barang" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">Nama Alias Barang</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <input type="text" class="form-control"  name="alias_barang" id="alias_barang" placeholder="Nama Alias Barang" >
                <span class="help-block with-errors"></span>
              </div>
            </div>
            <!-- /.form-group -->

            <div class="form-group">
              <label class="col-sm-2 control-label">Satuan</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <select  id="satuan_id" name="satuan_id" class="form-control js-example-basic-single" style="width: 100%;">
                  <option value="0" >--Pilih Satuan--</option>
                  @foreach($data['satuan'] as $list2)
                    <option  value="{{ $list2->satuan_id }}"> {{ $list2->satuan_nama }} ({{$list2->satuan_satuan}})</option>
                  @endforeach
                </select>
              </div>
            </div>
            <!-- /.form-group -->

            <!-- <div class="form-group">
              <label class="col-sm-2 control-label">Satuan Konversi</label>

              <div class="input-group col-sm-8">
                <select  id="satuan_konversi_id" name="satuan_id_konversi" class="form-control js-example-basic-single" style="width: 100%;">
                  <option value="0" >--Pilih Satuan--</option>
                  @foreach($data['satuan'] as $list2)
                    <option  value="{{ $list2->satuan_id }}" > {{ $list2->satuan_nama }} ({{$list2->satuan_satuan}})</option>
                  @endforeach
                </select>
              </div>
            </div> -->
            <!-- /.form-group -->

            <div class="form-group">
              <label class="col-sm-2 control-label">Status Bahan</label>

              <div class="input-group col-sm-8">
                <!-- <span class="input-group-addon"><i class="fa  fa-user"></i></span> -->
                <select  id="barang_status_bahan" name="barang_status_bahan" class="form-control" style="width: 100%;">
                  <option value="1" >Bahan Baku</option>
                  <option value="2" >Pendukung</option>
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

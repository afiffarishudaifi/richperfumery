<style type="text/css">
  .barcode-qr .img-responsive {
    margin: 0 auto;
}
</style>
<div class="modal" id="modal-form_viewcode" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
          <h4 class="modal-title_viewcode"></h4>
        </div>
      <div class="modal-body">
        <form class="form form_filter" method="get" target="blank" action="{{url('barangprint_cetakonemany')}}" autocomplete="off">
        <input type="hidden" name="barcode_barang" id="barcode_barang">
        <div class="col-sm-12">
          <div class="col-sm-8">
          <div class="form-group">
              <label class="control-label">Satuan</label>
                <select id="barcode_satuan" name="barcode_satuan" class="form-control js-example-basic-single" style="width: 100%;">
                  <option value="0">--Pilih Satuan--</option>
                  @foreach($data['satuan'] as $list2)
                    <option  value="{{ $list2->satuan_id }}"> {{ $list2->satuan_nama }} ({{$list2->satuan_satuan}})</option>
                  @endforeach
                </select>
            </div>
          </div>

          <div class="col-md-4">
             <div class="form-group">
             <button type="submit" style="margin-top: 20px;" class="btn btn-success"><b><i class="fa fa-print"></i></b> Cetak</button>
           </div>                    
          </div>

          </div>
          <div class="center">
            <center><img src="" class="barcode-qr"></center>
          </div>
        </form>
        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

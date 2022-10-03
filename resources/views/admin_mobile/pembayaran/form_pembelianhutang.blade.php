<div class="modal fade" id="modal-form"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">

      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title titleform">Form Barang</h4>
          </div>
          <form action="#" role="form" class="form-horizontal" autocomplete="off" id="form_pembayaran">
          <input type="hidden" name="popup_id_table" id="popup_id_table">
          <input type="hidden" name="td_uangmuka" id="td_uangmuka">
          <input type="hidden" name="td_ongkir" id="td_ongkir">
          <input type="hidden" name="td_potongan" id="td_potongan">
          <input type="hidden" name="td_bayar" id="td_bayar">
          <input type="hidden" name="td_pajak" id="td_pajak">
          <input type="hidden" name="td_subtotal" id="td_subtotal">
          <input type="hidden" name="popup_carabayar" id="popup_carabayar">
          <input type="hidden" name="popup_tanggal_bayar" id="popup_tanggal_bayar">
          <div class="modal-body">
            <div class="row">             
              <div class="col-md-6">
              <table class="table table-bordered table-hover table-striped" width="40%">
                  <tbody>
                    <tr>
                      <td width="30%">Supplier</td>
                      <td width="3%">:</td>
                      <td id="popup_supplier"></td>
                    </tr>
                    <tr>
                      <td width="30%">Tanggal Input</td>
                      <td width="3%">:</td>
                      <td id="popup_tanggal"></td>
                    </tr>
                    <tr>
                      <td width="30%">Tanggal Faktur</td>
                      <td width="3%">:</td>
                      <td id="popup_tanggal_faktur"></td>
                    </tr>
                    <tr>
                      <td width="30%">Tanggal Tempo</td>
                      <td width="3%">:</td>
                      <td id="popup_tanggal_tempo"></td>
                    </tr>
                    <tr>
                      <td width="30%">Nomor Faktur</td>
                      <td width="3%">:</td>
                      <td id="popup_nomor"></td>
                    </tr>
                    <tr>
                      <td width="30%">Gudang</td>
                      <td width="3%">:</td>
                      <td id="popup_gudang"></td>
                    </tr>
                    <tr>
                      <td width="30%">Keterangan</td>
                      <td width="3%">:</td>
                      <td id="popup_keterangan"></td>
                    </tr>
                  </tbody>
              </table>
              </div>
              <style>  
                .alert2 {
                    padding: 15px;
                    margin-bottom: 20px;
                    border: 1px solid transparent;
                    border-radius: 3px;
                }            
                .alert2-info {
                    color: #00545c;
                    background-color: #e0f7fa;
                    border-color: #00bcd4;
                    color: #00838f;
                }
                
                
              </style>
              <div class="col-md-6">
              <div class="alert2 alert2-info">
              <table class="table table-total">
                <tbody>
                  <tr style="font-size: 20px;">
                    <th width="25%">Pembayaran</th>
                    <th class="text-right" id="popup_bayar"></th>
                  </tr>
                  <tr>
                    <td>Tagihan</td>
                    <td class="text-right" id="popup_tagihan"></td>
                  </tr>
                  <tr id="tr_uangmuka">
                    <td>Uang Muka</td>
                    <td class="text-right" id="popup_uangmuka"></td>
                  </tr>
                  <tr style="font-size: 15px;">
                    <th class="text-left">Sisa Tagihan</th>
                    <th class="text-right" id="popup_sisa"></th>
                  </tr>
                </tbody>
              </table>
            </div>
              <div class="form-group">
                <label class="col-md-2">Status</label>
                <div class="col-md-6">
                  <select class="form-control select2" name="popup_status" style="width: 100%;">
                    <option value="3">Belum Bayar</option>
                    <option value="1">Lunas</option>
                    <option value="2">Uang Muka</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <button type="submit" class="btn btn-success btn-save" id="btn_popup_simpan"><i class="fa fa-money"></i> Simpan </button>
                </div>
              </div>
              </div>               
            </div>

            <div class="row">
                <div class="col-md-12">                  
                  <table class="table table-bordered table-hover table-striped" id="table_barang">
                    <thead>
                      <tr>
                        <th width="3%">No.</th>
                        <th width="42%">Nama Produk</th>
                        <th width="20%">Harga</th>
                        <th width="15%">Jumlah</th>
                        <th width="20%">Total</th>
                      </tr>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
              </div>
              </div>

          </div>
          <div class="clearix"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
          </div>

      </form>
      </div>
  </div>
</div>




<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('auth/login');
// });
// Route::get('/','LoginController@index');
Route::get('/', 'HomeController@index');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('get_omset', 'HomeController@getomset')->name('get_omset');
Route::get('gettabelproduk', 'HomeController@gettabelproduk')->name('gettabelproduk');
Route::get('getbarangbanyak', 'HomeController@getbarangbanyak')->name('getbarangbanyak');
Route::get('getbarangpilihan', 'HomeController@getbarangpilihan')->name('getbarangpilihan');
Route::get('getjumlahnota', 'HomeController@getjumlahnota')->name('getjumlahnota');
Route::get('gettabelpelanggan', 'HomeController@gettabelpelanggan')->name('gettabelpelanggan');
Route::get('getjumlahomset', 'HomeController@getjumlahomset')->name('getjumlahomset');
Route::get('getjumlahomsetdanongkir', 'HomeController@getjumlahomsetdanongkir')->name('getjumlahomsetdanongkir');

Route::group(['middleware' => 'auth'], function () {

  Route::get('index_admin.html', 'PecahTemplateAdminController@index');
  Route::resource('admin_user', 'AdminUserController');

//MASTER ===================================================================================================================================
//======================= master m program ==============
  Route::resource('mprogram','MprogramController');
  Route::get('data_program','MprogramController@listData')->name('data_program');

  Route::group(['namespace'=> 'Pengiriman'], function(){
    //PENGIRIMAN BARANG
    Route::resource('pengiriman', 'IPengirimanController');
    Route::get('data_pengiriman', 'IPengirimanController@listData')->name('data_pengiriman');
    Route::get('select2_pengiriman', 'IPengirimanController@select2')->name('select2_pengiriman');
    Route::post('pengiriman_hapus','IPengirimanController@hapus')->name('pengiriman_hapus');
    /*Search*/
    Route::get('pengiriman_searchtanggal','IPengirimanController@searchtanggal')->name('pengiriman_searchtanggal');
    /*End Search*/
    //DETAIL PENGIRIMAN
    Route::get('detail_pengiriman', 'DPengirimanController@index')->name('detail_pengiriman');    
    Route::get('select2barang', 'DPengirimanController@select2barang')->name('select2barang');
    Route::get('tampildetailpengiriman', 'DPengirimanController@listData')->name('tampildetailpengiriman');
    Route::post('detailpengiriman_kirimbarang', 'DPengirimanController@kirimbarang')->name('detailpengiriman_kirimbarang');
    Route::post('detailpengiriman_kirimbarang_peritem', 'DPengirimanController@kirimbarang_peritem')->name('detailpengiriman_kirimbarang_peritem');
    Route::post('detailpengiriman_hapus','DPengirimanController@hapus')->name('detailpengiriman_hapus');
    Route::post('simpanpersetujuan', 'DPengirimanController@simpandata')->name('simpanpersetujuan');
    Route::get('detailpengiriman_terima', 'DPengirimanController@terimasemua')->name('detailpengiriman_terima');
    Route::resource('detailpengiriman','DPengirimanController');
    Route::post('detailpengiriman/simpan_detail','DPengirimanController@simpan_detail');
    Route::get('detailpengiriman/show','DPengirimanController@show');
    Route::post('detailpengiriman/kirimbarang_detail','DPengirimanController@kirimbarang_detail');
    Route::post('detailpengiriman/hapus_detail','DPengirimanController@hapus_detail');
    //RETUR
    Route::resource('pengirimanreturindex', 'PengirimanreturController');
    Route::get('pengirimanreturliat', 'PengirimanreturController@lihatdata')->name('pengirimanreturliat');
    Route::get('select2pengiriman', 'PengirimanreturController@select2')->name('select2pengiriman');
    Route::get('select2pengirimanb', 'PengirimanreturController@select2barang')->name('select2pengirimanb');
    Route::post('simpanretur', 'PengirimanreturController@store')->name('simpanretur');
    Route::post('simpanreturkirim', 'PengirimanreturController@kirim')->name('simpanreturkirim');
    Route::post('pengirimanretur_noauto','PengirimanreturController@noauto2')->name('pengirimanretur_noauto');
    /*Search*/
    Route::get('pengirimanretur_searchtanggal','PengirimanreturController@searchtanggal')->name('pengirimanretur_searchtanggal');
    /*End Search*/
    //PENERIMAAN
    Route::resource('pengiriman_penerimaan', 'PengirimanpenerimaanController');
    Route::get('pengirimanrpenerimaanliat', 'PengirimanpenerimaanController@lihatdata')->name('pengirimanrpenerimaanliat');
    Route::post('pengirimanrpenerimaansetuju', 'PengirimanpenerimaanController@setuju')->name('pengirimanrpenerimaansetuju');
    /*Search*/
    Route::get('pengirimanpenerimaan_searchtanggal','PengirimanpenerimaanController@searchtanggal')->name('pengirimanpenerimaan_searchtanggal');
    /*End Search*/
    //cetak nota
    Route::get('cetaknotapengiriman', 'IPengirimanController@cetak')->name('cetaknotapengiriman');
    Route::get('penerimaanreturpengiriman_create','PengirimanpenerimaanController@retur_create')->name('penerimaanreturpengiriman_create');
    Route::post('penerimaanreturpengiriman_simpan','PengirimanpenerimaanController@simpanretur')->name('penerimaanreturpengiriman_simpan');
    // Route::post('penerimaanreturpengiriman_simpandetail','PengirimanpenerimaanController@simpanretur_detail')->name('penerimaanreturpengiriman_simpandetail');

    //Penerimaan pengiriman
    Route::resource('penerimaanpengiriman','PenerimaanpengirimanController');
    Route::get('penpengirimanlistData','PenerimaanpengirimanController@listData')->name('penpengirimanlistData');
    Route::get('detail_penerimaanpengiriman', 'PenerimaanpengirimanController@data')->name('detail_penerimaanpengiriman');
    Route::get('penerimaanpengiriman_searchtanggal','PenerimaanpengirimanController@searchtanggal')->name('penerimaanpengiriman_searchtanggal');
    Route::get('penerimaanpengiriman_tambah','PenerimaanpengirimanController@create')->name('penerimaanpengiriman_tambah');
    Route::get('penerimaanpengiriman_get_gudang','PenerimaanpengirimanController@get_gudang')->name('penerimaanpengiriman_get_gudang');
    Route::post('penerimaanpengiriman_get_edit','PenerimaanpengirimanController@get_edit')->name('penerimaanpengiriman_get_edit');
    Route::post('penerimaanpengiriman_simpan','PenerimaanpengirimanController@simpan')->name('penerimaanpengiriman_simpan');
    Route::get('penerimaanpengiriman_edit','PenerimaanpengirimanController@edit')->name('penerimaanpengiriman_edit');
    

  });
  //GUDANG PENGIRIMAN
  //Route::get('gudang_pengiriman','Gudang\TaGudangController');

  Route::group(['namespace' => 'Kasir'], function(){
    //KASIR
    Route::resource('kasir','Kasircontroller');
    Route::get('kasir_data','Kasircontroller@listData')->name('kasir_data');
    Route::get('kasir_tambah','Kasircontroller@create')->name('kasir_tambah');
    Route::get('kasir_get_penyedia','Kasircontroller@get_penyedia')->name('kasir_get_penyedia');
    Route::get('kasir_get_barang', 'Kasircontroller@get_barang')->name('kasir_get_barang');
    Route::get('kasir_get_produk', 'Kasircontroller@get_produk')->name('kasir_get_produk');
    Route::get('kasir_get_pelanggan', 'Kasircontroller@get_pelanggan')->name('kasir_get_pelanggan');
    Route::post('kasir_attr_pelanggan','Kasircontroller@attr_pelanggan')->name('kasir_attr_pelanggan');
    Route::post('kasir_simpan','Kasircontroller@simpan')->name('kasir_simpan');
    Route::get('kasir_edit/{id}','Kasircontroller@edit')->name('kasir_edit');
    Route::post('kasir_get_edit','Kasircontroller@get_edit')->name('kasir_get_edit');
    Route::post('kasir_hapus','Kasircontroller@hapus')->name('kasir_hapus');
    Route::get('kasir_cetak/{id}/{keterangan?}','Kasircontroller@cetak_laporan')->name('kasir_cetak');
    Route::post('kasir_simpan_pelanggan','Kasircontroller@simpan_pelanggan')->name('simpan_pelanggan');
    Route::get('kasir_detail/{id}','Kasircontroller@detail')->name('kasir_detail');
    Route::post('kasir_simpan_infocetak','Kasircontroller@simpan_infocetak')->name('kasir_simpan_infocetak');
    Route::get('kasir_simpan_infocetak_mobile/{id_kasir}/{keterangan?}','Kasircontroller@simpan_infocetak_mobile')->name('kasir_simpan_infocetak_mobile');
    /*Search*/
    Route::get('kasir_searchtanggal','Kasircontroller@searchtanggal')->name('kasir_searchtanggal');
    /*End Search*/
    Route::post('kasir_get_nota','Kasircontroller@get_nota')->name('kasir_get_nota');
    
    //PEMBATALAN
    Route::resource('kasir_pembatalan','Pembatalancontroller');
    Route::get('kasir_pembatalan_data','Pembatalancontroller@listData')->name('kasir_pembatalan_data');
    Route::get('kasir_pembatalan_tambah','Pembatalancontroller@create')->name('kasir_pembatalan_tambah');
    Route::get('kasir_pembatalan_get_penyedia','Pembatalancontroller@get_penyedia')->name('kasir_pembatalan_get_penyedia');
    Route::get('kasir_pembatalan_get_barang', 'Pembatalancontroller@get_barang')->name('kasir_pembatalan_get_barang');
    Route::get('kasir_pembatalan_get_produk', 'Pembatalancontroller@get_produk')->name('kasir_pembatalan_get_produk');
    Route::get('kasir_pembatalan_get_pelanggan', 'Pembatalancontroller@get_pelanggan')->name('kasir_pembatalan_get_pelanggan');
    Route::post('kasir_pembatalan_attr_pelanggan','Pembatalancontroller@attr_pelanggan')->name('kasir_pembatalan_attr_pelanggan');
    Route::post('kasir_pembatalan_simpan','Pembatalancontroller@simpan')->name('kasir_pembatalan_simpan');
    Route::get('kasir_pembatalan_edit/{id}','Pembatalancontroller@edit')->name('kasir_pembatalan_edit');
    Route::post('kasir_pembatalan_get_edit','Pembatalancontroller@get_edit')->name('kasir_pembatalan_get_edit');
    Route::post('kasir_pembatalan_hapus','Pembatalancontroller@hapus')->name('kasir_pembatalan_hapus');
    Route::get('kasir_pembatalan_cetak/{id}','Pembatalancontroller@cetak_laporan')->name('kasir_pembatalan_cetak');
    Route::post('kasir_pembatalan_simpan_pelanggan','Pembatalancontroller@simpan_pelanggan')->name('simpan_pembatalan_pelanggan');
    Route::get('kasir_pembatalan_detail/{id}','Pembatalancontroller@detail')->name('kasir_pembatalan_detail');
    Route::post('kasir_pembatalan_simpan_infocetak','Pembatalancontroller@simpan_infocetak')->name('kasir_pembatalan_simpan_infocetak');
    /*Search*/
    Route::get('kasir_pembatalan_searchtanggal','Pembatalancontroller@searchtanggal')->name('kasir_pembatalan_searchtanggal');
    /*End Search*/
    Route::post('kasir_get_nota','Pembatalancontroller@get_nota')->name('kasir_pembatalan_get_nota');

    Route::post('kasir_get_edit_retur','ReturKasirControllerBaru@get_edit_retur')->name('kasir_get_edit_retur');
    Route::get('kasir_retur/{id}','ReturKasirControllerBaru@retur')->name('kasir_retur');
    Route::post('simpan_kasir_retur','ReturKasirControllerBaru@simpan_retur')->name('kasir_retur_simpan');
    // Route::get('kasir_noauto','Kasircontroller@noauto')->name('kasir_noauto');
    
    Route::resource('laporan_kasir','LaporanKasircontroller');

    //POSTING KASIR
    Route::resource('kasirposting','PostingKasirController');
    Route::get('kasirposting_data','PostingKasirController@listData')->name('kasirposting_data');
    Route::post('kasirposting_simpan','PostingKasirController@simpan')->name('kasirposting_simpan');

    //CLOSING KASIR
    Route::resource('kasirclosing','ClosingKasirController');
    Route::get('kasirclosing_get_omset','ClosingKasirController@get_omset')->name('kasirclosing_get_omset');
    Route::get('kasirclosing_get_jumlahnota','ClosingKasirController@get_jumlahnota')->name('kasirclosing_get_jumlahnota');
    Route::get('kasirclosing_get_jumlahomset','ClosingKasirController@get_jumlahomset')->name('kasirclosing_get_jumlahomset');
    Route::get('kasirclosing_get_checkclosing','ClosingKasirController@get_checkclosing')->name('kasirclosing_get_checkclosing');
    Route::get('kasirclosing_get_uncheckclosing','ClosingKasirController@get_uncheckclosing')->name('kasirclosing_get_uncheckclosing');
    Route::get('kasirclosing_get_jumlahomsetdanongkir','ClosingKasirController@get_jumlahomsetdanongkir')->name('kasirclosing_get_jumlahomsetdanongkir');
    Route::get('kasirclosing_get_tabsproduk','ClosingKasirController@get_tabsproduk')->name('kasirclosing_get_tabsproduk');
    Route::get('kasirclosing_get_tabsbarang','ClosingKasirController@get_tabsbarang')->name('kasirclosing_get_tabsbarang');
    Route::get('kasirclosing_get_tabsprodukpernota','ClosingKasirController@get_tabsprodukpernota')->name('kasirclosing_get_tabsprodukpernota');
    Route::get('kasirclosing_get_tabsbarangpernota','ClosingKasirController@get_tabsbarangpernota')->name('kasirclosing_get_tabsbarangpernota');
    Route::get('kasirclosing_get_tabsbotol','ClosingKasirController@get_tabsbotol')->name('kasirclosing_get_tabsbotol');
    /*---Cetakclosing---*/
    Route::get('kasirclosing_get_print/{id}/{tanggal}','ClosingKasirController@get_printclosing')->name('kasirclosing_get_print');
    /*---endCetakclosing---*/

    //PEMBAYARAN KASIR
    Route::resource('kasirpembayaran','PembayaranKasirController');
    Route::get('kasirpembayaran_data','PembayaranKasirController@listData')->name('kasirpembayaran_data');
    Route::get('kasirpembayaran_bayar/{id}','PembayaranKasirController@bayar')->name('kasirpembayaran_bayar');
    Route::post('kasirpembayaran_simpan','PembayaranKasirController@simpan')->name('kasirpembayaran_simpan');
    Route::post('kasirpembayaran_get_detail','PembayaranKasirController@get_detail')->name('kasirpembayaran_get_detail');

    //RETUR KASIR
    Route::resource('kasirretur', 'ReturKasirController');
    Route::get('kasirretur_data','ReturKasirController@listData')->name('kasirretur_data');
    Route::get('kasirretur_get_barang','ReturKasirController@get_barang')->name('kasirretur_get_barang');
    Route::get('kasirretur_get_pelanggan','ReturKasirController@get_pelanggan')->name('kasirretur_get_pelanggan');
    Route::post('kasirretur_simpan','ReturKasirController@simpan')->name('kasirretur_simpan');
    Route::get('kasirretur_edit/{id}','ReturKasirController@edit')->name('kasirretur_edit');
    Route::post('kasirretur_hapus','ReturKasirController@hapus')->name('kasirretur_hapus');
    /*Search*/
    Route::get('kasirretur_searchtanggal','ReturKasirController@searchtanggal')->name('kasirretur_searchtanggal');
    /*End Search*/

    //SEARCH PELANGGAN
    Route::resource('kasirpelanggan','PelangganKasirController');
    Route::get('kasirpelanggan_data','PelangganKasirController@list_data')->name('kasirpelanggan_data');
    
    //RETUR KASIR BARU
    Route::resource('kasirreturbaru', 'ReturKasirControllerBaru');
    Route::get('kasirreturbaru_data','ReturKasirControllerBaru@listData')->name('kasirreturbaru_data');
    Route::get('kasirreturbaru_get_barang','ReturKasirControllerBaru@get_barang')->name('kasirreturbaru_get_barang');
    Route::get('kasirreturbaru_get_pelanggan','ReturKasirControllerBaru@get_pelanggan')->name('kasirreturbaru_get_pelanggan');
    Route::post('kasirreturbaru_simpan','ReturKasirControllerBaru@simpan')->name('kasirreturbaru_simpan');
    Route::get('kasirreturbaru_edit/{id}','ReturKasirControllerBaru@edit')->name('kasirreturbaru_edit');
    Route::post('kasirreturbaru_hapus','ReturKasirControllerBaru@hapus')->name('kasirreturbaru_hapus');
    Route::get('kasirreturbaru_detail/{id}','ReturKasirControllerBaru@detail')->name('kasirreturbaru_detail');
    Route::post('kasirreturbaru_get_edit','ReturKasirControllerBaru@get_edit')->name('kasirreturbaru_get_edit');
    Route::post('kasirretur_baru_attr_pelanggan','ReturKasirControllerBaru@attr_pelanggan')->name('kasirretur_baru_attr_pelanggan');
    /*Search Baru*/
    Route::get('kasirreturbaru_searchtanggal','ReturKasirControllerBaru@searchtanggal')->name('kasirreturbaru_searchtanggal');
    /*End Search Baru*/
    
  });
  //pelanggan
  Route::group(['namespace' => 'Pelanggan'], function () {
    Route::resource('pelanggan', 'PelangganController');
    Route::get('pelangganlihatdata', 'PelangganController@lihatdata')->name('pelangganlihatdata');
    Route::post('pelanggansimpandata', 'PelangganController@store')->name('pelanggansimpandata');
    Route::get('pelanggancekstatus','PelangganController@cek_status_pelanggan')->name('pelanggancekstatus');    
  });
  //penjualan
  Route::group(['namespace' => 'Penjualan'], function () {
    Route::resource('penjualangrosir', 'PenjualangrosirController');
    Route::get('penjualangrosir_data', 'PenjualangrosirController@listData')->name('penjualangrosir_data');
    Route::get('penjualangrosir_tambah', 'PenjualangrosirController@create')->name('penjualangrosir_tambah');
    Route::get('penjualangrosir_get_pelanggan', 'PenjualangrosirController@get_pelanggan')->name('penjualangrosir_get_pelanggan');
    Route::get('penjualangrosir_get_penyedia', 'PenjualangrosirController@get_penyedia')->name('penjualangrosir_get_penyedia');
    Route::get('penjualangrosir_get_barang', 'PenjualangrosirController@get_barang')->name('penjualangrosir_get_barang');
    Route::post('penjualangrosir_simpan', 'PenjualangrosirController@simpan')->name('penjualangrosir_simpan');
    Route::get('penjualangrosir_edit/{id}', 'PenjualangrosirController@edit')->name('penjualangrosir_edit');
    Route::post('penjualangrosir_get_edit', 'PenjualangrosirController@get_edit')->name('penjualangrosir_get_edit');
    Route::get('penjualangrosir_getsatuan', 'PenjualangrosirController@getsatuan')->name('penjualangrosir_getsatuan');
    Route::post('penjualangrosir_hapus', 'PenjualangrosirController@hapus')->name('penjualangrosir_hapus');
    Route::get('penjualangrosir_cetak/{id}','PenjualangrosirController@cetak_laporan')->name('penjualangrosir_cetak');
    //get barang
    Route::get('penjualangrosir_get_barang', 'PenjualangrosirController@get_barang')->name('penjualangrosir_get_barang');
    Route::get('penjualangrosir_getgudang', 'PenjualangrosirController@getgudang')->name('penjualangrosir_getgudang');
    Route::get('penjualangrosir_detail/{id}', 'PenjualangrosirController@detail')->name('penjualangrosir_detail');
    /*Search*/
    Route::get('penjualangrosir_searchtanggal','PenjualangrosirController@searchtanggal')->name('penjualangrosir_searchtanggal');
    /*End Search*/
    Route::post('penjualangrosir_get_stok','PenjualangrosirController@get_stok')->name('penjualangrosir_get_stok');

    //PEMBAYARAN KASIR
    Route::resource('penjualanpembayaran','PembayaranPenjualanController');
    Route::get('penjualanpembayaran_data','PembayaranPenjualanController@listData')->name('penjualanpembayaran_data');
    Route::get('penjualanpembayaran_bayar/{id}','PembayaranPenjualanController@bayar')->name('penjualanpembayaran_bayar');
    Route::post('penjualanpembayaran_simpan','PembayaranPenjualanController@simpan')->name('penjualanpembayaran_simpan');
    Route::post('penjualanpembayaran_get_detail','PembayaranPenjualanController@get_detail')->name('penjualanpembayaran_get_detail');
});

//Profil
Route::group(['namespace' => 'Profil'], function () {
    Route::resource('profil', 'ProfilController');
    Route::get('profillihatdata', 'ProfilController@lihatdata')->name('profillihatdata');
    Route::post('profilsimpandata', 'ProfilController@store')->name('profilsimpandata');
   
});

//metode
Route::group(['namespace' => 'Metode'], function () {
    Route::resource('metode', 'MetodePembayaranController');
    Route::get('metodelihatdata', 'MetodePembayaranController@lihatdata')->name('metodelihatdata');
    Route::post('metodesimpandata', 'MetodePembayaranController@store')->name('metodesimpandata');
});

//produk
Route::group(['namespace' => 'Produk'], function(){
  Route::resource('produk', 'ProdukController');    
  Route::get('select2barang2', 'ProdukController@select2barang')->name('select2barang2');
  Route::get('produk_tambah', 'ProdukController@tambah');
  Route::get('produk_listdata', 'ProdukController@listData')->name('produk_listdata');
  Route::get('produk_edit', 'ProdukController@edit')->name('produk_edit');
  Route::post('produk_getedit', 'ProdukController@getedit_data')->name('produk_getedit');
  Route::post('produk_simpan_detail', 'ProdukController@simpan');
  Route::post('produk_hapus','ProdukController@hapus')->name('produk_hapus');
});
//promo
Route::group(['namespace' => 'promo'], function(){
  Route::resource('promo', 'PromoController');
  Route::get('promo_edit/{id}', 'PromoController@edit');
  Route::post('promo_simpan', 'PromoController@simpan');
});

//GUDANG
Route::group(['namespace' => 'Gudang'], function () {
  Route::resource('refgudang', 'RefGudangController');
  Route::get('data_refgudang', 'RefGudangController@listData')->name('data_refgudang');
  Route::get('select2profil', 'RefGudangController@select2profil')->name('select2profil');
    
});

//MAPPING
  Route::resource('mapping','MappingakunController');
  Route::get('lihat','MappingakunController@dataprogram')->name('lihat');

// laporan penjualana
  Route::group(['namespace' => 'Penjualan'], function () {
    Route::resource('laporanpenjualan', 'LaporanpenjualanController');
    Route::get('cetaklaporan', 'LaporanpenjualanController@cetaklaporan')->name('cetaklaporan');
      
  });

//  ================================================ BARANG ==========================================================
  Route::resource('barang', 'BarangController');
  Route::get('data_barang', 'BarangController@listData')->name('data_barang');
  Route::post('barang_code', 'BarangController@barcode')->name('barang_code');
  Route::post('barang_detailcode', 'BarangController@barcode_detail')->name('barang_detailcode');
  
  Route::resource('barang_closing','BarangClosingController');
  Route::get('barang_closing_get_data','BarangClosingController@get_data');
  Route::get('barang_closing_get_barang','BarangClosingController@get_barang');
  Route::get('barang_closing_edit/{id}','BarangClosingController@edit');
  Route::post('barang_closing_simpan','BarangClosingController@simpan');
  Route::post('barang_closing_hapus','BarangClosingController@hapus');
//  ================================================ END BARANG ==========================================================

//  ================================================ DETAIL HARGA BARANG ==========================================================
  Route::resource('detail_harga_barang', 'DetailHargaBarangController');
  Route::get('data_detail_harga_barang/{barang_id}', 'DetailHargaBarangController@listData');
//  ================================================ END DETAIL HARGA BARANG ==========================================================

//  ===================================== PRINT BARANG ==========================================================
Route::resource('barangprint','BarangPrintController');
Route::get('barangprint_cetakonemany','BarangPrintController@cetak_onemany');
Route::get('barangprint_cetak','BarangPrintController@cetak');
//  ===================================== END PRINT BARANG ==========================================================

//  ================================================ LOG STOK ==========================================================
  Route::resource('log_stok', 'LogStokController');
//  ================================================ END LOG STOK ==========================================================

//  ================================================ SATUAN ==========================================================
  Route::resource('satuan', 'SatuanController');
  Route::get('data_satuan', 'SatuanController@listData')->name('data_satuan');
//  ================================================ END SATUAN ==========================================================


//  ================================================ SUPPLIER ==========================================================
  Route::resource('supplier', 'SupplierController');
  Route::get('data_supplier', 'SupplierController@listData')->name('data_supplier');
//  ================================================ END SUPPLIER ==========================================================

//END MASTER ===================================================================================================================================



//MANAJEMEN USER ===================================================================================================================================

//  ================================================ GROUP ==========================================================
  Route::resource('group', 'GroupController');
  Route::get('data_group', 'GroupController@listData')->name('data_group');
//  ================================================ END GROUP ==========================================================

//  ================================================ MASTER USER ==========================================================
  Route::resource('master_user', 'MasterUserController',['only'=>['index','update','destroy']]);
  Route::post('master_user/store','MasterUserController@store');
  Route::get('master_user/get_profil','MasterUserController@get_profil');
  Route::get('master_user/get_data','MasterUserController@listData');
  Route::get('master_user/edit/{id}','MasterUserController@edit');
//  ================================================ END MASTER USER ==========================================================

//  ================================================ MENU ==========================================================
  Route::resource('menu', 'MenuController');
  Route::get('data_menu', 'MenuController@listData')->name('data_menu');
//  ================================================ END MENU ==========================================================

//  ================================================ T USER ==========================================================
  Route::resource('t_user', 'TUserController');
//  ================================================ END T USER ==========================================================

//  ================================================ POIN ==========================================================
  Route::resource('poin', 'PoinController');
  Route::post('poin_simpan','PoinController@simpan')->name('poin_simpan');
  Route::get('poin_get_data','PoinController@get_data')->name('poin_get_data');
//  ================================================ END POIN ==========================================================

//  ================================================ MAPPING POIN ==========================================================
  Route::resource('produkpoin', 'ProdukPoinController');
  Route::get('produkpoin_detail/{id}','ProdukPoinController@detail')->name('produkpoin_detail');
  Route::get('produkpoin_get_data','ProdukPoinController@get_data')->name('produkpoin_get_data');
  Route::get('produkpoin_get_data_group','ProdukPoinController@get_data_group')->name('produkpoin_get_data_group');
  Route::post('produkpoin_hapus','ProdukPoinController@hapus')->name('produkpoin_hapus');
  // Route::get('produkpoin_edit/{id}','ProdukPoinController@edit')->name('produkpoin_edit');
  Route::get('produkpoin_get_produk','ProdukPoinController@get_produk')->name('produkpoin_get_produk');
  Route::get('produkpoin_get_gudang','ProdukPoinController@get_gudang')->name('produkpoin_get_gudang');
  Route::post('produkpoin_simpan','ProdukPoinController@simpan')->name('produkpoin_simpan');
//  ================================================ END MAPPING POIN ==========================================================

//  ================================================ PEMBELIAN ==========================================================
  Route::group(['namespace'=>'Pembelian'], function(){
    //PEMBELIAN
    Route::resource('pembelian', 'PembelianController');
    Route::get('pembelian_data','PembelianController@listData')->name('pembelian_data');
    Route::post('update_total_pembelian','PembelianController@totalBarang')->name('total_pembelian');
    Route::get('pembelian_tambah','PembelianController@create')->name('pembelian_tambah');
    Route::get('pembelian_get_penyedia','PembelianController@get_penyedia')->name('pembelian_get_penyedia');
    Route::get('pembelian_get_barang','PembelianController@get_barang')->name('pembelian_get_barang');
    Route::get('pembelian_get_gudang','PembelianController@get_gudang')->name('pembelian_get_gudang');
    Route::post('pembelian_simpan','PembelianController@simpan')->name('pembelian_simpan');
    Route::get('pembelian_edit/{id}','PembelianController@edit')->name('pembelian_edit');
    Route::post('pembelian_get_edit','PembelianController@get_edit')->name('pembelian_get_edit');
    Route::post('pembelian_hapus','PembelianController@hapus')->name('pembelian_hapus');
    Route::get('pembelian_detail/{id}','PembelianController@detail')->name('pembelian_detail');
    /*Search*/
    Route::get('pembelian_searchtanggal','PembelianController@searchtanggal')->name('pembelian_searchtanggal');
    /*End Search*/ 

    //SURAT JALAN
    Route::resource('suratjalan', 'SuratJalanController');
    Route::get('suratjalan_data','SuratJalanController@listData')->name('suratjalan_data');
    Route::get('suratjalan_tambah','SuratJalanController@tambah')->name('suratjalan_tambah');
    Route::get('suratjalan_get_penyedia','SuratJalanController@get_penyedia')->name('suratjalan_get_penyedia');
    Route::get('suratjalan_get_barang','SuratJalanController@get_barang')->name('suratjalan_get_barang');
    Route::post('suratjalan_simpan','SuratJalanController@simpan')->name('suratjalan_simpan');
    Route::get('suratjalan_edit/{id}','SuratJalanController@edit')->name('suratjalan_edit');
    Route::post('suratjalan_get_edit','SuratJalanController@get_edit')->name('suratjalan_get_edit');
    Route::post('suratjalan_hapus','SuratJalanController@hapus')->name('suratjalan_hapus');
    Route::get('suratjalan_detail/{id}','SuratJalanController@detail')->name('suratjalan_detail');
    /*Search*/
    Route::get('suratjalan_searchtanggal','SuratJalanController@searchtanggal')->name('suratjalan_searchtanggal');
    /*End Search*/

    //RETUR PEMBELIAN
    Route::resource('pembelianretur', 'ReturPembelianController');;
    Route::get('pembelianretur_data','ReturPembelianController@listData')->name('pembelianretur_data');
    Route::get('pembelianretur_get_barang','ReturPembelianController@get_barang')->name('pembelianretur_get_barang');
    Route::get('pembelianretur_get_supplier','ReturPembelianController@get_supplier')->name('pembelianretur_get_supplier');
    Route::post('pembelianretur_simpan','ReturPembelianController@simpan')->name('pembelianretur_simpan');
    Route::get('pembelianretur_edit/{id}','ReturPembelianController@edit')->name('pembelianretur_edit');
    Route::post('pembelianretur_hapus','ReturPembelianController@hapus')->name('pembelianretur_hapus');
    Route::post('pembelianretur_get_stok','ReturPembelianController@get_stok')->name('pembelianretur_get_stok');
    Route::post('pembelianretur_hapus','ReturPembelianController@hapus')->name('pembelianretur_hapus');
    /*Search*/
    Route::get('pembelianretur_searchtanggal','ReturPembelianController@searchtanggal')->name('pembelianretur_searchtanggal');
    /*End Search*/

    //PEMBAYARAN PEMBELIAN
    Route::resource('pembelianpembayaran','PembayaranPembelianController');
    Route::get('pembelianpembayaran_data','PembayaranPembelianController@listData')->name('pembelianpembayaran_data');
    //Route::get('pembelianpembayaran_bayar/{id}','PembayaranPembelianController@bayar')->name('pembelianpembayaran_bayar');
    Route::post('pembelianpembayaran_simpan','PembayaranPembelianController@simpan')->name('pembelianpembayaran_simpan');
    Route::post('pembelianpembayaran_get_detail','PembayaranPembelianController@get_detail')->name('pembelianpembayaran_get_detail');

  });
//  ================================================ END PEMBELIAN ==========================================================
  
// pengirim
Route::group(['namespace' => 'Pengirim'], function () {
    Route::resource('pengirim', 'PengirimController');
    Route::get('pengirimlihatdata', 'PengirimController@lihatdata')->name('pengirimlihatdata');
    Route::post('pengirimsimpandata', 'PengirimController@store')->name('pengirimsimpandata');

});

//  ================================================ INVENTORI ==========================================================
  Route::group(['namespace' => 'Inventori'], function(){    
    //STOKOPNAME
    Route::resource('stokopname', 'StokopnameController');
    Route::get('stokopname_data', 'StokopnameController@listData')->name('stokopname_data');
    Route::get('stokopname_tambah','StokopnameController@tambah')->name('stokopname_tambah');
    Route::get('stokopname_get_gudang','StokopnameController@get_gudang')->name('stokopname_get_gudang');
    Route::get('stokopname_get_barang','StokopnameController@get_barang')->name('stokopname_get_barang');
    Route::post('stokopname_simpan','StokopnameController@simpan')->name('stokopname_simpan');
    Route::get('stokopname_edit/{id}','StokopnameController@edit')->name('stokopname_edit');
    Route::post('stokopname_get_edit','StokopnameController@get_edit')->name('stokopname_get_edit');
    Route::post('stokopname_hapus','StokopnameController@hapus')->name('stokopname_hapus');



    //PERSETUJUAN STOKOPNAME
    Route::resource('persetujuanopname', 'PersetujuanOpnameController');
    Route::get('persetujuanopname_data', 'PersetujuanOpnameController@listData')->name('persetujuanopname_data');
    Route::get('persetujuanopname_tambah/{id}','PersetujuanOpnameController@tambah')->name('persetujuanopname_tambah');
    Route::post('persetujuanopname_get_edit','PersetujuanOpnameController@get_edit')->name('persetujuanopname_get_edit');
    Route::get('persetujuanopname_get_gudang','PersetujuanOpnameController@get_gudang')->name('persetujuanopname_get_gudang');
    Route::get('persetujuanopname_get_barang','PersetujuanOpnameController@get_barang')->name('persetujuanopname_get_barang');
    Route::post('persetujuanopname_simpan','PersetujuanOpnameController@simpan')->name('persetujuanopname_simpan');
    Route::post('persetujuanopname_simpanmulti','PersetujuanOpnameController@simpan_multi')->name('persetujuanopname_simpanmulti');    
    Route::get('persetujuanopname_get_data','PersetujuanOpnameController@get_data')->name('persetujuanopname_get_data');
    //PERSEDIAAN
    Route::resource('persediaan','PersediaanController');
    Route::get('persediaan_data','PersediaanController@listData')->name('persediaan_data');

    //PENERIMAAN BARANG
    Route::resource('penerimaanbarang','PenerimaanPembelianController');
    Route::get('penerimaan_data', 'PenerimaanPembelianController@listData')->name('penerimaan_data');
    Route::get('penerimaan_tambah/{id}','PenerimaanPembelianController@create')->name('penerimaan_create');
    Route::post('penerimaan_get_edit','PenerimaanPembelianController@get_edit')->name('penerimaan_get_edit');
    //Route::get('penerimaan_get_edit/{id}','PenerimaanPembelianController@get_edit')->name('penerimaan_get_edit');
    Route::get('penerimaan_get_gudang','PenerimaanPembelianController@get_gudang')->name('penerimaan_get_gudang');
    Route::post('penerimaan_simpan','PenerimaanPembelianController@simpan')->name('penerimaan_simpan');
    Route::get('penerimaan_get_data','PenerimaanPembelianController@get_data')->name('penerimaan_get_data');
    Route::post('penerimaan_simpanmulti','PenerimaanPembelianController@simpan_multi')->name('penerimaan_simpanmulti');
    Route::get('penerimaan_detail/{id}','PenerimaanPembelianController@detail')->name('penerimaan_detail');
    /*Search*/
    Route::get('penerimaan_searchtanggal','PenerimaanPembelianController@searchtanggal')->name('penerimaan_searchtanggal');
    /*End Search*/
    
    //PERSEDIAAN DALAM PROSES
    Route::resource('Persediaanproses','PersediaanprosesController');
    Route::get('persedianproses','PersediaanprosesController@listData')->name('persedianproses');

    //SALDO AWAL
    Route::resource('saldoawal','SaldoawalController');
    Route::get('saldoawal_data','SaldoawalController@listData')->name('saldoawal_data');
    Route::get('saldoawal_get_barang','SaldoawalController@get_barang')->name('saldowawal_get_barang');
    Route::get('saldoawal_get_gudang','SaldoawalController@get_gudang')->name('saldowawal_get_gudang');
    Route::post('saldoawal_simpan','SaldoawalController@simpan')->name('saldoawal_simpan');
    Route::get('saldoawal_edit/{id}','SaldoawalController@edit')->name('saldoawal_edit');
    Route::post('saldoawal_hapus','SaldoawalController@hapus')->name('saldoawal_hapus');
    /*Search*/
    Route::get('saldoawal_searchtanggal','SaldoawalController@searchtanggal')->name('saldoawal_searchtanggal');
    /*End Search*/


    //KONVEERSI PERSEDIAAN
    Route::resource('konversipersediaan','KonversiPersediaanController');
    Route::get('konversipersediaan_get_barang','KonversiPersediaanController@get_barang')->name('konversipersediaan_get_barang');
    //Route::get('konversipersediaan_get_gudang','KonversiPersediaanController@get_gudang')->name('konversipersediaan_get_barang');
    Route::post('konversipersediaan_simpan','KonversiPersediaanController@simpan')->name('konversipersediaan_simpan');
    Route::get('konversipersediaan_edit/{id}','KonversiPersediaanController@edit')->name('konversipersediaan_edit');
    Route::post('konversipersediaan_hapus','KonversiPersediaanController@hapus')->name('konversipersediaan_hapus');
    Route::get('konversipersediaan_data','KonversiPersediaanController@listData')->name('konversipersediaan_data');
    Route::post('konversipersediaan_konversi','KonversiPersediaanController@konversi')->name('konversipersediaan_konversi');

    //STOKOPNAME BARU
    Route::resource('stokopnamebaru','StokopnameBaruController');
    Route::get('stokopnamebaru_data','StokopnameBaruController@listData')->name('stokopnamebaru_data');
    Route::get('stokopnamebaru_get_barang','StokopnameBaruController@get_barang')->name('stokopnamebaru_get_barang');
    Route::get('stokopnamebaru_get_gudang','StokopnameBaruController@get_gudang')->name('stokopnamebaru_get_gudang');
    Route::post('stokopnamebaru_simpan','StokopnameBaruController@simpan')->name('stokopnamebaru_simpan');
    Route::get('stokopnamebaru_edit/{id}','StokopnameBaruController@edit')->name('stokopnamebaru_edit');
    Route::post('stokopnamebaru_hapus','StokopnameBaruController@hapus')->name('stokopnamebaru_hapus');
    Route::get('stokopnamebaru_detail/{id}','StokopnameBaruController@detail')->name('stokopnamebaru_detail');
    Route::get('stokopnamebaru_dataTanggal','StokopnameBaruController@listDataTanggal')->name('stokopnamebaru_dataTanggal');
    Route::get('stokopnamebaru_data2','StokopnameBaruController@listData2')->name('stokopnamebaru_data2');
    Route::get('stokopnamebaru_update','StokopnameBaruController@update')->name('stokopnamebaru_update');
    Route::get('stokopnamebaru_update_nontanggal','StokopnameBaruController@update_nontanggal')->name('stokopnamebaru_update_nontanggal');
    /*Search*/
    Route::get('stokopnamebaru_searchtanggal','StokopnameBaruController@searchtanggal')->name('stokopnamebaru_searchtanggal');
    Route::post('stokopnamebaru_updatetanggal','StokopnameBaruController@update')->name('stokopnamebaru_updatetanggal');
    Route::get('stokopnamebaru_get_barangupdate','StokopnameBaruController@get_barang_update')->name('stokopnamebaru_get_barangupdate');
    Route::get('stokopnamebaru_get_barangupdate_only','StokopnameBaruController@get_barang_update_only')->name('stokopnamebaru_get_barangupdate_only');
    /*End Search*/

    //PERSETUJUAN STOKOPNAME BARU
    Route::resource('persetujuanopnamebaru','PersetujuanOpnameBaruController');
    Route::get('persetujuanopnamebaru_dataTanggal','PersetujuanOpnameBaruController@listDataTanggal')->name('persetujuanopnamebaru_dataTanggal');
    Route::get('persetujuanopnamebaru_data','PersetujuanOpnameBaruController@listData')->name('persetujuanopnamebaru_data');
    /*Route::post('persetujuanopnamebaru_simpanmulti','PersetujuanOpnameBaruController@simpan_multi')->name('persetujuanopnamebaru_simpanmulti');*/
    Route::get('persetujuanopnamebaru_simpanmulti','PersetujuanOpnameBaruController@simpan_multi')->name('persetujuanopnamebaru_simpanmulti');  
    /*Search*/
    Route::get('persetujuanopnamebaru_searchtanggal','PersetujuanOpnameBaruController@searchtanggal')->name('persetujuanopnamebaru_searchtanggal');
    /*End Search*/  


});
//  ================================================ END INVENTORI ==========================================================

//  ================================================ LAPORAN ==========================================================
Route::group(['namespace' => 'Laporan'], function(){ 
    //LAPORAN STOKOPNAME
    Route::resource('laporanstokopname','LaporanStokopnameController');
    Route::get('laporanstokopname_cetak','LaporanStokopnameController@cetak')->name('laporanstokopname_cetak');
    Route::get('laporanstokopname_excel/{gudang}/{tanggal_awal}/{tanggal_akhir}','LaporanStokopnameController@excel')->name('laporanstokopname_excel');
    Route::get('laporanstokopname_hasil/{gudang}/{tanggal_awal}/{tanggal_akhir}','LaporanStokopnameController@hasil')->name('laporanstokopname_hasil');
    //LAPORAN PENJUALAN
    Route::resource('laporanpenjualan','LaporanPenjualanController');
    Route::get('laporanpenjualanoutlet_cetak','LaporanPenjualanController@cetaklaporan_outlet')->name('laporanpenjualanoutlet_cetak');
    Route::get('laporanpenjualangrosir_cetak','LaporanPenjualanController@cetaklaporan_grosir')->name('laporanpenjualangrosir_cetak');
    Route::get('laporanpenjualangrosir_excel/{gudang}/{tanggal}','LaporanPenjualanController@excel_grosir')->name('laporanpenjualangrosir_excel');
    
    Route::get('laporanpenjualan_cetak','LaporanPenjualanController@cetaklaporan_penjualan')->name('laporanpenjualan_cetak');
    Route::get('laporanpenjualan_excel/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}/{barang}','LaporanPenjualanController@excel_penjualan')->name('laporanpenjualan_excel');
    Route::get('laporanpenjualan_hasil/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}/{barang}','LaporanPenjualanController@hasil_penjualan')->name('laporanpenjualan_hasil');
    Route::get('laporanpenjualan_get_barang','LaporanPenjualanController@get_barang')->name('laporanpenjualan_get_barang');
    
    //LAPORAN PEMBATALAN
    Route::resource('laporanpembatalan','LaporanpembatalanController');
    Route::get('laporanpembatalanoutlet_cetak','LaporanpembatalanController@cetaklaporan_outlet')->name('laporanpembatalanoutlet_cetak');
    Route::get('laporanpembatalangrosir_cetak','LaporanpembatalanController@cetaklaporan_grosir')->name('laporanpembatalangrosir_cetak');
    Route::get('laporanpembatalangrosir_excel/{gudang}/{tanggal}','LaporanpembatalanController@excel_grosir')->name('laporanpembatalangrosir_excel');
    
    Route::get('laporanpembatalan_cetak','LaporanpembatalanController@cetaklaporan_pembatalan')->name('laporanpembatalan_cetak');
    Route::get('laporanpembatalan_excel/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}/{barang}','LaporanpembatalanController@excel_pembatalan')->name('laporanpembatalan_excel');
    Route::get('laporanpembatalan_hasil/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}/{barang}','LaporanpembatalanController@hasil_pembatalan')->name('laporanpembatalan_hasil');
    Route::get('laporanpembatalan_get_barang','LaporanpembatalanController@get_barang')->name('laporanpembatalan_get_barang');
    
    //LAPORAN PENGIRIMAN
    Route::resource('laporanpengiriman','LaporanPengirimanController');
    Route::get('laporanpengiriman_cetak','LaporanPengirimanController@cetaklaporan_pengiriman')->name('laporanpengiriman_cetak');
    Route::get('laporanpengiriman_excel/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}','LaporanPengirimanController@excel')->name('laporanpengiriman_excel');
    Route::get('laporanpengiriman_hasil/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}','LaporanPengirimanController@hasil')->name('laporanpengiriman_hasil');
    Route::get('laporanpengiriman_excel/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}/{barang}','LaporanPengirimanController@excel')->name('laporanpengiriman_excel');
    Route::get('laporanpengiriman_hasil/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}/{barang}','LaporanPengirimanController@hasil')->name('laporanpengiriman_hasil');
    Route::get('laporanpengiriman_get_barang','LaporanPengirimanController@get_barang')->name('laporanpengiriman_get_barang');
    //LAPORAN PEMBELIAN
    Route::resource('laporanpembelian','LaporanPembelianController');
    Route::get('laporanpembelian_cetak','LaporanPembelianController@cetaklaporan_pembelian')->name('laporanpembelian_cetak');
    Route::get('laporanpembelian_excel/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}','LaporanPembelianController@excel')->name('laporanpembelian_excel');
    Route::get('laporanpembelian_hasil/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}','LaporanPembelianController@hasil')->name('laporanpembelian_hasil');
    Route::get('laporanpembelian_excel/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}/{barang}','LaporanPembelianController@excel')->name('laporanpembelian_excel');
    Route::get('laporanpembelian_hasil/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}/{barang}','LaporanPembelianController@hasil')->name('laporanpembelian_hasil');
    Route::get('laporanpembelian_get_barang','LaporanPembelianController@get_barang')->name('laporanpembelian_get_barang');
    //LAPORAN PERSEDIAAN
    Route::resource('laporanpersediaan','LaporanPersediaanController');
    Route::get('laporanpersediaan_cetak','LaporanPersediaanController@cetaklaporan_persediaan')->name('laporanpersediaan_cetak');
    Route::get('laporanpersediaan_excel/{gudang}/{tanggal_awal}/{tanggal_akhir}/{barang}','LaporanPersediaanController@excel')->name('laporanpersediaan_excel');
    Route::get('laporanpersediaan_hasil/{gudang}/{tanggal_awal}/{tanggal_akhir}/{barang}','LaporanPersediaanController@hasil')->name('laporanpersediaan_hasil');
    Route::get('laporanpersediaan_get_barang','LaporanPersediaanController@get_barang')->name('laporanpersediaan_get_barang');
    Route::get('laporanpersediaan_detail_excel/{gudang}/{barang}/{jenis}','LaporanPersediaanController@excel_detail');
    Route::get('laporanpersediaan_detail_hasil/{gudang}/{barang}/{jenis}','LaporanPersediaanController@cetak_hasil_detail');
    //LAPORAN PENJUALAN DETAIL
    Route::resource('laporanpenjualandetail','LaporanPenjualanDetailController');
    Route::get('laporanpenjualandetail_hasil/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}','LaporanPenjualanDetailController@hasil_penjualan')->name('laporanpenjualandetail_hasil');
    Route::get('laporanpenjualandetail_cetak','LaporanPenjualanDetailController@cetaklaporan_penjualan')->name('laporanpenjualandetail_cetak');
    Route::get('laporanpenjualandetail_excel/{gudang}/{tanggal_awal}/{tanggal_akhir}/{kategori}','LaporanPenjualanDetailController@excel_penjualan')->name('laporanpenjualandetail_excel');
}); 

//  ================================================ END LAPORAN ========================================================== 

//END MANAJEMEN USER ===================================================================================================================================

//  ================================================ AKUN ==========================================================
  Route::resource('satuankonversi', 'SatuankonversiController');
  Route::get('satuankonversi_data', 'SatuankonversiController@listData')->name('satuankonversi_data');
  Route::get('satuankonversi_get_data', 'SatuankonversiController@get_data')->name('satuankonversi_get_data');
  Route::post('satuankonversi_simpan','SatuankonversiController@simpan')->name('satuankonversi_simpan');
  Route::get('satuankonversi_edit/{id}','SatuankonversiController@edit')->name('satuankonversi_edit');
  Route::post('satuankonversi_hapus','SatuankonversiController@hapus')->name('satuankonversi_hapus');
//  ================================================ END AKUN ==========================================================

//  ================================================ AKUN ==========================================================
  Route::resource('akun', 'AkunController');
  Route::get('data_akun', 'AkunController@listData')->name('data_akun');
//  ================================================ END AKUN ==========================================================

Route::group(['namespace'=> 'Redeem'], function(){
    Route::resource('redeem','RedeemController');
    Route::get('redeem_tambah','RedeemController@create')->name('redeem_tambah');
    Route::get('redeem_get_produk','RedeemController@get_produk')->name('redeem_get_produk');
    Route::get('redeem_edit/{id}','RedeemController@edit')->name('redeem_edit');
    Route::get('redeem_detail/{id}','RedeemController@detail')->name('redeem_detail');
    Route::get('redeem_data','RedeemController@get_data')->name('redeem_data');
    Route::get('redeem_searchtanggal','RedeemController@get_searchtanggal')->name('redeem_searchtanggal');
    Route::post('redeem_simpan','RedeemController@simpan')->name('redeem_simpan');
    Route::post('redeem_hapus','RedeemController@hapus')->name('redeem_hapus');
    Route::post('redeem_get_edit','RedeemController@get_edit')->name('redeem_get_edit');
    Route::post('redeem_attr_pelanggan','RedeemController@attr_pelanggan')->name('redeem_attr_pelanggan');
    Route::post('redeem_attr_get_pelanggan','RedeemController@attr_get_pelanggan')->name('redeem_attr_get_pelanggan');
    Route::post('redeem_get_nota','RedeemController@get_nota')->name('redeem_get_nota');
    Route::post('redeem_simpan_infocetak','RedeemController@simpan_infocetak')->name('redeem_simpan_infocetak');
});










});

<?php

namespace App\Http\Controllers\Penjualan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use DB;
use Auth;
use Illuminate\Support\Facades\Session;
use App\KasirModel;
use App\SupplierModel;


class PenjualangrosirController extends Controller
{
    public function index(){ 
      $id_group = Auth::user()->group_id;
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $tombol_create = tombol_create(url('penjualangrosir_tambah'),$group_where->group_aktif,2);   	
    	return view('admin.penjualangrosir.index',compact('tombol_create'));
    }

    public function get_penyedia(Request $request){
      $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT 
          supplier_id, 
          supplier_nama, 
          supplier_alamat, 
          supplier_telp 
          FROM tbl_supplier WHERE supplier_nama LIKE '%$search%' order by supplier_nama asc");
        return \Response::json($d_query);
    }

    public function get_barang(Request $request){
    $term = trim($request->q);
    $gudang = trim($request->gudang);

        if (empty($term)) {
            return \Response::json([]);
        }

        $search = strtolower($term);
        $id_gudang = strtolower($gudang);
        /*$barangs= DB::select("SELECT
            l.log_stok_id,
            b.barang_id,
            b.satuan_id,
            b.barang_kode,
            b.barang_nama,
            b.barang_id_parent,
            b.barang_status_bahan,
            s.satuan_nama,
            s.satuan_satuan,
            l.jumlah_masuk,
            l.jumlah_keluar ,
            l.id_satuan 
          FROM
            (
            SELECT
              t.log_stok_id,
              t.id_barang,
              t.id_ref_gudang,
              Sum( t.unit_masuk ) AS jumlah_masuk,
              Sum( t.unit_keluar ) AS jumlah_keluar,
              t.id_satuan 
            FROM
              tbl_log_stok AS t 
            WHERE
              t.id_ref_gudang = '$id_gudang' 
            GROUP BY
              t.id_barang,
              t.id_ref_gudang,
              t.id_satuan  
            ) l
            LEFT JOIN tbl_barang AS b ON l.id_barang = b.barang_id
            LEFT JOIN tbl_satuan AS s ON l.id_satuan = s.satuan_id
                WHERE b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%'");*/
        $barangs = DB::select($this->query_get_barang($search,$id_gudang));

        return \Response::json($barangs);
    }
    public function getgudang(Request $request)
    {
      $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }
 
        $search = strtolower($term);
        $barangs= DB::select("SELECT
          g.id,
          g.id_profil,
          g.nama,
          g.alamat,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.jenis_outlet
        FROM
          ref_gudang g
          LEFT JOIN m_profil p ON g.id_profil = p.id 
        WHERE
          p.jenis_outlet =1 and g.nama LIKE '%$search%'");

        return \Response::json($barangs);

    }
    public function getsatuan(Request $request)
    {
      $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }

        $search = strtolower($term);
        $barangs= DB::select("SELECT
					*
				FROM
				tbl_satuan s
                WHERE s.satuan_nama LIKE '%$search%'");

        return \Response::json($barangs);

    }

    public function create(){
      $id_group = Auth::user()->group_id;
      $id_profil = Auth::user()->id_profil;
        if ($id_group == 5) {
            $where="WHERE  p.jenis_outlet = 2 and g.id_profil='".$id_profil."'";
        } else {
            $where='WHERE  p.jenis_outlet ';
        }
      $data['gudang']     = DB::select("SELECT
          g.id,
          g.id_profil,
          g.nama,
          g.alamat,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.jenis_outlet
        FROM
          ref_gudang g
          LEFT JOIN m_profil p ON g.id_profil = p.id $where
        ");
      $gudang = DB::select('select * from ref_gudang where id_profil="'.$id_profil.'"');
      $data['data']       = $this->data(array());     
      $data['satuan']     = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['id_gudang']     = empty($gudang[0]->id)?'':$gudang[0]->id;
      $data['rekening']   = \Config::get('constants.rekening');
      $data['carabayar']  = \Config::get('constants.carabayar');
    //   $data['pembayaran'] = DB::select("select * from m_metode");
      $data['pembayaran'] = DB::select("select * from m_metode where status = '1'");
        return view('admin.penjualangrosir.create')->with('data',$data);
    }

    public function data($data = array()){
      if($data){
        $store['id_kasir']        = $data->id_kasir;
        $store['tanggal']         = tgl_full($data->tanggal,'');
        $store['tanggal_faktur']  = tgl_full($data->tanggal_faktur,'');
        $store['tanggal_tempo']   = tgl_full($data->tanggal_tempo,'');
        // $store['id_penyedia']     = $data->id_penyedia;
        $store['id_pelanggan']    = $data->id_pelanggan;
        $store['nama_pelanggan']  = $data->nama_pelanggan;
        $store['nomor']           = $data->no_faktur;
        $store['uang_muka']       = $data->uang_muka;
        $store['gudang']          = $data->id_gudang;
        $store['id_gudang']       = $data->id_gudang;
        $store['metode']          = $data->metodebayar;
        $store['nama_gudang']     = $data->nama_gudang;
        $store['td_ongkir']       = $data->ongkos_kirim;
        $store['td_potongan']     = $data->total_potongan;
        $store['td_tagihan']      = $data->total_tagihan;
        $store['td_subtotal']     = $data->total_subtotal;
        $store['carabayar']       = $data->carabayar;
        $store['id_rek']          = $data->id_rekening;
        $store['no_rek']          = $data->no_rekening;
        $store['keterangan']      = $data->keterangan;
        

      }else{
        $store['id_kasir']    = "";
        $store['tanggal']         = "";
        $store['tanggal_faktur']  = "";
        $store['tanggal_tempo']   = "";
        $store['id_penyedia']     = "";
        $store['id_pelanggan']    = "";
        $store['nama_penyedia']   = "";
        $store['nama_pelanggan']  = "";
        $store['gudang']          = "";
        $store['id_gudang']       = "";
        $store['metode']          = "";
        $store['nama_gudang']     = "";
        $store['nomor']           = "";
        $store['uang_muka']       = "Rp 0";
        $store['td_ongkir']       = "";
        $store['td_potongan']     = "";
        $store['td_tagihan']      = "";
        $store['td_subtotal']     = "";
        $store['carabayar']       = "";
        $store['id_rek']          = "";
        $store['no_rek']          = "";
        $store['keterangan']      = "";
        

      }

      return $store;
    }

    public function simpan(Request $request){
      $id_kasir               = $request->get('id_kasir');
      $data['tanggal']        = tgl_full($request->get('tanggal'),'99');
      $data['tanggal_tempo']  = tgl_full($request->get('tanggal_tempo'),'99');
      $data['tanggal_faktur'] = tgl_full($request->get('tanggal_faktur'),'99');
      $data['no_faktur']      = $request->get('nomor');
      $data['id_pelanggan']   = $request->get('id_pelanggan');
      $data['uang_muka']      = $request->get('td_uangmuka');
      $data['ongkos_kirim']   = $request->get('td_ongkir');
      $data['carabayar']      = $request->get('carabayar');
      $data['id_rekening']    = $request->get('id_rekening');
      $data['no_rekening']    = $request->get('no_rekening');
      $data['total_potongan'] = $request->get('td_diskon');
      $data['metodebayar']    = $request->get('viabayar');
      
      $data['total_tagihan']  = $request->get('td_total');
      $data['keterangan']     = $request->get('keterangan');
      $data['id_gudang']      = $request->get('gudang');
      $data['status']         = '1';
      $data['jenis_transaksi'] = '2';

      $tabel_id       = $request->get('tabel_id');
      $tabel_barang   = $request->get('tabel_idbarang');
      $tabel_jumlah   = $request->get('tabel_jumlah');
      $tabel_harga    = $request->get('tabel_harga');
      //$tabel_diskon   = $request->get('tabel_diskon');
      $tabel_satuan   = $request->get('tabel_idsatuan');
      // $tabel_subtotal = $request->get('tabel_subtotal');
      $tabel_total    = $request->get('tabel_total');
      $tabel_idsatuan    = $request->get('tabel_idsatuan');
      $tabel_idgudang   = $request->get('gudang');
      $tabel_satuan_awal   = $request->get('tabel_satuan_awal');

      $tabel_idsatuan = $request->get('tabel_idsatuan');
      $tabel_idlog    = $request->get('tabel_idlog');
      // print_r($tabel_idsatuan);exit;
      if($id_kasir == ''){
        $id = DB::table('tbl_kasir')->insertGetId($data);
        for($i=0;$i<count($tabel_id);$i++){
          if ($tabel_idsatuan[$i] == $tabel_satuan_awal[$i]) {       

          $input['id_barang']     = $tabel_barang[$i];
          $input['unit_masuk']    = "0";
          $input['unit_keluar']   = $tabel_jumlah[$i];
          $input['id_ref_gudang'] =  $tabel_idgudang ;
          $input['id_satuan'] = $tabel_idsatuan[$i];
          $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
          $input['status']        = 'J4';
          $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

          $barang['id_kasir'] = $id;
          $barang['id_barang']    = $tabel_barang[$i];
          $barang['jumlah']       = $tabel_jumlah[$i];
          //$barang['jumlah_konversi'] = $tabel_jumlah[$i];
          $barang['harga']        = $tabel_harga[$i];
          // $barang['potongan']     = $tabel_diskon[$i];
          //$barang['id_satuan_konversi'] = $tabel_satuan_awal[$i];
          $barang['id_satuan']     = $tabel_idsatuan[$i];
          // $barang['subtotal']     = $tabel_subtotal[$i];
          $barang['total']        = $tabel_total[$i];
          $barang['id_log_stok']  = $id_log_stok;
          DB::table('tbl_kasir_detail')->insert($barang);
          }else{
          $konversi = DB::select("select * from tbl_satuan_konversi where id_satuan_awal='".$tabel_idsatuan[$i]."' and id_satuan_akhir='".$tabel_satuan_awal[$i]."'");
          //  print_r($konversi[$i]->jumlah_akhir * $tabel_jumlah[$i]);exit;
          // $jumlah = ;
          // print_r($jumlah);exit;
          $input['id_barang']     = $tabel_barang[$i];
          $input['unit_masuk']    = "0";
          //$input['unit_keluar']   = $tabel_jumlah[$i] * $konversi[0]->jumlah_akhir;
          $input['unit_keluar']   = $tabel_jumlah[$i];
          $input['id_ref_gudang'] =  $tabel_idgudang ;
          //$input['id_satuan'] = $tabel_satuan_awal[$i];
          $input['id_satuan']     = $tabel_idsatuan[$i];
          $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
          $input['status']        = 'J4';
          $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

          $barang['id_kasir'] = $id;
          $barang['id_barang']    = $tabel_barang[$i];
          $barang['jumlah']       = $tabel_jumlah[$i];
          //$barang['jumlah_konversi'] = $tabel_jumlah[$i] * $konversi[0]->jumlah_akhir;
          $barang['harga']        = $tabel_harga[$i];
          //$barang['id_satuan_konversi'] = $tabel_satuan_awal[$i];
          $barang['id_satuan']    = $tabel_idsatuan[$i];
          // $barang['subtotal']     = $tabel_subtotal[$i];
          $barang['total']        = $tabel_total[$i];
          $barang['id_log_stok']  = $id_log_stok;
          DB::table('tbl_kasir_detail')->insert($barang);

          }
          
        }  

        trigger_log($id, "Menambah Nota Penjualan Grosir", 1, url('penjualangrosir_edit/'.$id));

      }else{
        DB::table('tbl_kasir')->where('id_kasir',$id_kasir)->update($data);

        $id_del = array();
        $id_store = array();
        $id_del_log = array();
        $id_store_log = array();
        $d_barang = DB::table('tbl_kasir_detail')->where('id_kasir',$id_kasir);
        foreach($d_barang->get() as $d){
          $id_store[] = $d->id_detail_kasir;
          $id_store_log[] = $d->id_log_stok;
        }

        foreach($id_store as $d){
                if(count($tabel_id) > 0){
                    if(!in_array($d, $tabel_id)){
                        $id_del[] = $d;
                    }
                }else{
                    $id_del[] = $d;
                }
            }
        foreach($id_store_log as $d){
                if(count($tabel_id) > 0){
                    if(!in_array($d, $tabel_id)){
                        $id_del_log[] = $d;
                    }
                }else{
                    $id_del_log[] = $d;
                }
            }



        for($i=0;$i<count($tabel_id);$i++){
          $input['id_barang']     = $tabel_barang[$i];
          $input['unit_masuk']    = "0";
          $input['unit_keluar']   = $tabel_jumlah[$i];
          $input['id_ref_gudang'] = $tabel_idgudang;
          $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
          $input['status']        = 'J4';
          $input['id_satuan']     = $tabel_idsatuan[$i];
          

          $barang['id_kasir'] = $id_kasir;
          $barang['id_satuan']     = $tabel_idsatuan[$i];
          $barang['id_barang']    = $tabel_barang[$i];
          $barang['jumlah']       = $tabel_jumlah[$i];
          $barang['harga']        = $tabel_harga[$i];
          // $barang['potongan']     = $tabel_diskon[$i];
          // $barang['subtotal']     = $tabel_subtotal[$i];
          $barang['total']        = $tabel_total[$i];
          if($tabel_id[$i] == ''){            
            $id_log = DB::table('tbl_log_stok')->insertGetId($input);
            $barang['id_log_stok'] = $id_log;
            DB::table('tbl_kasir_detail')->insert($barang);
          }else if($tabel_id[$i] != ''){
            DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]))->update($input);
            DB::table('tbl_kasir_detail')->where(array('id_detail_kasir' => $tabel_id[$i]))->update($barang);
          }

        }
        
        // if(count($id_del_log) > 0){
        //     DB::table('tbl_log_stok')->whereIn('log_stok_id', $id_del_log)->delete();
        // }

        if(count($id_del) > 0){
          $d_barang_stok = DB::table('tbl_kasir_detail')->whereIn('id_detail_kasir',$id_del)->get();
          foreach ($d_barang_stok as $d) {
            DB::table('tbl_log_stok')->where('log_stok_id',$d->id_log_stok)->delete();
          }
          DB::table('tbl_kasir_detail')->whereIn('id_detail_kasir', $id_del)->delete();
        }

        
        trigger_log($id_kasir, "Mengubah Nota Penjualan Grosir", 2, url('penjualangrosir_edit/'.$id_kasir));
      }

      return redirect('penjualangrosir');

    }

    public function hapus(Request $request){
      $id = $request->get('id');
      $d_barang = DB::table('tbl_kasir_detail')->where('id_kasir',$id);
      $id_log_stok = array();
      foreach($d_barang->get() AS $d){
        $id_log_stok[] = $d->id_log_stok;   

      }
      // print_r($id_log_stok);exit();
      DB::table('tbl_log_stok')->whereIn('log_stok_id',$id_log_stok)->delete();      

      DB::table('tbl_kasir')->where(array('id_kasir' => $id))->delete();
      DB::table('tbl_kasir_detail')->where(array('id_kasir' => $id))->delete();
      
      DB::table('tbl_kasir_batal')->where('id_kasir', $id)->update(['catatan' => $request->catatan, 'deleted_iduser' => Auth::id()]);
      trigger_log($id, "Menghapus Nota Penjualan Grosir", 3);
    }

    /*public function hapus($id){
      $d_barang = DB::table('tbl_kasir_detail')->where('id_kasir',$id);      
      $d_log_stok = array();
      foreach($d_barang->get() AS $d){
        $d_log_stok[] = $d->id_log_stok;
        
      }
      print_r($d_log_stok);

    }*/

    public function get_edit(Request $request){
      // DB::enableQueryLog();
      $id = $request->get('id');
      $d_data = DB::table('tbl_kasir_detail as tpd')->leftjoin('tbl_barang as tb','tpd.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','tpd.id_satuan','ts.satuan_id')->where('tpd.id_kasir',$id)->select('tpd.*','tb.barang_nama as nama_barang','tb.barang_kode as kode_barang','ts.satuan_nama as nama_satuan','ts.satuan_satuan')->orderBy('tpd.id_detail_kasir','asc');
      // dd( DB::getQueryLog());

      if($d_data->count() > 0){
        foreach($d_data->get() as $d){
            $arr[] = array('id' => $d->id_detail_kasir,
                            'id_kasir'  => $d->id_kasir,
                            'id_log_stok'  => $d->id_log_stok,
                            'id_barang'     => $d->id_barang,
                            'nama_barang'   => $d->nama_barang,
                            'kode_barang'   => $d->kode_barang,
                            'jumlah'        => $d->jumlah,
                            'nama_satuan'   => $d->nama_satuan,
                            'satuan_satuan' => $d->satuan_satuan,
                            'id_satuan'     => $d->id_satuan,
                            'harga'         => $d->harga,
                            'potongan'      => $d->potongan,
                            'subtotal'      => $d->subtotal,
                            'total'         => $d->total);
        }
      }else{
        $arr = array();
      }

      return response()->json($arr);
    }

    public function edit($id){
      $id_group = Auth::user()->group_id;
        $id_profil = Auth::user()->id_profil;
        if ($id_group == 5) {
            $where="WHERE  p.jenis_outlet = 2 and g.id_profil='".$id_profil."'";
        } else {
            $where='WHERE  p.jenis_outlet ';
        }
      $data['gudang']     = DB::select("SELECT
          g.id,
          g.id_profil,
          g.nama,
          g.alamat,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.jenis_outlet
        FROM
          ref_gudang g
          LEFT JOIN m_profil p ON g.id_profil = p.id $where
        ");
      $gudang = DB::select('select * from ref_gudang where id_profil="'.$id_profil.'"');
        // DB::enableQueryLog();
      $d_data = DB::table('tbl_kasir as tp')->leftjoin('m_pelanggan as ts','tp.id_pelanggan','ts.id')->leftjoin('ref_gudang as g','tp.id_gudang','g.id')->select('tp.*','ts.nama as nama_pelanggan','g.nama as nama_gudang')->where('id_kasir',$id)->first();
      // dd( DB::getQueryLog());
      // print_r($d_data );exit;
      $data['id_gudang'] = empty($gudang[0]->id)?'':$gudang[0]->id;
      $data['data']   = $this->data($d_data);     
      $data['satuan'] = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['rekening']   = \Config::get('constants.rekening');
      $data['carabayar'] = \Config::get('constants.carabayar');
    //   $data['pembayaran'] = DB::select("select * from m_metode");
      $data['pembayaran'] = DB::select("select * from m_metode where status = '1'");
      return view('admin.penjualangrosir.create')->with('data',$data);
    }

    public function detail($id){
        $id_group = Auth::user()->group_id;
        $id_profil = Auth::user()->id_profil;
        if ($id_group == 5) {
            $where="WHERE  p.jenis_outlet = 2 and g.id_profil='".$id_profil."'";
        } else {
            $where='WHERE  p.jenis_outlet ';
        }
      $data['gudang']     = DB::select("SELECT
          g.id,
          g.id_profil,
          g.nama,
          g.alamat,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.jenis_outlet
        FROM
          ref_gudang g
          LEFT JOIN m_profil p ON g.id_profil = p.id $where
        ");
      $gudang = DB::select('select * from ref_gudang where id_profil="'.$id_profil.'"');
        // DB::enableQueryLog();
      $d_data = DB::table('tbl_kasir as tp')->leftjoin('m_pelanggan as ts','tp.id_pelanggan','ts.id')->leftjoin('ref_gudang as g','tp.id_gudang','g.id')->select('tp.*','ts.nama as nama_pelanggan','g.nama as nama_gudang')->where('id_kasir',$id)->first();
      // dd( DB::getQueryLog());
      // print_r($d_data );exit;
      $data['id_gudang'] = empty($gudang[0]->id)?'':$gudang[0]->id;
      $data['data']   = $this->data($d_data);     
      $data['satuan'] = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['rekening']   = \Config::get('constants.rekening');
      $data['carabayar'] = \Config::get('constants.carabayar');
    //   $data['pembayaran'] = DB::select("select * from m_metode");
      $data['pembayaran'] = DB::select("select * from m_metode where status = '1'");
      // print_r($data['data']);exit();
      return view('admin.penjualangrosir.detail')->with('data',$data);
    }

    public function get_pelanggan(Request $request){
      $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6){
          $where = "WHERE id_profil='$id_profil'";
        }
        $d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
        }
        $where_gudang = "";
        if(sizeof($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $where_gudang = "AND b.id_gudang IN ($gudang)";
        }

      $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }

        $search = strtolower($term);
        $barangs= DB::select("SELECT
          b.id as pelanggan_id,
          b.nama as pelanggan_nama,
          b.telp as pelanggan_telp,
          b.alamat as pelanggan_alamat
        FROM m_pelanggan as b LEFT JOIN ref_gudang as rg ON b.id_gudang=rg.id WHERE (b.nama LIKE '%$search%') $where_gudang order by b.nama asc");

        return \Response::json($barangs);
        
    }
    public function listData(){
      // DB::enableQueryLog();
      $id_group = Auth::user()->group_id;
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $kasir = DB::select("SELECT
            k.id_kasir,
            k.id_pelanggan,
            k.no_faktur,
            k.tanggal,
            k.tanggal_tempo,
            k.tanggal_faktur,
            k.uang_muka,
            k.ongkos_kirim,
            k.carabayar,
            k.id_rekening,
            k.no_rekening,
            k.total_subtotal,
            k.total_tagihan,
            k.total_potongan,
            k.total_bayar,
            k.keterangan,
            k.`status`,
            k.jenis_transaksi,
            p.nama,
            k.status_posting,
            tkp.jumlah_cetak
          FROM
            tbl_kasir AS k
            LEFT JOIN m_pelanggan AS p ON k.id_pelanggan = p.id 
            LEFT JOIN (SELECT id_kasir, COUNT(id_print) as jumlah_cetak FROM tbl_kasir_print GROUP BY id_kasir) AS tkp ON k.id_kasir = tkp.id_kasir
          WHERE
            k.jenis_transaksi = 2 
            /*AND k.tanggal > DATE_SUB(DATE(NOW()), INTERVAL DAYOFWEEK(NOW())+6 DAY) 
            AND k.tanggal <= DATE_SUB(DATE(NOW()), INTERVAL DAYOFWEEK(NOW())-1 DAY)*/
            AND k.tanggal_faktur > DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) 
            AND k.tanggal_faktur <= DATE_SUB(DATE(NOW()), INTERVAL 0 DAY)
          ORDER BY
            k.tanggal ASC");
      // dd( DB::getQueryLog());
      $no = 0;
      $data = array();
        foreach ($kasir as $list){
            $total = ($list->total_tagihan + $list->ongkos_kirim)- $list->total_potongan;
            if ($list->status == '1') {
              $status = '<label class=" label label-success">Lunas</label>';
            }else if($list->status == '2'){
              $status = '<label class=" label label-warning">Uang Muka</label>';
              
            }else if($list->status == '3'){
              $status = '<label class=" label label-danger">Hutang</label>';

            }
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = tgl_full($list->tanggal_faktur,'');
            $row[] = $list->no_faktur;
            $row[] = $list->nama;
            $row[] = $status;
            $row[] = 'Rp '.format_angka($total);
            if($id_group == 1) $row[] = $list->jumlah_cetak ?? 0;
            $row[] = $this->posting($list->status_posting,$list->id_kasir,$group_where->group_aktif);
            $data[] = $row;


            /*'<a  href="'.url('penjualangrosir_edit/'.$list->id_kasir).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->id_kasir.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>'*/
        }

      $output = array("data" => $data);
      return response()->json($output);
    }

    public function searchtanggal(Request $request){
      // DB::enableQueryLog();
      $id_group = Auth::user()->group_id;
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $tanggalrange = explode('s.d.',$request->get('tanggal'));
      $tanggal_start  = tgl_full($tanggalrange[0],99);
      $tanggal_end    = tgl_full($tanggalrange[1],99);
      $kasir = DB::select("SELECT
            k.id_kasir,
            k.id_pelanggan,
            k.no_faktur,
            k.tanggal,
            k.tanggal_tempo,
            k.tanggal_faktur,
            k.uang_muka,
            k.ongkos_kirim,
            k.carabayar,
            k.id_rekening,
            k.no_rekening,
            k.total_subtotal,
            k.total_tagihan,
            k.total_potongan,
            k.total_bayar,
            k.keterangan,
            k.`status`,
            k.jenis_transaksi,
            p.nama,
            k.status_posting
          FROM
            tbl_kasir AS k
            LEFT JOIN m_pelanggan AS p ON k.id_pelanggan = p.id 
          WHERE
            k.jenis_transaksi = 2 
            AND k.tanggal_faktur >= '$tanggal_start' 
            AND k.tanggal_faktur <= '$tanggal_end'
          ORDER BY
            k.tanggal ASC");
        $no = 0;
        $data = array();
        foreach ($kasir as $list){
            $total = ($list->total_tagihan + $list->ongkos_kirim)- $list->total_potongan;
            if ($list->status == '1') {
              $status = '<label class=" label label-success">Lunas</label>';
            }else if($list->status == '2'){
              $status = '<label class=" label label-warning">Uang Muka</label>';
              
            }else if($list->status == '3'){
              $status = '<label class=" label label-danger">Hutang</label>';

            }
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = tgl_full($list->tanggal_faktur,'');
            $row[] = $list->no_faktur;
            $row[] = $list->nama;
            $row[] = $status;
            $row[] = 'Rp '.format_angka($total);
            $row[] = $this->posting($list->status_posting,$list->id_kasir,$group_where->group_aktif);
            $data[] = $row;
        }

      $output = array("data" => $data);
      return response()->json($output);
    }

    public function posting($status,$id,$status_edit){
      switch (true) {
        case ($status=='1'&&$status_edit=='2'):
          $html = '<div class="btn-group"><a  href="'.url('penjualangrosir_cetak/'.$id).'" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" title="Print Data"  style="color:white;" target="_blank"><i class="fa  fa-print"></i></a><a  href="'.url('penjualangrosir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
          break;
        case ($status=='2'&&$status_edit=='2'):
          $html = '<div class="btn-group"><a  href="'.url('penjualangrosir_cetak/'.$id).'" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" title="Print Data"  style="color:white;" target="_blank"><i class="fa  fa-print"></i></a><a  href="'.url('penjualangrosir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a>';
          break;        
        case ($status=='1'&&$status_edit=='1'):
          $html = '<div class="btn-group"><a  href="'.url('penjualangrosir_cetak/'.$id).'" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" title="Print Data"  style="color:white;" target="_blank"><i class="fa  fa-print"></i></a><a  href="'.url('penjualangrosir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-eye"></i></a></div>';
          break;
        case ($status=='2'&&$status_edit=='1'):
          $html = '<div class="btn-group"><a  href="'.url('penjualangrosir_cetak/'.$id).'" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" title="Print Data"  style="color:white;" target="_blank"><i class="fa  fa-print"></i></a><a  href="'.url('penjualangrosir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a>';
          break;
        default:
          $html = '<div class="btn-group"><a  href="'.url('penjualangrosir_cetak/'.$id).'" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" title="Print Data"  style="color:white;" target="_blank"><i class="fa  fa-print"></i></a><a  href="'.url('penjualangrosir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
          break;
      }
      return $html;
    }

    function cetak_laporan($id){
     require(public_path('fpdf1813/Mc_table.php'));
        $data['data'] = DB::table('tbl_kasir as tk')->join('m_pelanggan as mp','tk.id_pelanggan','mp.id')->join('ref_gudang as rg','tk.id_gudang','rg.id')->leftjoin('m_metode as mm','tk.metodebayar','mm.id')->leftjoin('m_metode as mm2','tk.metodebayar2','mm2.id')->where('id_kasir',$id)->select(DB::raw('tk.*,mp.nama as nama_pelanggan, mp.alamat as alamat_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang, rg.alamat as alamat_gudang, mm.nama as nama_metodebayar, mm2.nama as nama_metodebayar2'))->first();
        $data['detail'] = DB::table('tbl_kasir_detail_produk as tkp')->join('m_produk as mp','tkp.id_produk','mp.id')->join('tbl_satuan as ts','tkp.id_satuan','ts.satuan_id')->where('tkp.id_kasir','=',$id)->select(DB::raw('tkp.*,mp.kode_produk as kode_produk,mp.nama as nama_produk,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        $data['barang'] = DB::table('tbl_kasir_detail as tkd')->join('tbl_barang as tb','tkd.id_barang','tb.barang_id')->join('tbl_satuan as ts','tkd.id_satuan','ts.satuan_id')->where('tkd.id_kasir','=',$id)->select(DB::raw('tkd.*,tb.barang_kode as kode_barang,tb.barang_nama as nama_barang, tb.barang_alias as alias,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();

        if(Auth::user()->id_profil != 0){
          $id_profil = Auth::user()->id_profil;
        }else{
          $id_profil = '1';
        }

        $data['rich'] = DB::table('m_profil as mp')->where('id',$id_profil)->select(DB::raw('mp.*'))->first();

      $html = view('admin.penjualangrosir.CetakPenjualanGrosir')->with('data',$data);
      $print['id_kasir'] = $id;
      $print['id_user'] = Auth::id();
      $print['keterangan'] = 'Cetak Nota Penjualan Grosir';
      DB::table('tbl_kasir_print')->insert($print);
      trigger_log($id, "Cetak Nota Penjualan Grosir", 7);
      return response($html)->header('Content-Type', 'application/pdf');

    }


    public function query_get_barang($where,$id_gudang){
      $sql = "
      SELECT d.barang_id, d.satuan_id, d.barang_kode, d.barang_nama, d.barang_alias, 
      d.barang_id_parent, d.barang_status_bahan, d.satuan_nama, d.satuan_satuan, SUM(d.harga) harga, SUM(d.jumlah) jumlah_stok  
      FROM(
      SELECT
          b.barang_id, b.satuan_id, b.barang_kode, b.barang_nama, b.barang_alias, b.barang_id_parent, b.barang_status_bahan, s.satuan_nama, s.satuan_satuan, d.detail_harga_barang_harga_jual  harga, 0 as jumlah
      FROM
          tbl_barang AS b
      LEFT JOIN tbl_satuan AS s ON b.satuan_id = s.satuan_id          
      LEFT JOIN (
          SELECT
            *
          FROM
            tbl_detail_harga_barang AS a
          WHERE
            detail_harga_barang_tanggal = ( SELECT MAX( detail_harga_barang_tanggal ) 
                                            FROM tbl_detail_harga_barang AS b WHERE a.barang_id = b.barang_id )
                                          ) AS d ON b.barang_id = d.barang_id
        WHERE (b.barang_kode LIKE '%$where%' OR b.barang_nama LIKE '%$where%' OR b.barang_alias LIKE '%$where%')        
      UNION ALL
      SELECT
            b.barang_id, b.satuan_id, b.barang_kode, b.barang_nama, b.barang_alias, b.barang_id_parent, b.barang_status_bahan, s.satuan_nama, s.satuan_satuan, 0 as harga, l.jumlah
      FROM (
          SELECT
              t.log_stok_id, t.id_barang, t.id_ref_gudang, Sum( t.unit_masuk ) AS jumlah_masuk, Sum( t.unit_keluar ) AS jumlah_keluar, Sum( t.unit_masuk-t.unit_keluar ) AS jumlah, t.id_satuan
          FROM
              tbl_log_stok AS t
          WHERE
              t.id_ref_gudang = '$id_gudang'
          GROUP BY
              t.id_barang,
              t.id_ref_gudang,
              t.id_satuan
          ) l
      LEFT JOIN tbl_barang AS b ON l.id_barang = b.barang_id
      LEFT JOIN tbl_satuan AS s ON l.id_satuan = s.satuan_id
      LEFT JOIN ref_gudang AS rf ON l.id_ref_gudang = rf.id
      WHERE (b.barang_kode LIKE '%$where%' OR b.barang_nama LIKE '%$where%' OR b.barang_alias LIKE '%$where%')
      ) d
      GROUP BY
      d.barang_id,
      d.satuan_id";
      return $sql;

    }

    public function get_stok(Request $request){
      $id_barang = $request->get('id_barang');
      $id_satuan = $request->get('id_satuan');
      $id_gudang = $request->get('id_gudang');

      $d_data = DB::SELECT("
      SELECT
            b.barang_id, b.satuan_id, b.barang_kode, b.barang_nama, b.barang_alias, b.barang_id_parent, b.barang_status_bahan, s.satuan_nama, s.satuan_satuan, 0 as harga, l.jumlah as jumlah_stok
      FROM (
          SELECT
              t.log_stok_id, t.id_barang, t.id_ref_gudang, Sum( t.unit_masuk ) AS jumlah_masuk, Sum( t.unit_keluar ) AS jumlah_keluar, Sum( t.unit_masuk-t.unit_keluar ) AS jumlah, t.id_satuan
          FROM
              tbl_log_stok AS t
          WHERE
              t.id_ref_gudang = '$id_gudang' AND t.id_barang = '$id_barang' AND t.id_satuan = '$id_satuan'
          GROUP BY
              t.id_barang,
              t.id_ref_gudang,
              t.id_satuan
          ) l
      LEFT JOIN tbl_barang AS b ON l.id_barang = b.barang_id
      LEFT JOIN tbl_satuan AS s ON l.id_satuan = s.satuan_id
      LEFT JOIN ref_gudang AS rf ON l.id_ref_gudang = rf.id
      ");

      $stok = 0;
      foreach ($d_data as $d) {
        $stok = $d->jumlah_stok;
      }
      return response()->json($stok);
    }


}

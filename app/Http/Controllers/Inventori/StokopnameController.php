<?php

namespace App\Http\Controllers\Inventori;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\BarangModel;
use App\StokopnameModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Auth;

class StokopnameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $barang = BarangModel::All();
      return view('admin.stokopname.index', compact('barang'));
    }

    public function tambah(){
    $id_profil = Auth::user()->id_profil;
      $group = Auth::user()->group_id;
      $where = "";
      if($group == 5 || $group == 6){
        $where = "WHERE id_profil='$id_profil'";
      }

    $data['data'] = $this->data(array());
    $data['gudang']     = DB::select(base_gudang($where));
    return view('admin.stokopname.tambah')->with('data',$data);
    }

    public function get_gudang(Request $request){
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT 
          id, 
          nama, 
          alamat
          FROM ref_gudang WHERE nama LIKE '%$search%' OR alamat LIKE '%$search%' order by nama asc");
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
        /*$d_query= DB::select("SELECT
          b.barang_id,
          b.satuan_id,
          b.barang_kode,
          b.barang_nama,
          b.barang_id_parent,
          b.barang_status_bahan,
          s.satuan_nama,
          s.satuan_satuan,
          d.detail_harga_barang_harga_jual  harga
        FROM
          tbl_barang AS b
          LEFT JOIN tbl_satuan AS s ON b.satuan_id = s.satuan_id
          LEFT JOIN (
          SELECT
            * 
          FROM
            tbl_detail_harga_barang AS a 
        WHERE
        detail_harga_barang_tanggal = 
        ( SELECT MAX( detail_harga_barang_tanggal ) FROM tbl_detail_harga_barang AS b WHERE a.barang_id = b.barang_id )) AS d ON b.barang_id = d.barang_id
                WHERE b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%'");*/
        $d_query= DB::select("SELECT
            l.log_stok_id,
            b.barang_id,
            b.satuan_id,
            b.barang_kode,
            b.barang_nama,
            b.barang_id_parent,
            b.barang_alias,
            b.barang_status_bahan,
            s.satuan_nama as nama_satuan,
            s.satuan_satuan,
            l.jumlah_masuk,
            l.jumlah_keluar,
            l.jumlah,
            l.id_satuan,
            l.id_ref_gudang as id_gudang,
            rf.nama as nama_gudang  
          FROM
            (
            SELECT
              t.log_stok_id,
              t.id_barang,
              t.id_ref_gudang,
              Sum( t.unit_masuk ) AS jumlah_masuk,
              Sum( t.unit_keluar ) AS jumlah_keluar,
              Sum( t.unit_masuk-t.unit_keluar ) AS jumlah,
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
            LEFT JOIN ref_gudang AS rf ON l.id_ref_gudang = rf.id
            WHERE (b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%') AND l.id_satuan != '0'");
        return \Response::json($d_query);
    }

    public function edit($id){
    $id_profil = Auth::user()->id_profil;
      $group = Auth::user()->group_id;
      $where = "";
      if($group == 5 || $group == 6){
        $where = "WHERE id_profil='$id_profil'";
      }

    $d_data = DB::table('tbl_stokopname as ts')->leftjoin('ref_gudang as rg','ts.id_gudang','rg.id')->where('ts.id_stokopname',$id)->select('ts.*','rg.nama as nama_gudang')->first();
    $data['data'] = $this->data($d_data);
    $data['gudang']     = DB::select(base_gudang($where));
    return view('admin.stokopname.tambah')->with('data',$data);
    }

    function simpan(Request $request){
        $id_stokopname = $request->get('id_stokopname');
        $data['tanggal']    = tgl_full($request->get('tanggal'),'99');
        $data['id_gudang']  = $request->get('gudang');
        $data['keterangan'] = $request->get('keterangan');
        $data['status']     = $request->get('status');

        $tabel_id = $request->get('tabel_id');
        $tabel_barang       = $request->get('tabel_idbarang');
        $tabel_gudang       = $request->get('tabel_idgudang');
        $tabel_jumStok      = $request->get('tabel_jumStok');
        $tabel_jumFisik     = $request->get('tabel_jumFisik');
        $tabel_satuan       = $request->get('tabel_idsatuan');
        $tabel_selisih      = $request->get('tabel_selisih');
        $tabel_keterangan   = $request->get('tabel_keterangan');

        if($id_stokopname == ''){
            $id = DB::table('tbl_stokopname')->insertGetId($data);
            for($i=0;$i<count($tabel_id);$i++){
                /*$jum = parseFloat($tabel_jumStok[$i]-$tabel_jumFisik[$i]);
                if($jum > 0){
                  $input['unit_masuk']    = $jum;
                  $input['unit_keluar']   = "0";
                }else{
                  $input['unit_masuk']    = "0";
                  $input['unit_keluar']   = $jum;
                }

                $input['id_barang']     = $tabel_barang[$i];
                $input['id_ref_gudang'] = '2';
                $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
                $input['status']        = 'J1';
                $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);*/

                $barang['id_stokopname']    = $id;
                $barang['id_barang']        = $tabel_barang[$i];
                $barang['id_gudang']        = $tabel_gudang[$i];
                $barang['stok']             = $tabel_jumStok[$i];
                $barang['fisik']            = $tabel_jumFisik[$i];
                $barang['selisih']          = $tabel_selisih[$i];
                $barang['id_satuan']        = $tabel_satuan[$i];
                $barang['keterangan']       = $tabel_keterangan[$i];
                /*$barang['id_log_stok']      = $id_log_stok;*/
                DB::table('tbl_stokopname_detail')->insert($barang);
            }
            trigger_log($id, "Menambahkan Data Stokopname", 1);
        }else{
            DB::table('tbl_stokopname')->where('id_stokopname',$id_stokopname)->update($data);

            $id_del = array();
            $id_store = array();
            $d_barang = DB::table('tbl_stokopname_detail')->where('id_stokopname',$id_stokopname);
            foreach($d_barang->get() as $d){
              $id_store[] = $d->id_detail_stokopname;
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
            //print_r($tabel_keterangan);
            //print_r(count($tabel_id)."id ".count($tabel_jumStok)."ket");
            for($i=0;$i<count($tabel_id);$i++){
              $barang['id_stokopname']    = $id_stokopname;
              $barang['id_barang']        = $tabel_barang[$i];
              $barang['id_gudang']        = $tabel_gudang[$i];
              $barang['stok']             = $tabel_jumStok[$i];
              $barang['fisik']            = $tabel_jumFisik[$i];
              $barang['selisih']          = $tabel_selisih[$i];
              $barang['id_satuan']        = $tabel_satuan[$i];
              $barang['keterangan']       = $tabel_keterangan[$i];
              if($tabel_id[$i] == ''){
                DB::table('tbl_stokopname_detail')->insert($barang);
                //print_r($tabel_id[$i]." oke "."<br>");
              }else if($tabel_id[$i] != ''){
                //$barang['keterangan']       = $tabel_keterangan[$i];
                DB::table('tbl_stokopname_detail')->where(array('id_detail_stokopname' => $tabel_id[$i]))->update($barang);
                //print_r($tabel_id[$i]."<br>");
              }
              

              //print_r($barang);
            }


            

            if(count($id_del) > 0){
              DB::table('tbl_stokopname_detail')->whereIn('id_detail_stokopname', $id_del)->delete();
            }
            trigger_log($id_stokopname, "Mengubah Data Stokopname", 2);
        }
        
        return redirect('stokopname');
    }

    function hapus(Request $request){
        $id = $request->get('id');
        DB::table('tbl_stokopname')->where('id_stokopname',$id)->delete();
        DB::table('tbl_stokopname_detail')->where(array('id_stokopname'=>$id))->delete();
        trigger_log($id, "Menghapus Data Stokopname", 3);
    }

    function data($data = array()){
        if($data != null){
            $store['id_stokopname'] = $data->id_stokopname;
            $store['tanggal']       = tgl_full($data->tanggal,'');
            $store['id_gudang']     = $data->id_gudang;
            $store['nama_gudang']   = $data->nama_gudang;
            $store['keterangan']    = $data->keterangan;
            $store['status']        = $data->status;
        }else{
            $store['id_stokopname'] = "";
            $store['tanggal']       = "";
            $store['id_gudang']     = "";
            $store['nama_gudang']   = "";
            $store['keterangan']    = ".";
            $store['status']        = "1";
        }
        return $store;
    }


    public function get_edit(Request $request){
        $id = $request->get('id');
        /*$d_data = DB::table('tbl_stokopname_detail as tpd')->leftjoin('tbl_barang as tb','tpd.id_barang','tb.barang_id')->leftjoin('ref_gudang as rg','tpd.id_gudang','rg.id')->where('tpd.id_stokopname',$id)->select('tpd.*','tb.barang_nama as nama_barang','tb.barang_kode as kode_barang','tb.barang_alias as alias_barang','rg.nama as nama_gudang')->orderBy('tpd.id_stokopname','asc');*/

        $d_data = DB::select("select tpd.id_detail_stokopname, tpd.id_stokopname, tpd.id_barang, tb.barang_kode as kode_barang, tb.barang_nama as nama_barang, tb.barang_alias as alias_barang, tpd.id_gudang, rg.nama as nama_gudang, CASE WHEN tpd.stok=0 THEN '0' ELSE tpd.stok END AS stok, CASE WHEN tpd.fisik=0 THEN '0' ELSE tpd.fisik END AS fisik, CASE WHEN tpd.selisih=0 THEN '0' ELSE tpd.selisih END AS selisih, tpd.id_satuan, ts.satuan_nama as nama_satuan, CASE WHEN tpd.keterangan='null' THEN '' WHEN tpd.keterangan IS NULL THEN '' ELSE tpd.keterangan END AS keterangan FROM tbl_stokopname_detail as tpd JOIN tbl_barang as tb ON tpd.id_barang=tb.barang_id JOIN ref_gudang as rg ON tpd.id_gudang=rg.id JOIN tbl_satuan as ts ON tpd.id_satuan=ts.satuan_id WHERE tpd.id_stokopname='$id' order by tpd.id_stokopname asc");
        /*if($d_data->count() > 0 ){
            foreach($d_data->get() as $d){*/
        if(count($d_data) > 0 ){
            foreach($d_data as $d){
                $arr[] = array('id' => $d->id_detail_stokopname,
                                'id_stokopname' => $d->id_stokopname,
                                'id_barang'     => $d->id_barang,
                                'nama_barang'   => $d->nama_barang,
                                'kode_barang'   => $d->kode_barang,
                                'alias_barang'  => $d->alias_barang,
                                'id_gudang'     => $d->id_gudang,
                                'nama_gudang'   => $d->nama_gudang,
                                'jumlah_stok'   => $d->stok,
                                'jumlah_fisik'  => $d->fisik,
                                'id_satuan'     => $d->id_satuan,
                                'nama_satuan'   => $d->nama_satuan,
                                'selisih'       => $d->selisih,
                                'keterangan'    => $d->keterangan);
            }
        }else{
            $arr = array();
        }
        return response()->json($arr);
        //print_r($d_data);
    }


    public function listData()
    {
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

        $stok = StokopnameModel::with(['dataGudang'])
                ->whereIn('id_gudang',$id_gudang)->orderBy('id_stokopname', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($stok as $list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = tgl_full($list->tanggal,'');
            $row[] = $list->dataGudang->nama;
            $row[] = $list->keterangan;
            $row[] = '<div class="btn-group"><a href="'.url('stokopname_edit',$list->id_stokopname).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Update Stok Fisik"  style="color:white;"><i class="fa  fa-edit"></i></a></a>
            <a onclick="deleteData('.$list->id_stokopname.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);
        //dd($output);

    }

}

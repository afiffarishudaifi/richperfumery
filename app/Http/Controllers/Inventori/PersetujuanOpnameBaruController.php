<?php

namespace App\Http\Controllers\Inventori;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Jenssegers\Agent\Agent;

class PersetujuanOpnameBaruController extends Controller
{
    //
    public function __construct(){
      $this->agent = new Agent();
    }
    public function index(){
        $id_group = Auth::user()->group_id;
        $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
        $tombol_create = tombol_create('',$group_where->group_aktif,5);
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.stokopname_baru.index_persetujuan',compact('tombol_create'));
      }else {
        // code...
        return view('admin.stokopname_baru.index_persetujuan',compact('tombol_create'));
      }
    }

    public function listDataTanggal(Request $request){
        DB::enableQueryLog();
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
          $where_gudang = "WHERE g.id IN ($gudang)";
        }

        $draw   = $request->get('draw');
        $start  = $request->get('start');
        $length = $request->get('length');
        $filter = $request->get('search');
        $search = (isset($filter['value']))? strtolower($filter['value']) : false;
        $tanggal = (isset($filter['value']))? tgl_full($filter['value'],'99') : false;

        $tanggal_start = date('Y-m-d', strtotime('-7 days'));
        $tanggal_end = date('Y-m-d');
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')->where('tsd.tanggal','>',$tanggal_start)->where('tsd.tanggal','<=',$tanggal_end)->whereIn('tsd.id_gudang',$id_gudang)->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, sum(tsd.stok) as stok, count(tsd.fisik) as fisik, sum(tsd.selisih) as selisih, tsd.id_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tsd.tanggal','DESC')->groupBy('tsd.tanggal','tsd.id_gudang');

        if($search){
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')
            ->where('tsd.tanggal','>',$tanggal_start)
            ->where('tsd.tanggal','<=',$tanggal_end)
            ->where(function($query) use ($search,$tanggal) {
                $query->orwhere('tsd.tanggal','like','%'.$tanggal.'%');
                $query->orWhere('rg.nama','like','%'.$search.'%');
            })
            ->whereIn('tsd.id_gudang',$id_gudang)
            ->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, sum(tsd.stok) as stok, count(tsd.fisik) as fisik, sum(tsd.selisih) as selisih, tsd.id_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tsd.tanggal','DESC')->groupBy('tsd.tanggal','tsd.id_gudang');
        }

        $totalrecord = count($stok->get());
        $stok_2 = $stok->offset($start)->limit($length);
        $no=($start==0)?0:$start;
        $arr = array();
        foreach ($stok_2->get() as $list) {
            $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->alias_barang;
            if($list->alias_barang == ""){
              $nama_barang = $list->kode_barang." || ".$list->nama_barang;
            }
            $keterangan = $list->keterangan;
            if($list->keterangan == "" || $list->keterangan == "null"){
              $keterangan = "";
            }
            $tanggal = tgl_full($list->tanggal,'');
            $no++;
            $arr[] = array('nomor'      =>$no,
                          'tanggal'     =>tgl_full($list->tanggal,''),
                          'nama_gudang' =>$list->nama_gudang,
                          'fisik'       =>$list->fisik,
                          'aksi'        => '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail(\''.$tanggal.'\','.$list->id_gudang.')" title="Detail Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div');

        }

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $totalrecord,
            'recordsFiltered' => $totalrecord,
            'data' => $arr
        );

        return response()->json($data);

    }

    public function searchtanggal(Request $request){
        DB::enableQueryLog();
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
          $where_gudang = "WHERE g.id IN ($gudang)";
        }

        $draw   = $request->get('draw');
        $start  = $request->get('start');
        $length = $request->get('length');
        $filter = $request->get('search');
        $search = (isset($filter['value']))? strtolower($filter['value']) : false;
        $tanggal = (isset($filter['value']))? tgl_full($filter['value'],'99') : false;

        $tanggalrange = explode('s.d.',$request->get('tanggal'));
        $tanggal_start  = tgl_full($tanggalrange[0],99);
        $tanggal_end    = tgl_full($tanggalrange[1],99);
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')->where('tsd.tanggal','>=',$tanggal_start)->where('tsd.tanggal','<=',$tanggal_end)->whereIn('tsd.id_gudang',$id_gudang)->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, sum(tsd.stok) as stok, count(tsd.fisik) as fisik, sum(tsd.selisih) as selisih, tsd.id_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tsd.tanggal','DESC')->groupBy('tsd.tanggal','tsd.id_gudang');

        if($search){
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')
            ->where('tsd.tanggal','>=',$tanggal_start)
            ->where('tsd.tanggal','<=',$tanggal_end)
            ->where(function($query) use ($search,$tanggal) {
                $query->orwhere('tsd.tanggal','like','%'.$tanggal.'%');
                $query->orWhere('rg.nama','like','%'.$search.'%');
            })
            ->whereIn('tsd.id_gudang',$id_gudang)
            ->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, sum(tsd.stok) as stok, count(tsd.fisik) as fisik, sum(tsd.selisih) as selisih, tsd.id_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tsd.tanggal','DESC')->groupBy('tsd.tanggal','tsd.id_gudang');
        }

        $totalrecord = count($stok->get());
        $stok_2 = $stok->offset($start)->limit($length);
        $no=($start==0)?0:$start;
        $arr = array();
        foreach ($stok_2->get() as $list) {
            $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->alias_barang;
            if($list->alias_barang == ""){
              $nama_barang = $list->kode_barang." || ".$list->nama_barang;
            }
            $keterangan = $list->keterangan;
            if($list->keterangan == "" || $list->keterangan == "null"){
              $keterangan = "";
            }
            $tanggal = tgl_full($list->tanggal,'');
            $no++;
            $arr[] = array('nomor'      =>$no,
                          'tanggal'     =>tgl_full($list->tanggal,''),
                          'nama_gudang' =>$list->nama_gudang,
                          'fisik'       =>$list->fisik,
                          'aksi'        => '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail(\''.$tanggal.'\','.$list->id_gudang.')" title="Detail Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div');

        }

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $totalrecord,
            'recordsFiltered' => $totalrecord,
            'data' => $arr
        );

        return response()->json($data);

    }

    public function listData(Request $request){
      DB::enableQueryLog();
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
          $where_gudang = "WHERE g.id IN ($gudang)";
        }

        $draw   = $request->get('draw');
        $start  = $request->get('start');
        $length = $request->get('length');
        $filter = $request->get('search');
        $search = (isset($filter['value']))? strtolower($filter['value']) : false;
        $search_tanggal = (isset($filter['value']))? tgl_full($filter['value'],'99') : false;

        $tanggal = tgl_full($request->get('tanggal'),'99');
        $e_gudang = $request->get('gudang');
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')->where('tsd.tanggal','=',$tanggal)->where('tsd.id_gudang',$e_gudang)->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, tsd.stok as stok, tsd.fisik, tsd.selisih, tsd.id_satuan, tsd.id_log_stok, tsd.keterangan, tsd.status'))->orderBy('tsd.id_stokopname','ASC');

        if($search!="false"){
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')
        	->where('tsd.tanggal','=',$tanggal)
        	->where('tsd.id_gudang',$e_gudang)
          ->where(function($query) use ($search){
                  $query->orwhere('tb.barang_kode','like','%'.$search.'%');
                  $query->orwhere('tb.barang_nama','like','%'.$search.'%');
                  $query->orwhere('tb.barang_alias','like','%'.$search.'%');
                  $query->orwhere('rg.nama','like','%'.$search.'%');
             })
        	->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, tsd.stok as stok, tsd.fisik, tsd.selisih, tsd.id_satuan, tsd.id_log_stok, tsd.keterangan, tsd.status'))->orderBy('tsd.id_stokopname','ASC');
        }
        $totalrecord = count($stok->get());
        //dd($stok->get());
        if($length < 0){
        $stok_2 = $stok;
        }else{
        $stok_2 = $stok->offset($start)->limit($length);
        }
        //$stok_2 = $stok->offset($start)->limit($length);
        //dd(DB::getQueryLog());
        $no=($start==0)?0:$start;
        $arr = array();
        foreach ($stok_2->get() as $list) {
            $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->alias_barang;
            if($list->alias_barang == ""){
              $nama_barang = $list->kode_barang." || ".$list->nama_barang;
            }
            $keterangan = $list->keterangan;
            if($list->keterangan == "" || $list->keterangan == "null"){
              $keterangan = "";
            }

            $no++;
            $arr[] = array('nomor'      =>$no.$this->get_check($list->status,$list->id_stokopname),
                          'tanggal'     =>tgl_full($list->tanggal,''),
                          'nama_barang' =>$nama_barang,
                          'nama_gudang' =>$list->nama_gudang,
                          'fisik'       =>$list->fisik,
                          'selisih'     =>$list->selisih,
                          'keterangan'	=>$keterangan,
                          'aksi'        =>'<div class="text-center">'.$this->get_status($list->status).'</div>'
                      );

        }

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $totalrecord,
            'recordsFiltered' => $totalrecord,
            'data' => $arr
        );

        //print_r($arr);
        return response()->json($data);

    }

    /*public function simpan_multi(Request $request){
        $id = $request->get('id');   
        for($i=0; $i<count($id); $i++){
            $id_stokopname = $id[$i];
            $data[$id_stokopname]['status'] = '2';
            $cek = DB::table('tbl_stokopname_baru')->where('id_stokopname',$id_stokopname)
                    ->orderBy('tanggal','DESC')->first();                    
            $id_barang = $cek->id_barang;
            $id_gudang = $cek->id_gudang;
            $id_satuan = $cek->id_satuan;
            $tanggal   = tgl_full($cek->created_at,'99');
            $tanggal_input = tgl_full($cek->tanggal,'99');
            $tanggal_time  = tgl_full($cek->created_at,'7'); 
            $fisik     = $cek->fisik;

            //DB::enableQueryLog();
            //$where_cek = "WHERE t.id_barang='".$id_barang."' AND t.id_ref_gudang='".$id_gudang."' AND t.id_satuan='".$id_satuan."' AND t.tanggal_opname<='".$tanggal."'";
            $where_cek = "WHERE t.id_barang='".$id_barang."' AND t.id_ref_gudang='".$id_gudang."' AND t.id_satuan='".$id_satuan."' AND t.created_at<='".$tanggal_time."'";

            $d_query[$i] = $this->cek_stok($where_cek);
            //$selisih = (float)$d_query[$i][0]->stok-$fisik;
            //dd(DB::getQueryLog());
            //print_r((float)$d_query[$i][0]->stok);exit;


            if(count($this->cek_stok($where_cek)) > 0){
            $data[$id_stokopname]['stok']   = $d_query[$i][0]->stok;
            $data[$id_stokopname]['fisik']  = $fisik;
            $data[$id_stokopname]['selisih']= (float)$d_query[$i][0]->stok-$fisik;
            $selisih = (float)$d_query[$i][0]->stok-$fisik;
            }else{
            $selisih = 0;
            }
            
            if($selisih > 0){
                $input[$id_stokopname]['unit_masuk']    = "0";
                $input[$id_stokopname]['unit_keluar']   = $selisih;
                $input[$id_stokopname]['status']        = 'S2';
            }else{
                $input[$id_stokopname]['unit_masuk']    = abs($selisih);
                $input[$id_stokopname]['unit_keluar']   = "0";
                $input[$id_stokopname]['status']        = 'S1';
            }                
                $input[$id_stokopname]['id_barang']     = $id_barang;
                $input[$id_stokopname]['id_ref_gudang'] = $id_gudang;
                $input[$id_stokopname]['tanggal']       = $tanggal_input;
                $input[$id_stokopname]['id_satuan']     = $id_satuan;
                $input[$id_stokopname]['created_at']    = $tanggal_time;
            
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input[$id_stokopname]);
            $data[$id_stokopname]['id_log_stok'] = $id_log_stok;                
            DB::table('tbl_stokopname_baru')->where('id_stokopname',$id_stokopname)->update($data[$id_stokopname]);
            
        } 
        //print_r($selisih);       
        return response()->json(array('status' => '1'));
    }*/

    public function simpan_multi(Request $request){
        $id = $request->get('id');   
        for($i=0; $i<count($id); $i++){
            $id_stokopname = $id[$i];
            $data[$id_stokopname]['status'] = '2';
            /*$cek = DB::table('tbl_stokopname_baru')->where('id_stokopname',$id_stokopname)
                    ->orderBy('tanggal','DESC')->first();                    
            $id_barang = $cek->id_barang;
            $id_gudang = $cek->id_gudang;
            $id_satuan = $cek->id_satuan;
            $tanggal   = tgl_full($cek->created_at,'99');
            $tanggal_input = tgl_full($cek->tanggal,'99');
            $tanggal_time  = tgl_full($cek->created_at,'7'); 
            $fisik     = $cek->fisik;
            //print_r($tanggal_time);exit();
            //DB::enableQueryLog();
            //$where_cek = "WHERE t.id_barang='".$id_barang."' AND t.id_ref_gudang='".$id_gudang."' AND t.id_satuan='".$id_satuan."' AND t.tanggal_opname<='".$tanggal."'";
            $where_cek = "WHERE t.id_barang='".$id_barang."' AND t.id_ref_gudang='".$id_gudang."' AND t.id_satuan='".$id_satuan."' AND t.created_at<'".$tanggal_time."'";
            
            $d_query[$i] = $this->cek_stok($where_cek);
            //$selisih = (float)$d_query[$i][0]->stok-$fisik;
            //dd(DB::getQueryLog());
            //print_r((float)$d_query[$i][0]->stok);exit;


            if(count($this->cek_stok($where_cek)) > 0){
            $data[$id_stokopname]['stok']   = $d_query[$i][0]->stok;
            $data[$id_stokopname]['fisik']  = $fisik;
            $data[$id_stokopname]['selisih']= (float)$d_query[$i][0]->stok-$fisik;
            $selisih = (float)$d_query[$i][0]->stok-$fisik;
            }else{
            $selisih = 0;
            }
            
            if($selisih > 0){
                $input[$id_stokopname]['unit_masuk']    = "0";
                $input[$id_stokopname]['unit_keluar']   = $selisih;
                $input[$id_stokopname]['status']        = 'S2';
            }else{
                $input[$id_stokopname]['unit_masuk']    = abs($selisih);
                $input[$id_stokopname]['unit_keluar']   = "0";
                $input[$id_stokopname]['status']        = 'S1';
            }                
                $input[$id_stokopname]['id_barang']     = $id_barang;
                $input[$id_stokopname]['id_ref_gudang'] = $id_gudang;
                $input[$id_stokopname]['tanggal']       = $tanggal_input;
                $input[$id_stokopname]['id_satuan']     = $id_satuan;
                $input[$id_stokopname]['created_at']    = $tanggal_time;
            
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input[$id_stokopname]);
            $data[$id_stokopname]['id_log_stok'] = $id_log_stok; */               
            DB::table('tbl_stokopname_baru')->where('id_stokopname',$id_stokopname)->update($data[$id_stokopname]);
            
        } 
        trigger_log(NULL, "Persetujuan Stokopname", 2);
        //print_r($selisih);       
        return response()->json(array('status' => '1'));
    }


    public function cek_stok($where){
        /*$cek = DB::select("SELECT * FROM
                        (
                        SELECT
                            tls.*,
                            tb.barang_kode AS kode_barang,
                            tb.barang_nama AS nama_barang,
                            rg.nama AS nama_gudang,
                            SUM( tls.unit_masuk - tls.unit_keluar ) AS stok,
                            ts.satuan_nama AS nama_satuan  ,
                        SUM( (tls.unit_masuk * ts.konversi) - (tls.unit_keluar * ts.konversi) ) as konversi
                        FROM
                            tbl_log_stok AS tls
                            LEFT JOIN tbl_barang AS tb ON tls.id_barang = barang_id
                            LEFT JOIN ref_gudang AS rg ON tls.id_ref_gudang = rg.id
                            LEFT JOIN tbl_satuan AS ts ON tls.id_satuan = ts.satuan_id
                        GROUP BY
                            id_barang,
                            id_ref_gudang, id_satuan
                        ORDER BY
                            tb.barang_nama
                        ) b
                        WHERE
                          b.stok NOT LIKE '%-%' $where ");*/
          $cek = DB::select("SELECT q.*, 
                              SUM( q.unit_masuk ) AS jumlah_masuk,
                              SUM( q.unit_keluar ) AS jumlah_keluar,
                              SUM( q.unit_masuk - q.unit_keluar ) AS stok
                              from (  
                            SELECT
                              t.id_barang,
                              b.barang_nama,
                              b.barang_kode,                      
                              t.id_satuan AS satuan_id,
                              s.satuan_nama,
                              s.satuan_satuan,
                              t.id_ref_gudang AS gudang_id,
                              g.nama AS gudang_nama,                      
                              t.tanggal_opname as tanggal,
                              t.unit_masuk,
                              t.unit_keluar,
                              t.status
                            FROM
                              view_stok_logopname AS t                   
                            JOIN tbl_barang AS b ON t.id_barang = b.barang_id
                            JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
                            lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id           
                            $where
                              ) q
                          GROUP BY
                            q.id_barang,
                            q.satuan_id,
                            q.gudang_id
                          ORDER BY
                            q.barang_nama ASC");
          /*$cek2 = DB::select("SELECT q.*, 
                              SUM( q.unit_masuk ) AS jumlah_masuk,
                              SUM( q.unit_keluar ) AS jumlah_keluar,
                              SUM( q.unit_masuk - q.unit_keluar ) AS stok
                              from (  
                                SELECT
                              t.id_barang,
                              b.barang_nama,
                              b.barang_kode,                      
                              t.id_satuan AS satuan_id,
                              s.satuan_nama,
                              s.satuan_satuan,
                              t.id_ref_gudang AS gudang_id,
                              g.nama AS gudang_nama,                      
                              t.tanggal_opname as tanggal,
                              t.unit_masuk,
                              t.unit_keluar,
                              t.status
                            FROM
                              view_stok_logopname AS t                   
                            JOIN tbl_barang AS b ON t.id_barang = b.barang_id
                            JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
                            lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id           
                            $where2
                              ) q
                          GROUP BY
                            q.id_barang,
                            q.satuan_id,
                            q.gudang_id
                          ORDER BY
                            q.barang_nama ASC");
          if(count($cek) > 0){
            $html = $cek;
          }else{
            $html = $cek;
          }*/
        return $cek;
    }


    public function get_check($status,$id){
      switch ($status) {
        case '1':
          $html = ' <input type="checkbox" id="check_verifikasi" class="check_verifikasi" value="'.$id.'" jenis="'.$status.'">';
          break;
        case '2':
          $html = '';
          break;
        default:
          $html = ' <input type="checkbox" id="check_verifikasi" class="check_verifikasi" value="'.$id.'" jenis="'.$status.'">';
          break;
      }
      return $html;
    }

   	public function get_status($status){
      switch ($status) {
        case '1':
          $html = '<label class="label label-sm label-danger">Belum Diterima</label>';
          break;
        case '2':
          $html = '<label class="label label-sm label-success">Sudah Diterima</label>';
          break;
        default:
          $html = '<label class="label label-sm label-danger">Belum Diterima</label>';
          break;
      }
      return $html;
    }


}

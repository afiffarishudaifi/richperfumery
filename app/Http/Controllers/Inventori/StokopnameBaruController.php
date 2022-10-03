<?php

namespace App\Http\Controllers\Inventori;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Jenssegers\Agent\Agent;
class StokopnameBaruController extends Controller
{
    //
    public function __construct(){
       $this->agent = new Agent();
    }
    public function index(){
    	  $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6 || $group == 8){
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
        $group_where = DB::table('tbl_group')->where('group_id',$group)->first();
        $data['gudang'] = DB::select("
        SELECT
          g.id as id_gudang,
          g.id_profil,
          g.nama as nama_gudang,
          g.alamat as alamat_gudang,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.jenis_outlet
        FROM
          ref_gudang g
          LEFT JOIN m_profil p ON g.id_profil = p.id $where_gudang
        ");
    	$data['satuan']     = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['user_group'] = Auth::user()->group_id;
      if ($this->agent->isMobile()) {

        $data['tombol_create'] = tombol_create('',$group_where->group_aktif,6,$group);
        return view('admin_mobile.stokopname_baru.index')->with('data',$data);
      }else {

        $data['tombol_create'] = tombol_create('',$group_where->group_aktif,3,$group);
        return view('admin.stokopname_baru.index')->with('data',$data);
      }
    }

    function detail($tanggal){
      $id_profil = Auth::user()->id_profil;
      $group = Auth::user()->group_id;
      $where = "";
        if($group == 5 || $group == 6  || $group == 8){
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
        $data['gudang'] = DB::select("
        SELECT
          g.id as id_gudang,
          g.id_profil,
          g.nama as nama_gudang,
          g.alamat as alamat_gudang,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.jenis_outlet
        FROM
          ref_gudang g
          LEFT JOIN m_profil p ON g.id_profil = p.id $where_gudang
        ");
      $data['satuan'] = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      if ($this->agent->isMobile()) {
        return view('admin_mobile.stokopname_baru.index')->with('data',$data);
      }else {
        return view('admin.stokopname_baru.index')->with('data',$data);
      }
    }

    public function get_gudang(Request $request){
    	$id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6  || $group == 8){
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
          $where_gudang = "AND g.id IN ($gudang)";
        }
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
        LEFT JOIN
        m_profil p ON g.id_profil = p.id
        WHERE
          p.jenis_outlet =1 and g.nama LIKE '%$search%' $where_gudang");

        return \Response::json($barangs);
    }

    public function get_barang(Request $request){
    	$term = trim($request->q);
        $gudang = trim($request->gudang);
        $tanggal = trim(tgl_full($request->tanggal,'99'));
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $id_gudang = strtolower($gudang);
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
              t.id_ref_gudang = '$id_gudang' AND t.tanggal < '$tanggal'
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
    
    public function get_barang_update(Request $request){
      // dd($request->all());
      $term = trim($request->q);
      $gudang = trim($request->gudang);
      $tanggal = trim(tgl_full($request->tanggal,'99'));
      // $tanggalrange = explode('s.d.',$request->tanggal);
      // $tanggal_start  = tgl_full($tanggalrange[0],99);
      // $tanggal_end    = tgl_full($tanggalrange[1],99);
      if (empty($term)) {
          return \Response::json([]);
      }

      $search     = strtolower($term);
      $id_gudang  = strtolower($gudang);
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
              t.id_ref_gudang = '$id_gudang' AND t.tanggal < '$tanggal'
            GROUP BY
              t.id_barang,
              t.id_ref_gudang,
              t.id_satuan
            ) l
            LEFT JOIN tbl_barang AS b ON l.id_barang = b.barang_id
            LEFT JOIN tbl_satuan AS s ON l.id_satuan = s.satuan_id
            LEFT JOIN ref_gudang AS rf ON l.id_ref_gudang = rf.id
            WHERE (b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%') AND l.id_satuan != '0'");
        // return \Response::json($d_query);
        return response()->json($d_query);
    }
    
    public function get_barang_update_only(Request $request){
      $input = $request->all();
      $d_search = (isset($input['q'])) ? trim($input['q']) : '';
      $search = strtolower($d_search);
      $page = (isset($input['page'])) ? $input['page']:'1';
      $page_limit = $page*10;
		  $page_start = $page_limit-10;
      $data = DB::table('tbl_barang as tb')->leftjoin('tbl_satuan as ts','tb.satuan_id','ts.satuan_id')
                      ->where('tb.barang_id_parent','!=','0');
      if($search != ''){
        $data = $data->where(function ($query) use ($search){
                  $query->orwhereRaw('LOWER(tb.barang_nama) LIKE ?',["%".$search."%"]);
                  $query->orwhereRaw('LOWER(tb.barang_kode) LIKE ?',["%".$search."%"]);
                  $query->orwhereRaw('LOWER(tb.barang_alias) LIKE ?',["%".$search."%"]);
                  $query->orwhereRaw('LOWER(ts.satuan_satuan) LIKE ?',["%".$search."%"]);
        });
      }

      $data = $data->selectRaw('tb.barang_id as id_barang, tb.barang_kode as kode_barang, tb.barang_nama as nama_barang, tb.barang_alias as alias_barang, tb.satuan_id as id_satuan, ts.satuan_nama as nama_satuan, ts.satuan_satuan')
              ->skip($page_start)->take($page_limit)->get();
      // dd($data2);
      return Response()->json($data);
    }

    public function listData()
    {
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6 || $group == 8){
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
        $group_where = DB::table('tbl_group')->where('group_id',$group)->first();

        $stok = DB::table('tbl_stokopname_baru as tsd')
              ->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')
              ->join('ref_gudang as rg','tsd.id_gudang','rg.id')
              ->join('tbl_satuan as ts','tsd.id_satuan','ts.satuan_id')
              ->whereIn('tsd.id_gudang',$id_gudang)
              ->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, 
              tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, 
              CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, 
              tsd.id_gudang, rg.nama as nama_gudang, tsd.stok as stok, tsd.fisik, tsd.selisih, tsd.id_satuan, 
              ts.satuan_satuan, tsd.id_log_stok, tsd.keterangan, tsd.status'))
              ->orderBy('tsd.id_stokopname','DESC');
        $no = 0;
        $data = array();
        foreach ($stok->get() as $list) {
            $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->alias_barang." || ".$list->satuan_satuan;;
            if($list->alias_barang == ""){
              $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->satuan_satuan;;
            }
            $keterangan = $list->keterangan;
            if($list->keterangan == "" || $list->keterangan == "null"){
              $keterangan = "";
            }
            if($group == 8){
              if($list->created_iduser == $sess_user){
                $btn = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$list->id_stokopname.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                        <a onclick="deleteData('.$list->id_stokopname.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
              }else{
                $btn = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail_form('.$list->id_stokopname.')" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i> </button></a>';
              }
            }else{
              $btn = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$list->id_stokopname.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                      <a onclick="deleteData('.$list->id_stokopname.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
            }

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = tgl_full($list->tanggal,'');
            $row[] = $nama_barang;
            $row[] = $list->nama_gudang;
            $row[] = $list->stok;
            $row[] = $list->fisik;
            $row[] = $list->selisih;
            $row[] = $keterangan;
            $row[] = $btn.
                        '<input type="hidden" id="table_id'.$list->id_stokopname.'" value="'.$list->id_stokopname.'">'.
                        '<input type="hidden" id="table_idbarang'.$list->id_stokopname.'" value="'.$list->id_barang.'">'.
                        '<input type="hidden" id="table_namabarang'.$list->id_stokopname.'" value="'.$nama_barang.'">'.
                        '<input type="hidden" id="table_idgudang'.$list->id_stokopname.'" value="'.$list->id_gudang.'">'.
                        '<input type="hidden" id="table_idsatuan'.$list->id_stokopname.'" value="'.$list->id_satuan.'">'.
                        '<input type="hidden" id="table_stok'.$list->id_stokopname.'" value="'.$list->stok.'">'.
                        '<input type="hidden" id="table_fisik'.$list->id_stokopname.'" value="'.$list->fisik.'">'.
                        '<input type="hidden" id="table_selisih'.$list->id_stokopname.'" value="'.$list->selisih.'">'.
                        '<input type="hidden" id="table_tanggal'.$list->id_stokopname.'" value="'.tgl_full($list->tanggal,'').'">'.
                        '<input type="hidden" id="table_keterangan'.$list->id_stokopname.'" value="'.$keterangan.'">'.
                        '<input type="hidden" id="table_idlog_stok'.$list->id_stokopname.'" value="'.$list->id_log_stok.'">'.
                        '<input type="hidden" id="table_status'.$list->id_stokopname.'" value="'.$list->status.'">';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

    public function listData2(Request $request)
    {
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6|| $group == 8){
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

        $tanggal = tgl_full($request->get('tanggal'),'99');
        $e_gudang = $request->get('gudang');

        $group_where = DB::table('tbl_group')->where('group_id',$group)->first();

        /*$stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')->where('tsd.tanggal','=',$tanggal)->whereIn('tsd.id_gudang',$id_gudang)->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, tsd.stok as stok, tsd.fisik, tsd.selisih, tsd.id_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tsd.id_stokopname','DESC');*/
        $stok = DB::table('tbl_stokopname_baru as tsd')
                ->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')
                ->join('ref_gudang as rg','tsd.id_gudang','rg.id')
                ->join('tbl_satuan as ts','tsd.id_satuan','ts.satuan_id')
                ->where('tsd.tanggal','=',$tanggal)->where('tsd.id_gudang',$e_gudang)
                ->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, 
                 tb.barang_nama as nama_barang, tb.barang_kode as kode_barang,
                 CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, 
                 rg.nama as nama_gudang, tsd.stok as stok, tsd.fisik, tsd.selisih, tsd.id_satuan, ts.satuan_satuan, 
                 tsd.id_log_stok, tsd.keterangan, tsd.status, tsd.created_iduser'))
                 ->orderBy('tsd.id_stokopname','DESC');
        $no = 0;
        $data = array();
        foreach ($stok->get() as $list) {
            $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->alias_barang." || ".$list->satuan_satuan;
            if($list->alias_barang == ""){
              $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->satuan_satuan;
            }
            $keterangan = $list->keterangan;
            if($list->keterangan == "" || $list->keterangan == "null"){
              $keterangan = "";
            }

            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['tanggal'] = tgl_full($list->tanggal,'');
              $row['nama_barang'] = $nama_barang;
              $row['nama_gudang'] = $list->nama_gudang;
              $row['fisik'] = $list->fisik;
              $row['keterangan'] = $keterangan;
              $row['aksi'] = $this->get_aksi($list->id_stokopname,$group_where->group_aktif,$list->created_iduser).
              '<input type="hidden" id="table_id'.$list->id_stokopname.'" value="'.$list->id_stokopname.'">'.
              '<input type="hidden" id="table_idbarang'.$list->id_stokopname.'" value="'.$list->id_barang.'">'.
              '<input type="hidden" id="table_namabarang'.$list->id_stokopname.'" value="'.$nama_barang.'">'.
              '<input type="hidden" id="table_idgudang'.$list->id_stokopname.'" value="'.$list->id_gudang.'">'.
              '<input type="hidden" id="table_namagudang'.$list->id_stokopname.'" value="'.$list->nama_gudang.'">'.
              '<input type="hidden" id="table_idsatuan'.$list->id_stokopname.'" value="'.$list->id_satuan.'">'.
              '<input type="hidden" id="table_stok'.$list->id_stokopname.'" value="'.$list->stok.'">'.
              '<input type="hidden" id="table_fisik'.$list->id_stokopname.'" value="'.$list->fisik.'">'.
              '<input type="hidden" id="table_selisih'.$list->id_stokopname.'" value="'.$list->selisih.'">'.
              '<input type="hidden" id="table_tanggal'.$list->id_stokopname.'" value="'.tgl_full($list->tanggal,'').'">'.
              '<input type="hidden" id="table_keterangan'.$list->id_stokopname.'" value="'.$keterangan.'">'.
              '<input type="hidden" id="table_idlog_stok'.$list->id_stokopname.'" value="'.$list->id_log_stok.'">'.
              '<input type="hidden" id="table_status'.$list->id_stokopname.'" value="'.$list->status.'">';
            }else {
              $row[] = $no;
              $row[] = tgl_full($list->tanggal,'');
              $row[] = $nama_barang;
              $row[] = $list->nama_gudang;
              $row[] = $list->fisik;
              $row[] = $keterangan;
              $row[] = $this->get_aksi($list->id_stokopname,$group_where->group_aktif,$list->created_iduser).
              '<input type="hidden" id="table_id'.$list->id_stokopname.'" value="'.$list->id_stokopname.'">'.
              '<input type="hidden" id="table_idbarang'.$list->id_stokopname.'" value="'.$list->id_barang.'">'.
              '<input type="hidden" id="table_namabarang'.$list->id_stokopname.'" value="'.$nama_barang.'">'.
              '<input type="hidden" id="table_idgudang'.$list->id_stokopname.'" value="'.$list->id_gudang.'">'.
              '<input type="hidden" id="table_namagudang'.$list->id_stokopname.'" value="'.$list->nama_gudang.'">'.
              '<input type="hidden" id="table_idsatuan'.$list->id_stokopname.'" value="'.$list->id_satuan.'">'.
              '<input type="hidden" id="table_stok'.$list->id_stokopname.'" value="'.$list->stok.'">'.
              '<input type="hidden" id="table_fisik'.$list->id_stokopname.'" value="'.$list->fisik.'">'.
              '<input type="hidden" id="table_selisih'.$list->id_stokopname.'" value="'.$list->selisih.'">'.
              '<input type="hidden" id="table_tanggal'.$list->id_stokopname.'" value="'.tgl_full($list->tanggal,'').'">'.
              '<input type="hidden" id="table_keterangan'.$list->id_stokopname.'" value="'.$keterangan.'">'.
              '<input type="hidden" id="table_idlog_stok'.$list->id_stokopname.'" value="'.$list->id_log_stok.'">'.
              '<input type="hidden" id="table_status'.$list->id_stokopname.'" value="'.$list->status.'">';
            }
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

    public function listDataTanggal(Request $request){
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6  || $group == 8){
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
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')->join('tbl_satuan as ts','tsd.id_satuan','ts.satuan_id')->where('tsd.tanggal','>',$tanggal_start)->where('tsd.tanggal','<=',$tanggal_end)->whereIn('tsd.id_gudang',$id_gudang)->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, sum(tsd.stok) as stok, count(tsd.fisik) as fisik, sum(tsd.selisih) as selisih, tsd.id_satuan, ts.satuan_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tsd.tanggal','DESC')->groupBy('tsd.tanggal','tsd.id_gudang');

        if($search){
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')
          ->where('tsd.tanggal','>',$tanggal_start)
          ->where('tsd.tanggal','<=',$tanggal_end)
          ->where(function($query) use ($search,$tanggal) {
                $query->orwhere('tsd.tanggal','like','%'.$tanggal.'%');
                $query->orWhere('rg.nama','like','%'.$search.'%');
          })
          ->whereIn('tsd.id_gudang',$id_gudang)->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, sum(tsd.stok) as stok, count(tsd.fisik) as fisik, sum(tsd.selisih) as selisih, tsd.id_satuan, ts.satuan_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tsd.tanggal','DESC')->groupBy('tsd.tanggal','tsd.id_gudang');
        }

        $totalrecord = count($stok->get());
        $stok_2 = $stok->offset($start)->limit($length);
        $no=($start==0)?0:$start;
        $arr = array();
        foreach ($stok_2->get() as $list) {
            $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->alias_barang." || ".$list->satuan_satuan;
            if($list->alias_barang == ""){
              $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->satuan_satuan;
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
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6  || $group == 8){
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
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')->join('tbl_satuan as ts','tsd.id_satuan','ts.satuan_id')->where('tsd.tanggal','>=',$tanggal_start)->where('tsd.tanggal','<=',$tanggal_end)->whereIn('tsd.id_gudang',$id_gudang)->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, sum(tsd.stok) as stok, count(tsd.fisik) as fisik, sum(tsd.selisih) as selisih, tsd.id_satuan, ts.satuan_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tsd.tanggal','DESC')->groupBy('tsd.tanggal','tsd.id_gudang');

        if($search){
        $stok = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')
          ->where('tsd.tanggal','>=',$tanggal_start)
          ->where('tsd.tanggal','<=',$tanggal_end)
          ->where(function($query) use ($search,$tanggal) {
                $query->orwhere('tsd.tanggal','like','%'.$tanggal.'%');
                $query->orWhere('rg.nama','like','%'.$search.'%');
          })
          ->whereIn('tsd.id_gudang',$id_gudang)->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, sum(tsd.stok) as stok, count(tsd.fisik) as fisik, sum(tsd.selisih) as selisih, tsd.id_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tsd.tanggal','DESC')->groupBy('tsd.tanggal','tsd.id_gudang');
        }

        $totalrecord = count($stok->get());
        $stok_2 = $stok->offset($start)->limit($length);
        $no=($start==0)?0:$start;
        $arr = array();
        foreach ($stok_2->get() as $list) {
            $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->alias_barang." || ".$list->satuan_satuan;
            if($list->alias_barang == ""){
              $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->satuan_satuan;
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
    
    function simpan(Request $request){
      DB::beginTransaction();
        $id = $request->get("popup_id_table");
        $tabel_idgudang   = $request->get("popup_gudang");
        $tabel_idsatuan   = $request->get("popup_satuan");
        $tabel_fisik      = $request->get("popup_fisik");
        $tabel_tanggal    = ($request->get("popup_tanggal")=="") ? tgl_full(date('d-m-Y'), 99):tgl_full($request->get('popup_tanggal'), 99);
        $tabel_keterangan = $request->get("popup_keterangan");
        $tabel_idlog      = $request->get("popup_idlog_stok");
        $tabel_status     = $request->get("popup_status");

        $barang = explode('/',$request->get('popup_barang'));
        $tabel_idbarang = $barang[0];

        $data['id_barang']    = $tabel_idbarang;
        $data['id_gudang']    = $tabel_idgudang;
        $data['id_satuan']    = $tabel_idsatuan;
        $data['fisik']        = $tabel_fisik;
        $data['tanggal']      = $tabel_tanggal;
        $data['keterangan']   = $tabel_keterangan;        

        $tanggal = tgl_full($request->get('popup_tanggal'),99); 
        $where_cek = "WHERE t.id_barang='".$tabel_idbarang."' AND t.id_ref_gudang='".$tabel_idgudang."' AND t.id_satuan='".$tabel_idsatuan."' AND t.tanggal<'".$tanggal."'";
        $cek = $this->cek_stok($where_cek);
        if($cek){
        $cek_selisih = $cek[0]->stok-$tabel_fisik;
        $data['stok'] = $cek[0]->stok;
        }else{
        $cek_selisih = 0-$tabel_fisik;
        $data['stok'] = 0;  
        }


        if($cek_selisih > 0){
          $input['unit_masuk']    = "0";
          $input['unit_keluar']   = $cek_selisih;
          $input['status']        = 'S2';

          $data['selisih'] = $cek_selisih;
        }else{
          $input['unit_masuk']    = abs($cek_selisih);
          $input['unit_keluar']   = "0";
          $input['status']        = 'S1';

          $data['selisih'] = abs($cek_selisih);
        }
          $input['id_barang']     = $tabel_idbarang;
          $input['id_ref_gudang'] = $tabel_idgudang;
          $input['tanggal']       = $tabel_tanggal;
          $input['id_satuan']     = $tabel_idsatuan;

        try{
        if($id == ''){            
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);
            $data['id_log_stok']  = $id_log_stok;            
            $data['status']       = '2';
            // Create User input
            $data['created_by']   = Auth::user()->name;
            $data['created_iduser'] = Auth::user()->id; 
            $id_stokopnamebaru = DB::table('tbl_stokopname_baru')->insertGetId($data);
            trigger_log($id_stokopnamebaru, "Menambahkan Data Stokopname", 1);
            $status = array('status'=>1);
        }else{
            $data['id_log_stok']  = $tabel_idlog;
            $data['status']       = $tabel_status;
            // Create User input
            $data['updated_by']   = Auth::user()->name;
            $data['updated_iduser'] = Auth::user()->id;  
            DB::table('tbl_log_stok')->where('log_stok_id',$tabel_idlog)->update($input);
            DB::table('tbl_stokopname_baru')->where('id_stokopname',$id)->update($data);
            trigger_log($id, "Mengubah Data Stokopname", 2);
            $status = array('status'=>2);
        }

        DB::commit();
        }catch(\Exception $e){
          DB::rollback();
          $status = array('status'=>0);
        }

        return response()->json($status);
    }

    function simpan2(Request $request){
        $id = $request->get("popup_id_table");

        $tabel_idbarang   = $request->get("popup_barang");
        $tabel_idgudang   = $request->get("popup_gudang");
        $tabel_idsatuan   = $request->get("popup_satuan");
        $tabel_stok       = $request->get("popup_stok");
        $tabel_fisik      = $request->get("popup_fisik");
        $tabel_selisih    = $request->get("popup_selisih");
        $tabel_tanggal    = ($request->get("popup_tanggal")=="") ? tgl_full(date('d-m-Y'), 99):tgl_full($request->get('popup_tanggal'), 99);
        $tabel_keterangan = $request->get("popup_keterangan");
        $tabel_idlog      = $request->get("popup_idlog_stok");
        $tabel_status     = $request->get("popup_status");

        $data['id_barang']    = $tabel_idbarang;
        $data['id_gudang']    = $tabel_idgudang;
        $data['id_satuan']    = $tabel_idsatuan;
        $data['stok']         = $tabel_stok;
        $data['fisik']        = $tabel_fisik;
        $data['selisih']      = $tabel_selisih;
        $data['tanggal']      = $tabel_tanggal;
        $data['keterangan']   = $tabel_keterangan;        

        /*$data['id_barang'] = $request->get("popup_barang");
        $data['id_gudang'] = $request->get("popup_gudang");
        $data['id_satuan'] = $request->get("popup_satuan");
        $data['stok'] = $request->get("popup_stok");
        $data['fisik'] = $request->get("popup_fisik");
        $data['selisih'] = $request->get("popup_selisih");
        $data['tanggal'] = ($request->get("popup_tanggal")=="") ? tgl_full(date('d-m-Y'), 99):tgl_full($request->get('popup_tanggal'), 99);
        $data['keterangan'] = $request->get('popup_keterangan');
        $data['id_log_stok'] = $request->get("popup_idlog_stok");*/
        //DB::enableQueryLog(); 
        $tanggal_time = tgl_full($request->get('popup_tanggal'),6);
        $where_cek = "WHERE t.id_barang='".$tabel_idbarang."' AND t.id_ref_gudang='".$tabel_idgudang."' AND t.id_satuan='".$tabel_idsatuan."' AND t.created_at<='".$tanggal_time."'";
        $cek = $this->cek_stok($where_cek);
        //dd(DB::getQueryLog());
        if($cek){
        $cek_selisih = $cek[0]->stok-$tabel_fisik;
        }else{
        $cek_selisih = 0-$tabel_fisik;  
        }
        if($cek_selisih == $tabel_selisih){
          $selisih['selisih'] = $tabel_selisih; 
          $selisih['text']    = "tabel";
          $selisih['status']  = "1";
        }else{
          $selisih['selisih'] = $cek_selisih;
          $selisih['text']    = "cek";
          $selisih['status']  = "2";
        }

        if($tabel_selisih > 0){
          $input['unit_masuk']    = "0";
          $input['unit_keluar']   = $tabel_selisih;
          $input['status']        = 'S2';
        }else{
          $input['unit_masuk']    = abs($tabel_selisih);
          $input['unit_keluar']   = "0";
          $input['status']        = 'S1';
        }
          $input['id_barang']     = $tabel_idbarang;
          $input['id_ref_gudang'] = $tabel_idgudang;
          $input['tanggal']       = $tabel_tanggal;
          $input['id_satuan']     = $tabel_idsatuan;

        if($id == ''){            
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);
            $data['id_log_stok']  = $id_log_stok;            
            $data['status']       = '2';
            $id_stokopnamebaru = DB::table('tbl_stokopname_baru')->insertGetId($data);
            trigger_log($id_stokopnamebaru, "Menambahkan Data Stokopname", 1);
        }else{
            $data['id_log_stok']  = $tabel_idlog;
            $data['status']       = $tabel_status;
            DB::table('tbl_log_stok')->where('log_stok_id',$tabel_idlog)->update($input);
            DB::table('tbl_stokopname_baru')->where('id_stokopname',$id)->update($data);
            trigger_log($id, "Mengubah Data Stokopname", 2);
        }

        return response()->json(array('status' => '1'));
    }

    public function hapus(Request $request){
      $id = $request->get('id');
      $d_barang = DB::table('tbl_stokopname_baru')->where('id_stokopname',$id)->first();
      $id_log_stok = $d_barang->id_log_stok;
      DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->delete();
      DB::table('tbl_stokopname_baru')->where('id_stokopname',$id)->delete();
      trigger_log($id, "Menghapus Data Stokopname", 3);
    }

    public function cek_stok($where){
      $html = DB::SELECT("SELECT q.*, 
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
      return $html;
    }
    
    public function update(Request $request){
        ini_set('max_execution_time', '0');
        DB::beginTransaction();
        $barang = explode('/',$request->get('update_barang'));
        $tanggal        = tgl_full($request->get('update_tanggal'),99);
        $tanggal_barang = $barang[0];
        $tanggal_gudang = $request->get('update_gudang');

        $where = array();
        if(!empty($tanggal)){
          $where[] = "tanggal <= '$tanggal'";
          // $where[] = "tanggal <= '2020-02-10'";
        }
        if(!empty($tanggal_barang)){
          $where[] = "id_barang = '$tanggal_barang'";
        }
        if(!empty($tanggal_gudang)){
          $where[] = "id_gudang = '$tanggal_gudang'";
        }

        $add_where = " WHERE ";
        if(count($where) > 0){
        $where = $add_where.implode(' AND ', $where);
        }else{
        $where = '';
        }
        
        try{
        $query = DB::SELECT("SELECT * FROM view_stok_stokopname $where");
        foreach($query as $key => $value){
            $data['tanggal']      = tgl_full($value->tanggal,99);
            $data['id_barang']    = $value->id_barang;
            $data['id_gudang']    = $value->id_gudang;
            $data['id_satuan']    = $value->id_satuan;
            $data['status']       = $value->status;
            $data['fisik']        = $value->fisik;

            $w_barang   = $value->id_barang; 
            $w_tanggal  = tgl_full($value->tanggal,99);
            $w_gudang   = $value->id_gudang; 
            $w_satuan   = $value->id_satuan;
            $where_cek = "WHERE t.id_barang='".$w_barang."' AND t.id_ref_gudang='".$w_gudang."' AND t.id_satuan='".$w_satuan."' AND t.tanggal<'".$w_tanggal."'";
            $d_query = $this->cek_stok($where_cek);
            // dd($d_query);
            if(count($d_query) > 0){
              $stok     = $d_query[0]->stok;
              $selisih  = (float)$d_query[0]->stok-$value->fisik;
            }else{
              $stok     = 0;
              $selisih  = 0;
            }
            $data['stok']     = $stok;
            $data['selisih']  = $selisih;

            // dd($data);
            if($selisih > 0){
              $input['unit_masuk']  = "0";
              $input['unit_keluar'] = $selisih;
              $input['status']      = 'S2';
            }else{
              $input['unit_masuk']  = abs($selisih);
              $input['unit_keluar'] = "0";
              $input['status']      = 'S1';
            }
            $input['id_barang']     = $value->id_barang;
            $input['id_ref_gudang'] = $value->id_gudang;
            $input['id_satuan']     = $value->id_satuan;
            $input['tanggal']       = tgl_full($value->tanggal,'99');

            // dd($data, $value->id_stokopname);
            DB::table('tbl_log_stok')->where('log_stok_id',$value->id_log_stok)->update($input);
            DB::table('tbl_stokopname_baru')->where('id_stokopname',$value->id_stokopname)->update($data);

        }
        DB::commit();
      }catch(\Exception $e){
        DB::rollback();
      }
        return redirect('stokopnamebaru');

    }

    public function update2(){
        ini_set('max_execution_time', '0');
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
          $where_gudang = "WHERE id_gudang IN ($gudang) AND status='1'";
        }
        //DB::enableQueryLog();
        //$query = $this->cek_stok($where_gudang);
        $query = DB::SELECT("SELECT * FROM view_stok_stokopname $where_gudang");
        foreach($query as $key => $value){
            $barang[$key]['id_stokopname'] = $value->id_stokopname;
            $barang[$key]['tanggal']       = $value->tanggal;
            $barang[$key]['id_barang']     = $value->id_barang;
            $barang[$key]['id_gudang']     = $value->id_gudang;
            $barang[$key]['id_satuan']     = $value->id_satuan;
            $barang[$key]['id_log_stok']   = $value->id_log_stok;
            $barang[$key]['status']        = $value->status;
            $barang[$key]['stok']          = $value->stok;
            $barang[$key]['fisik']         = $value->fisik;
            $barang[$key]['selisih']       = $value->selisih;
            $barang[$key]['created_at']    = $value->created_at;
            $barang[$key]['updated_at']    = $value->updated_at;
            $barang[$key]['tanggal_time']  = tgl_full($value->created_at,'7');

            $where_cek[$key] = "WHERE t.id_barang='".$barang[$key]['id_barang']."' AND t.id_ref_gudang='".$barang[$key]['id_gudang']."' AND t.id_satuan='".$barang[$key]['id_satuan']."' AND t.created_at<'".$barang[$key]['tanggal_time']."'";
            $d_query[$key] = $this->cek_stok($where_cek[$key]);

            if(count($this->cek_stok($where_cek[$key])) > 0){              
            $selisih = (float)$d_query[$key][0]->stok-$barang[$key]['fisik'];
            }else{
            $selisih = 0;
            }
            
            if($selisih > 0){
                $input[$key]['unit_masuk']    = "0";
                $input[$key]['unit_keluar']   = $selisih;
                $input[$key]['status']        = 'S2';
            }else{
                $input[$key]['unit_masuk']    = abs($selisih);
                $input[$key]['unit_keluar']   = "0";
                $input[$key]['status']        = 'S1';
            }                
                $input[$key]['id_barang']     = $barang[$key]['id_barang'];
                $input[$key]['id_ref_gudang'] = $barang[$key]['id_gudang'];
                $input[$key]['tanggal']       = $barang[$key]['tanggal'];
                $input[$key]['id_satuan']     = $barang[$key]['id_satuan'];
                $input[$key]['created_at']    = $barang[$key]['tanggal_time'];

            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input[$key]);
            $data[$key]['id_log_stok'] = $id_log_stok; 
            $data[$key]['status'] = "2";               
            DB::table('tbl_stokopname_baru')->where('id_stokopname',$barang[$key]['id_stokopname'])->update($data[$key]);

        }
        //print_r($input);exit();
        //dd(DB::getQueryLog());
        return redirect('stokopnamebaru');

    }
    
    public function update_nontanggal(Request $request){
        ini_set('max_execution_time', '0');
        $barang = explode('/',$request->get('update_barang'));
        $tanggal        = tgl_full($request->get('update_tanggal'),99);
        $tanggal_barang = $barang[0];
        $tanggal_gudang = $request->get('update_gudang');
        $tanggal_cek = $request->get('update_tanggal');
        $tanggal_satuan = isset($barang[1])?$barang[1]:'';
        // dd($request->get('update_barang')."/".$request->get('update_gudang'));

        $where = array();
        if(isset($tanggal_cek)){
          $where[] = "tanggal <= '".$tanggal."'";
        }
        if(!empty($tanggal_barang)){
          $where[] = "id_barang = '$tanggal_barang'";
        }
        if(!empty($tanggal_gudang)){
          $where[] = "id_gudang = '$tanggal_gudang'";
        }
        if($tanggal_satuan != ''){
          $where[] = "id_satuan = '$tanggal_satuan'";
        }

        $add_where = " WHERE ";
        if(count($where) > 0){
        $where = $add_where.implode(' AND ', $where);
        }else{
        $where = '';
        }
        
        try{
        $query = DB::SELECT("SELECT * FROM view_stok_stokopname $where");
        foreach($query as $key => $value){
            $data['tanggal']      = tgl_full($value->tanggal,99);
            $data['id_barang']    = $value->id_barang;
            $data['id_gudang']    = $value->id_gudang;
            $data['id_satuan']    = $value->id_satuan;
            $data['status']       = $value->status;
            $data['fisik']        = $value->fisik;

            $w_barang   = $value->id_barang; 
            $w_tanggal  = tgl_full($value->tanggal,99);
            $w_gudang   = $value->id_gudang; 
            $w_satuan   = $value->id_satuan;
            $where_cek = "WHERE t.id_barang='".$w_barang."' AND t.id_ref_gudang='".$w_gudang."' AND t.id_satuan='".$w_satuan."' AND t.tanggal<'".$w_tanggal."'";
            $d_query = $this->cek_stok($where_cek);

            if(count($d_query) > 0){
              $stok     = $d_query[0]->stok;
              $selisih  = (float)$d_query[0]->stok-$value->fisik;
            }else{
              $stok     = 0;
              $selisih  = 0;
            }
            $data['stok']     = $stok;
            $data['selisih']  = $selisih;

            if($selisih > 0){
              $input['unit_masuk']  = "0";
              $input['unit_keluar'] = $selisih;
              $input['status']      = 'S2';
            }else{
              $input['unit_masuk']  = abs($selisih);
              $input['unit_keluar'] = "0";
              $input['status']      = 'S1';
            }
            $input['id_barang']     = $value->id_barang;
            $input['id_ref_gudang'] = $value->id_gudang;
            $input['id_satuan']     = $value->id_satuan;
            $input['tanggal']       = tgl_full($value->tanggal,'99');
            
            DB::table('tbl_log_stok')->where('log_stok_id',$value->id_log_stok)->update($input);
            DB::table('tbl_stokopname_baru')->where('id_stokopname',$value->id_stokopname)->update($data);

        }
        DB::commit();
      }catch(\Exception $e){
        DB::rollback();
        // dd($e);
      }
        return redirect('stokopnamebaru');

    }

    public function cek_unitmasuk(){
      $html = DB::SELECT("INSERT INTO tbl_log_stok 
        ('', id_barang, id_ref_gudang, id_satuan, tanggal, unit_masuk, unit_keluar, status, created_at)
        SELECT '' as id_log_stok, id_barang, id_gudang, id_satuan, tanggal, selisih, '0', 'S1', created_at
        FROM tbl_stokopname_baru WHERE selisih <= 0 AND status=2 AND tanggal = '2019-10-28' AND id_gudang=8");
      return $html;
    }

    public function cek_unitkeluar(){
      $html = DB::SELECT("INSERT INTO tbl_log_stok 
        ('', id_barang, id_ref_gudang, id_satuan, tanggal, unit_masuk, unit_keluar, status, created_at)
        SELECT '' as id_log_stok, id_barang, id_gudang, id_satuan, tanggal, '0', selisih, 'S2', created_at
        FROM tbl_stokopname_baru WHERE selisih > 0 AND status=2 AND tanggal = '2019-10-28' AND id_gudang=8");
    }

    function get_aksi($id,$status,$input_user){
      $sess = Auth::user();
      switch ($status) {
        case '2':
          # code...
          if($sess->group_id == 8){
            if($input_user == $sess->id){
              /*$html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';*/
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail_form('.$id.')" title="Edit Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div>';
            }else{
              $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail_form('.$id.')" title="Edit Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div>';
            }
          }else{
            /*$html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';*/
            if($sess->group_id == 5){
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail_form('.$id.')" title="Edit Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div>';
            }else{
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
            }
          }
          break;
        case '1':
          $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail_form('.$id.')" title="Edit Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div>';
          break;        
        default:
          // $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
          //     <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
          /*if($sess->group_id == 8){
            if($input_user == $sess->id){
              $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
            }else{
              $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail_form('.$id.')" title="Edit Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div>';
            }
          }else{
            $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
          }*/
          if($sess->group_id == 8){
            if($input_user == $sess->id){
              /*$html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';*/
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail_form('.$id.')" title="Edit Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div>';
            }else{
              $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail_form('.$id.')" title="Edit Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div>';
            }
          }else{
            /*$html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';*/
            if($sess->group_id == 5){
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail_form('.$id.')" title="Edit Data" style="color:white;"><i class="fa fa-eye"></i> </button></a></div>';
            }else{
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
            }
          }
          break;
      }
      return $html;
    }
}

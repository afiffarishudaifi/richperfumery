<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent;

use Yajra\Datatables\Datatables;

class ProdukPoinController extends Controller
{
    //

    public function index(){
    	$data['hari'] = \Config::get('constants.hari');
    	return view('admin.poin.index_produk')->with('data',$data);
    }

    public function detail($d_id){
        $id = dec($d_id);
        $d_produk = DB::table('m_produk')->where('id',$id)->first();
        $data['id_produk'] = $id;
        $data['nama_produk'] = $d_produk->nama;
        $data['hari'] = \Config::get('constants.hari');
        return view('admin.poin.index_detail')->with('data',$data);
    }

    public function get_gudang(Request $request){
    	$term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT
          g.id as id_gudang,
          g.nama as nama_gudang,
          g.alamat as alamat_gudang,
          g.kode as kode_gudang
        FROM
          ref_gudang as g
         WHERE g.kode LIKE '%$search%' OR g.nama LIKE '%$search%'");
        return \Response::json($d_query);
    }

    public function get_data(Request $request){
        $id_produk = $request->id_produk;

    	$data = DB::table('m_produkpoin as mpo')->leftjoin('m_produk as mp','mpo.id_produk','mp.id')->where('mp.id',$id_produk)->SELECT(DB::Raw('mpo.*, mp.nama as nama_produk, mp.kode_produk'));   

        return Datatables::of($data)
               ->addColumn('kategori', function($data){
                    if($data->kategori==1){
                      $kategori = 'Rutin';
                    }else if($data->kategori==2){
                      $kategori = 'Khusus';
                    }else{
                      $kategori = 'Keseluruhan';
                    }
                    return $kategori;
               })
               ->addColumn('d_hari', function($data){
                    if($data->kategori==1){
                      $hari = tgl_full($data->hari,'100');
                    }else{
                      $hari = '';
                    }
                    return $hari;
               })
               ->addColumn('d_tanggal', function($data){
                    if($data->kategori==2){
                      $tanggal = tgl_full($data->tanggal,'');
                    }else{
                      $tanggal = '';
                    }
                    return $tanggal;
               })
               ->addColumn('d_poin', function($data){
                    return format_angka($data->poin);
               })
               ->addColumn('aksi',function($data){
                    $aksi = '<div class="btn-group"><a onclick="editForm('.$data->id.')" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
                        <a onclick="deleteData('.$data->id.','.$data->id_produk.')" class="btn btn-danger btn-xs" data-toggle="tooltip" 
                                    data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>'.
                            '<input type="hidden" name="table_id" id="table_id'.$data->id.'" value="'.$data->id.'">'.
                            '<input type="hidden" name="table_idproduk" id="table_idproduk'.$data->id.'" value="'.$data->id_produk.'">'.
                            '<input type="hidden" name="table_namaproduk" id="table_namaproduk'.$data->id.'" value="'.$data->nama_produk.'">'.
                            '<input type="hidden" name="table_kodeproduk" id="table_kodeproduk'.$data->id.'" value="'.$data->kode_produk.'">'.
                            '<input type="hidden" name="table_kategori" id="table_kategori'.$data->id.'" value="'.$data->kategori.'">'.
                            '<input type="hidden" name="table_hari" id="table_hari'.$data->id.'" value="'.$data->hari.'">'.
                            '<input type="hidden" name="table_tanggal" id="table_tanggal'.$data->id.'" value-"'.tgl_full($data->tanggal,'99').'">'.
                            '<input type="hidden" name="table_poin" id="table_poin'.$data->id.'" value="'.$data->poin.'"></div>';
                    return $aksi;
               })
               ->rawColumns(['aksi'])
               ->make(true);     
    }


    public function get_data_group(Request $request){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');  
        $limit = is_null($request["length"]) ? 25 : $request["length"];
        $offset = is_null($request["start"]) ? 0 : $request["start"];
        $searchs = $request["search.value"];
        $dirs = array("asc", "desc");
        $draw = $request["draw"];

        $count = 0;
        if($searchs != ''){
            $count += 1;
            $where[]  = "mp.nama LIKE '%$searchs%'";
        }

        $arr['where'] = '';
        if($count > 0){
            $where = " WHERE ".implode(' AND ', $where);
            $arr['where'] = $where;
        }

        $arr['orderBy'] = "ORDER BY mp.nama ASC";
        $arr['groupBy'] = "GROUP BY mp.id";
        $arr['limit'] = " LIMIT ".$offset.",".$limit;
        $query = get_produk_group($arr);

        $totalDataRecord = $query['total'][0]->total;
        $data = $query['data'];

        return Datatables::of($data)
               ->addColumn('aksi',function($data){
                    return "<div class='btn-group'><a href='".url('produkpoin_detail/'.enc($data->id))."' class='btn btn-primary btn-xs'><i class='fa fa-eye'></i></a></div>";
               })
               ->setTotalRecords($totalDataRecord)
               ->setFilteredRecords($totalDataRecord)
               ->rawColumns(['aksi'])
               ->make(true);
    }

    public function get_produk(Request $request){
    	$term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT
          mp.id as id_produk,
          mp.kode_produk,
          mp.nama as nama_produk,
          mp.harga as harga_produk
        FROM
          m_produk as mp
         WHERE mp.kode_produk LIKE '%$search%' OR mp.nama LIKE '%$search%' order by mp.nama asc");
        // return \Response::json($d_query);
        return Response()->json($d_query);
    }

    public function simpan(Request $request){
    	$id = $request->popup_id;
    	$data['id_produk'] 	= $request->get('popup_produk');
    	$data['kategori'] 	= $request->get('popup_kategori');
        if($request->get('popup_kategori') == 1){
    	$data['hari'] 		= $request->get('popup_hari');
        $data['tanggal']    = null;
        }else if($request->get('popup_kategori') == 2){
        $data['hari']       = null;
        $data['tanggal']    = tgl_full($request->get('popup_tanggal'),'99');
        }else{
        $data['hari']       = null;
    	$data['tanggal'] 	= null;
        }
    	$data['poin'] 		= $request->get('popup_poin');

    	if($id == ''){
    		DB::table('m_produkpoin')->insert($data);
    	}else{
    		DB::table('m_produkpoin')->where('id','=',$id)->update($data);
    	}
    }


    public function hapus(Request $request){
    	$id = $request->id;
        $produk = $request->produk;

        DB::beginTransaction();
        try{
    	   DB::table('m_produkpoin')->where('id','=',$id)->delete();
           DB::commit();
           $cek = DB::table('m_produkpoin')->where('id_produk')->count();
           $data['jum'] = $cek;
           $data['status'] = 1;
        }catch(\Exception $e){
            DB::rollback();
            $data['jum'] = 0;
            $data['status'] = 0;
        }

        return response()->json($data);
    }
}

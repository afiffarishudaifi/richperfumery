<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Jenssegers\Agent\Agent;

class ClosingKasirController extends Controller
{
    //
    public function __construct(){
      $this->agent = new Agent();
    }
    public function index(){
    	$id_profil = Auth::user()->id_profil;
      	$group = Auth::user()->group_id;
      	$where = "";
	      	if($group == 5 || $group == 8){
	        $where = "WHERE id_profil='$id_profil'";
	      	}
        $group_where = DB::table('tbl_group')->where('group_id',$group)->first();

     	$d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
	        foreach($d_gudang as $d){
	          $id_gudang[] = $d->id_gudang;
	        }
        $where_gudang = "";
	        if(sizeof($id_gudang) > 0){
	          $gudang = implode(',',$id_gudang);
	          $where_gudang = "WHERE rf.id IN ($gudang)";
	        }

    	$data['gudang'] = DB::select(base_gudang($where));
        $data['group']  = Auth::user()->group_id;
        $data['tombol_create'] = tombol_create('',$group_where->group_aktif,7,$group);
        if ($this->agent->isMobile()) {
          // code...
          return view('admin_mobile.kasir.index_closing')->with('data',$data);
        }else {
          // code...
          return view('admin.kasir.index_closing')->with('data',$data);
        }
    }
    
    public function get_omset(Request $request){
        $tanggal = tgl_full($request->tanggal,'99');
        $gudang = $request->gudang;
        
        $d_metodebayar = DB::table('tbl_kasir as k')
                        ->leftjoin('m_metode as m','k.metodebayar','m.id')
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw("k.id_gudang = ?",[$keyword]);
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw("k.tanggal_faktur = ?",[$keyword]);
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy('k.metodebayar','k.metodebayar2','m.nama')
                        ->selectRaw('k.metodebayar, m.nama as metode, m.urutan, 
                        CASE WHEN k.metodebayar2 IS NOT NULL THEN SUM( k.total_metodebayar ) 
                        ELSE SUM( k.total_tagihan - k.total_potongan ) END AS jumlah');


        $d_metodebayar2 = DB::table('tbl_kasir as k')
                        ->join('m_metode as m','k.metodebayar2','m.id')
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.id_gudang = ?',[$keyword]);
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.tanggal_faktur = ?',[$keyword]);
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy('k.metodebayar','k.metodebayar2','m.nama')
                        ->selectRaw('k.metodebayar2 as metodebayar, m.nama as metode, m.urutan,
                        CASE WHEN k.metodebayar2 IS NOT NULL THEN SUM( k.total_metodebayar2 ) 
                        ELSE 0 END AS jumlah');
        
        $d_metodeongkir = DB::table("tbl_kasir as k")
                        ->join("m_metode as m","k.metodeongkir","m.id")
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy("k.metodeongkir","k.metodeongkir2","m.nama")
                        ->selectRaw("k.metodeongkir as metodebayar, m.nama as metode, m.urutan, 
                        CASE WHEN k.metodeongkir IS NOT NULL THEN CONCAT('-',SUM( k.total_metodeongkir )) 
                        ELSE 0 END AS jumlah");
        
        $d_metodeongkir2 = DB::table("tbl_kasir as k")
                        ->join("m_metode as m","k.metodeongkir2","m.id")
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy("k.metodeongkir","k.metodeongkir2","m.nama")
                        ->selectRaw("k.metodeongkir2 as metodebayar, m.nama as metode, m.urutan, 
                        CASE WHEN k.metodeongkir2 IS NOT NULL THEN CONCAT('-',SUM( k.total_metodeongkir2 )) ELSE 0 END AS jumlah");

        /*$d_left_subquery = DB::table(DB::raw("({$d_metodebayar->unionAll($d_metodebayar2)->unionAll($d_metodeongkir)->unionAll($d_metodeongkir2)->toSql()}) as q"))
                        ->mergeBindings($d_metodebayar->unionAll($d_metodebayar2)->unionAll($d_metodeongkir)->unionAll($d_metodeongkir2))
                        ->selectRaw("q.metodebayar, q.metode, q.urutan, SUM(q.jumlah) AS jumlah")
                        ->groupBy('q.metodebayar','q.metode');*/
        $d_left_subquery = DB::table(DB::raw("({$d_metodebayar->unionAll($d_metodebayar2)->toSql()}) as q"))
                        ->mergeBindings($d_metodebayar->unionAll($d_metodebayar2))
                        ->selectRaw("q.metodebayar, q.metode, q.urutan, SUM(q.jumlah) AS jumlah")
                        ->groupBy('q.metodebayar','q.metode');

        $d_omset    = DB::table('m_metode as me')
                        ->leftJoinSub($d_left_subquery,'su',function($join){
                            $join->on('su.metodebayar','=','me.id');
                        })
                        ->whereRaw("me.status = '1'")
                        ->selectRaw("me.id, me.nama as name, me.urutan as urutan,
                        CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data");
        
        $d_ongkir   = DB::table('tbl_kasir as k')
                    ->when($gudang, function($query,$keyword){
                        if($keyword != ''){
                            $query->whereRaw("k.id_gudang = '$keyword'");
                        }
                    })
                    ->when($tanggal, function($query, $keyword){
                        if($keyword != ''){
                            $query->whereRaw("k.tanggal_faktur = '$keyword'");
                        }
                    })
                    ->selectRaw("'99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan,
                    SUM(k.ongkos_kirim) AS data");

        $d_query    = DB::table(DB::raw("({$d_omset->unionAll($d_ongkir)->toSql()}) as p"))
                    ->mergeBindings($d_omset->unionAll($d_ongkir))
                    ->orderBy('p.urutan','ASC')
                    ->get();

        $data = array();
        foreach($d_query as $list){
            $row = array();
            $row[] = $list->name;
            $row[] = 'Rp.'.rupiah($list->data);
            $data[] = $row;
        }
        return response()->json(array('data'=>$data));
    }
    
    public function get_jumlahomset(Request $request){
        $tanggal = tgl_full($request->tanggal,'99');
        $gudang = $request->gudang;

        $d_metodebayar = DB::table('tbl_kasir as k')
                        ->leftjoin('m_metode as m','k.metodebayar','m.id')
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.id_gudang = ?',[$keyword]);
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.tanggal_faktur = ?',[$keyword]);
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy('k.metodebayar','k.metodebayar2','m.nama')
                        ->selectRaw('k.metodebayar, m.nama as metode, k.created_at, 
                        CASE WHEN k.metodebayar2 IS NOT NULL THEN SUM( k.total_metodebayar ) 
                        ELSE SUM( k.total_tagihan - k.total_potongan ) END AS jumlah');


        $d_metodebayar2 = DB::table('tbl_kasir as k')
                        ->join('m_metode as m','k.metodebayar2','m.id')
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.id_gudang = ?',[$keyword]);
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.tanggal_faktur = ?',[$keyword]);
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy('k.metodebayar','k.metodebayar2','m.nama')
                        ->selectRaw('k.metodebayar2 as metodebayar, m.nama as metode, k.created_at, 
                        CASE WHEN k.metodebayar2 IS NOT NULL THEN SUM( k.total_metodebayar2 ) 
                        ELSE 0 END AS jumlah');
        
        $d_metodeongkir = DB::table("tbl_kasir as k")
                        ->join("m_metode as m","k.metodeongkir","m.id")
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy("k.metodeongkir","k.metodeongkir2","m.nama")
                        ->selectRaw("k.metodeongkir as metodebayar, m.nama as metode, m.urutan, 
                        CASE WHEN k.metodeongkir IS NOT NULL THEN CONCAT('-',SUM( k.total_metodeongkir )) 
                        ELSE 0 END AS jumlah");
        
        $d_metodeongkir2 = DB::table("tbl_kasir as k")
                        ->join("m_metode as m","k.metodeongkir2","m.id")
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy("k.metodeongkir","k.metodeongkir2","m.nama")
                        ->selectRaw("k.metodeongkir2 as metodebayar, m.nama as metode, m.urutan, 
                        CASE WHEN k.metodeongkir2 IS NOT NULL THEN CONCAT('-',SUM( k.total_metodeongkir2 )) ELSE 0 END AS jumlah");

        /*$d_left_subquery = DB::table(DB::raw("({$d_metodebayar->unionAll($d_metodebayar2)->unionAll($d_metodeongkir)->unionAll($d_metodeongkir2)->toSql()}) as q"))
                        ->mergeBindings($d_metodebayar->unionAll($d_metodebayar2)->unionAll($d_metodeongkir)->unionAll($d_metodeongkir2))
                        ->selectRaw("q.metodebayar, q.metode, SUM(q.jumlah) AS jumlah")
                        ->groupBy('q.metodebayar','q.metode');*/
        $d_left_subquery = DB::table(DB::raw("({$d_metodebayar->unionAll($d_metodebayar2)->toSql()}) as q"))
                        ->mergeBindings($d_metodebayar->unionAll($d_metodebayar2))
                        ->selectRaw("q.metodebayar, q.metode, SUM(q.jumlah) AS jumlah")
                        ->groupBy('q.metodebayar','q.metode');

        $d_omset        = DB::table('m_metode as me')
                        ->leftJoinSub($d_left_subquery,'su',function($join){
                            $join->on('su.metodebayar','=','me.id');
                        })
                        ->whereRaw("me.status = '1'")
                        ->selectRaw("me.id, CASE WHEN SUM(su.jumlah) IS NULL THEN '0' ELSE sum(su.jumlah) END AS data")
                        ->orderBy('me.urutan','ASC')
                        ->get();
                        
        $data = $d_omset;
        return response()->json($data);
    }

    public function get_jumlahomsetdanongkir(Request $request){
        $tanggal = tgl_full($request->tanggal,'99');
        $gudang = $request->gudang;

        $d_metodebayar = DB::table('tbl_kasir as k')
                        ->leftjoin('m_metode as m','k.metodebayar','m.id')
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.id_gudang = ?',[$keyword]);
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.tanggal_faktur = ?',[$keyword]);
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy('k.metodebayar','k.metodebayar2','m.nama')
                        ->selectRaw('k.metodebayar, m.nama as metode, k.created_at, 
                        CASE WHEN k.metodebayar2 IS NOT NULL THEN SUM( k.total_metodebayar ) 
                        ELSE SUM( k.total_tagihan - k.total_potongan ) END AS jumlah');


        $d_metodebayar2 = DB::table('tbl_kasir as k')
                        ->join('m_metode as m','k.metodebayar2','m.id')
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.id_gudang = ?',[$keyword]);
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.tanggal_faktur = ?',[$keyword]);
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy('k.metodebayar','k.metodebayar2','m.nama')
                        ->selectRaw('k.metodebayar2 as metodebayar, m.nama as metode, k.created_at, 
                        CASE WHEN k.metodebayar2 IS NOT NULL THEN SUM( k.total_metodebayar2 ) 
                        ELSE 0 END AS jumlah');
        
        $d_metodeongkir = DB::table("tbl_kasir as k")
                        ->join("m_metode as m","k.metodeongkir","m.id")
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy("k.metodeongkir","k.metodeongkir2","m.nama")
                        ->selectRaw("k.metodeongkir as metodebayar, m.nama as metode, m.urutan, 
                        CASE WHEN k.metodeongkir IS NOT NULL THEN CONCAT('-',SUM( k.total_metodeongkir )) 
                        ELSE 0 END AS jumlah");
        
        $d_metodeongkir2 = DB::table("tbl_kasir as k")
                        ->join("m_metode as m","k.metodeongkir2","m.id")
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.id_gudang = '$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy("k.metodeongkir","k.metodeongkir2","m.nama")
                        ->selectRaw("k.metodeongkir2 as metodebayar, m.nama as metode, m.urutan, 
                        CASE WHEN k.metodeongkir2 IS NOT NULL THEN CONCAT('-',SUM( k.total_metodeongkir2 )) ELSE 0 END AS jumlah");

        /*$d_left_subquery = DB::table(DB::raw("({$d_metodebayar->unionAll($d_metodebayar2)->unionAll($d_metodeongkir)->unionAll($d_metodeongkir2)->toSql()}) as q"))
                        ->mergeBindings($d_metodebayar->unionAll($d_metodebayar2)->unionAll($d_metodeongkir)->unionAll($d_metodeongkir2))
                        ->selectRaw("q.metodebayar, q.metode, SUM(q.jumlah) AS jumlah")
                        ->groupBy('q.metodebayar','q.metode');*/
        $d_left_subquery = DB::table(DB::raw("({$d_metodebayar->unionAll($d_metodebayar2)->toSql()}) as q"))
                        ->mergeBindings($d_metodebayar->unionAll($d_metodebayar2))
                        ->selectRaw("q.metodebayar, q.metode, SUM(q.jumlah) AS jumlah")
                        ->groupBy('q.metodebayar','q.metode');

        $d_omset    = DB::table('m_metode as me')
                        ->leftJoinSub($d_left_subquery,'su',function($join){
                            $join->on('su.metodebayar','=','me.id');
                        })
                        ->whereRaw("me.status = '1'")
                        ->selectRaw("me.id, me.nama as name, me.urutan as urutan,
                        CASE WHEN SUM(su.jumlah) IS NULL THEN '0' ELSE SUM(su.jumlah) END AS data");
        
        $d_ongkir   = DB::table('tbl_kasir as k')
                    ->when($gudang, function($query,$keyword){
                        if($keyword != ''){
                            $query->whereRaw("k.id_gudang = '$keyword'");
                        }
                    })
                    ->when($tanggal, function($query, $keyword){
                        if($keyword != ''){
                            $query->whereRaw("k.tanggal_faktur = '$keyword'");
                        }
                    })
                    ->selectRaw("'99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan,
                    k.ongkos_kirim AS data");

        $d_ongkir   = DB::table(DB::raw("({$d_ongkir->toSql()}) as o"))
                    ->selectRaw('id, name, urutan, sum(data) as data');

        $d_query    = DB::table(DB::raw("({$d_omset->unionAll($d_ongkir)->toSql()}) as p"))
                    ->mergeBindings($d_omset->unionAll($d_ongkir))
                    ->selectRaw("p.id, CASE WHEN SUM(p.data) IS NULL THEN '0' ELSE sum(p.data) END AS data")
                    ->orderBy('p.urutan','ASC')
                    ->get();
        $data = $d_query;
        return response()->json($data);
    }

    public function get_omset_2(Request $request){
        $id_profil = Auth::user()->id_profil;
        $tanggal = tgl_full($request->tanggal,'99');
        $gudang = $request->gudang;

        if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."' and m.status = '1'";
        	$where2 = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            // $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and m.status = '1'";
        	$where2 = "WHERE k.id_gudang = '".$gudang."'";
        }else{
        	$where = "WHERE m.status = '1'";
        	$where2 = "";
        }

        /*$data=DB::select("SELECT
            me.id,
            me.nama as name,
            CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data
        FROM
            m_metode AS me
            LEFT JOIN (
            SELECT
                k.metodebayar,
                m.nama AS metode,k.created_at,
                Sum( k.total_tagihan - k.total_potongan ) AS jumlah
            FROM
                tbl_kasir AS k
                LEFT JOIN m_metode AS m ON k.metodebayar = m.id
            $where
            GROUP BY
            k.metodebayar,m.nama
            ) AS su ON su.metodebayar = me.id");*/
            $data=DB::select("
            SELECT * FROM (
            SELECT
                me.id,
                me.nama as name,
                me.urutan as urutan,
                CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data
                FROM
                    m_metode AS me
                    LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                                    CASE WHEN k.metodebayar2 IS NOT NULL 
                                    THEN SUM(k.total_metodebayar2) ELSE 0 
                                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id
                where me.status = 1
            UNION ALL
            SELECT '99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan, SUM(k.ongkos_kirim) AS data FROM tbl_kasir AS k $where2
            ) AS p ORDER BY p.urutan ASC
            ");

            $no = 0;
            $data1 = array();
            $jumlah= 0;
            foreach ($data as $list) {
                    $no++;
                    $jumlah += $list->data ;
                    $row = array();
                    $row[] = $list->name;
                    $row[] = 'Rp.'.rupiah($list->data) ;
                    $data1[] = $row;
            }
            $output = array("data" => $data1);
            return response()->json($output);

    }

    public function get_jumlahomset_2(Request $request){
       	$id_profil = Auth::user()->id_profil;
        $tanggal = tgl_full($request->tanggal,'99');
        $gudang  = $request->gudang;

        if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."' and m.status = '1'";
            // $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and m.status = '1'";
        }else{
        	$where = "WHERE m.status = '1'";
        }

        /*$data=DB::select("SELECT
            me.id,
            CASE WHEN sum(su.jumlah) IS NULL THEN '0' ELSE sum( su.jumlah) END AS data,
            CASE WHEN su.tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') ELSE DATE_FORMAT(su.tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar
        FROM
            m_metode AS me
            LEFT JOIN (
            SELECT
                k.metodebayar,
                m.nama AS metode,k.created_at,
                SUM( k.total_tagihan - k.total_potongan ) AS jumlah,
                k.tanggal_bayar
            FROM
                tbl_kasir AS k
                LEFT JOIN m_metode AS m ON k.metodebayar = m.id
            $where
            GROUP BY
            k.metodebayar,m.nama
            ) AS su ON su.metodebayar = me.id");*/
        $data=DB::select("SELECT
            me.id,
            CASE WHEN sum(su.jumlah) IS NULL THEN '0' ELSE sum( su.jumlah) END AS data,
            CASE WHEN su.tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') ELSE DATE_FORMAT(su.tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar
            FROM
                m_metode AS me
                LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah, q.tanggal_bayar FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar2) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id");

        return json_encode($data);


    }

    public function get_jumlahomsetdanongkir_2(Request $request){
        $id_profil = Auth::user()->id_profil;
        $tanggal = tgl_full($request->tanggal,'99');
        $gudang  = $request->gudang;

        if($gudang != "" && $tanggal != ""){
            $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."' and m.status = '1'";
            $where2 = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            // $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
            $where = "WHERE k.id_gudang = '".$gudang."' and m.status = '1'";
            $where2 = "WHERE k.id_gudang = '".$gudang."'";
        }else{
            $where = "WHERE m.status = '1'";
            $where2 = "";
        }

        $data=DB::select("
        SELECT p.id, CASE WHEN SUM(p.data) IS NULL THEN '0' ELSE sum(p.data) END AS data, p.tanggal_bayar
        FROM (
            SELECT
            me.id,
            CASE WHEN sum(su.jumlah) IS NULL THEN '0' ELSE sum( su.jumlah) END AS data,
            CASE WHEN su.tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') ELSE DATE_FORMAT(su.tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar
            FROM
                m_metode AS me
                LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah, q.tanggal_bayar FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar2) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id
                UNION
                SELECT '99' AS id,CASE WHEN sum(ongkos_kirim) IS NULL THEN '0' ELSE sum(ongkos_kirim) END AS data,
                CASE WHEN tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') 
                ELSE DATE_FORMAT(tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar
                FROM tbl_kasir AS k $where2
                ) AS p 
                ");

        return json_encode($data);


    }

    public function get_jumlahnota(Request $request){
       	$id_profil = Auth::user()->id_profil;
       	$tanggal = tgl_full($request->tanggal,'99');
        $gudang  = $request->gudang;


        if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            // $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal = '".$tanggal."'";
            $where_nota = "id_gudang = '".$gudang."' and tanggal_faktur = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        	$where_nota = "id_gudang = '".$gudang."'";
        }else{
        	$where = "";
        	$where_nota = "";
        }

        // $data1=DB::select("SELECT COUNT(k.id_kasir) jumlah_nota, k.status_posting as status  FROM tbl_kasir AS k ".$where." ");
        $data['data']   = DB::select("SELECT COUNT(k.id_kasir) jumlah_nota, k.status_posting as status FROM tbl_kasir AS k ".$where." ");
        $data_nota  = DB::table('view_closing_jumlahnota');
        if($where_nota != ''){
            $data_nota  = $data_nota->whereRaw($where_nota);
        }
        $data['nota']   = $data_nota->first();
        $data['group'] = Auth::user()->group_id;

        return json_encode($data);
    }

    public function get_checkclosing(Request $request){
    	$tanggal = tgl_full($request->tanggal,'99');
    	$gudang  = $request->gudang;

    	if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            // $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal = '".$tanggal."'";
            $where_nota = "id_gudang = '".$gudang."' and tanggal_faktur = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
            $where_nota = "id_gudang = '".$gudang."'";
        }else{
        	$where = "";
            $where_nota = "";
        }

        /*$d_query = DB::select("SELECT k.id_kasir, k.tanggal_faktur FROM tbl_kasir AS k ".$where."");
        $id_kasir = array();
        foreach($d_query as $d){
        	$id_kasir[] = $d->id_kasir;
        }
        $data['status_posting'] = '2';
        $data['tanggal_bayar']  = date('Y-m-d');
        DB::table('tbl_kasir')->whereIn('id_kasir', $id_kasir)->update($data);

        $d_cek = DB::table('tbl_kasir')->whereIn('id_kasir', $id_kasir)->first();

        if($d_cek->status_posting == 2){
        	$status=1;
        }else{
        	$status=0;
        }*/
        $d_penjualan    = DB::table('tbl_kasir')->whereRaw($where_nota)->where('status_posting','1')->update(array('status_posting'=>'2','tanggal_bayar'=>date('Y-m-d')));
        $d_closing      = DB::table('view_closing_jumlahnota')->whereRaw($where_nota)->first();
        $group          = Auth::user()->group_id;

        $status['jumlah_nota_closing']  = $d_closing->jumlah_nota_closing;
        $status['jumlah_nota_unclosing']= $d_closing->jumlah_nota_unclosing;
        if($d_closing->jumlah_nota_unclosing > 0){
                $status['closing']      = 1;
            if($group == '1' || $group == '6'){
                $status['print']        = 0;
                $status['unclosing']    = 1;
            }else{
                $status['print']        = 0;
                $status['unclosing']    = 0;
            }
        }else{
            if($group == '1' || $group == '6'){
                // if($d_closing->jumlah_nota_closing > 0){
                //     $status['closing']      = 0;
                // }else{
                //     $status['closing']      = 1;
                // }
                $status['closing']      = 0;
                $status['unclosing']    = 1;
                $status['print']        = 1;
            }else{
                $status['closing']      = 0;
                $status['unclosing']    = 0;
                $status['print']        = 1;
            }
        }
        trigger_log(NULL, "Melakukan Closing Penjualan", 8);
        return json_encode($status);

    }

    public function get_uncheckclosing(Request $request){
    	$tanggal = tgl_full($request->tanggal,'99');
    	$gudang  = $request->gudang;

    	if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            // $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal = '".$tanggal."'";
            $where_nota = "id_gudang = '".$gudang."' and tanggal_faktur = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
            $where_nota = "id_gudang = '".$gudang."'";
        }else{
        	$where = "";
            $where_nota = "";
        }

        /*$d_query = DB::select("SELECT k.id_kasir, k.tanggal_faktur FROM tbl_kasir AS k ".$where."");
        $id_kasir = array();
        foreach($d_query as $d){
        	$id_kasir[] = $d->id_kasir;
        }
        $data['status_posting'] = '1';
        $data['tanggal_bayar']  = tgl_full($request->tanggal_bayar,'99');
        DB::table('tbl_kasir')->whereIn('id_kasir', $id_kasir)->update($data);

        $d_cek = DB::table('tbl_kasir')->whereIn('id_kasir', $id_kasir)->first();

        if($d_cek->status_posting == 1){
        	$status=1;
        }else{
        	$status=0;
        }*/
        $d_penjualan    = DB::table('tbl_kasir')->whereRaw($where_nota)->where('status_posting','2')->update(array('status_posting'=>'1'));
        $d_closing      = DB::table('view_closing_jumlahnota')->whereRaw($where_nota)->first();
        $group          = Auth::user()->group_id;

        $status['jumlah_nota_closing']  = $d_closing->jumlah_nota_closing;
        $status['jumlah_nota_unclosing']= $d_closing->jumlah_nota_unclosing;
        if($d_closing->jumlah_nota_unclosing > 0){
                $status['closing']      = 1;
                $status['unclosing']    = 0;
            if($group == '1' && $group == '6'){
                $status['print']        = 0;
            }else{
                $status['print']        = 0;
            }
        }else{
            if($group == '1' && $group == '6'){
                $status['closing']      = 1;
                $status['unclosing']    = 1;
                $status['print']        = 1;
            }else{
                $status['closing']      = 0;
                $status['unclosing']    = 0;
                $status['print']        = 1;
            }
        }
        trigger_log(NULL, "Melakukan Unclosing Penjualan", 8);
        return json_encode($status);
    }

    public function get_printclosing($gudang,$tanggal){
        $id_profil = Auth::user()->id_profil;
        $tanggal = tgl_full($tanggal,'99');
        $gudang = $gudang;

        $d_metodebayar = DB::table('tbl_kasir as k')
                        ->leftjoin('m_metode as m','k.metodebayar','m.id')
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw("k.id_gudang = ?",[$keyword]);
                                $query->whereRaw("k.id_gudang='$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw("k.tanggal_faktur = ?",[$keyword]);
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy('k.metodebayar','k.metodebayar2','m.nama')
                        ->selectRaw('k.metodebayar, m.nama as metode, m.urutan,
                        CASE WHEN k.metodebayar2 IS NOT NULL THEN SUM( k.total_metodebayar ) 
                        ELSE SUM( k.total_tagihan - k.total_potongan ) END AS jumlah');


        $d_metodebayar2 = DB::table('tbl_kasir as k')
                        ->join('m_metode as m','k.metodebayar2','m.id')
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw("k.id_gudang = ?",[$keyword]);
                                $query->whereRaw("k.id_gudang='$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw("k.tanggal_faktur = ?",[$keyword]);
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->whereRaw("m.status = '1'")
                        ->groupBy('k.metodebayar','k.metodebayar2','m.nama')
                        ->selectRaw('k.metodebayar2 as metodebayar, m.nama as metode, m.urutan,
                        CASE WHEN k.metodebayar2 IS NOT NULL THEN SUM( k.total_metodebayar2 ) 
                        ELSE 0 END AS jumlah');

        $d_left_subquery = DB::table(DB::raw("({$d_metodebayar->unionAll($d_metodebayar2)->toSql()}) as q"))
                        ->mergeBindings($d_metodebayar->unionAll($d_metodebayar2))
                        ->groupBy('q.metodebayar','q.metode');

        $d_omset    = DB::table('m_metode as me')
                        ->leftJoinSub($d_left_subquery,'su',function($join){
                            $join->on('su.metodebayar','=','me.id');
                        })
                        ->whereRaw("me.status = '1'")
                        ->selectRaw("me.id, me.nama as name, me.urutan as urutan,
                        CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data");
        
        $d_ongkir   = DB::table('tbl_kasir as k')
                    ->when($gudang, function($query,$keyword){
                        if($keyword != ''){
                            // $query->whereRaw("k.id_gudang = ?",[$keyword]);
                            $query->whereRaw("k.id_gudang='$keyword'");
                        }
                    })
                    ->when($tanggal, function($query, $keyword){
                        if($keyword != ''){
                            // $query->whereRaw("k.tanggal_faktur = ?",[$keyword]);
                            $query->whereRaw("k.tanggal_faktur = '$keyword'");
                        }
                    })
                    ->selectRaw("'99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan,
                    SUM(k.ongkos_kirim) AS data");

        $detail_omset    = DB::table(DB::raw("({$d_omset->unionAll($d_ongkir)->toSql()}) as p"))
                        ->mergeBindings($d_omset->unionAll($d_ongkir))
                        ->orderBy('p.urutan','ASC');

        $t_omset    = DB::table('m_metode as me')
                        ->leftJoinSub($d_left_subquery,'su',function($join){
                            $join->on('su.metodebayar','=','me.id');
                        })
                        ->whereRaw("me.status = '1'")
                        ->selectRaw("me.id, me.nama as name, me.urutan as urutan,
                        CASE WHEN SUM(su.jumlah) IS NULL THEN '0' ELSE SUM(su.jumlah) END AS data");
        
        $t_ongkir   = DB::table('tbl_kasir as k')
                    ->when($gudang, function($query,$keyword){
                        if($keyword != ''){
                            $query->whereRaw("k.id_gudang = '$keyword'");
                        }
                    })
                    ->when($tanggal, function($query, $keyword){
                        if($keyword != ''){
                            $query->whereRaw("k.tanggal_faktur = '$keyword'");
                        }
                    })
                    ->whereRaw('(k.metodebayar2 > 0 OR k.metodebayar2 IS NOT NULL)')
                    ->selectRaw("'99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan,
                    k.ongkos_kirim AS data");

        $t_ongkir   = DB::table(DB::raw("({$t_ongkir->toSql()}) as o"))
                    ->selectRaw("id, name, urutan, CONCAT('-',sum(data)) as data");
        
        $t_omsetongkir    = DB::table('m_metode as me')
                        ->leftJoinSub($d_left_subquery,'su',function($join){
                            $join->on('su.metodebayar','=','me.id');
                        })
                        ->whereRaw("me.status = '1'")
                        ->selectRaw("me.id, me.nama as name, me.urutan as urutan,
                        CASE WHEN SUM(su.jumlah) IS NULL THEN '0' ELSE SUM(su.jumlah) END AS data");

        $t_omsetongkir_ongkir   = DB::table('tbl_kasir as k')
                    ->when($gudang, function($query,$keyword){
                        if($keyword != ''){
                            // $query->whereRaw("k.id_gudang = ?",[$keyword]);
                            $query->whereRaw("k.id_gudang = '$keyword'");
                        }
                    })
                    ->when($tanggal, function($query, $keyword){
                        if($keyword != ''){
                            $query->whereRaw("k.tanggal_faktur = '$keyword'");
                        }
                    })
                    ->whereRaw('(k.metodebayar2 = 0 OR k.metodebayar2 IS NULL)')
                    ->selectRaw("'99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan,
                    k.ongkos_kirim AS data");

        $t_omsetongkir_ongkir   = DB::table(DB::raw("({$t_omsetongkir_ongkir->toSql()}) as o"))
                    ->selectRaw('id, name, urutan, sum(data) as data');
        

        $total_omset    = DB::table(DB::raw("({$t_omset->unionAll($t_ongkir)->toSql()}) as p"))
                        ->mergeBindings($t_omset->unionAll($t_ongkir))
                        ->selectRaw("p.id, CASE WHEN SUM(p.data) IS NULL THEN '0' ELSE sum(p.data) END AS data")
                        ->orderBy('p.urutan','ASC');

        $total_omsetongkir  = DB::table(DB::raw("({$t_omsetongkir->unionAll($t_omsetongkir_ongkir)->toSql()}) as p"))
                            ->mergeBindings($t_omsetongkir->unionAll($t_omsetongkir_ongkir))
                            ->selectRaw("p.id, CASE WHEN SUM(p.data) IS NULL THEN '0' ELSE sum(p.data) END AS data")
                            ->orderBy('p.urutan','ASC');
        
        $count_omset    = DB::table('tbl_kasir as k')
                        ->when($gudang, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.id_gudang = ?',[$keyword]);
                                $query->whereRaw("k.id_gudang='$keyword'");
                            }
                        })
                        ->when($tanggal, function($query,$keyword){
                            if($keyword != ''){
                                // $query->whereRaw('k.tanggal_faktur = ?',[$keyword]);
                                $query->whereRaw("k.tanggal_faktur = '$keyword'");
                            }
                        })
                        ->selectRaw("COUNT(k.id_kasir) jumlah_nota, k.status_posting as status");
        if($this->agent->isMobile()){
            $data   = $detail_omset->get();
            $total  = $total_omset->get();
            $nota   = $count_omset->get();
            return view('admin_mobile.kasir.struk_closing',compact('data','total','nota','gudang','tanggal'));
        }else{
            require(public_path('fpdf1813/Mc_table.php'));
            $data['data'] = array('tanggal' =>$tanggal,
                                  'nama'    =>Auth::user()->name,
                                  'gudang'  =>DB::table('ref_gudang')->where('id',$gudang)->first()->nama);
            $c_rich = DB::table('m_profil')->where('id',$id_profil);
            $d_rich = DB::table('m_profil')->where('id',$id_profil)->first();
            $data['rich'] = array('nama'=>'',
                                'alamat'=>'',
                                'telp'  =>'');
            if($c_rich->count() > 0){
            $data['rich'] = array('nama'=>$d_rich->nama,
                                'alamat'=>$d_rich->alamat,
                                'telp'  =>$d_rich->telp);
            }

            $data['detail']         = $detail_omset->get();
            $data['total']          = $total_omset->get();
            $data['total_ongkir']   = $total_omsetongkir->get();
            $data['nota']           = $count_omset->get();

            $html = view('admin.kasir.CetakClosing')->with('data',$data);
            return response($html)->header('Content-Type', 'application/pdf');
        }
    }

    public function get_printclosing_2($gudang,$tanggal){
        $id_profil = Auth::user()->id_profil;
        $tanggal = tgl_full($tanggal,'99');
        $gudang = $gudang;

        if($gudang != "" && $tanggal != ""){
            $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."' and m.status = '1'";
            $where2 = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            // $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
            $where = "WHERE k.id_gudang = '".$gudang."' and m.status = '1'";
            $where2 = "WHERE k.id_gudang = '".$gudang."'";
        }else{
            $where = "WHERE m.status = '1'";
            $where2 = "";
        }

        if($this->agent->isMobile()){
        $data=DB::select("SELECT
            me.id,
            me.nama as name,
            CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data
                FROM
                    m_metode AS me
                    LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                                    CASE WHEN k.metodebayar2 IS NOT NULL 
                                    THEN SUM(k.total_metodebayar2) ELSE 0 
                                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id");
        $total =DB::select("SELECT
            me.id,
            CASE WHEN sum(su.jumlah) IS NULL THEN '0' ELSE sum( su.jumlah) END AS data,
            CASE WHEN su.tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') ELSE DATE_FORMAT(su.tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar
            FROM
                m_metode AS me
                LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah, q.tanggal_bayar FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar2) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id");
        $nota = DB::select("SELECT COUNT(k.id_kasir) jumlah_nota, k.status_posting as status  FROM tbl_kasir AS k ".$where2." ");

        // dd($total);
        return view('admin_mobile.kasir.struk_closing',compact('data','total','nota','gudang','tanggal'));
        exit();

        }else{
            require(public_path('fpdf1813/Mc_table.php'));

            $data['data'] = array('tanggal' =>$tanggal,
                                  'nama'    =>Auth::user()->name,
                                  'gudang'  =>DB::table('ref_gudang')->where('id',$gudang)->first()->nama);
            $c_rich = DB::table('m_profil')->where('id',$id_profil);
            $d_rich = DB::table('m_profil')->where('id',$id_profil)->first();
            $data['rich'] = array('nama'=>'',
                                'alamat'=>'',
                                'telp'  =>'');
            if($c_rich->count() > 0){
            $data['rich'] = array('nama'=>$d_rich->nama,
                                'alamat'=>$d_rich->alamat,
                                'telp'  =>$d_rich->telp);
            }
            $data['detail'] = DB::SELECT("SELECT
            me.id,
            me.nama as name,
            CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data
                FROM
                    m_metode AS me
                    LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                                    CASE WHEN k.metodebayar2 IS NOT NULL 
                                    THEN SUM(k.total_metodebayar2) ELSE 0 
                                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id");
            $data['total'] = DB::SELECT("SELECT
            me.id,
            CASE WHEN sum(su.jumlah) IS NULL THEN '0' ELSE sum( su.jumlah) END AS data,
            CASE WHEN su.tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') ELSE DATE_FORMAT(su.tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar
            FROM
                m_metode AS me
                LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah, q.tanggal_bayar FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar2) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id");
            $data['total_ongkir'] = DB::SELECT("SELECT 
                        id,
                        CASE WHEN sum(data) IS NULL THEN '0' ELSE sum(data) END AS data,
                        CASE WHEN tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') 
                        ELSE DATE_FORMAT(tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar FROM (
            SELECT
            me.id,
            me.nama as name,
            me.urutan as urutan,
            CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data,
            su.tanggal_bayar
                FROM
                    m_metode AS me
                    LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,
                    q.created_at, 
                    SUM(q.jumlah) AS jumlah,
                    q.tanggal_bayar FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah,
                                        k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                                    CASE WHEN k.metodebayar2 IS NOT NULL 
                                    THEN SUM(k.total_metodebayar2) ELSE 0 
                                    END AS jumlah,
                                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id
                UNION ALL
                SELECT '99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan, SUM(k.ongkos_kirim) AS data, 
                k.tanggal_faktur as tanggak_bayar FROM tbl_kasir AS k 
                $where2
                ) AS p ORDER BY p.urutan ASC");
            // print_r($data);exit();
            $html = view('admin.kasir.CetakClosing')->with('data',$data);
            return response($html)->header('Content-Type', 'application/pdf');
        }

    }
    
    public function get_tabsproduk(Request $request){
        $id_profil  = Auth::user()->id_profil;
        $tanggal    = tgl_full($request->tanggal,'99');
        $gudang     = $request->gudang;

        if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        }else{
        	$where = "";
        }
        $d_data = DB::select('SELECT * FROM (
                SELECT p.id_produk, sum(p.jumlah) jumlah, mp.nama, mp.kode_produk, SUM( p.total ) jumlah_total
                FROM tbl_kasir_detail_produk AS p
                LEFT JOIN m_produk AS mp ON p.id_produk = mp.id
                LEFT JOIN tbl_kasir AS k ON p.id_kasir = k.id_kasir
                '.$where.'
                GROUP BY p.id_produk, mp.nama, mp.kode_produk
            ) p
            ORDER BY p.jumlah_total DESC');
        $no = 0;
        $data = array();
        foreach ($d_data as $list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->nama;
            $row[] = format_angka($list->jumlah);
            $row[] = 'Rp '.format_angka($list->jumlah_total);
            $data[] = $row;
        }
        $output = array("data" => $data);
        return response()->json($output);
    }

    public function get_tabsbarang(Request $request){
        $id_profil  = Auth::user()->id_profil;
        $tanggal    = tgl_full($request->tanggal,'99');
        $gudang     = $request->gudang;

        if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."' and d.id_detail_kasir_produk='0'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and d.id_detail_kasir_produk='0'";
        }else{
        	$where = "";
        }
        $d_data = DB::select('SELECT b.barang_id, b.barang_kode, b.barang_nama as nama, s.satuan_nama, k.id_gudang , Sum( d.jumlah ) AS jumlah, SUM(d.total) AS jumlah_total
                FROM tbl_kasir_detail AS d
                LEFT JOIN tbl_barang AS b ON d.id_barang = b.barang_id
                LEFT JOIN tbl_satuan AS s ON d.id_satuan = s.satuan_id
                LEFT JOIN tbl_kasir AS k ON d.id_kasir = k.id_kasir
                '.$where.'
                GROUP BY d.id_barang, d.id_satuan 
                ORDER BY b.barang_nama DESC');
        $no = 0;
        $data = array();
        foreach ($d_data as $list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->nama;
            $row[] = format_angka($list->jumlah);
            $row[] = 'Rp '.format_angka($list->jumlah_total);
            $data[] = $row;
        }
        $output = array("data" => $data);
        return response()->json($output);
    }

    public function get_tabsprodukpernota(Request $request){
        $id_profil  = Auth::user()->id_profil;
        $tanggal    = tgl_full($request->tanggal,'99');
        $gudang     = $request->gudang;

        if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        }else{
        	$where = "";
        }
        $d_data = DB::select('SELECT * FROM (
                SELECT p.id_produk, sum(p.jumlah) jumlah, mp.nama, mp.kode_produk, SUM( p.total ) jumlah_total, k.no_faktur, k.tanggal_faktur, CONCAT(mpe.nama," (",mpe.telp,")") as nama_pelanggan
                FROM tbl_kasir_detail_produk AS p
                LEFT JOIN m_produk AS mp ON p.id_produk = mp.id
                LEFT JOIN tbl_kasir AS k ON p.id_kasir = k.id_kasir
                LEFT JOIN m_pelanggan as mpe ON k.id_pelanggan = mpe.id
                '.$where.'
                GROUP BY p.id_produk, mp.nama, mp.kode_produk, k.id_kasir
            ) p
            ORDER BY p.jumlah_total DESC');
        $no = 0;
        $data = array();
        foreach ($d_data as $list) {
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
            $row['no']              = $no;
            $row['no_faktur']       = $list->no_faktur;
            $row['tanggal_faktur']  = tgl_full($list->tanggal_faktur,'');
            $row['nama_pelanggan']  = $list->nama_pelanggan;
            $row['nama']            = $list->nama;
            $row['jumlah']          = format_angka($list->jumlah);
            $row['jumlah_total']    = 'Rp '.format_angka($list->jumlah_total);
            }else{
            $row[] = $no;
            $row[] = $list->no_faktur;
            $row[] = tgl_full($list->tanggal_faktur,'');
            $row[] = $list->nama_pelanggan;
            $row[] = $list->nama;
            $row[] = format_angka($list->jumlah);
            $row[] = 'Rp '.format_angka($list->jumlah_total);
            }
            $data[] = $row;
        }
        $output = array("data" => $data);
        return response()->json($output);
    }

    public function get_tabsbarangpernota(Request $request){
        $id_profil  = Auth::user()->id_profil;
        $tanggal    = tgl_full($request->tanggal,'99');
        $gudang     = $request->gudang;

        if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."' and d.id_detail_kasir_produk='0'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and d.id_detail_kasir_produk='0'";
        }else{
        	$where = "";
        }
        $d_data = DB::select('SELECT b.barang_id, b.barang_kode, b.barang_nama as nama, s.satuan_nama, k.id_gudang , Sum( d.jumlah ) AS jumlah, SUM(d.total) AS jumlah_total, k.no_faktur, k.tanggal_faktur, CONCAT(mpe.nama," (",mpe.telp,")") as nama_pelanggan
                FROM tbl_kasir_detail AS d
                LEFT JOIN tbl_barang AS b ON d.id_barang = b.barang_id
                LEFT JOIN tbl_satuan AS s ON d.id_satuan = s.satuan_id
                LEFT JOIN tbl_kasir AS k ON d.id_kasir = k.id_kasir
                LEFT JOIN m_pelanggan as mpe ON k.id_pelanggan = mpe.id
                '.$where.'
                GROUP BY d.id_barang, d.id_satuan, k.id_kasir 
                ORDER BY b.barang_nama DESC');
        $no = 0;
        $data = array();
        foreach ($d_data as $list) {
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
            $row['no']              = $no;
            $row['no_faktur']       = $list->no_faktur;
            $row['tanggal_faktur']  = tgl_full($list->tanggal_faktur,'');
            $row['nama_pelanggan']  = $list->nama_pelanggan;
            $row['nama']            = $list->nama;
            $row['jumlah']          = format_angka($list->jumlah);
            $row['jumlah_total']    = 'Rp '.format_angka($list->jumlah_total);
            }else{
            $row[] = $no;
            $row[] = $list->no_faktur;
            $row[] = tgl_full($list->tanggal_faktur,'');
            $row[] = $list->nama_pelanggan;
            $row[] = $list->nama;
            $row[] = format_angka($list->jumlah);
            $row[] = 'Rp '.format_angka($list->jumlah_total);
            }
            $data[] = $row;
        }
        $output = array("data" => $data);
        return response()->json($output);
    }
    
    public function get_tabsbotol(Request $request){
        $id_profil  = Auth::user()->id_profil;
        $tanggal    = tgl_full($request->tanggal,'99');
        $gudang     = $request->gudang;

        $d_barangclosing = DB::table('m_barang_closing')->get();
        $arr_barangclosing = array();
        foreach($d_barangclosing as $d){
            $arr_barangclosing[] = $d->id_barang;
        }

        $d_data = DB::table('tbl_kasir_detail as tkd')->join('tbl_kasir as tk','tkd.id_kasir','tk.id_kasir')->join('tbl_barang as tb','tkd.id_barang','tb.barang_id');
        if($gudang != "") $d_data->where('tk.id_gudang',$gudang);
        if($tanggal != "") $d_data->where('tk.tanggal_faktur',$tanggal);
        if(count($arr_barangclosing) > 0) $d_data->whereIn('tkd.id_barang',$arr_barangclosing);
        $d_data = $d_data->selectRaw('tb.barang_nama AS nama_barang, tb.barang_kode as kode_barang, CASE WHEN SUM(jumlah) IS NULL THEN 0 ELSE SUM(jumlah) END AS jumlah')->groupBy('tkd.id_barang', 'tkd.id_satuan')->get();
        $no = 0;
        $data = array();
        foreach ($d_data as $list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->kode_barang;
            $row[] = format_angka($list->jumlah);
            $data[] = $row;
        }
        $output = array("data" => $data);
        return response()->json($output);
    }
}

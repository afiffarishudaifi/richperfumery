<?php

namespace App\Http\Controllers\Pembelian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

class ReturPembelianController extends Controller
{
    //
    public function index(){
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
        $group_where = DB::table('tbl_group')->where('group_id',$group)->first();

        $data['gudang'] = DB::select("
        SELECT
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
          LEFT JOIN m_profil p ON g.id_profil = p.id $where_gudang
        ");
    	$data['satuan']     = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
        $data['tombol_create'] = tombol_create('',$group_where->group_aktif,1);
    	return view('admin.pembelian.index_retur')->with('data',$data);
    }

    public function listData(Request $request){
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
        $group_where = DB::table('tbl_group')->where('group_id',$group)->first();

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $filter = $request->get('search');
        $search = (isset($filter['value']))? strtolower($filter['value']) : false;
        $tanggal = (isset($filter['value']))? tgl_full($filter['value'],'99') : false;

        $tanggal_start = date('Y-m-d', strtotime('-7 days'));
        $tanggal_end = date('Y-m-d');
        $return = DB::table('tbl_pembelian_retur as tpr')->leftjoin('tbl_barang as tb','tpr.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','tpr.id_satuan','ts.satuan_id')->leftjoin('ref_gudang as rg','tpr.id_gudang','rg.id')->leftjoin('tbl_supplier as tsp','tpr.id_supplier','tsp.supplier_id')->where('tpr.tanggal','>',$tanggal_start)->where('tpr.tanggal','<=',$tanggal_end)->whereIn('tpr.id_gudang',$id_gudang)->select(DB::raw('tpr.id_retur, tpr.kode_retur, tpr.tanggal, tpr.id_barang, tb.barang_nama as nama_barang, tpr.id_satuan, ts.satuan_nama as nama_satuan, tpr.id_gudang, rg.nama as nama_gudang, tpr.id_supplier, tsp.supplier_nama as nama_supplier, tpr.jumlah, tpr.keterangan, tpr.id_log_stok'))->orderBy('tpr.id_retur','DESC');
        if($search){
        $return = DB::table('tbl_pembelian_retur as tpr')->leftjoin('tbl_barang as tb','tpr.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','tpr.id_satuan','ts.satuan_id')->leftjoin('ref_gudang as rg','tpr.id_gudang','rg.id')->leftjoin('tbl_supplier as ts','tpr.id_supplier','ts.supplier_id')
            ->where('tpr.tanggal','>',$tanggal_start)
            ->where('tpr.tanggal','<=',$tanggal_end)
            ->where(function($query) use ($search,$tanggal) {
            	$query->orwhere('tpr.kode_retur','like','%'.$search.'%');
            	$query->orwhere('tpr.tanggal','like','%'.$search.'%');
            })
        	->whereIn('tpr.id_gudang',$id_gudang)
        	->select(DB::raw('tpr.id_retur, tpr.kode_retur, tpr.tanggal, tpr.id_barang, tb.barang_nama as nama_barang, tpr.id_satuan, ts.satuan_nama as nama_satuan, tpr.id_gudang, rg.nama as nama_gudang, tpr.id_supplier, tsp.supplier_nama as nama_supplier, tpr.jumlah, tpr.keterangan, tpr.id_log_stok'))->orderBy('tpr.id_retur','DESC');
        }
        $totalrecord = count($return->get());
        $return_2	 = $return->offset($start)->limit($length);
        $no			 = ($start==0)?0:$start;
        $arr = array();
        foreach ($return_2->get() as $list) {
            $no++;
            $arr[] = array('nomor'=>$no,
        					'kode_retur' 	=> $list->kode_retur,
        					'tanggal'		=> tgl_full($list->tanggal,''),
        					'nama_barang'	=> $list->nama_barang,
        					'jumlah'		=> $list->jumlah,
        					'nama_satuan'	=> $list->nama_satuan,
        					'nama_supplier'	=> $list->nama_supplier,
        					'nama_gudang'	=> $list->nama_gudang,
        					'aksi'			=> $this->get_aksi($list->id_retur,$group_where->group_aktif).
        							'<input type="hidden" id="table_id'.$list->id_retur.'" value="'.$list->id_retur.'">'.
        							'<input type="hidden" id="table_kode'.$list->id_retur.'" value="'.$list->kode_retur.'">'.
        							'<input type="hidden" id="table_tanggal'.$list->id_retur.'" value="'.tgl_full($list->tanggal,'').'">'.
        							'<input type="hidden" id="table_idbarang'.$list->id_retur.'" value="'.$list->id_barang.'">'.
        							'<input type="hidden" id="table_namabarang'.$list->id_retur.'" value="'.$list->nama_barang.'">'.
        							'<input type="hidden" id="table_idgudang'.$list->id_retur.'" value="'.$list->id_gudang.'">'.
        							'<input type="hidden" id="table_namagudang'.$list->id_retur.'" value="'.$list->nama_gudang.'">'.
        							'<input type="hidden" id="table_idsupplier'.$list->id_retur.'" value="'.$list->id_supplier.'">'.
                                    '<input type="hidden" id="table_namasupplier'.$list->id_retur.'" value="'.$list->nama_supplier.'">'.
        							'<input type="hidden" id="table_idsatuan'.$list->id_retur.'" value="'.$list->id_satuan.'">'.
                                    '<input type="hidden" id="table_namasatuan'.$list->id_retur.'" value="'.$list->nama_satuan.'">'.
        							'<input type="hidden" id="table_jumlah'.$list->id_retur.'" value="'.$list->jumlah.'">'.
                                    '<input type="hidden" id="table_keterangan'.$list->id_retur.'" value="'.$list->keterangan.'">'.
        							'<input type="hidden" id="table_idlog_stok'.$list->id_retur.'" value="'.$list->id_log_stok.'">');

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
        if($group == 5 || $group == 6){
          $where = "WHERE id_profil='$id_profil'";
        }
        $d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
        }
        
        $group_where = DB::table('tbl_group')->where('group_id',$group)->first();

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $filter = $request->get('search');
        $search = (isset($filter['value']))? strtolower($filter['value']) : false;
        $tanggal = (isset($filter['value']))? tgl_full($filter['value'],'99') : false;

        $tanggalrange = explode('s.d.',$request->get('tanggal'));
        $tanggal_start  = tgl_full($tanggalrange[0],99);
        $tanggal_end    = tgl_full($tanggalrange[1],99);
        $return = DB::table('tbl_pembelian_retur as tpr')->leftjoin('tbl_barang as tb','tpr.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','tpr.id_satuan','ts.satuan_id')->leftjoin('ref_gudang as rg','tpr.id_gudang','rg.id')->leftjoin('tbl_supplier as tsp','tpr.id_supplier','tsp.supplier_id')->where('tpr.tanggal','>=',$tanggal_start)->where('tpr.tanggal','<=',$tanggal_end)->whereIn('tpr.id_gudang',$id_gudang)->select(DB::raw('tpr.id_retur, tpr.kode_retur, tpr.tanggal, tpr.id_barang, tb.barang_nama as nama_barang, tpr.id_satuan, ts.satuan_nama as nama_satuan, tpr.id_gudang, rg.nama as nama_gudang, tpr.id_supplier, tsp.supplier_nama as nama_supplier, tpr.jumlah, tpr.keterangan, tpr.id_log_stok'))->orderBy('tpr.id_retur','DESC');
        if($search){
        $return = DB::table('tbl_pembelian_retur as tpr')->leftjoin('tbl_barang as tb','tpr.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','tpr.id_satuan','ts.satuan_id')->leftjoin('ref_gudang as rg','tpr.id_gudang','rg.id')->leftjoin('tbl_supplier as ts','tpr.id_supplier','ts.supplier_id')
            ->where('tpr.tanggal','>=',$tanggal_start)
            ->where('tpr.tanggal','<=',$tanggal_end)
            ->where(function($query) use ($search,$tanggal) {
                $query->orwhere('tpr.kode_retur','like','%'.$search.'%');
                $query->orwhere('tpr.tanggal','like','%'.$search.'%');
            })
            ->whereIn('tpr.id_gudang',$id_gudang)
            ->select(DB::raw('tpr.id_retur, tpr.kode_retur, tpr.tanggal, tpr.id_barang, tb.barang_nama as nama_barang, tpr.id_satuan, ts.satuan_nama as nama_satuan, tpr.id_gudang, rg.nama as nama_gudang, tpr.id_supplier, tsp.supplier_nama as nama_supplier, tpr.jumlah, tpr.keterangan, tpr.id_log_stok'))->orderBy('tpr.id_retur','DESC');
        }
        $totalrecord = count($return->get());
        $return_2    = $return->offset($start)->limit($length);
        $no          = ($start==0)?0:$start;
        $arr = array();
        foreach ($return_2->get() as $list) {
            $no++;
            $arr[] = array('nomor'=>$no,
                            'kode_retur'    => $list->kode_retur,
                            'tanggal'       => tgl_full($list->tanggal,''),
                            'nama_barang'   => $list->nama_barang,
                            'jumlah'        => $list->jumlah,
                            'nama_satuan'   => $list->nama_satuan,
                            'nama_supplier' => $list->nama_supplier,
                            'nama_gudang'   => $list->nama_gudang,
                            'aksi'          => $this->get_aksi($list->id_retur,$group_where->group_aktif).
                                    '<input type="hidden" id="table_id'.$list->id_retur.'" value="'.$list->id_retur.'">'.
                                    '<input type="hidden" id="table_kode'.$list->id_retur.'" value="'.$list->kode_retur.'">'.
                                    '<input type="hidden" id="table_tanggal'.$list->id_retur.'" value="'.tgl_full($list->tanggal,'').'">'.
                                    '<input type="hidden" id="table_idbarang'.$list->id_retur.'" value="'.$list->id_barang.'">'.
                                    '<input type="hidden" id="table_namabarang'.$list->id_retur.'" value="'.$list->nama_barang.'">'.
                                    '<input type="hidden" id="table_idgudang'.$list->id_retur.'" value="'.$list->id_gudang.'">'.
                                    '<input type="hidden" id="table_namagudang'.$list->id_retur.'" value="'.$list->nama_gudang.'">'.
                                    '<input type="hidden" id="table_idsupplier'.$list->id_retur.'" value="'.$list->id_supplier.'">'.
                                    '<input type="hidden" id="table_namasupplier'.$list->id_retur.'" value="'.$list->nama_supplier.'">'.
                                    '<input type="hidden" id="table_idsatuan'.$list->id_retur.'" value="'.$list->id_satuan.'">'.
                                    '<input type="hidden" id="table_namasatuan'.$list->id_retur.'" value="'.$list->nama_satuan.'">'.
                                    '<input type="hidden" id="table_jumlah'.$list->id_retur.'" value="'.$list->jumlah.'">'.
                                    '<input type="hidden" id="table_keterangan'.$list->id_retur.'" value="'.$list->keterangan.'">'.
                                    '<input type="hidden" id="table_idlog_stok'.$list->id_retur.'" value="'.$list->id_log_stok.'">');

        }

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $totalrecord,
            'recordsFiltered' => $totalrecord,
            'data' => $arr
        );
        return response()->json($data);
    }

    public function get_barang(Request $request){
    	$term = trim($request->q);
      	$gudang = trim($request->gudang); 
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

    public function get_supplier(Request $request){
      $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT 
          supplier_id, 
          supplier_nama, 
          supplier_alamat, 
          supplier_telp,
          supplier_tempo 
          FROM tbl_supplier WHERE supplier_nama LIKE '%$search%' order by supplier_nama asc");
        return \Response::json($d_query);
    }

    public function get_stok(Request $request){
        $req = $request->all();
        $id_barang = $req['id_barang'];
        $id_gudang = $req['id_gudang'];
        $id_satuan = $req['id_satuan'];
        $d_where = array();
        $add_where = "";
        if(count($req) > 0){
            $add_where = "WHERE ";
        }
        if(isset($id_barang)){
            $d_where[] = "t.id_barang='".$id_barang."'";
        }
        if(isset($id_gudang)){
            $d_where[] = "t.id_ref_gudang='".$id_gudang."'";
        }
        if(isset($id_satuan)){
            $d_where[] = "t.id_satuan='".$id_satuan."'";
        }

        $implode_where = implode(" AND ", $d_where);
        $where = $add_where.$implode_where;
        $d_query = DB::SELECT("
            SELECT
                t.log_stok_id,
                t.id_barang,
                t.id_ref_gudang,
                Sum( t.unit_masuk ) AS jumlah_masuk,
                Sum( t.unit_keluar ) AS jumlah_keluar,
                Sum( t.unit_masuk - t.unit_keluar ) AS jumlah,
                t.id_satuan 
            FROM
                tbl_log_stok AS t 
            $where
            GROUP BY
                t.id_barang,
                t.id_ref_gudang,
                t.id_satuan
            ");
        //dd($d_query);

        return \Response::json($d_query);
    }

    function simpan(Request $request){
        $id = $request->get("popup_id_table");

    	$data_log['id_barang'] 		= $request->get("popup_barang");
        $data_log['id_ref_gudang'] 	= $request->get("popup_gudang");
        $data_log['id_satuan'] 		= $request->get("popup_satuan");
        $data_log['unit_masuk'] 	= '0';
        $data_log['unit_keluar'] 	= $request->get("popup_jumlah");
        $data_log['tanggal'] 		= ($request->get('popup_tanggal')=='') ? tgl_full(date('d-m-Y'), 2):tgl_full($request->get('popup_tanggal'), 2);
        $data_log['ket'] = $request->get('popup_ket');
        $data_log['status'] = 'P2';
        if($id == ''){
        $id_log_stok = DB::table('tbl_log_stok')->insertGetId($data_log);
    	}else{
    	$id_log_stok = $request->get("popup_idlog_stok");
    	}

    	$data['kode_retur']= $request->get("popup_kode");
        $data['id_barang'] = $request->get("popup_barang");
        $data['id_gudang'] = $request->get("popup_gudang");
        $data['id_supplier'] = $request->get("popup_penyedia");
        $data['id_satuan'] = $request->get("popup_satuan");
        $data['jumlah'] = $request->get("popup_jumlah");
        $data['tanggal'] = ($request->get("popup_tanggal")=="") ? tgl_full(date('d-m-Y'), 99):tgl_full($request->get('popup_tanggal'), 99);
        $data['keterangan'] = ($request->get('popup_ket')=="")?".":$request->get('popup_ket');
        $data['id_log_stok'] = $id_log_stok;

        if($id == ''){
            // DB::table('tbl_pembelian_retur')->insert($data);
            $id = DB::table('tbl_pembelian_retur')->insertGetId($data);
		    trigger_log($id,'Menambah data Menu Return Pembelian',1,null);
        }else{
            DB::table('tbl_pembelian_retur')->where('id_retur',$id)->update($data);
            trigger_log($id,'Mengubah data Menu Return Pembelian',2,null);
            DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->update($data_log);
        }

        return response()->json(array('status' => '1'));
    }


    public function hapus(Request $request){
      $id = $request->get('id');
      $d_barang = DB::table('tbl_pembelian_retur')->where('id_retur',$id);
      $id_log_stok = array();
      foreach($d_barang->get() AS $d){
        $id_log_stok[] = $d->id_log_stok;
      }
      DB::table('tbl_log_stok')->whereIn('log_stok_id',$id_log_stok)->delete();
      DB::table('tbl_pembelian_retur')->where(array('id_retur' => $id))->delete();
      trigger_log($id,'Menghapus dataMenu Return Pembeliann',3,null);

    }

    public function get_aksi($id, $status){
        switch ($status) {
            case '2':
                # code...
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i></button><a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
                break;
            case '1':
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-eye"></i></button></div>';
                break;
            default:
                # code...
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i></button><a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
                break;
        }

        return $html;
    }


}

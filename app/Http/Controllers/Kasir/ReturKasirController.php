<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Jenssegers\Agent\Agent;

class ReturKasirController extends Controller
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
    	$data['pelanggan']  = DB::select("select mp.*, g.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as g ON mp.id_gudang=g.id $where_gudang");
        $data['tombol_create'] = tombol_create('',$group_where->group_aktif,1);
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.kasir.index_retur')->with('data',$data);
      }else {
        // code...
        return view('admin.kasir.index_retur')->with('data',$data);
      }
    }

    public function listData(Request $request){        
        DB::enableQueryLog();
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
        $group_where = DB::table('tbl_group')->where('group_id',$group)->first();

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $filter = $request->get('search');
        $search = (isset($filter['value']))? strtolower($filter['value']) : false;
        $tanggal = (isset($filter['value']))? tgl_full($filter['value'],'99') : false;

        $tanggal_start = date('Y-m-d', strtotime('-7 days'));
        $tanggal_end = date('Y-m-d');
        $return = DB::table('tbl_kasir_detail_retur as tkr')->leftjoin('tbl_barang as tb','tkr.id_barang','tb.barang_id')
        	->leftjoin('tbl_satuan as ts','tkr.id_satuan','ts.satuan_id')
        	->leftjoin('ref_gudang as rg','tkr.id_gudang','rg.id')
        	->leftjoin('m_pelanggan as mp','tkr.id_pelanggan','mp.id')
            ->where('tkr.tanggal','>',$tanggal_start)
            ->where('tkr.tanggal','<=',$tanggal_end)
        	->whereIn('tkr.id_gudang',$id_gudang)
        	->select(DB::raw('tkr.id_returkasir_detail, tkr.kode_retur, tkr.tanggal, tkr.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, 
        	tkr.id_satuan, ts.satuan_nama as nama_satuan, tkr.id_gudang, rg.nama as nama_gudang, tkr.id_pelanggan, mp.nama as nama_pelanggan, tkr.jumlah, tkr.harga, tkr.total, tkr.keterangan, tkr.id_log_stok, tkr.created_iduser'))->orderBy('tkr.id_returkasir_detail','DESC');
        
        //dd(DB::getQueryLog());
        if($search){
        $return = DB::table('tbl_kasir_detail_retur as tkr')->leftjoin('tbl_barang as tb','tkr.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','tkr.id_satuan','ts.satuan_id')->leftjoin('ref_gudang as rg','tkr.id_gudang','rg.id')->leftjoin('m_pelanggan as mp','tkr.id_pelanggan','mp.id')
            ->where('tkr.tanggal','>',$tanggal_start)
            ->where('tkr.tanggal','<=',$tanggal_end)
            ->where(function($query) use ($search,$tanggal) {
                	$query->orwhere('tkr.kode_retur','like','%'.$search.'%');
                	$query->orwhere('tkr.tanggal','like','%'.$tanggal.'%');
                	$query->orwhere('tb.barang_nama','like','%'.$search.'%');
                	$query->orwhere('rg.nama','like','%'.$search.'%');
                	$query->orwhere('mp.nama','like','%'.$search.'%');
            })
        	->whereIn('tkr.id_gudang',$id_gudang)
        	->select(DB::raw('tkr.id_returkasir_detail, tkr.kode_retur, tkr.tanggal, tkr.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, tkr.id_satuan,
        	ts.satuan_nama as nama_satuan, tkr.id_gudang, rg.nama as nama_gudang, tkr.id_pelanggan, mp.nama as nama_pelanggan, tkr.jumlah, tkr.harga, tkr.total, tkr.keterangan, tkr.id_log_stok, tkr.created_iduser'))->orderBy('tkr.id_returkasir_detail','DESC');
        }
        $totalrecord = count($return->get());
        $return_2	 = $return->offset($start)->limit($length);
        $no			 = ($start==0)?0:$start;
        $arr = array();
        foreach ($return_2->get() as $list) {
            $no++;
            $arr[] = array(
                  'nomor'=>$no,
        					'kode_retur' 	=> $list->kode_retur,
        					'tanggal'		=> tgl_full($list->tanggal,''),
        					'nama_barang'	=> $list->nama_barang,
        					'jumlah'		=> $list->jumlah,
        					'nama_satuan'	=> $list->nama_satuan,
        					'nama_pelanggan'=> $list->nama_pelanggan,
        					'nama_gudang'	=> $list->nama_gudang,
        					'harga'			=> $list->harga,
        					'total'			=> $list->total,
        					'aksi'			=> $this->get_aksi($list->id_returkasir_detail,$group_where->group_aktif,$list->created_iduser).
        							'<input type="hidden" id="table_id'.$list->id_returkasir_detail.'" value="'.$list->id_returkasir_detail.'">'.
        							'<input type="hidden" id="table_kode'.$list->id_returkasir_detail.'" value="'.$list->kode_retur.'">'.
        							'<input type="hidden" id="table_tanggal'.$list->id_returkasir_detail.'" value="'.tgl_full($list->tanggal,'').'">'.
        							'<input type="hidden" id="table_idbarang'.$list->id_returkasir_detail.'" value="'.$list->id_barang.'">'.
        							'<input type="hidden" id="table_namabarang'.$list->id_returkasir_detail.'" value="'.$list->nama_barang.'">'.
        							'<input type="hidden" id="table_kodebarang'.$list->id_returkasir_detail.'" value="'.$list->kode_barang.'">'.
        							'<input type="hidden" id="table_aliasbarang'.$list->id_returkasir_detail.'" value="'.$list->alias_barang.'">'.
        							'<input type="hidden" id="table_idgudang'.$list->id_returkasir_detail.'" value="'.$list->id_gudang.'">'.
        							'<input type="hidden" id="table_namagudang'.$list->id_returkasir_detail.'" value="'.$list->nama_gudang.'">'.
        							'<input type="hidden" id="table_idpelanggan'.$list->id_returkasir_detail.'" value="'.$list->id_pelanggan.'">'.
                                    '<input type="hidden" id="table_namapelanggan'.$list->id_returkasir_detail.'" value="'.$list->nama_pelanggan.'">'.
        							'<input type="hidden" id="table_idsatuan'.$list->id_returkasir_detail.'" value="'.$list->id_satuan.'">'.
        							'<input type="hidden" id="table_jumlah'.$list->id_returkasir_detail.'" value="'.$list->jumlah.'">'.
        							'<input type="hidden" id="table_harga'.$list->id_returkasir_detail.'" value="'.$list->harga.'">'.
        							'<input type="hidden" id="table_total'.$list->id_returkasir_detail.'" value="'.$list->total.'">'.
                                    '<input type="hidden" id="table_keterangan'.$list->id_returkasir_detail.'" value="'.$list->keterangan.'">'.
        							'<input type="hidden" id="table_idlog_stok'.$list->id_returkasir_detail.'" value="'.$list->id_log_stok.'">');
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
        if($group == 5 || $group == 6 || $group == 8){
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
        $return = DB::table('tbl_kasir_detail_retur as tkr')->leftjoin('tbl_barang as tb','tkr.id_barang','tb.barang_id')
            ->leftjoin('tbl_satuan as ts','tkr.id_satuan','ts.satuan_id')
            ->leftjoin('ref_gudang as rg','tkr.id_gudang','rg.id')
            ->leftjoin('m_pelanggan as mp','tkr.id_pelanggan','mp.id')
            ->where('tkr.tanggal','>=',$tanggal_start)
            ->where('tkr.tanggal','<=',$tanggal_end)
            ->whereIn('tkr.id_gudang',$id_gudang)
            ->select(DB::raw('tkr.id_returkasir_detail, tkr.kode_retur, tkr.tanggal, tkr.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, tkr.id_satuan, 
            ts.satuan_nama as nama_satuan, tkr.id_gudang, rg.nama as nama_gudang, tkr.id_pelanggan, mp.nama as nama_pelanggan, tkr.jumlah, tkr.harga, tkr.total, tkr.keterangan, tkr.id_log_stok, tkr.created_iduser'))->orderBy('tkr.id_returkasir_detail','DESC');
        if($search){
        $return = DB::table('tbl_kasir_detail_retur as tkr')->leftjoin('tbl_barang as tb','tkr.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','tkr.id_satuan','ts.satuan_id')->leftjoin('ref_gudang as rg','tkr.id_gudang','rg.id')->leftjoin('m_pelanggan as mp','tkr.id_pelanggan','mp.id')
            ->where('tkr.tanggal','>=',$tanggal_start)
            ->where('tkr.tanggal','<=',$tanggal_end)
            ->where(function($query) use ($search,$tanggal) {
                    $query->orwhere('tkr.kode_retur','like','%'.$search.'%');
                    $query->orwhere('tkr.tanggal','like','%'.$tanggal.'%');
                    $query->orwhere('tb.barang_nama','like','%'.$search.'%');
                    $query->orwhere('rg.nama','like','%'.$search.'%');
                    $query->orwhere('mp.nama','like','%'.$search.'%');
            })
            ->whereIn('tkr.id_gudang',$id_gudang)
            ->select(DB::raw('tkr.id_returkasir_detail, tkr.kode_retur, tkr.tanggal, tkr.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, tkr.id_satuan, 
            ts.satuan_nama as nama_satuan, tkr.id_gudang, rg.nama as nama_gudang, tkr.id_pelanggan, mp.nama as nama_pelanggan, tkr.jumlah, tkr.harga, tkr.total, tkr.keterangan, tkr.id_log_stok, tkr.created_iduser'))->orderBy('tkr.id_returkasir_detail','DESC');
        }
        $totalrecord = count($return->get());
        $return_2    = $return->offset($start)->limit($length);
        $no          = ($start==0)?0:$start;
        $arr = array();
        foreach ($return_2->get() as $list) {
            $no++;
            $arr[] = array(
                  'nomor'=>$no,
                            'kode_retur'    => $list->kode_retur,
                            'tanggal'       => tgl_full($list->tanggal,''),
                            'nama_barang'   => $list->nama_barang,
                            'jumlah'        => $list->jumlah,
                            'nama_satuan'   => $list->nama_satuan,
                            'nama_pelanggan'=> $list->nama_pelanggan,
                            'nama_gudang'   => $list->nama_gudang,
                            'harga'         => $list->harga,
                            'total'         => $list->total,
                            'aksi'          => $this->get_aksi($list->id_returkasir_detail,$group_where->group_aktif,$list->created_iduser).
                                    '<input type="hidden" id="table_id'.$list->id_returkasir_detail.'" value="'.$list->id_returkasir_detail.'">'.
                                    '<input type="hidden" id="table_kode'.$list->id_returkasir_detail.'" value="'.$list->kode_retur.'">'.
                                    '<input type="hidden" id="table_tanggal'.$list->id_returkasir_detail.'" value="'.tgl_full($list->tanggal,'').'">'.
                                    '<input type="hidden" id="table_idbarang'.$list->id_returkasir_detail.'" value="'.$list->id_barang.'">'.
                                    '<input type="hidden" id="table_namabarang'.$list->id_returkasir_detail.'" value="'.$list->nama_barang.'">'.
                                    '<input type="hidden" id="table_kodebarang'.$list->id_returkasir_detail.'" value="'.$list->kode_barang.'">'.
                                    '<input type="hidden" id="table_aliasbarang'.$list->id_returkasir_detail.'" value="'.$list->alias_barang.'">'.
                                    '<input type="hidden" id="table_idgudang'.$list->id_returkasir_detail.'" value="'.$list->id_gudang.'">'.
                                    '<input type="hidden" id="table_namagudang'.$list->id_returkasir_detail.'" value="'.$list->nama_gudang.'">'.
                                    '<input type="hidden" id="table_idpelanggan'.$list->id_returkasir_detail.'" value="'.$list->id_pelanggan.'">'.
                                    '<input type="hidden" id="table_namapelanggan'.$list->id_returkasir_detail.'" value="'.$list->nama_pelanggan.'">'.
                                    '<input type="hidden" id="table_idsatuan'.$list->id_returkasir_detail.'" value="'.$list->id_satuan.'">'.
                                    '<input type="hidden" id="table_jumlah'.$list->id_returkasir_detail.'" value="'.$list->jumlah.'">'.
                                    '<input type="hidden" id="table_harga'.$list->id_returkasir_detail.'" value="'.$list->harga.'">'.
                                    '<input type="hidden" id="table_total'.$list->id_returkasir_detail.'" value="'.$list->total.'">'.
                      '<input type="hidden" id="table_keterangan'.$list->id_returkasir_detail.'" value="'.$list->keterangan.'">'.
                                    '<input type="hidden" id="table_idlog_stok'.$list->id_returkasir_detail.'" value="'.$list->id_log_stok.'">');
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
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT
					b.barang_id,
					b.satuan_id,
					b.barang_kode,
					b.barang_nama,
          			b.barang_alias,
					b.barang_id_parent,
					b.barang_status_bahan,
					s.satuan_nama,
					s.satuan_satuan,
					d.detail_harga_barang_harga_jual harga
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
                WHERE b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%' OR b.barang_alias LIKE '%$search%'");
        return \Response::json($d_query);
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
          $where_gudang = "AND b.id IN ($gudang)";
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

    function simpan(Request $request){
        $id = $request->get("popup_id_table");
        $gudang = $request->get("popup_gudang");
    	$data_log['id_barang'] 		= $request->get("popup_barang");
        $data_log['id_ref_gudang'] 	= $gudang;
        $data_log['id_satuan'] 		= $request->get("popup_satuan");
        if($gudang == '8'){
        $data_log['unit_masuk'] 	= $request->get("popup_jumlah");
        $data_log['unit_keluar'] 	= '0';
        }else{
        $data_log['unit_masuk']     = '0';
        $data_log['unit_keluar']    = $request->get("popup_jumlah");    
        }
        $data_log['tanggal'] 		= ($request->get('popup_tanggal')=='') ? tgl_full(date('d-m-Y'), 2):tgl_full($request->get('popup_tanggal'), 2);
        $data_log['ket'] = $request->get('popup_ket');
        $data_log['status'] = 'J2';
        if($id == ''){
        $id_log_stok = DB::table('tbl_log_stok')->insertGetId($data_log);
    	}else{
    	$id_log_stok = $request->get("popup_idlog_stok");
    	}

    	$data['kode_retur']= $request->get("popup_kode");
        $data['id_barang'] = $request->get("popup_barang");
        $data['id_gudang'] = $request->get("popup_gudang");
        $data['id_pelanggan'] = $request->get("popup_pelanggan");
        $data['id_satuan'] = $request->get("popup_satuan");
        $data['jumlah'] = $request->get("popup_jumlah");
        $data['tanggal'] = ($request->get("popup_tanggal")=="") ? tgl_full(date('d-m-Y'), 99):tgl_full($request->get('popup_tanggal'), 99);
        $data['keterangan'] = ($request->get('popup_ket')=="")?".":$request->get('popup_ket');
        $data['id_log_stok'] = $id_log_stok;

        if($id == ''){
            //Create User Input
            $data['created_by'] = Auth::user()->name;
            $data['created_iduser'] = Auth::user()->id;
            $id_kasir_detail_retur = DB::table('tbl_kasir_detail_retur')->insertGetId($data);
            trigger_log($id_kasir_detail_retur, "Menambahkan Retur Penjualan", 1);
        }else{
            //Update User Input
            $data['updated_by'] = Auth::user()->name;
            $data['updated_iduser'] = Auth::user()->id;
            DB::table('tbl_kasir_detail_retur')->where('id_returkasir_detail',$id)->update($data);
            DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->update($data_log);
            trigger_log($id, "Mengubah Retur Penjualan", 2);
        }

        return response()->json(array('status' => '1'));
    }

    public function hapus(Request $request){
      $id = $request->get('id');
      $d_barang = DB::table('tbl_kasir_detail_retur')->where('id_returkasir_detail',$id);
      $id_log_stok = array();
      foreach($d_barang->get() AS $d){
        $id_log_stok[] = $d->id_log_stok;
      }
      DB::table('tbl_log_stok')->whereIn('log_stok_id',$id_log_stok)->delete();
      DB::table('tbl_kasir_detail_retur')->where(array('id_returkasir_detail' => $id))->delete();
      trigger_log($id, "Menghapus Retur Penjualan", 3);

    }


    function get_aksi($id, $status, $input_user){
        $session = Auth::user();
        switch ($status) {
            case '2':
                if($session->group_id == 8){
                  if($session->id == $input_user){
                    $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button> <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
                  }else{
                    $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-eye"></i> </button></div>';
                  }
                }else{
                  $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button> <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
                }
                break;
            case '1':
                $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-eye"></i> </button></div>';
                break;            
            default:
                # code...
                // $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button> <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
                if($session->group_id == 8){
                  if($session->id == $input_user){
                    $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button> <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
                  }else{
                    $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-eye"></i> </button></div>';
                  }
                }else{
                  $html = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button> <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
                }
                break;
        }
        return $html;
    }

}

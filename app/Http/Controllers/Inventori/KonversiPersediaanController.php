<?php

namespace App\Http\Controllers\Inventori;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use DB;
use App\KonversiPersediaanModel;
use Auth;


class KonversiPersediaanController extends Controller
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
      $data['data']   = array();      
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
      return view("admin.konversipersediaan.index")->with('data',$data);
    }

    public function get_barang(Request $request){
    $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $search = strtolower($term);
        $barangs= DB::select("SELECT
            l.log_stok_id,
            b.barang_id,
            b.barang_kode,
            b.barang_nama,
            b.barang_alias,
            b.barang_id_parent,
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
            GROUP BY
              t.id_barang,
              t.id_ref_gudang,
              t.id_satuan  
            ) l
            LEFT JOIN tbl_barang AS b ON l.id_barang = b.barang_id
            LEFT JOIN tbl_satuan AS s ON l.id_satuan = s.satuan_id
            LEFT JOIN ref_gudang AS rf ON l.id_ref_gudang = rf.id
            WHERE jumlah > 0 AND (b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%' OR rf.nama LIKE '%$search%')");

        return \Response::json($barangs);
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
        $saldoawal = KonversiPersediaanModel::with(['dataBarang','dataGudang','dataSatuan','dataSatuan_Konversi','dataDetail'])->whereIn('id_gudang',$id_gudang)->orderBy('tanggal', 'DESC')->get();
        /*<button class="btn btn-xs btn-primary" onclick="edit('.$list->id_saldoawal.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button>*/
        $no = 0;
        $data = array();
        foreach ($saldoawal as $list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = tgl_full($list->tanggal,'');
            /*$row[] = $list->nama_barang;
            $row[] = $list->nama_gudang;*/
            $row[] = $list->dataBarang->barang_nama;
            $row[] = $list->dataGudang->nama;
            $row[] = '<p class="text-right">'.format_angka($list->jumlah)." ".$list->dataSatuan->satuan_satuan.'</p>';
            $row[] = '<p class="text-right">'.format_angka($list->jumlah_konversi)." ".$list->dataSatuan_Konversi->satuan_satuan.'</p>';
            $row[] = $list->keterangan;
            $row[] = '<div class="btn-group">
            <a onclick="deleteData('.$list->id_konversi.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>'.
            			'<input type="hidden" id="table_id'.$list->id_konversi.'" value="'.$list->id_konversi.'">'.
                        '<input type="hidden" id="table_idbarang'.$list->id_konversi.'" value="'.$list->id_barang.'">'.
                        '<input type="hidden" id="table_namabarang'.$list->id_konversi.'" value="'.$list->dataBarang->barang_nama.'">'.
                        '<input type="hidden" id="table_idgudang'.$list->id_konversi.'" value="'.$list->id_gudang.'">'.
                        '<input type="hidden" id="table_namagudang'.$list->id_konversi.'" value="'.$list->dataGudang->nama.'">'.
                        '<input type="hidden" id="table_idsatuan'.$list->id_konversi.'" value="'.$list->id_satuan.'">'.
                        '<input type="hidden" id="table_jumlah'.$list->id_konversi.'" value="'.$list->jumlah.'">'.
                        '<input type="hidden" id="table_tanggal'.$list->id_konversi.'" value="'.tgl_full($list->tanggal,'').'">'.
                        '<input type="hidden" id="table_keterangan'.$list->id_konversi.'" value="'.$list->keterangan.'">'.
                        '<input type="hidden" id="table_idlog_stok'.$list->id_konversi.'" value="'.$list->id_log_stok.'">'.
                        '<input type="hidden" id="table_idsatuan_konversi'.$list->id_konversi.'" value="'.$list->id_satuan_konversi.'">'.
                        '<input type="hidden" id="table_jumlah_konversi'.$list->id_konversi.'" value="'.$list->jumlah_konversi.'">';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);
    }

  	function simpan(Request $request){
        $id = $request->get("popup_id_table");

    	$data['id_barang'] 			= $request->get("popup_barang");
        $data['id_gudang'] 			= $request->get("popup_gudang");
        $data['id_satuan'] 			= $request->get("popup_satuan");
        $data['jumlah']	   			= $request->get("popup_jumlah");
        $data['id_satuan_konversi'] = $request->get("popup_satuan_konversi");
        $data['jumlah_konversi']	= $request->get("popup_jumlah_konversi");
        $data['tanggal']   			= ($request->get('popup_tanggal') == "") ? tgl_full(date('Y-m-d'),99):tgl_full($request->get('popup_tanggal'), 2);
        $data['keterangan'] 		= $request->get('popup_ket');
        
        $satuan_log['id_barang'] 	= $request->get("popup_barang");
        $satuan_log['id_ref_gudang']= $request->get("popup_gudang");
        $satuan_log['id_satuan'] 	= $request->get("popup_satuan");
        $satuan_log['unit_masuk'] 	= 0;
        $satuan_log['unit_keluar'] 	= $request->get("popup_jumlah");
        $satuan_log['tanggal'] 		= ($request->get('popup_tanggal') == "") ? tgl_full(date('Y-m-d'),99):tgl_full($request->get('popup_tanggal'), 2);
        $satuan_log['status']		= "KO1";
        $satuan_log['ket']	= $request->get('popup_ket');

        $satuan_konversi['id_barang'] 	= $request->get("popup_barang");
        $satuan_konversi['id_ref_gudang']= $request->get("popup_gudang");
        $satuan_konversi['id_satuan'] 	= $request->get("popup_satuan_konversi");
        $satuan_konversi['unit_masuk'] 	= $request->get("popup_jumlah_konversi");
        $satuan_konversi['unit_keluar'] 	= 0;
        $satuan_konversi['tanggal'] 		= ($request->get('popup_tanggal') == "") ? tgl_full(date('Y-m-d'),99):tgl_full($request->get('popup_tanggal'), 2);
        $satuan_konversi['status']		= "KO2";
        $satuan_konversi['ket']	= $request->get('popup_ket');

        if($id == ''){
            $id_konversi = DB::table('tbl_konversipersediaan')->insertGetId($data);
            $id_log_stok_satuan		= DB::table('tbl_log_stok')->insertGetId($satuan_log);
            $id_log_stok_konversi	= DB::table('tbl_log_stok')->insertGetId($satuan_konversi);

            $satuan['id_barang'] 	= $request->get("popup_barang");
	        $satuan['id_gudang']	= $request->get("popup_gudang");
	        $satuan['id_satuan'] 	= $request->get("popup_satuan");
	        $satuan['jumlah'] 		= $request->get("popup_jumlah");
	        $satuan['id_log_stok']	= $id_log_stok_satuan;
	        $satuan['id_konversi']	= $id_konversi;
	        DB::table('tbl_konversipersediaan_detail')->insert($satuan);

	        $konversi['id_barang'] 		= $request->get("popup_barang");
	        $konversi['id_gudang']		= $request->get("popup_gudang");
	        $konversi['id_satuan'] 		= $request->get("popup_satuan_konversi");
	        $konversi['jumlah'] 		= $request->get("popup_jumlah_konversi");
	        $konversi['id_log_stok']	= $id_log_stok_konversi;
	        $konversi['id_konversi']	= $id_konversi;
	        DB::table('tbl_konversipersediaan_detail')->insert($konversi);

        }/*else{
            DB::table('tbl_saldoawal')->where('id_saldoawal',$id)->update($data);
            DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->update($data_log);
        }*/

        return response()->json(array('status' => '1'));
    }

    function konversi(Request $request){
    	$id_satuan = $request->get('id_satuan');
    	$id_satuan_konversi = $request->get('id_satuan_konversi');
    	$d_jumlah 			= $request->get('jumlah');
    	$d_query = DB::table('tbl_satuan_konversi')->where('id_satuan_awal',$id_satuan)->where('id_satuan_akhir',$id_satuan_konversi);
    	$d_jumlah_awal = 0;
    	$d_jumlah_akhir = 0;
    	$arr = array();
    	$jumlah = 0;
    	if($d_query->count() > 0){
    		$d_jumlah_awal	= $d_query->first()->jumlah_awal;
    		$d_jumlah_akhir = $d_query->first()->jumlah_akhir;
    		$jumlah = (float)($d_jumlah/$d_jumlah_awal)*$d_jumlah_akhir;
    		$arr[] = array('jumlah'=>$jumlah);
    	}    	

    	return response()->json($jumlah);
    	//dd($d_query->first()->jumlah_awal);

    }

    function hapus(Request $request){
    	$id = $request->get('id');
    	$d_query = DB::table('tbl_konversipersediaan_detail')->where('id_konversi',$id)->get();
    	$id_log_stok = array();
    	foreach($d_query as $d){
    		$id_log_stok[] = $d->id_log_stok;
    	}

    	DB::table('tbl_log_stok')->whereIn('log_stok_id',$id_log_stok)->delete();
    	DB::table('tbl_konversipersediaan_detail')->where(array('id_konversi'=>$id))->delete();
    	DB::table('tbl_konversipersediaan')->where('id_konversi',$id)->delete();

    }
}

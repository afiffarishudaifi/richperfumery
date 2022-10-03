<?php

namespace App\Http\Controllers\Inventori;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use App\SaldoawalModel;
use Jenssegers\Agent\Agent;

class SaldoawalController extends Controller
{
    //
    public function __construct(){
      // $this->group_id = Auth::user()->group_id;
      $this->agent = new Agent();
    }

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


      /*$id_group = Auth::user()->group_id;
        $id_profil = Auth::user()->id_profil;
        if ($id_group == 5) {
            $where="WHERE  p.jenis_outlet = 2 and g.id_profil='".$id_profil."'";
        } else{

           $where='WHERE  p.jenis_outlet ';

        }
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
          LEFT JOIN m_profil p ON g.id_profil = p.id $where
        ");*/
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
      if ($this->agent->isMobile()) {
        return view('admin_mobile.saldoawal.index')->with('data',$data);
      }else {
        return view('admin.saldoawal.index')->with('data',$data);
      }
    }

    public function get_gudang(Request $request){
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
                WHERE b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%' OR b.barang_alias LIKE '%$search%'");
        return \Response::json($d_query);
    }

    public function listData(){
       /*$id_group = Auth::user()->group_id;
        $id_profil = Auth::user()->id_profil;
        if ($id_group == 5) {
          $a = DB::table("ref_gudang")->where('id_profil',$id_profil)->get()->first();
            $where="WHERE rg.id = '".$a->id."' ";
        } else if($id_group == 6) {
            $a = DB::table("ref_gudang")->where('id_profil', $id_profil)->get()->first();
            $where="WHERE rg.id = '".$a->id."' ";
        }*/

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

        //$pembelian = PembelianModel::with(['dataPenyedia'])->orderBy('id_pembelian', 'DESC')->get();
        // DB::enableQueryLog();
        /*$saldoawal = DB::select("SELECT
                `tsd`.*,
                `tb`.`barang_nama` AS `nama_barang`,
                `ts`.`satuan_nama` AS `nama_satuan`,
                `ts`.`satuan_satuan` AS `satuan_satuan`,
                `rg`.`nama` AS `nama_gudang`
              FROM
                `tbl_saldoawal` AS `tsd`
                LEFT JOIN `tbl_barang` AS `tb` ON `tsd`.`id_barang` = `tb`.`barang_id`
                LEFT JOIN `tbl_satuan` AS `ts` ON `tsd`.`id_satuan` = `ts`.`satuan_id`
                LEFT JOIN `ref_gudang` AS `rg` ON `tsd`.`id_gudang` = `rg`.`id` $where");*/
        // dd(DB::getQueryLog());
        $tanggal_start = date('Y-m-d', strtotime('-7 days'));
        $tanggal_end = date('Y-m-d');
        $saldoawal = SaldoawalModel::with(['dataBarang','dataGudang','dataSatuan'])->where('tanggal','>',$tanggal_start)->where('tanggal','<=',$tanggal_end)->whereIn('id_gudang',$id_gudang)->orderBy('tanggal', 'DESC')->get();

        $no = 0;
        $data = array();
        foreach ($saldoawal as $list) {
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['tanggal'] = tgl_full($list->tanggal,'');
              /*$row['ahvfa'] = $list->nama_barang;
              $row['ahvfa'] = $list->nama_gudang;*/
              $row['barang_nama'] = $list->dataBarang->barang_nama;
              $row['gudang_nama'] = $list->dataGudang->nama;
              $row['jumlah'] = format_angka($list->jumlah)." ".$list->satuan_satuan;
              $row['keterangan'] = $list->keterangan;
              $row['aksi'] = $this->get_aksi($list->id_saldoawal,$group_where->group_aktif).
              			'<input type="hidden" id="table_id'.$list->id_saldoawal.'" value="'.$list->id_saldoawal.'">'.
                          '<input type="hidden" id="table_idbarang'.$list->id_saldoawal.'" value="'.$list->id_barang.'">'.
                          '<input type="hidden" id="table_namabarang'.$list->id_saldoawal.'" value="'.$list->dataBarang->barang_nama.'">'.
                          '<input type="hidden" id="table_idgudang'.$list->id_saldoawal.'" value="'.$list->id_gudang.'">'.
                          '<input type="hidden" id="table_namagudang'.$list->id_saldoawal.'" value="'.$list->dataGudang->nama.'">'.
                          '<input type="hidden" id="table_idsatuan'.$list->id_saldoawal.'" value="'.$list->id_satuan.'">'.
                          '<input type="hidden" id="table_namasatuan'.$list->id_saldoawal.'" value="'.$list->dataSatuan->satuan_nama.'">'.
                          '<input type="hidden" id="table_jumlah'.$list->id_saldoawal.'" value="'.$list->jumlah.'">'.
                          '<input type="hidden" id="table_tanggal'.$list->id_saldoawal.'" value="'.tgl_full($list->tanggal,'').'">'.
                          '<input type="hidden" id="table_keterangan'.$list->id_saldoawal.'" value="'.$list->keterangan.'">'.
                          '<input type="hidden" id="table_idlog_stok'.$list->id_saldoawal.'" value="'.$list->id_log_stok.'">';
            }else {
              // code...
              $row[] = $no;
              $row[] = tgl_full($list->tanggal,'');
              //$row[] = $list->nama_barang;
              //$row[] = $list->nama_gudang;
              $row[] = $list->dataBarang->barang_nama;
              $row[] = $list->dataGudang->nama;
              $row[] = '<p class="text-right">'.format_angka($list->jumlah)." ".$list->satuan_satuan.'</p>';
              $row[] = $list->keterangan;
              $row[] = $this->get_aksi($list->id_saldoawal,$group_where->group_aktif).
              '<input type="hidden" id="table_id'.$list->id_saldoawal.'" value="'.$list->id_saldoawal.'">'.
              '<input type="hidden" id="table_idbarang'.$list->id_saldoawal.'" value="'.$list->id_barang.'">'.
              '<input type="hidden" id="table_namabarang'.$list->id_saldoawal.'" value="'.$list->dataBarang->barang_nama.'">'.
              '<input type="hidden" id="table_idgudang'.$list->id_saldoawal.'" value="'.$list->id_gudang.'">'.
              '<input type="hidden" id="table_namagudang'.$list->id_saldoawal.'" value="'.$list->dataGudang->nama.'">'.
              '<input type="hidden" id="table_idsatuan'.$list->id_saldoawal.'" value="'.$list->id_satuan.'">'.
              '<input type="hidden" id="table_namasatuan'.$list->id_saldoawal.'" value="'.$list->dataSatuan->satuan_nama.'">'.
              '<input type="hidden" id="table_jumlah'.$list->id_saldoawal.'" value="'.$list->jumlah.'">'.
              '<input type="hidden" id="table_tanggal'.$list->id_saldoawal.'" value="'.tgl_full($list->tanggal,'').'">'.
              '<input type="hidden" id="table_keterangan'.$list->id_saldoawal.'" value="'.$list->keterangan.'">'.
              '<input type="hidden" id="table_idlog_stok'.$list->id_saldoawal.'" value="'.$list->id_log_stok.'">';
            }
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);
    }

    public function searchtanggal(Request $request){
       /*$id_group = Auth::user()->group_id;
        $id_profil = Auth::user()->id_profil;
        if ($id_group == 5) {
          $a = DB::table("ref_gudang")->where('id_profil',$id_profil)->get()->first();
            $where="WHERE rg.id = '".$a->id."' ";
        } else if($id_group == 6) {
            $a = DB::table("ref_gudang")->where('id_profil', $id_profil)->get()->first();
            $where="WHERE rg.id = '".$a->id."' ";
        }*/

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

        //$pembelian = PembelianModel::with(['dataPenyedia'])->orderBy('id_pembelian', 'DESC')->get();
        // DB::enableQueryLog();
        /*$saldoawal = DB::select("SELECT
                `tsd`.*,
                `tb`.`barang_nama` AS `nama_barang`,
                `ts`.`satuan_nama` AS `nama_satuan`,
                `ts`.`satuan_satuan` AS `satuan_satuan`,
                `rg`.`nama` AS `nama_gudang`
              FROM
                `tbl_saldoawal` AS `tsd`
                LEFT JOIN `tbl_barang` AS `tb` ON `tsd`.`id_barang` = `tb`.`barang_id`
                LEFT JOIN `tbl_satuan` AS `ts` ON `tsd`.`id_satuan` = `ts`.`satuan_id`
                LEFT JOIN `ref_gudang` AS `rg` ON `tsd`.`id_gudang` = `rg`.`id` $where");*/
        // dd(DB::getQueryLog());
        $tanggalrange = explode('s.d.',$request->get('tanggal'));
        $tanggal_start  = tgl_full($tanggalrange[0],99);
        $tanggal_end    = tgl_full($tanggalrange[1],99);
        $saldoawal = SaldoawalModel::with(['dataBarang','dataGudang','dataSatuan'])->where('tanggal','>=',$tanggal_start)->where('tanggal','<=',$tanggal_end)->whereIn('id_gudang',$id_gudang)->orderBy('tanggal', 'DESC')->get();

        $no = 0;
        $data = array();
        foreach ($saldoawal as $list) {
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['tanggal'] = tgl_full($list->tanggal,'');
              /*$row['ahvfa'] = $list->nama_barang;
              $row['ahvfa'] = $list->nama_gudang;*/
              $row['barang_nama'] = $list->dataBarang->barang_nama;
              $row['gudang_nama'] = $list->dataGudang->nama;
              $row['jumlah'] = format_angka($list->jumlah)." ".$list->satuan_satuan;
              $row['keterangan'] = $list->keterangan;
              $row['aksi'] = $this->get_aksi($list->id_saldoawal,$group_where->group_aktif).
                    '<input type="hidden" id="table_id'.$list->id_saldoawal.'" value="'.$list->id_saldoawal.'">'.
                          '<input type="hidden" id="table_idbarang'.$list->id_saldoawal.'" value="'.$list->id_barang.'">'.
                          '<input type="hidden" id="table_namabarang'.$list->id_saldoawal.'" value="'.$list->dataBarang->barang_nama.'">'.
                          '<input type="hidden" id="table_idgudang'.$list->id_saldoawal.'" value="'.$list->id_gudang.'">'.
                          '<input type="hidden" id="table_namagudang'.$list->id_saldoawal.'" value="'.$list->dataGudang->nama.'">'.
                          '<input type="hidden" id="table_idsatuan'.$list->id_saldoawal.'" value="'.$list->id_satuan.'">'.
                          '<input type="hidden" id="table_namasatuan'.$list->id_saldoawal.'" value="'.$list->dataSatuan->satuan_nama.'">'.
                          '<input type="hidden" id="table_jumlah'.$list->id_saldoawal.'" value="'.$list->jumlah.'">'.
                          '<input type="hidden" id="table_tanggal'.$list->id_saldoawal.'" value="'.tgl_full($list->tanggal,'').'">'.
                          '<input type="hidden" id="table_keterangan'.$list->id_saldoawal.'" value="'.$list->keterangan.'">'.
                          '<input type="hidden" id="table_idlog_stok'.$list->id_saldoawal.'" value="'.$list->id_log_stok.'">';
            }else {
              // code...
              $row[] = $no;
              $row[] = tgl_full($list->tanggal,'');
              $row[] = $list->dataBarang->barang_nama;
              $row[] = $list->dataGudang->nama;
              $row[] = '<p class="text-right">'.format_angka($list->jumlah)." ".$list->satuan_satuan.'</p>';
              $row[] = $list->keterangan;
              $row[] = $this->get_aksi($list->id_saldoawal,$group_where->group_aktif).
              '<input type="hidden" id="table_id'.$list->id_saldoawal.'" value="'.$list->id_saldoawal.'">'.
              '<input type="hidden" id="table_idbarang'.$list->id_saldoawal.'" value="'.$list->id_barang.'">'.
              '<input type="hidden" id="table_namabarang'.$list->id_saldoawal.'" value="'.$list->dataBarang->barang_nama.'">'.
              '<input type="hidden" id="table_idgudang'.$list->id_saldoawal.'" value="'.$list->id_gudang.'">'.
              '<input type="hidden" id="table_namagudang'.$list->id_saldoawal.'" value="'.$list->dataGudang->nama.'">'.
              '<input type="hidden" id="table_idsatuan'.$list->id_saldoawal.'" value="'.$list->id_satuan.'">'.
              '<input type="hidden" id="table_namasatuan'.$list->id_saldoawal.'" value="'.$list->dataSatuan->satuan_nama.'">'.
              '<input type="hidden" id="table_jumlah'.$list->id_saldoawal.'" value="'.$list->jumlah.'">'.
              '<input type="hidden" id="table_tanggal'.$list->id_saldoawal.'" value="'.tgl_full($list->tanggal,'').'">'.
              '<input type="hidden" id="table_keterangan'.$list->id_saldoawal.'" value="'.$list->keterangan.'">'.
              '<input type="hidden" id="table_idlog_stok'.$list->id_saldoawal.'" value="'.$list->id_log_stok.'">';
            }
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);
    }

    function simpan(Request $request){
        $id = $request->get("popup_id_table");

    	  $data_log['id_barang'] 		= $request->get("popup_barang");
        $data_log['id_ref_gudang'] 	= $request->get("popup_gudang");
        $data_log['id_satuan'] 		= $request->get("popup_satuan");
        $data_log['unit_masuk'] 	= $request->get("popup_jumlah");
        /*$data_log['tanggal'] 		= is_null($request->get('popup_tanggal')) ? tgl_full($request->get('popup_tanggal'), 2):tgl_full(date('d-m-Y'), 2);*/
        $data_log['tanggal'] = tgl_full(date('d-m-Y'), 2);
        $data_log['ket'] = $request->get('popup_ket');
        $data_log['status'] = 'SA1';
        if($id == ''){
        $id_log_stok = DB::table('tbl_log_stok')->insertGetId($data_log);
    	}else{
    	$id_log_stok = $request->get("popup_idlog_stok");
    	}

        $data['id_barang'] = $request->get("popup_barang");
        $data['id_gudang'] = $request->get("popup_gudang");
        $data['id_satuan'] = $request->get("popup_satuan");
        $data['jumlah'] = $request->get("popup_jumlah");
        /*$data['tanggal'] = is_null($request->get('popup_tanggal')) ? tgl_full($request->get('popup_tanggal'), 2):tgl_full(date('d-m-Y'), 2);*/
        $data['tanggal'] = ($request->get("popup_tanggal")=="") ? tgl_full(date('d-m-Y'), 99):tgl_full($request->get('popup_tanggal'), 99);
        $data['keterangan'] = $request->get('popup_ket');
        $data['id_log_stok'] = $id_log_stok;

        if($id == ''){
            $id_saldoawal = DB::table('tbl_saldoawal')->insertGetId($data);
            trigger_log($id_saldoawal, "Menambahkan Data Saldo Awal", 1);
        }else{
            DB::table('tbl_saldoawal')->where('id_saldoawal',$id)->update($data);
            DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->update($data_log);
            trigger_log($id, "Mengubah Data Saldo Awal", 2);
        }

        return response()->json(array('status' => '1'));
    }


    public function hapus(Request $request){
      $id = $request->get('id');
      $d_barang = DB::table('tbl_saldoawal')->where('id_saldoawal',$id)->first();
      $id_log_stok = $d_barang->id_log_stok;
      DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->delete();
      DB::table('tbl_saldoawal')->where('id_saldoawal',$id)->delete();
      trigger_log($id, "Menghapus Data Saldo Awal", 3);
    }

    function get_aksi($id,$status){
      switch ($status) {
        case '2':
            $tombol = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button>
              <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
          break;
        case '1':
            $tombol = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="detail('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-eye"></i> </button></div>';
          break;
        
        default:
            $tombol = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$id.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button>
              <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
          break;
      }

      return $tombol;
    }


}

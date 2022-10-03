<?php

namespace App\Http\Controllers\Pengiriman;

use Illuminate\Http\Request;
use DB;
use App\InvPengiriman;
use Redirect;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use Auth;

class PengirimanreturController extends Controller
{
  public function __construct(){
    $this->agent = new Agent();
  }
    public function index(){
      $id_group = Auth::user()->group_id;
      $id_profil = Auth::user()->id_profil;
      $where = "";
      if($id_group == 5){
        $where = "AND g.id_profil='$id_profil'";
      }

      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $gudang['tombol_create'] = tombol_create('',$group_where->group_aktif,1);      
      $gudang['gudang'] = DB::select('SELECT
                                        g.id,
                                        g.id_profil,
                                        g.nama,
                                        g.alamat,
                                        g.`status`,
                                        g.created_at,
                                        g.updated_at,
                                        p.nama nama_profil,
                                        p.jenis_outlet
                                      FROM
                                        ref_gudang AS g
                                        LEFT JOIN m_profil AS p ON g.id_profil = p.id
                                        WHERE p.jenis_outlet =1
                                      ORDER BY
                                        g.id ASC');
      $gudang['outlet'] = DB::select("SELECT
                                      g.id,
                                      g.id_profil,
                                      g.nama,
                                      g.alamat,
                                      g.`status`,
                                      g.created_at,
                                      g.updated_at,
                                      p.nama nama_profil,
                                      p.jenis_outlet
                                    FROM
                                      ref_gudang AS g
                                      LEFT JOIN m_profil AS p ON g.id_profil = p.id
                                      WHERE p.jenis_outlet=2 $where
                                    ORDER BY
                                      g.id ASC");
      $gudang['noauto'] = $this->noauto();
          if($this->agent->isMobile()) {
            // code...
            return view('admin_mobile.pengiriman.retur',compact('gudang'));
          }else{
            // code...
            return view('admin.pengiriman.retur',compact('gudang'));
          }
    }

    function noauto(){
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($id_profil != ''){
          $where = "WHERE id_profil='$id_profil'";
        }

        $d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        $a_kode = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
          $a_kode[] = $d->kode;
        }
        $where_gudang = "";
        $where_kode = "";
        if(sizeof($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $d_kode = implode(',',$a_kode);
          $where_gudang = "WHERE rf.id IN ($gudang)";
          $where_kode = $d_kode;
        }

        $no_faktur = $where_kode."R.";
        if(sizeof($id_gudang) > 1){
          $no_faktur = "KIRR.";
        }

        $nama = $no_faktur.date('dmY');
        $d_barang = DB::table('pengiriman_retur')->where('kode_retur', 'like', $nama.'%')->orderBy('kode_retur', 'desc');
        if($d_barang->get()->count() > 0){
            $kode = $d_barang->first()->kode_retur;
        }else{
            $kode = 0;
        }
        $kode_kat = strlen($nama);
        $kode = substr($kode, ($kode_kat));
        $kode = sprintf('%04d', $kode+1);
        $kode = $nama.$kode;
        return $kode;
    }

    function noauto2(Request $request){
      $idgudang = $request->gudang;
      $d_gudang = DB::table('ref_gudang')->where('id','=',$idgudang)->first();
      $no_faktur = $d_gudang->kode."R.";

      $nama = $no_faktur.date('dmY');
      $d_barang = DB::table('pengiriman_retur')->where('kode_retur', 'like', $nama.'%')->orderBy('kode_retur', 'desc');
      if($d_barang->get()->count() > 0){
          $kode = $d_barang->first()->kode_retur;
      }else{
          $kode = 0;
      }
      $kode_kat = strlen($nama);
      $kode = substr($kode, ($kode_kat));
      $kode = sprintf('%04d', $kode+1);
      $kode = $nama.$kode;
      // return $kode;

      return response()->json($kode);
    }

    function select2barang(Request $request){
    $term = trim($request->q);
    $gudang = $request->gudang;

        if (empty($term)) {
            return \Response::json([]);
        }
        
        // dd($gudang);
        // $barangs =BarangModel::query()
        //         ->where('barang_kode', 'LIKE', "%{$term}%")
        //         ->get();
        $search = strtolower($term);
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
          l.jumlah_keluar,
          l.id_satuan
        FROM
          (
          SELECT
            t.log_stok_id,
            t.id_barang,
            t.id_ref_gudang,
            Sum( t.unit_masuk ) AS jumlah_masuk,
            Sum( t.unit_keluar ) AS jumlah_keluar,
            SUM( t.unit_masuk - t.unit_keluar ) AS stok,
            t.id_satuan
          FROM
            tbl_log_stok AS t
          WHERE
            t.id_ref_gudang = '$gudang'
          GROUP BY
            t.id_barang,
            t.id_ref_gudang
          ) l
          LEFT JOIN tbl_barang AS b ON l.id_barang = b.barang_id
          LEFT JOIN tbl_satuan AS s ON l.id_satuan = s.satuan_id WHERE LOWER(b.barang_kode) LIKE '%$search%' or LOWER(b.barang_nama) LIKE '%$search%' ");*/

        $barangs= DB::select("SELECT  
                q.id_barang,
                q.kode_barang,
                q.nama_barang,
                q.id_gudang,
                q.nama_gudang,
                q.id_satuan,
                q.nama_satuan,
                q.alias_satuan,
                sum(q.unit_masuk-q.unit_keluar) as stok,
                sum(q.unit_masuk) as jumlah_masuk,
                sum(q.unit_keluar) as jumlah_keluar,
                q.log_stok_id
            FROM 
            (                                              
                SELECT
                    tls.id_barang as id_barang,
                    tls.id_ref_gudang as id_gudang,
                    tls.id_satuan as id_satuan,
                    tb.barang_kode AS kode_barang,
                    tb.barang_nama AS nama_barang,
                    rg.nama AS nama_gudang,                                                     
                    ts.satuan_nama AS nama_satuan,
                    ts.satuan_satuan AS alias_satuan,
                    tls.unit_masuk AS unit_masuk,
                    tls.unit_keluar AS unit_keluar,
                    '0' AS fisik,
                    tls.log_stok_id
                FROM
                    tbl_log_stok AS tls
                    JOIN tbl_barang AS tb ON tls.id_barang = barang_id
                    JOIN ref_gudang AS rg ON tls.id_ref_gudang = rg.id
                    JOIN tbl_satuan AS ts ON tls.id_satuan = ts.satuan_id
                WHERE
                    tls.id_ref_gudang = '$gudang' AND (LOWER(tb.barang_kode) LIKE '%$search%' or LOWER(tb.barang_nama) LIKE '%$search%')
            ) q
            GROUP BY 
                q.id_barang,
                q.id_gudang, 
                q.id_satuan
            ORDER BY
            q.nama_barang");


        // $formatted_tags = [];

        // foreach ($barangs as $barang) {
        //     $formatted_tags[] = ['id' => $barang->barang_id,'satuan'=>$barang->satuan_nama, 'text' => $barang->barang_kode];
        // }

        return \Response::json($barangs);
  }
  
     public function lihatdata(){
       $id_group = Auth::user()->group_id;
       $id_profil = Auth::user()->id_profil;
       $where = "";
       if($id_group == 5 || $id_group == 6){
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
         $where_gudang = "WHERE rf.id IN ($gudang)";
       }       
       $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();

       $pengiriman_retur = DB::select("SELECT
        r.id,
        r.id_barang,
        r.id_gudang_pusat,
        r.id_gudang_outlet,
        r.id_log_stok,
        r.id_log_stok_penerimaan,
        r.id_pengiriman,
        r.id_satuan,
        r.kode_retur,
        r.tanggal_pengiriman as tanggal,
        r.jumlah,
        r.`status`,
        r.created_at,
        r.updated_at,
        b.barang_nama,
        b.barang_kode,
        ga.nama nama_gudangpusat,
        outl.nama nama_gudangoutlet,
        r.keterangan,
        r.status as status
        FROM
        pengiriman_retur AS r
        LEFT JOIN tbl_barang AS b ON r.id_barang = b.barang_id
        LEFT JOIN ref_gudang AS ga ON r.id_gudang_pusat = ga.id
        LEFT JOIN ref_gudang AS outl ON outl.id = r.id_gudang_outlet
        WHERE r.tanggal_pengiriman > DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) 
        AND r.tanggal_pengiriman <= DATE_SUB(DATE(NOW()), INTERVAL 0 DAY) AND r.id_gudang_outlet IN ($gudang)");
      $no = 0;
      $data = array();
      foreach ($pengiriman_retur as $list) {
          //0 = tambahkan detail barang
           //1 = sedang dikirim
           //2 = terkirim
           //3 = diterima sebagian
           //4 = dikembalikan
          $d_kirim = '<a data-id="'.$list->id.'" data-id_satuan="'.$list->id_satuan.'" data-id_barang="'.$list->id_barang.'" data-barang_kode="'.$list->barang_kode.'" data-barang_nama="'.$list->barang_nama.'"  data-jumlah="'.$list->jumlah.'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_pengiriman="'.tgl_full($list->tanggal,'').'" data-kode_retur="'.$list->kode_retur.'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" id="btn_kirim" class="btn btn-xs  btn-success" data-toggle="tooltip" data-placement="botttom" title="kirim Data"  style="color:white;"><i class="fa  fa-send"></i></a>';
          $d_edit = '<a data-id="'.$list->id.'" data-id_satuan="'.$list->id_satuan.'" data-id_barang="'.$list->id_barang.'" data-barang_nama="'.$list->barang_nama.'" data-barang_kode="'.$list->barang_kode.'" data-jumlah="'.$list->jumlah.'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_pengiriman="'.tgl_full($list->tanggal,'').'" data-kode_retur="'.$list->kode_retur.'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>'; 
          $d_detail = '<a data-id="'.$list->id.'" data-id_satuan="'.$list->id_satuan.'" data-id_barang="'.$list->id_barang.'" data-barang_nama="'.$list->barang_nama.'" data-barang_kode="'.$list->barang_kode.'" data-jumlah="'.$list->jumlah.'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_pengiriman="'.tgl_full($list->tanggal,'').'" data-kode_retur="'.$list->kode_retur.'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>';
          $d_hapus = '<a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';

          if($group_where->group_aktif == 2){
            if($list->status == 0){
                $status = '<label class=" label label-default">Draf</label>';
                $action = $d_kirim.' '.$d_edit.' '.$d_hapus;
            }elseif($list->status == 1){
                $status = '<label class=" label label-primary">Dikirim</label>';
                $action = $d_edit.' '.$d_hapus;
            }elseif($list->status == 2){
                $status = '<label class=" label label-success">Diterima</label>';
                $action = '';
            }elseif($list->status == 3){
                $status = '<label class=" label label-success">Diterima Sebagian</label>';
                $action = '';
            }else{
                $status = '';
                $action = '';
            }
          }else{
            if($list->status == 0){
                $status = '<label class=" label label-default">Draf</label>';
                $action = $d_kirim.' '.$d_detail.' '.$d_hapus;
            }elseif($list->status == 1){
                $status = '<label class=" label label-primary">Dikirim</label>';
                $action = $d_detail.' '.$d_hapus;
            }elseif($list->status == 2){
                $status = '<label class=" label label-success">Diterima</label>';
                $action = '';
            }elseif($list->status == 3){
                $status = '<label class=" label label-success">Diterima Sebagian</label>';
                $action = '';
            }else{
                $status = '';
                $action = '';
            }
          }

          $no++;
          $row = array();
          if ($this->agent->isMobile()) {
            // code...
            $row['no'] = $no;
            $row['kode_retur'] = $list->kode_retur;
            $row['barang_nama'] = $list->barang_nama;
            $row['nama_outlet'] = $list->nama_gudangoutlet;
            $row['nama_gudang'] = $list->nama_gudangpusat;
            $row['status'] = $status;
            $row['tanggal'] = date("d-m-Y", strtotime($list->tanggal));
            $row['jumlah'] = $list->jumlah ;
            $row['aksi'] = $action;
          }else {
            // code...
            $row[] = $no;
            $row[] = $list->kode_retur;
            $row[] = $list->barang_nama;
            $row[] = $list->nama_gudangoutlet;
            $row[] = $list->nama_gudangpusat;
            $row[] = $status;
            $row[] = date("d-m-Y", strtotime($list->tanggal));
            $row[] = $list->jumlah;
            $row[] = $action;
          }
          $data[] = $row;

      }
      $output = array("data" => $data);
      return response()->json($output);
    }

    public function searchtanggal(Request $request){
      $id_group = Auth::user()->group_id;
      $id_profil = Auth::user()->id_profil;
       $where = "";
       if($id_group == 5 || $id_group == 6){
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
         $where_gudang = "WHERE rf.id IN ($gudang)";
       }
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();

      $tanggalrange = explode('s.d.',$request->get('tanggal'));
      $tanggal_start  = tgl_full($tanggalrange[0],99);
      $tanggal_end    = tgl_full($tanggalrange[1],99);
      $pengiriman_retur = DB::select("SELECT
        r.id,
        r.id_barang,
        r.id_gudang_pusat,
        r.id_gudang_outlet,
        r.id_log_stok,
        r.id_log_stok_penerimaan,
        r.id_pengiriman,
        r.id_satuan,
        r.kode_retur,
        r.tanggal_pengiriman as tanggal,
        r.jumlah,
        r.`status`,
        r.created_at,
        r.updated_at,
        b.barang_nama,
        b.barang_kode,
        ga.nama nama_gudangpusat,
        outl.nama nama_gudangoutlet,
        r.keterangan,
        r.status as status
        FROM
        pengiriman_retur AS r
        LEFT JOIN tbl_barang AS b ON r.id_barang = b.barang_id
        LEFT JOIN ref_gudang AS ga ON r.id_gudang_pusat = ga.id
        LEFT JOIN ref_gudang AS outl ON outl.id = r.id_gudang_outlet
        WHERE r.tanggal_pengiriman >= '$tanggal_start' 
        AND r.tanggal_pengiriman <= '$tanggal_end' AND r.id_gudang_outlet IN ($gudang)");
      $no = 0;
      $data = array();
      foreach ($pengiriman_retur as $list) {
          //0 = tambahkan detail barang
           //1 = sedang dikirim
           //2 = terkirim
           //3 = diterima sebagian
           //4 = dikembalikan

          $d_kirim = '<a data-id="'.$list->id.'" data-id_satuan="'.$list->id_satuan.'" data-id_barang="'.$list->id_barang.'" data-barang_kode="'.$list->barang_kode.'" data-barang_nama="'.$list->barang_nama.'"  data-jumlah="'.$list->jumlah.'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_pengiriman="'.tgl_full($list->tanggal,'').'" data-kode_retur="'.$list->kode_retur.'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" id="btn_kirim" class="btn btn-xs  btn-success" data-toggle="tooltip" data-placement="botttom" title="kirim Data"  style="color:white;"><i class="fa  fa-send"></i></a>';
          $d_edit = '<a data-id="'.$list->id.'" data-id_satuan="'.$list->id_satuan.'" data-id_barang="'.$list->id_barang.'" data-barang_nama="'.$list->barang_nama.'" data-barang_kode="'.$list->barang_kode.'" data-jumlah="'.$list->jumlah.'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_pengiriman="'.tgl_full($list->tanggal,'').'" data-kode_retur="'.$list->kode_retur.'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>'; 
          $d_detail = '<a data-id="'.$list->id.'" data-id_satuan="'.$list->id_satuan.'" data-id_barang="'.$list->id_barang.'" data-barang_nama="'.$list->barang_nama.'" data-barang_kode="'.$list->barang_kode.'" data-jumlah="'.$list->jumlah.'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_pengiriman="'.tgl_full($list->tanggal,'').'" data-kode_retur="'.$list->kode_retur.'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>';
          $d_hapus = '<a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';

          if($group_where->group_aktif == 2){
            if($list->status == 0){
                $status = '<label class=" label label-default">Draf</label>';
                $action = $d_kirim.' '.$d_edit.' '.$d_hapus;
            }elseif($list->status == 1){
                $status = '<label class=" label label-primary">Dikirim</label>';
                $action = $d_edit.' '.$d_hapus;
            }elseif($list->status == 2){
                $status = '<label class=" label label-success">Diterima</label>';
                $action = '';
            }elseif($list->status == 3){
                $status = '<label class=" label label-success">Diterima Sebagian</label>';
                $action = '';
            }else{
                $status = '';
                $action = '';
            }
          }else{
            if($list->status == 0){
                $status = '<label class=" label label-default">Draf</label>';
                $action = $d_kirim.' '.$d_detail.' '.$d_hapus;
            }elseif($list->status == 1){
                $status = '<label class=" label label-primary">Dikirim</label>';
                $action = $d_detail.' '.$d_hapus;
            }elseif($list->status == 2){
                $status = '<label class=" label label-success">Diterima</label>';
                $action = '';
            }elseif($list->status == 3){
                $status = '<label class=" label label-success">Diterima Sebagian</label>';
                $action = '';
            }else{
                $status = '';
                $action = '';
            }
          }

           
          $no++;
          $row = array();
          if ($this->agent->isMobile()) {
            // code...
            $row['no'] = $no;
            $row['kode_retur'] = $list->kode_retur;
            $row['barang_nama'] = $list->barang_nama;
            $row['nama_outlet'] = $list->nama_gudangoutlet;
            $row['nama_gudang'] = $list->nama_gudangpusat;
            $row['status'] = $status;
            $row['tanggal'] = date("d-m-Y", strtotime($list->tanggal));
            $row['jumlah'] = $list->jumlah ;
            $row['aksi']  = $action;
          }else {
            // code...
            $row[] = $no;
            $row[] = $list->kode_retur;
            $row[] = $list->barang_nama;
            $row[] = $list->nama_gudangoutlet;
            $row[] = $list->nama_gudangpusat;
            $row[] = $status;
            $row[] = date("d-m-Y", strtotime($list->tanggal));
            $row[] = $list->jumlah ;
            $row[] = $action;
          }
          $data[] = $row;

      }

      $output = array("data" => $data);
      return response()->json($output);
    }

    public function destroy($id)
    {
       $d_data = DB::table('pengiriman_retur')->where(array('id' => $id))->first();
       $id_log_stok = $d_data->id_log_stok;
       $id_log_stok_penerimaan = $d_data->id_log_stok_penerimaan;
       DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->delete();
       DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok_penerimaan)->delete();
       DB::table('pengiriman_retur')->where(array('id' => $id))->delete();
        trigger_log($id,'Menghapus data Menu Pengiriman Return',3,null);
    }
    public function select2(Request $request){
    $term = trim($request->q);
    $gudang = $request->gudang;

        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $pengiriman= DB::select("SELECT
                *
              FROM
                pengiriman p
              WHERE
                LOWER( p.kode_pengiriman ) LIKE  '%$search%' ");

        return \Response::json($pengiriman);
    }
    public function select2b(Request $request){
    $term = trim($request->q);
    $id = trim($request->id);
    $gudang = $request->gudang;

        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $pengiriman= DB::select("SELECT
          p.id,
          p.id_inv_pengiriman,
          p.id_barang,
          p.id_log_stok,
          p.id_log_stok_penerimaan,
          p.jumlah,
          p.retur,
          p.diterima,
          p.`status`,
          p.created_at,
          p.updated_at,
          p.id_satuan,
          b.barang_nama
          FROM
          pengiriman_detail AS p
          LEFT JOIN tbl_barang AS b ON p.id_barang = b.barang_id
             ");
        return \Response::json($pengiriman);
    }
    public function kirim(Request $request){
      $id_a = $request['id'];
      // dd($request);
      $log['id_barang'] = $request['id_barang'];
      $log['id_ref_gudang'] = $request['id_gudang_outlet'];
      $log['id_satuan'] = $request['id_satuan'];
      $log['tanggal'] = date("Y/m/d");
      $log['unit_keluar'] = $request['jumlah'];
      $log['status'] = 'K3';
      $id = DB::table('tbl_log_stok')->insertGetId($log);

      $data['status']=1;
      $data['id_log_stok'] = $id ;
      DB::table('pengiriman_retur')->where('id',$id_a)->update($data);
      trigger_log($id_a,'Mengubah data Menu Pengiriman Return dengan mengirim data',2,null);

    }
    public function store(Request $request){
    $crud = $request['crud'];
    $a=0;
    $alert=array("","");
    if ($crud =='tambah') {
      // $data['id_log_stok'] = $id ;
      $data['kode_retur'] = $request['kode'] ;
      $data['id_barang'] = $request['barang'] ;
      $data['id_gudang_outlet'] = $request['id_gudang_outlet'] ;
      $data['id_gudang_pusat'] = $request['id_gudang_pusat'] ;
      $data['id_satuan'] = $request['id_satuan'] ;
      $data['tanggal_pengiriman'] = tgl_full($request['tanggal'],'99');
      $data['jumlah'] = $request['jumlah'] ;
      $data['status'] = 0;
      $data['keterangan'] = $request['keterangan'];
      $id = DB::table('pengiriman_retur')->insertGetId($data);
      trigger_log($id,'Menambah data Menu Return Pengiriman',1,null);
      
      return redirect($_SERVER['HTTP_REFERER']);
      $alert=array("Failed to create new data","New data created successfully");
    }elseif ($crud == 'edit') {
      $id = $request['id_retur'];
      $data['kode_retur'] = $request['kode'] ;
      $data['id_barang'] = $request['barang'] ;
      $data['id_gudang_outlet'] = $request['id_gudang_outlet'] ;
      $data['id_gudang_pusat'] = $request['id_gudang_pusat'] ;
      $data['id_satuan'] = $request['id_satuan'] ;
      $data['tanggal_pengiriman'] = tgl_full($request['tanggal'],'99');
      $data['jumlah'] = $request['jumlah'] ;
      $data['keterangan'] = $request['keterangan'];
      if($request['status'] != '0'){
        $id_log_stok = $request['id_log_stok'];
        $input['id_barang']     = $request['barang'];
        $input['id_satuan']     = $request['id_satuan'];
        $input['id_ref_gudang'] = $request['id_gudang_pusat'];
        $input['tanggal']       = tgl_full($request['tanggal'],'99');
        $input['unit_keluar']   = $request['jumlah'];
        $input['unit_masuk']    = '0';
        $input['status']        = 'K3';
        DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->update($input);
      }
      DB::table('pengiriman_retur')->where('id',$id)->update($data);
      trigger_log($id,'Mengubah data Menu Pengiriman Return',2,null);
      return redirect($_SERVER['HTTP_REFERER']);
      $alert=array("Failed to update data","Data updated successfully");      
    }
    // echo json_encode(array('result'=>$a,'alert'=>$alert[$a]));
    // print_r($request['kode']);exit;
    }
}

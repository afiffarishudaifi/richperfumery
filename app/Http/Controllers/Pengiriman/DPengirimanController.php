<?php

namespace App\Http\Controllers\Pengiriman;

use Illuminate\Http\Request;
use DB;
use App\InvPengiriman;
use App\DetailPengiriman;
use App\BarangModel;
use Redirect;
use App\Http\Controllers\Controller;
use Auth;
use Yajra\DataTables\DataTables;

class DPengirimanController extends Controller
{
  public function index(Request $request) {
    $id_group = Auth::user()->group_id;
    $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
    $id = dec($request['id']);
    $gudang['id'] = $id;
    $g = DB::table('pengiriman')->where('id', $id)->first();      
    // print_r($request['id']);exit;
    $gudang['status']     = $g->status_pengiriman;
    $gudang['gudang']     = $g->gudang_awal;
    $gudang['tujuan']     = $g->gudang_tujuan;
    $gudang['tanggal']    = tgl_full($g->tanggal_pengiriman,'99');
    $gudang['user_group'] = Auth::user()->group_id;
    $gudang['tombol_create'] = $group_where->group_aktif;
    // dd($gudang);
    // $gudang['outlet'] = DB::table('ref_gudang')->where('jenis_gudang', '2')->get();
    return view('admin.pengiriman.detail_baru', compact('gudang'));
  }

  function show(Request $request){
      $limit    = is_null($request["length"]) ? 25 : $request["length"];
      $search   = strtolower(trim($request["search.value"]));
      $dirs     = array("asc", "desc");
      $draw     = $request["draw"];
      $id       = $request->get('id_pengiriman');
      
      $data = DB::table('pengiriman_detail AS pd')
              ->join('pengiriman as p','pd.id_inv_pengiriman','p.id')
              ->leftjoin('tbl_barang as tb','pd.id_barang','tb.barang_id')
              ->leftjoin('tbl_satuan as ts','pd.id_satuan','ts.satuan_id')
              ->whereRaw('pd.id_inv_pengiriman = ?',[$id])
              ->when($search, function($query_sub, $keyword){
                if($keyword != ''){
                  $query_sub->where(function($query) use ($keyword){
                    $query->whereRaw('tb.barang_nama LIKE ?',["%".$keyword."%"]);
                    $query->orWhereRaw('ts.satuan_nama LIKE ?',['%'.$keyword."%"]);
                    if(is_numeric($keyword)){
                      $query->orWhereRaw('pd.jumlah LIKE ?',["%".$keyword."%"]);
                      $query->orWhereRaw('pd.harga LIKE ?',["%".$keyword."%"]);
                    }
                    if(str_contains('belum dikirim',$keyword)){
                      $query->orWhereRaw('pd.status LIKE ?',["%0%"]);
                    }
                    if(str_contains('sedang dikirim',$keyword)){
                      $query->orWhereRaw('pd.status LIKE ?',["%1%"]);
                    }
                    if(str_contains('diterima',$keyword)){
                      $query->orWhereRaw('pd.status LIKE ?',["%2%"]);
                    }
                    if(str_contains('dikembalikan',$keyword)){
                      $query->orWhereRaw('pd.status LIKE ?',["%3%"]);
                    }
                  });
                }
              })
              ->selectRaw("pd.id as id_detail_pengiriman, pd.id_inv_pengiriman as id_pengiriman, pd.id_barang, 
              tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, pd.id_satuan, ts.satuan_satuan as nama_satuan, pd.status as status_detail_pengiriman,
              pd.jumlah as jumlah_detail_pengiriman, pd.harga as harga_detail_pengiriman, pd.diterima, pd.keterangan, pd.id_log_stok, pd.id_log_stok_penerimaan,
              p.tanggal_pengiriman as tanggal, p.status_pengiriman as status_pengiriman");
      // dd($data->toSql());        
      return DataTables::of($data)
              ->addColumn('action', function($data){
                $kirim  = '<a data-id_detail_pengiriman="'.$data->id_detail_pengiriman.'" data-id_barang="'.$data->id_barang.'" 
                          data-nama="'.$data->nama_barang.'" data-jumlah="'.$data->jumlah_detail_pengiriman.'" data-kode="'.$data->kode_barang.'"
                          data-satuan="'.$data->nama_satuan.'" data-id_satuan="'.$data->id_satuan.'" data-harga="'.$data->harga_detail_pengiriman.'" 
                          data-keterangan="'.$data->keterangan.'" data-id_log_stok="'.$data->id_log_stok.'"
                          data-id_log_stok_penerimaan="'.$data->id_log_stok_penerimaan.'" 
                          data-tanggal="'.tgl_full($data->tanggal,'99').'" data-status="'.$data->status_detail_pengiriman.'"
                          id="btn_a" class="btn btn-xs  btn-success" data-toggle="tooltip" 
                          data-placement="botttom" title="Kirim Barang"  style="color:white;">
                          <i class="fa fa-check-square"></i></a>';
                $edit   = '<a data-id_barang="'.$data->id_barang.'" data-nama="'.$data->nama_barang.'" data-jumlah="'.$data->jumlah_detail_pengiriman.'"
                          data-kode="'.$data->kode_barang.'" data-id_satuan="'.$data->id_satuan.'" data-satuan="'.$data->nama_satuan.'" data-id_pengiriman="'.$data->id_pengiriman.'"
                          data-id_detail_pengiriman="'.$data->id_detail_pengiriman.'" data-harga="'.$data->harga_detail_pengiriman.'" 
                          data-keterangan="'.$data->keterangan.'" data-id_log_stok="'.$data->id_log_stok.'"
                          data-id_log_stok_penerimaan="'.$data->id_log_stok_penerimaan.'"  
                          data-tanggal="'.tgl_full($data->tanggal,'99').'" data-status="'.$data->status_detail_pengiriman.'"  id="btn_edit" 
                          class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data" 
                          style="color:white;"><i class="fa  fa-edit"></i></a>';
                $detail = '<a data-id_detail_pengiriman="'.$data->id_detail_pengiriman.'" data-id_barang="'.$data->id_barang.'" 
                          data-nama="'.$data->nama_barang.'" data-jumlah="'.$data->jumlah_detail_pengiriman.'" data-kode="'.$data->kode_barang.'"
                          data-satuan="'.$data->nama_satuan.'" data-id_detail_pengiriman="'.$data->id_detail_pengiriman.'" 
                          data-harga="'.$data->harga_detail_pengiriman.'" data-keterangan="'.$data->keterangan.'" 
                          data-id_log_stok="'.$data->id_log_stok.'" 
                          data-id_log_stok_penerimaan="'.$data->id_log_stok_penerimaan.'" 
                          data-tanggal="'.tgl_full($data->tanggal,'99').'" data-status="'.$data->status_detail_pengiriman.'" id="btn_detail" 
                          class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" 
                          title="Edit Data"  style="color:white;"><i class="fa  fa-eye"></i></a>';
                $hapus  = '<a data-id="'.$data->id_detail_pengiriman.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" 
                          data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;">
                          <i class="fa  fa-trash"></i></a>';
                if($data->status_pengiriman != 0){
                  if($data->status_detail_pengiriman == 0){
                    $action = $kirim.''.$edit.''.$detail.''.$hapus;
                  }else{
                    $action = $edit.''.$detail.''.$hapus;
                  }
                }else{
                  $action = $edit.''.$detail.''.$hapus;
                }
                return $action;
              })
              ->addColumn('status', function($data){
                if($data->status_detail_pengiriman == 1){
                  $status = "<span class='label label-warning'>Sedang Dikirim</span";
                }else if($data->status_detail_pengiriman == 2){
                  $status = "<span class='label label-success'>Diterima</span";
                }else if($data->status_detail_pengiriman == 3){
                  $status = "<span class='label label-danger'>Dikembalikan</span";
                }else{
                  $status = "<span class='label label-default'>Belum Dikirim</span";
                }

                return $status;
              })
              ->addColumn('jumlah',function($data){
                $return = format_angka($data->jumlah_detail_pengiriman);
                return $return;
              })
              ->addColumn('harga', function($data){
                $return = ($data->harga_detail_pengiriman==null)?"Rp. 0":"Rp. ".format_angka($data->harga_detail_pengiriman);
                return $return;
              })
              ->rawColumns(['action','status','jumlah','harga'])
              ->make(true);
  }

  function select2barang(Request $request){
      $search = strtolower(trim($request->get('q')));
      $gudang = $request->get('gudang');

      if(empty($search)){
          return response()->json([]);
      }

      $data = DB::table('tbl_log_stok AS tls')
              ->leftjoin('tbl_barang AS tb','tls.id_barang','tb.barang_id')
              ->leftjoin('tbl_satuan AS ts','tls.id_satuan','ts.satuan_id')
              ->when($search, function($query_sub,$keyword){
                if($keyword != ''){
                  $query_sub->where(function($query) use ($keyword){
                    $query->whereRaw("CONCAT(LOWER(tb.barang_kode),' || ',LOWER(tb.barang_nama)) LIKE ?",["%".$keyword."%"]);
                  });
                }
              })
              ->when($gudang, function($query,$keyword){
                $query->whereRaw("tls.id_ref_gudang = ?",[$keyword]);
              })
              ->groupBy('tls.id_barang','tls.id_satuan','tls.id_ref_gudang')
              ->selectRaw("tb.barang_id, tb.barang_nama, tb.barang_kode, tls.id_satuan, ts.satuan_nama, 
              ts.satuan_satuan, tls.id_ref_gudang as id_gudang, SUM(tls.unit_masuk) as jumlah_masuk,
              SUM(tls.unit_keluar) as jumlah_keluar, tls.log_stok_id")
              ->orderBy('tb.barang_nama','ASC')
              ->get();
      return response()->json($data);
  }

  function simpan_detail(Request $request){
    DB::beginTransaction();
    $input  = $request->all();
    $id_detail  = $input['id_detail'];
    $aksi       = $input['crud'];
    try{
      $data['id_inv_pengiriman']      = $input['id'];
      $data['id_barang']              = explode('.',$input['barang'])[0];
      $data['id_satuan']              = $input['id_satuan'];
      $data['nama']                   = $input['nama'];
      $data['jumlah']                 = $input['jumlah'];
      $data['harga']                  = $input['harga'];
      $data['total']                  = (float)$input['jumlah']*(float)$input['harga'];
      $data['id_log_stok']            = $input['id_log_stok'];
      $data['keterangan']             = $input['keterangan'];

      if($aksi == 'tambah'){
        DB::table('pengiriman_detail')->insert($data);
        $status = '1';
      }else{
        $pengiriman = DB::table('pengiriman')->whereRaw("id = ?",[$input['id']])->first();
        if($input['id_log_stok'] != '' || $input['id_log_stok'] != null || $input['id_log_stok'] == 0){
          $log['id_barang']     = explode('.',$input['barang'])[0];
          $log['id_ref_gudang'] = $pengiriman->gudang_awal;
          $log['tanggal']       = $pengiriman->tanggal_pengiriman;
          $log['id_satuan']     = $input['id_satuan'];
          $log['status']        = "K1";
          $log['unit_keluar']   = $input['jumlah'];
          DB::table('tbl_log_stok')->whereRaw('log_stok_id = ?',[$input['id_log_stok']])->update($log);
        }
        DB::table('pengiriman_detail')->whereRaw('id = ?',[$id_detail])->update($data);
        $status = '2';
      }
      DB::commit();
    }catch(\Exception $e){
      DB::rollback();
      $status = '0';
    }

    return response()->json(array('status'=>$status));
  }

  function kirimbarang_detail(Request $request){
    $id_pengiriman = $request->get('id');
    DB::beginTransaction();
    try{
    //   DB::table('pengiriman')->update(array('status_pengiriman'=>1));
      DB::table('pengiriman')->where('id', $id_pengiriman)->update(array('status_pengiriman'=>1));
      trigger_log($id_pengiriman,'Mengubah data Menu Detail Pengiriman proses pengiriman barang',2,null);
      $query = DB::table("pengiriman_detail as pd")
              ->join("pengiriman as p","pd.id_inv_pengiriman","p.id")
              ->whereRaw('p.id = ?',[$id_pengiriman])
              ->selectRaw('pd.*,p.tanggal_pengiriman,p.gudang_awal as id_gudang, 
              p.gudang_tujuan as id_gudang_penerimaan')
              ->get();

      foreach($query as $d){
        if($d->id_log_stok == '' || $d->id_log_stok == null || $d->id_log_stok == 0){
          $log['id_barang']     = $d->id_barang;
          $log['id_satuan']     = $d->id_satuan;
          $log['id_ref_gudang'] = $d->id_gudang;
          $log['unit_keluar']   = $d->jumlah;
          $log['tanggal']       = $d->tanggal_pengiriman;
          $log['status']        = 'K1';
          $id_log_stok = DB::table('tbl_log_stok')->insertGetId($log);

          $detail['status']       = 1;
          $detail['id_log_stok']  = $id_log_stok;
          $detail['diterima']     = $d->jumlah;
          DB::table('pengiriman_detail')->whereRaw('id = ?',[$d->id])->update($detail);
        }else{
          $log['id_barang']     = $d->id_barang;
          $log['id_satuan']     = $d->id_satuan;
          $log['id_ref_gudang'] = $d->id_gudang;
          $log['unit_keluar']   = $d->jumlah;
          $log['tanggal']       = $d->tanggal_pengiriman;
          $log['status']        = 'K1';
          DB::table('tbl_log_stok')->whereRaw('log_stok_id = ?',[$d->id_log_stok])->update($log);

          if($d->id_log_stok_penerimaan != '' || $d->id_log_stok_penerimaan != null || $d->id_log_stok_penerimaan != 0){
            $log_penerimaan['id_barang']      = $d->id_barang;
            $log_penerimaan['id_satuan']      = $d->id_satuan;
            $log_penerimaan['id_ref_gudang']  = $d->id_gudang_penerimaan;
            $log_penerimaan['unit_masuk']     = $d->diterima;
            $log_penerimaan['tanggal']        = $d->tanggal_pengiriman;
            $log_penerimaan['status']         = "K2";
            DB::table('tbl_log_stok')->whereRaw('log_stok_id = ?',[$d->id_log_stok_penerimaan])->update($log_penerimaan);
          }

          // $detail['status']                 = $d->status;
          $detail['status']                 = 1;
          $detail['id_log_stok']            = $d->id_log_stok;
          $detail['id_log_stok_penerimaan'] = $d->id_log_stok_penerimaan;
          $detail['diterima']               = $d->diterima;
          DB::table('pengiriman_detail')->whereRaw('id = ?',[$d->id])->update($detail);
        }

      }
      
      $status = '1';
      DB::commit();
    }catch(Exception $e){
      $status = '0';
      DB::rollback();
    }

    return response()->json(array('status'=>$status));
  }

  function hapus_detail(Request $request){
    $id = $request->get('id');
    DB::beginTransaction();
    try{
      $detail = DetailPengiriman::find($id);
      if($detail->id_log_stok != '' || $detail->id_log_stok != null || $detail->id_log_stok != 0){
        DB::table('tbl_log_stok')->whereRaw('log_stok_id = ?',[$detail->id_log_stok])->delete();
      }
      if($detail->id_log_stok_penerimaan != '' || $detail->id_log_stok_penerimaan != null || $detail->id_log_stok_penerimaan != 0){
        DB::table('tbl_log_stok')->whereRaw('log_stok_id = ?',[$detail->id_log_stok_penerimaan])->delete();
      }
      $detail->delete();
      $status = 1;
      DB::commit();
    }catch(Exception $e){
      DB::rollback();
      $status = 0;
    }
    return response()->json(array('status'=>$status));
  }

  function kirimbarang(Request $request){
      DB::beginTransaction();
      $id = $request['id'];
      $da = DB::table("pengiriman")->where('id', $id)->first();
      $data_a['status_pengiriman'] = 1 ;
      try{
      DB::table('pengiriman')->where('id',$id)->update($data_a);
      trigger_log($id,'Mengubah data Menu Detail Pengiriman proses pengiriman barang',2,null);
      $query = DB::select("select pd.*,p.tanggal_pengiriman from pengiriman_detail as pd JOIN pengiriman as p ON pd.id_inv_pengiriman=p.id where pd.id_inv_pengiriman='$id'");
      foreach ($query as $key => $value) {
        //tbl_log_stok
        $id_log_stok = $value->id_log_stok;
        $id_log_stok_penerimaan = $value->id_log_stok_penerimaan;
        if($id_log_stok == '' || $id_log_stok == null || $id_log_stok == 0){
          $tes['id_barang'] = $value->id_barang;
          $tes['id_ref_gudang'] = $da->gudang_awal;
          $tes['tanggal'] = $da->tanggal_pengiriman;
          $tes['id_satuan'] = $value->id_satuan;
          $tes['status'] = "K1";
          $tes['unit_keluar'] = $value->jumlah;
          $id = DB::table('tbl_log_stok')->insertGetId($tes);
          //simpan detail
          $id_a = $value->id ; 
          $data['status'] = 1 ; 
          $data['id_log_stok'] = $id ; 
          $data['diterima'] = $value->jumlah ; 
          DB::table('pengiriman_detail')->where('id',$id_a)->update($data);
        }else{
          $log['id_barang']     = $value->id_barang;
          $log['id_ref_gudang'] = $da->gudang_awal;
          $log['tanggal']       = $da->tanggal_pengiriman;
          $log['id_satuan']     = $value->id_satuan;
          $log['status']        = "K1";
          $log['unit_keluar']   = $value->jumlah;
          DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->update($log);
          
          if($id_log_stok_penerimaan !='' || $id_log_stok_penerimaan != null || $id_log_stok_penerimaan != 0){
            $log_penerimaan['id_barang']     = $value->id_barang;
            $log_penerimaan['id_ref_gudang'] = $da->gudang_akhir;
            $log_penerimaan['tanggal']       = $da->tanggal_pengiriman;
            $log_penerimaan['id_satuan']     = $value->id_satuan;
            $log_penerimaan['status']        = "K2";
            $log_penerimaan['unit_keluar']   = $value->jumlah;
            DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok_penerimaan)->update($log);
          }

          $id_a = $value->id; 
          $data['status'] = 1; 
          $data['id_log_stok'] = $id_log_stok;
          $data['id_log_stok_penerimaan'] = $id_log_stok_penerimaan; 
          $data['diterima'] = $value->jumlah; 
          DB::table('pengiriman_detail')->where('id',$id_a)->update($data);
        } 
      }
      DB::commit();
      $status = array('status'=>1);
      }catch(Exception $e){
        DB::rollback();
        $status = array('status'=>0);
      }

      return response()->json($status);
  }

  public function kirimbarang_peritem(Request $request){
    $id = $request['id'];
    DB::beginTransaction();
    try{
    $d_data = DB::table('pengiriman_detail as pd')
              ->leftjoin('pengiriman as p','pd.id_inv_pengiriman','p.id')
              ->where('pd.id',$id);
    if($d_data->count()>0){
        $d = $d_data->first();
        $input['id_barang']     = $d->id_barang;
        $input['id_satuan']     = $d->id_satuan;
        $input['id_ref_gudang'] = $d->gudang_awal;
        $input['tanggal']       = tgl_full($d->tanggal_pengiriman,'99');
        $input['unit_masuk']    = '0';
        $input['unit_keluar']   = $d->jumlah;
        $input['status']        = 'K1';
        $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

        if($d->status_pengiriman == 2){
        $input_penerimaan['id_barang']     = $d->id_barang;
        $input_penerimaan['id_satuan']     = $d->id_satuan;
        $input_penerimaan['id_ref_gudang'] = $d->gudang_tujuan;
        $input_penerimaan['tanggal']       = tgl_full($d->tanggal_pengiriman,'99');
        $input_penerimaan['unit_masuk']    = $d->jumlah;
        $input_penerimaan['unit_keluar']   = '0';
        $input_penerimaan['status']        = 'K2';
        $id_log_stok_penerimaan = DB::table('tbl_log_stok')->insertGetId($input);

        $data['id_log_stok_penerimaan'] = $id_log_stok_penerimaan;
        }

        if($d->status_pengiriman == 0){
        $data['status']       = 1;
        }else{
        $data['status']       = $d->status_pengiriman;
        }
        $data['id_log_stok']  = $id_log_stok;
        $data['diterima']     = $d->jumlah;
        DB::table('pengiriman_detail')->where('id',$id)->update($data);      
    }
    $status = array('status'=>1);
    DB::commit();
    }catch(\Exception $e){
      $status = array('status'=>0);
      DB::rollback();
    }

    return response()->json($status);

  }
    
  public function index_lama(Request $request) {
      // dd();
      $id_group = Auth::user()->group_id;
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $id = dec($request['id']);
      $gudang['id'] = $id;
      $g = DB::table('pengiriman')->where('id', $id)->first();      
      // print_r($request['id']);exit;
      $gudang['status']     = $g->status_pengiriman;
      $gudang['gudang']     = $g->gudang_awal;
      $gudang['tujuan']     = $g->gudang_tujuan;
      $gudang['tanggal']    = tgl_full($g->tanggal_pengiriman,'99');
      $gudang['user_group'] = Auth::user()->group_id;
      $gudang['tombol_create'] = $group_where->group_aktif;
      // dd($gudang);
      // $gudang['outlet'] = DB::table('ref_gudang')->where('jenis_gudang', '2')->get();
    return view('admin.pengiriman.detail', compact('gudang'));
  }

  function select2barang_lama(Request $request){
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
        $barangs= DB::select("SELECT
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
          LEFT JOIN tbl_satuan AS s ON l.id_satuan = s.satuan_id WHERE LOWER(b.barang_kode) LIKE '%$search%' or LOWER(b.barang_nama) LIKE '%$search%' ");

        // $formatted_tags = [];

        // foreach ($barangs as $barang) {
        //     $formatted_tags[] = ['id' => $barang->barang_id,'satuan'=>$barang->satuan_nama, 'text' => $barang->barang_kode];
        // }

        return \Response::json($barangs);
  }

  public function kirimbarang_lama(Request $request){
      DB::beginTransaction();
      $id = $request['id'];
      $da = DB::table("pengiriman")->where('id', $id)->first();
      $data_a['status_pengiriman'] = 1 ;
      try{
      DB::table('pengiriman')->where('id',$id)->update($data_a);
      // print_r($id);exit;
      $query = DB::select("select pd.*,p.tanggal_pengiriman from pengiriman_detail as pd JOIN pengiriman as p ON pd.id_inv_pengiriman=p.id where pd.id_inv_pengiriman='$id'");
      foreach ($query as $key => $value) {
        //tbl_log_stok
        $id_log_stok = $value->id_log_stok;
        $id_log_stok_penerimaan = $value->id_log_stok_penerimaan;
        if($id_log_stok == '' || $id_log_stok == null || $id_log_stok == 0){
          $tes['id_barang'] = $value->id_barang;
          $tes['id_ref_gudang'] = $da->gudang_awal;
          // $tes['tanggal'] = date("Y/m/d");
          $tes['tanggal'] = $da->tanggal_pengiriman;
          $tes['id_satuan'] = $value->id_satuan;
          $tes['status'] = "K1";
          $tes['unit_keluar'] = $value->jumlah;
          $id = DB::table('tbl_log_stok')->insertGetId($tes);
          //simpan detail
          $id_a = $value->id ; 
          $data['status'] = 1 ; 
          $data['id_log_stok'] = $id ; 
          $data['diterima'] = $value->jumlah ; 
          DB::table('pengiriman_detail')->where('id',$id_a)->update($data);
        }else{
          $log['id_barang']     = $value->id_barang;
          $log['id_ref_gudang'] = $da->gudang_awal;
          $log['tanggal']       = $da->tanggal_pengiriman;
          $log['id_satuan']     = $value->id_satuan;
          $log['status']        = "K1";
          $log['unit_keluar']   = $value->jumlah;
          DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok)->update($log);
          // $tes2['status']
          if($id_log_stok_penerimaan !='' || $id_log_stok_penerimaan != null || $id_log_stok_penerimaan != 0){
            $log_penerimaan['id_barang']     = $value->id_barang;
            $log_penerimaan['id_ref_gudang'] = $da->gudang_akhir;
            $log_penerimaan['tanggal']       = $da->tanggal_pengiriman;
            $log_penerimaan['id_satuan']     = $value->id_satuan;
            $log_penerimaan['status']        = "K2";
            $log_penerimaan['unit_keluar']   = $value->jumlah;
            DB::table('tbl_log_stok')->where('log_stok_id',$id_log_stok_penerimaan)->update($log);
          }

          $id_a = $value->id; 
          $data['status'] = 1; 
          $data['id_log_stok'] = $id_log_stok;
          $data['id_log_stok_penerimaan'] = $id_log_stok_penerimaan; 
          $data['diterima'] = $value->jumlah; 
          DB::table('pengiriman_detail')->where('id',$id_a)->update($data);
        } 
      }
      DB::commit();
      $status = array('status'=>1);
      }catch(Exception $e){
        DB::rollback();
        $status = array('status'=>0);
      }

      return $status;
  }

  public function kirimbarang_peritem_lama(Request $request){
    $id = $request['id'];
    DB::beginTransaction();
    try{
    $d_data = DB::table('pengiriman_detail as pd')->leftjoin('pengiriman as p','pd.id_inv_pengiriman','p.id')
              ->where('pd.id',$id);
    if($d_data->count()>0){
        $d = $d_data->first();
        $input['id_barang']     = $d->id_barang;
        $input['id_satuan']     = $d->id_satuan;
        $input['id_ref_gudang'] = $d->gudang_awal;
        $input['tanggal']       = tgl_full($d->tanggal_pengiriman,'99');
        $input['unit_masuk']    = '0';
        $input['unit_keluar']   = $d->jumlah;
        $input['status']        = 'K1';
        $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

        if($d->status_pengiriman == 2){
        $input_penerimaan['id_barang']     = $d->id_barang;
        $input_penerimaan['id_satuan']     = $d->id_satuan;
        $input_penerimaan['id_ref_gudang'] = $d->gudang_tujuan;
        $input_penerimaan['tanggal']       = tgl_full($d->tanggal_pengiriman,'99');
        $input_penerimaan['unit_masuk']    = $d->jumlah;
        $input_penerimaan['unit_keluar']   = '0';
        $input_penerimaan['status']        = 'K2';
        $id_log_stok_penerimaan = DB::table('tbl_log_stok')->insertGetId($input);

        $data['id_log_stok_penerimaan'] = $id_log_stok_penerimaan;
        }

        if($d->status_pengiriman == 0){
        $data['status']       = 1;
        }else{
        $data['status']       = $d->status_pengiriman;
        }
        $data['id_log_stok']  = $id_log_stok;
        $data['diterima']     = $d->jumlah;
        DB::table('pengiriman_detail')->where('id',$id)->update($data);      
    }
    $status = array('status'=>1);
    DB::commit();
    }catch(\Exception $e){
      $status = array('status'=>0);
      DB::rollback();
    }

    return $status;

  }
    
  function store(Request $request){
    $a=0;
    $alert=array("","");
    // dd($request);
    // print_r($request['id']);exit;
     $crud = $request['crud'];
    //0 = belum dikirim
    //1 = sedang dikirim
    //2 = terkirim/diterima
    //3 = dikembalikan/return
    DB::beginTransaction();
    try{
    if ($crud =='tambah') {
      //simpan detail pengiriman
      $program = new DetailPengiriman;
      $program->id_inv_pengiriman = $request['id'];
      $program->id_barang = $request['barang'];
      $program->nama = $request['nama'];
      $program->id_satuan = $request['id_satuan'];
      $program->status = 0;
      $program->jumlah = $request['jumlah'];
      $program->harga = $request['harga'];
      $program->total = $request['jumlah']*$request['harga'];
      $program->keterangan = $request['keterangan'];
      $a = $program-> save();
      // print_r($program);exit;
      $status = array('status'=>1);
      $alert=array("Failed to create new data","New data created successfully");
    }elseif ($crud == 'edit') {
      $id = $request['id'];
      $program = DetailPengiriman::find($id);

      $program->id_barang = $request['barang'];
      $program->nama = $request['nama'];
      // $program->status = 0;
      $program->jumlah = $request['jumlah'];
      $program->harga = $request['harga'];
      $program->total = $request['jumlah']*$request['harga'];
      $program->keterangan = $request['keterangan'];
      $a = $program -> update();
      $detail_peng = DB::table('pengiriman_detail as pd')->join('pengiriman as p','pd.id_inv_pengiriman','p.id')
                    ->where('pd.id', $id)->first();
      // $log_stok = DB::table('tbl_log_stok')->where('log_stok_id',$detail_peng->id_log_stok)->get()->first();
      if($detail_peng->id_log_stok != '' || $detail_peng->id_log_stok != null || $detail_peng->id_log_stok != 0){
        $data['id_barang']    = $request['barang'];
        $data['id_satuan']    = $request['id_satuan'];
        $data['unit_masuk']   = '0';
        $data['unit_keluar']  = $request['jumlah'];
        $data['tanggal']      = $request['tanggal'];
        $data['id_ref_gudang']= $detail_peng->gudang_awal;
      DB::table('tbl_log_stok')->where('log_stok_id',$detail_peng->id_log_stok)->update($data);
      }

      if($detail_peng->id_log_stok_penerimaan != '' || $detail_peng->id_log_stok_penerimaan != null || $detail_peng->id_log_stok_penerimaan != 0){
        $data_penerimaan['id_barang']    = $request['barang'];
        $data_penerimaan['id_satuan']    = $request['id_satuan'];
        $data_penerimaan['unit_masuk']   = $request['jumlah'];
        $data_penerimaan['unit_keluar']  = '0';
        $data_penerimaan['tanggal']      = $request['tanggal'];
        $data_penerimaan['id_ref_gudang']= $detail_peng->gudang_tujuan;
      DB::table('tbl_log_stok')->where('log_stok_id',$detail_peng->id_log_stok_penerimaan)->update($data_penerimaan);
      }
      
      $alert=array("Failed to update data","Data updated successfully");
      // return redirect($_SERVER['HTTP_REFERER']);
      $status = array('status'=>2);
    }
      DB::commit();
    }catch(Exception $e){
      DB::rollback();
      $status = array('status'=>0);
    }
    return $status;
    
  }
  
  public function destroy($id)
  {

    $menu = DetailPengiriman::find($id);
    if($menu->id_log_stok != '' || $menu->id_log_stok != null || $menu->id_log_stok != 0){
      DB::table('tbl_log_stok')->where('log_stok_id',$menu->id_log_stok)->delete();
    }

    if($menu->id_log_stok_penerimaan != '' || $menu->id_log_stok_penerimaan != null || $menu->id_log_stok_penerimaan != 0){
       DB::table('tbl_log_stok')->where('log_stok_id',$menu->id_log_stok_penerimaan)->delete();
    }
    $menu -> delete();
    // print_r($menu->id_log_stok);exit();


  }
  
  public function hapus(Request $request)
  {
    $id = $request['id'];
    DB::beginTransaction();
    try{
    $menu = DetailPengiriman::find($id);
    if($menu->id_log_stok != '' || $menu->id_log_stok != null || $menu->id_log_stok != 0){
      DB::table('tbl_log_stok')->where('log_stok_id',$menu->id_log_stok)->delete();
    }

    if($menu->id_log_stok_penerimaan != '' || $menu->id_log_stok_penerimaan != null || $menu->id_log_stok_penerimaan != 0){
       DB::table('tbl_log_stok')->where('log_stok_id',$menu->id_log_stok_penerimaan)->delete();
    }
    $menu -> delete();
    $status = array('status'=>1);
    DB::commit();
    }catch(\Exception $e){
      $status = array('status'=>0);
      DB::rollback();
    }
    return $status;
    // print_r($menu->id_log_stok);exit();


  }
  
  
  public function edit($id)
  {
    $menu = DetailPengiriman::find($id);
    echo json_encode($menu);
  }
  public function update(Request $request, $id)
  {
    $program = DetailPengiriman::find($id);
    $program->id_inv_pengiriman = $request['id'];
    $program->id_barang = $request['barang'];
    $program->nama = $request['nama'];
    // $program->status = 0;
    $program->jumlah = $request['jumlah'];
    $a = $program -> update();
  }

  public function simpandata(Request $request){
      $id_a = $request['id_detail_pengiriman'];
      // print_r($id);exit;
      $tes['id_barang'] = $request['id_barang'];
      $tes['id_ref_gudang'] = $request['gudang'];
      $tes['tanggal'] = date("Y/m/d");
      $tes['unit_masuk'] = $request['diterima'];
      $tes['status'] = "K2";
      $id = DB::table('tbl_log_stok')->insertGetId($tes);

      $data['id_log_stok_penerimaan'] = $id;
      $data['diterima'] = $request['diterima'];
      $data['status'] = '2' ;
      $data['keterangan'] = $request['keterangan'];
      // $data['retur'] = $request['dikembalikan'];
      DB::table('pengiriman_detail')->where('id',$id_a)->update($data);

      
      return redirect($_SERVER['HTTP_REFERER']);
  }

  public function terimasemua(Request $request){
      $id = $request['id'];
      // $id = 2;
      $da = DB::table("pengiriman")->where('id', $id)->get()->first();
      // print_r($da->gudang_tujuan);exit;
      // $gudang = $request['gudang'];
      $data_a['status_pengiriman'] = 2 ;
      DB::table('pengiriman')->where('id',$id)->update($data_a);
      trigger_log($id,'Mengubah data Pengiriman proses penerimaan barang',2,null);
      // print_r($id);exit;
      $query = DB::select("select * from pengiriman_detail as pd join pengiriman as p on pd.id_inv_pengiriman=p.id where id_inv_pengiriman='$id'");
      foreach ($query as $key => $value) {
        //tbl_log_stok
        $tes['id_barang'] = $value->id_barang;
        $tes['id_ref_gudang'] = $da->gudang_tujuan;
        $tes['id_satuan'] = $value->id_satuan;
        // $tes['tanggal'] = date("Y/m/d");
        $tes['tanggal'] = tgl_full($value->tanggal_pengiriman,'99');
        $tes['status'] = "K2";
        $tes['unit_masuk'] = $value->jumlah;
        $id = DB::table('tbl_log_stok')->insertGetId($tes);
        //simpan detail
        $id_a = $value->id ; 
        $data['status'] = 2 ; 
        $data['id_log_stok_penerimaan'] = $id; 
        $data['diterima'] = $value->jumlah; 
        DB::table('pengiriman_detail')->where('id',$id_a)->update($data);
        
      }
      // return redirect('detail_pengiriman?id='.$id.'&status=2&gudang='.$gudang);
      return redirect($_SERVER['HTTP_REFERER']);
  }
    
  public function show_lama($id){ 
      // $id_inv = $request['id'];
      $id_group = Auth::user()->group_id;
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $barang = DB::select("SELECT
          d.id,
          d.id_inv_pengiriman,
          d.id_barang,
          b.barang_nama nama,
          d.jumlah,
          d.`status`,
          d.retur,
          d.diterima,
          b.barang_kode,
          b.barang_nama,
          d.id_satuan,
          s.satuan_nama,
          s.satuan_satuan,
          d.harga,
          d.keterangan,
          d.id_log_stok,
          d.id_log_stok_penerimaan,
          p.tanggal_pengiriman as tanggal,
          p.status_pengiriman as status_pengiriman
        FROM
          pengiriman_detail AS d
          JOIN pengiriman AS p ON d.id_inv_pengiriman = p.id
          LEFT JOIN tbl_barang AS b ON d.id_barang = b.barang_id
          LEFT JOIN tbl_satuan AS s ON d.id_satuan = s.satuan_id 
        WHERE
          d.id_inv_pengiriman = '$id'");
        $no = 0;
        $data = array();
        foreach ($barang as $list) {  
            /*if ($list->status == 0) {
              $status = "<span class='label label-default'>Belum Dikirim</span";
              }elseif ($list->status == 1) {
                $status = "<span class='label label-warning'>Sedang Dikirim</span";
              }elseif ($list->status == 2) {
                  $status = "<span class='label label-success'>Diterima</span";
              }elseif ($list->status == 3) {
                  $status = "<span class='label label-danger'>Dikembalikan</span";
                  # code...
              }*/
              /*if ($list->status == 0 && $group_where->group_aktif==2) {
                $edit = '<a data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" data-id_detail_pengiriman="'.$list->id.'" data-harga="'.$list->harga.'" data-keterangan="'.$list->keterangan.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'"  id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>';
                $hapus = '<a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
                $detail = '';
                $ass = '';
              }else if ($list->status == 1 && $group_where->group_aktif==2) {
                if($id_group == 1){
                  $edit = '<a data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" data-id_detail_pengiriman="'.$list->id.'" data-harga="'.$list->harga.'" data-keterangan="'.$list->keterangan.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'"  id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>';
                  $hapus = '<a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
                  $detail = '';
                  $ass = '';
                }else{
                $ass = ' <a data-id_detail_pengiriman="'.$list->id.'" data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" data-harga="'.$list->harga.'" data-keterangan="'.$list->keterangan.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'" id="btn_a" class="btn btn-xs  btn-success" data-toggle="tooltip" data-placement="botttom" title="Terima Barang"  style="color:white;"><i class="fa  fa-check-square"></i></a>';
                $edit = '';
                $detail = '<a data-id_detail_pengiriman="'.$list->id.'" data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" data-id_detail_pengiriman="'.$list->id.'" data-harga="'.$list->harga.'" data-keterangan="'.$list->keterangan.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'"  id="btn_detail" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-eye"></i></a>';
                $hapus = '';
                }
              }else{
                if($id_group == 1){
                  $ass = ' <a data-id_detail_pengiriman="'.$list->id.'" data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" data-harga="'.$list->harga.'" data-keterangan="'.$list->keterangan.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'" id="btn_a" class="btn btn-xs  btn-success" data-toggle="tooltip" data-placement="botttom" title="Terima Barang"  style="color:white;"><i class="fa  fa-check-square"></i></a>';
                  $edit = '<a data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" data-id_detail_pengiriman="'.$list->id.'" data-harga="'.$list->harga.'" data-keterangan="'.$list->keterangan.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'"  id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>';
                  $detail = '<a data-id_detail_pengiriman="'.$list->id.'" data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" data-id_detail_pengiriman="'.$list->id.'" data-harga="'.$list->harga.'" data-keterangan="'.$list->keterangan.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'"  id="btn_detail" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-eye"></i></a>';
                  $hapus = '<a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';

                }else{
                  $ass = '';
                  $edit = '';
                  $hapus = '';
                  $detail = '';
                }
              }*/

            $kirim  = '<a data-id_detail_pengiriman="'.$list->id.'" data-id_barang="'.$list->id_barang.'" 
                      data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'"
                      data-satuan="'.$list->satuan_nama.'" data-id_satuan="'.$list->id_satuan.'" data-harga="'.$list->harga.'" 
                      data-keterangan="'.$list->keterangan.'" data-id_log_stok="'.$list->id_log_stok.'"
                      data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'" 
                      data-tanggal="'.tgl_full($list->tanggal,'99').'" data-status="'.$list->status.'"
                      id="btn_a" class="btn btn-xs  btn-success" data-toggle="tooltip" 
                      data-placement="botttom" title="Terima Barang"  style="color:white;">
                      <i class="fa fa-check-square"></i></a>';
            $edit   = '<a data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'"
                      data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" 
                      data-id_detail_pengiriman="'.$list->id.'" data-harga="'.$list->harga.'" 
                      data-keterangan="'.$list->keterangan.'" data-id_log_stok="'.$list->id_log_stok.'"
                      data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'"  
                      data-tanggal="'.tgl_full($list->tanggal,'99').'" data-status="'.$list->status.'"  id="btn_edit" 
                      class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data" 
                      style="color:white;"><i class="fa  fa-edit"></i></a>';
            $detail = '<a data-id_detail_pengiriman="'.$list->id.'" data-id_barang="'.$list->id_barang.'" 
                      data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'"
                      data-satuan="'.$list->satuan_nama.'" data-id_detail_pengiriman="'.$list->id.'" 
                      data-harga="'.$list->harga.'" data-keterangan="'.$list->keterangan.'" 
                      data-id_log_stok="'.$list->id_log_stok.'" 
                      data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'" 
                      data-tanggal="'.tgl_full($list->tanggal,'99').'" data-status="'.$list->status.'" id="btn_detail" 
                      class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" 
                      title="Edit Data"  style="color:white;"><i class="fa  fa-eye"></i></a>';
            $hapus  = '<a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" 
                      data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;">
                      <i class="fa  fa-trash"></i></a>';

            $action = '';
            if ($list->status == 0) {
              $status = "<span class='label label-default'>Belum Dikirim</span";
            }elseif ($list->status == 1) {
              $status = "<span class='label label-warning'>Sedang Dikirim</span";
            }elseif ($list->status == 2) {
                  $status = "<span class='label label-success'>Diterima</span";
            }elseif ($list->status == 3) {
                  $status = "<span class='label label-danger'>Dikembalikan</span";
                  # code...
            }

            if($list->status_pengiriman != 0){
              if($list->status == 0){
                $action = $kirim.''.$edit.''.$detail.''.$hapus;
              }else{
                $action = $edit.''.$detail.''.$hapus;
              }
            }else{
              $action = $edit.''.$detail.''.$hapus;
            }

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->nama;
            $row[] = $list->satuan_nama;
            // $row[] = empty($list->retur)?0:$list->retur;
            // $row[] = empty($list->diterima)?0:$list->diterima;
            $row[] = format_angka($list->jumlah);
            if(Auth::user()->group_id==1||Auth::user()->group_id==6){
            $row[] = ($list->harga==null)?"Rp. 0":"Rp. ".format_angka($list->harga);
            }else{
            $row[] = '';
            }
            $row[] =  $status;
            // $row[] = $ass.' '.$edit.''.$detail.' '.$hapus.' ';
            $row[] = $action;
            $data[] = $row;

        }
        $output = array("data" => $data);
        return response()->json($output);
  }
   
}
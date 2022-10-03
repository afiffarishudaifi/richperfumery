<?php

namespace App\Http\Controllers\Inventori;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\StokopnameModel;
use DB;
use Yajra\Datatables\Datatables;

class PersetujuanOpnameController extends Controller
{
    //

    public function index(){
    	return view('admin.stokopname.index_persetujuan');
    }

    public function listData(Request $request){        
        DB::enableQueryLog();
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $filter = $request->get('search');
        $search = (isset($filter['value']))? strtolower($filter['value']) : false;
        $tanggal = (isset($filter['value']))? tgl_full($filter['value'],'99') : false;
        $persetujuan = DB::table('tbl_stokopname_baru as tsb')->join('tbl_barang as tb','tsb.id_barang','tb.barang_id')->join('ref_gudang as rg','tsb.id_gudang','rg.id')->join('tbl_satuan as ts','tsb.id_satuan','ts.satuan_id')->select(DB::raw('tsb.id_stokopname as id_stokopname, tsb.tanggal, tsb.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsb.id_gudang, rg.nama as nama_gudang, tsb.stok as stok, tsb.fisik, tsb.selisih, tsb.id_satuan, tsb.id_log_stok, tsb.keterangan, tsb.status'))->where('tsb.tanggal','like','%'.$tanggal.'%')
            ->orWhere('tb.barang_kode','like','%'.$search.'%')
            ->orWhere('tb.barang_nama','like','%'.$search.'%')
            ->orWhere('tb.barang_alias','like','%'.$search.'%')
            ->orWhere('rg.nama','like','%'.$search.'%')
            ->orderBy('tsb.id_stokopname','DESC');
        

        //dd(DB::getQueryLog());
        $total_members = $persetujuan->count();
        $persetujuan_2 = DB::table('tbl_stokopname_baru as tsb')->join('tbl_barang as tb','tsb.id_barang','tb.barang_id')->join('ref_gudang as rg','tsb.id_gudang','rg.id')->join('tbl_satuan as ts','tsb.id_satuan','ts.satuan_id')->select(DB::raw('tsb.id_stokopname as id_stokopname, tsb.tanggal, tsb.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsb.id_gudang, rg.nama as nama_gudang, tsb.stok as stok, tsb.fisik, tsb.selisih, tsb.id_satuan, tsb.id_log_stok, tsb.keterangan, tsb.status'))->where('tsb.tanggal','like','%'.$tanggal.'%')
            ->orWhere('tb.barang_kode','like','%'.$search.'%')
            ->orWhere('tb.barang_nama','like','%'.$search.'%')
            ->orWhere('tb.barang_alias','like','%'.$search.'%')
            ->orWhere('rg.nama','like','%'.$search.'%')
            ->orderBy('tsb.id_stokopname','DESC')->offset($start)->limit($length)->get();
        $no=($start==0)?0:$start;
        $arr = array();
        foreach ($persetujuan_2 as $list) {
            $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->alias_barang;
            if($list->alias_barang == ""){
              $nama_barang = $list->kode_barang." || ".$list->nama_barang;
            } 
            $keterangan = $list->keterangan;
            if($list->keterangan == "" || $list->keterangan == "null"){
              $keterangan = "";
            }
            $no++;

            $arr[] = array( 
                'nomor' => $no.$this->get_check($list->status,$list->id_stokopname),
                'id_stokopname' => $list->id_stokopname,
                'tanggal' => tgl_full($list->tanggal,''),
                'nama_barang' => $nama_barang,
                'nama_gudang' => $list->nama_gudang,
                'stok' => $list->stok,
                'fisik' => $list->fisik,
                'selisih' => $list->selisih,
                'keterangan' => $keterangan,
                'status' => '<div class="text-center">'.$this->get_status($list->status).'</div>'
            );

        }

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $arr
        );

        return response()->json($data);
        
    }

    function get_data(){
        DB::statement(DB::raw('set @rownum=0'));
        $persetujuan = DB::table('tbl_stokopname_baru as tsb')->join('tbl_barang as tb','tsb.id_barang','tb.barang_id')->join('ref_gudang as rg','tsb.id_gudang','rg.id')->join('tbl_satuan as ts','tsb.id_satuan','ts.satuan_id')->select(DB::raw('tsb.id_stokopname as id_stokopname, tsb.tanggal, tsb.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsb.id_gudang, rg.nama as nama_gudang, tsb.stok as stok, tsb.fisik, tsb.selisih, tsb.id_satuan, tsb.id_log_stok, tsb.keterangan, tsb.status, @rownum  := @rownum  + 1 AS rownum'))->orderBy('tsb.id_stokopname','DESC')->get();

        $arr = array();
        $no=0;
        foreach($persetujuan as $list){
            $nama_barang = $list->kode_barang." || ".$list->nama_barang." || ".$list->alias_barang;
            if($list->alias_barang == ""){
              $nama_barang = $list->kode_barang." || ".$list->nama_barang;
            } 
            $keterangan = $list->keterangan;
            if($list->keterangan == "" || $list->keterangan == "null"){
              $keterangan = "";
            }
            $arr[] = array( 
                'rownum' => $list->rownum.$this->get_check($list->status,$list->id_stokopname),
                'id_stokopname' => $list->id_stokopname,
                'tanggal' => tgl_full($list->tanggal,''),
                'nama_barang' => $nama_barang,
                'nama_gudang' => $list->nama_gudang,
                'stok' => $list->stok,
                'fisik' => $list->fisik,
                'selisih' => $list->selisih,
                'keterangan' => $keterangan,
                'status' => '<div class="text-center">'.$this->get_status($list->status).'</div>'
            );
            /*$row[] = $list->rownum.$this->get_check($list->status,$list->id_stokopname);
            $row[] = '<div class="text-center">'.$this->get_status($list->status).'</div>';*/
        }

        $data = new Collection($arr);
        return Datatables::of($data)
                ->make(true);

    }

    public function simpan_multi(Request $request){
        $id = $request->get('id');
        for($i=0; $i<count($id); $i++){
            $id_stokopname = $id[$i];
            $data['status'] = '2';
            DB::table('tbl_stokopname_baru')->where('id_stokopname',$id_stokopname)->update($data);
            $d[] = array($id[$i]);            
        }
        $d_barang = DB::table('tbl_stokopname_baru')->whereIn('id_stokopname',$d);
            if($d_barang->count() > 0){
                foreach($d_barang->get() as $value){
                	if($value->selisih > 0){
	                  $input['unit_masuk']    = $value->selisih;
	                  $input['unit_keluar']   = "0";
	                  $input['status']        = 'S1';
	                }else{
	                  $input['unit_masuk']    = "0";
	                  $input['unit_keluar']   = trim($value->selisih,'-');
	                  $input['status']        = 'S2';
	                }
                    $id_stokopname          = $value->id_stokopname;
                    $input['id_barang']     = $value->id_barang;
                    $input['id_ref_gudang'] = $value->id_gudang;
                    $input['tanggal']       = date('Y-m-d');
                    $input['id_satuan']     = $value->id_satuan;
                    

                    $id = DB::table('tbl_log_stok')->insertGetId($input);
                    $barang['id_log_stok'] = $id;
                    DB::table('tbl_stokopname_baru')->where('id_stokopname',$id_stokopname)->update($barang);
                }
                
            }else{
                $input = array();
            }
        trigger_log(NULL, "Persetujuan Stokopname Multi", 2);
        return response()->json(array('status' => '1'));


    }

    public function simpan(Request $request){
      $id_stokopname = $request->get('id_stokopname');
      $data['tanggal']    = tgl_full($request->get('tanggal'),'99');
      $data['id_gudang']  = $request->get('gudang');
      $data['keterangan'] = $request->get('keterangan');
      $data['status']     = $request->get('status');


      $tabel_id = $request->get('tabel_id');
      $tabel_barang       = $request->get('tabel_idbarang');
      $tabel_gudang       = $request->get('tabel_idgudang');
      $tabel_jumStok      = $request->get('tabel_jumStok');
      $tabel_jumFisik     = $request->get('tabel_jumFisik');
      $tabel_satuan       = $request->get('tabel_idsatuan');
      $tabel_selisih      = $request->get('tabel_selisih');
      $tabel_keterangan   = $request->get('tabel_keterangan');
      $tabel_idlog        = $request->get('tabel_idlog_stok');
      DB::table('tbl_stokopname')->where('id_stokopname',$id_stokopname)->update($data);
      
        for($i=0;$i<count($tabel_id);$i++){
            if($tabel_selisih[$i] > 0){
                $input['unit_masuk']    = $tabel_selisih[$i];
                $input['unit_keluar']   = "0";
                $input['status']        = 'S1';
            }else{
                $input['unit_masuk']    = "0";
                $input['unit_keluar']   = trim($tabel_selisih[$i],'-');
                $input['status']        = 'S2';
            }

            $input['id_barang']     = $tabel_barang[$i];
            $input['id_ref_gudang'] = $tabel_gudang;
            $input['tanggal']       = date('Y-m-d');
            $input['id_satuan']     = $tabel_satuan[$i];

            $cek = DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]));
            if($cek->count() > 0){
               if($request->get('status') == 2){
                DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]))->update($input);
                $barang['id_log_stok'] = $tabel_idlog[$i];
               }else{
                DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]))->delete();
                $barang['id_log_stok'] = "";
               }     
            }else{
                $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);
                $barang['id_log_stok'] = $id_log_stok;
            }            
            DB::table('tbl_stokopname_detail')->where(array('id_detail_stokopname' => $tabel_id[$i]))->update($barang);

          }
     
      trigger_log($id_stokopname, "Persetujuan Stokopname", 2);
      return redirect('persetujuanopname');
    }

    public function tambah($id){
      $d_data = DB::table('tbl_stokopname as ts')->leftjoin('ref_gudang as rg','ts.id_gudang','rg.id')->where('ts.id_stokopname',$id)->select('ts.*','rg.nama as nama_gudang')->first();
      $data['data'] = $this->data($d_data);
      $data['status']     = \Config::get('constants.status_persetujuan');
      return view('admin.stokopname.tambah_persetujuan')->with('data',$data);
    }

    public function get_edit(Request $request){
        $id = $request->get('id');
        $d_data = DB::table('tbl_stokopname_detail as tpd')->leftjoin('tbl_barang as tb','tpd.id_barang','tb.barang_id')->leftjoin('ref_gudang as rg','tpd.id_gudang','rg.id')->where('tpd.id_stokopname',$id)->select('tpd.*','tb.barang_nama as nama_barang','tb.barang_kode as kode_barang','tb.barang_alias as alias_barang','rg.nama as nama_gudang')->orderBy('tpd.id_stokopname','asc');
        if($d_data->count() > 0 ){
            foreach($d_data->get() as $d){
                $arr[] = array('id' => $d->id_detail_stokopname,
                                'id_stokopname' => $d->id_stokopname,
                                'id_barang'     => $d->id_barang,
                                'nama_barang'   => $d->nama_barang,
                                'kode_barang'   => $d->kode_barang,
                                'alias_barang'  => $d->alias_barang,
                                'id_gudang'     => $d->id_gudang,
                                'nama_gudang'   => $d->nama_gudang,
                                'jumlah_stok'   => $d->stok,
                                'jumlah_fisik'  => $d->fisik,
                                'id_satuan'     => $d->id_satuan,
                                'selisih'       => $d->selisih,
                                'id_log_stok'   => $d->id_log_stok,
                                'keterangan'    => $d->keterangan);
            }
        }else{
            $arr = array();
        }
        return response()->json($arr);
    }

    function data($data = array()){
        if($data != null){
            $store['id_stokopname'] = $data->id_stokopname;
            $store['tanggal']       = tgl_full($data->tanggal,'');
            $store['id_gudang']     = $data->id_gudang;
            $store['nama_gudang']   = $data->nama_gudang;
            $store['keterangan']    = $data->keterangan;
            $store['status']        = $data->status;
        }else{
            $store['id_stokopname'] = "";
            $store['tanggal']       = "";
            $store['id_gudang']     = "";
            $store['nama_gudang']   = "";
            $store['keterangan']    = "";
            $store['status']        = "1";
        }
        return $store;
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

    public function get_aksi($aksi,$id){
        switch ($aksi) {
            case '1':
                $html = '<div class="btn-group"><a href="'.url('persetujuanopname_tambah/'.$id).'" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="botttom" title="Persetujuan Data"  style="color:white;"><i class="fa  fa-check"></i></div>';
                break;            
            case '2':
                $html = '<div class="btn-group"><a href="'.url('persetujuanopname_tambah/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Persetujuan Data"  style="color:white;"><i class="fa  fa-check"></i></div>';
                break;            
            default:
                $html = '<div class="btn-group"><a href="'.url('persetujuanopname_tambah/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Persetujuan Data"  style="color:white;"><i class="fa  fa-check"></i></div>';
                break;
        }
        return $html;
    }


}

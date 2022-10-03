<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\KasirModel;
use App\SupplierModel;
use App\PelanggahModel;
use Fpdf;
use Illuminate\Support\Collection;

class PostingKasirController extends Controller
{
    //

    public function index(){
		return view('admin.kasir.index_posting');
	}

	public function listData(){
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
      $kasir = DB::table('tbl_kasir as tk')->leftjoin('m_pelanggan as mp','tk.id_pelanggan','mp.id')->leftjoin('ref_gudang as rg','tk.id_gudang','rg.id')->where('tk.jenis_transaksi','1')->whereIn('tk.id_gudang',$id_gudang)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang'))->orderBy('tk.tanggal','DESC')->get();
      $no = 0;
      $data = array();
        foreach ($kasir as $list){
            if($list->carabayar == '1'){
            $total_tagihan = 'Rp. 0';
            }else{
            $total_tagihan = 'Rp '.format_angka($list->total_tagihan);
            }
            $no++;
            $row = array();
            $check = "";
            if($list->status_posting == 1){
              $check = ' <input type="checkbox" id="check_verifikasi" class="check_verifikasi" value="'.$list->id_kasir.'" jenis="'.$list->status_posting.'">';
            }

            $row[] = $no.$check;
            $row[] = tgl_full($list->tanggal,'');
            $row[] = $list->no_faktur;
            $row[] = $list->nama_pelanggan;
            $row[] = $list->nama_gudang;
            $row[] = 'Rp '.format_angka($list->total_tagihan);
            $row[] = $this->get_status($list->status_posting);
            $data[] = $row;


        }

      $output = array("data" => $data);
      return response()->json($output);
    }

    public function simpan(Request $request){
    	$id = $request->get('id');
        for($i=0; $i<count($id); $i++){
            $id_kasir = $id[$i];
            $data['status_posting'] = '2';
            DB::table('tbl_kasir')->where('id_kasir',$id_kasir)->update($data);
            $d[] = array($id[$i]);            
        }
        /*$d_barang = DB::table('tbl_pembelian_detail')->whereIn('id_pembelian',$d);
            if($d_barang->count() > 0){
                foreach($d_barang->get() as $value){
                    $id_detail_pembelian    = $value->id_detail_pembelian;
                    $input['id_barang']     = $value->id_barang;
                    $input['unit_masuk']    = $value->jumlah_konversi;
                    $input['unit_keluar']   = '0';
                    $input['id_ref_gudang'] = $gudang[0];
                    $input['id_satuan']     = $value->id_satuan_konversi;
                    $input['tanggal']       = date('Y-m-d');
                    $input['status']        = 'P1';

                    $id = DB::table('tbl_log_stok')->insertGetId($input);
                    $barang['id_log_stok'] = $id;
                    DB::table('tbl_pembelian_detail')->where('id_detail_pembelian',$id_detail_pembelian)->update($barang);
                }
                
            }else{
                $input = array();
            }*/
        
        return response()->json(array('status' => '1'));
    }

    public function get_status($status){
      switch ($status) {
        case '1':
          $html = '<label class="label label-sm label-default">Belum</label>';
          break;
        case '2':
          $html = '<label class="label label-sm label-success">Sudah</label>';
          break;
        default:
          $html = '<label class="label label-sm label-default">Belum</label>';
          break;
      }
      return $html;
    }

}

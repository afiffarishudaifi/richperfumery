<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Jenssegers\Agent\Agent;
use Yajra\DataTables\DataTables;

class PembayaranKasirController extends Controller
{
    //
    public function __construct(){
       $this->agent = new Agent();
    }
   public function index(){
     if ($this->agent->isMobile()) {
       // code...
       return view('admin_mobile.pembayaran.index_kasir');
     }else {
       // code...
       return view('admin.pembayaran.index_kasir');
     }
   }

   public function bayar($id){
   		$d_data = DB::table('tbl_kasir as tk')->leftjoin('m_pelanggan as mp','tk.id_pelanggan','mp.id')->leftjoin('ref_gudang as rg','tk.id_gudang','rg.id')->where('tk.id_kasir',$id)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang'))->orderBy('tk.tanggal','DESC')->first();
   		$data['data'] = $this->data($d_data);
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.pembayaran.bayar_kasir')->with('data',$data);
      }else {
        // code...
        return view('admin.pembayaran.bayar_kasir')->with('data',$data);
      }
   }

   public function data($data = array()){
   		if($data != null){
   			$store['id_kasir'] 			= $data->id_kasir;
   			$store['tanggal']			= $data->tanggal;
   			$store['tanggal_faktur']	= $data->tanggal_faktur;
   			$store['tanggal_tempo']		= $data->tanggal_tempo;
   			$store['nomor']				= $data->no_faktur;
   			$store['id_pelanggan']		= $data->id_pelanggan;
   			$store['nama_pelanggan']	= $data->nama_pelanggan;
   			$store['id_gudang']			= $data->id_gudang;
   			$store['nama_gudang']		= $data->nama_gudang;
   			$store['uang_muka']       	= $data->uang_muka;
	        $store['td_ongkir']       	= $data->ongkos_kirim;
	        $store['td_potongan']     	= $data->total_potongan;
	        $store['td_tagihan']      	= $data->total_tagihan;
	        $store['td_subtotal']     	= $data->total_subtotal;
   			$store['keterangan']		= $data->keterangan;
   		}else{
   			$store['id_kasir'] 			= "";
   			$store['tanggal']			= "";
   			$store['tanggal_faktur']	= "";
   			$store['tanggal_tempo']		= "";
   			$store['nomor']				= "";
   			$store['id_pelanggan']		= "";
   			$store['nama_pelanggan']	= "";
   			$store['id_gudang']			= "";
   			$store['nama_gudang']		= "";
   			$store['uang_muka']       	= "";
	        $store['td_ongkir']       	= "";
	        $store['td_potongan']     	= "";
	        $store['td_tagihan']      	= "";
	        $store['td_subtotal']     	= "";
   			$store['keterangan']		= "";
   		}
   	return $store;
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
      $draw = $request->get('draw');
      $start = $request->get('start');
      $length = $request->get('length');
      $filter = $request->get('search');
      $search = (isset($filter['value']))? strtolower($filter['value']) : false;
      $tanggal = (isset($filter['value']))? tgl_full($filter['value'],'99') : false;

      DB::statement(DB::raw('SET @rownum=0'));
      $kasir = DB::table('tbl_kasir as tk')
          ->leftjoin('m_pelanggan as mp','tk.id_pelanggan','mp.id')
          ->leftjoin('ref_gudang as rg','tk.id_gudang','rg.id')
          ->where('tk.jenis_transaksi','1')
          ->where('tk.carabayar','3')
      		->when($search, function($query,$keyword){
            if($keyword != ''){
              $query->where(function($query) use($keyword){
                $tanggal = tgl_full($keyword,'99');
                $query->orwhere('tk.no_faktur','like','%'.$keyword.'%');
                $query->orwhere('mp.nama','like','%'.$keyword.'%');
                $query->orwhere('rg.nama','like','%'.$keyword.'%');
                if($tanggal){
                    $query->where('tk.tanggal','like','%'.$tanggal.'%');
                }
              });
            }
          })
          ->when($id_profil, function($query,$keyword) use($group){
            if($keyword != ''){
              if($group == 6 || $group == 5){
                $query->where('rg.id_profil',$keyword);
              }
            }
          })
      		->select(DB::raw("@rownum := @rownum + 1 AS rownum, tk.*, mp.nama as nama_pelanggan,rg.nama as nama_gudang"))
          ->orderBy("tk.tanggal","DESC")
          ->orderBy("tk.no_faktur","DESC")
          ->orderBy("tk.status","ASC")
          ->get();

      return Datatables::of($kasir)
            ->addColumn('nomor',function($kasir){
              return $kasir->rownum;
            })
            ->addColumn('tanggal',function($kasir){
              return tgl_full($kasir->tanggal,'');
            })
            ->addColumn('pelanggan',function($kasir){
              return $kasir->nama_pelanggan;
            })
            ->addColumn('gudang',function($kasir){
              return $kasir->nama_gudang;
            })
            ->addColumn('tempo',function($kasir){
              return tgl_full($kasir->tanggal_tempo,'');
            })
            ->addColumn('tagihan',function($kasir){
              return "Rp. ".format_angka($kasir->total_tagihan-($kasir->total_potongan+$kasir->ongkos_kirim));
            })
            ->addColumn('status',function($kasir){
              return $this->get_status2($kasir->status);
            })
            ->addColumn('aksi',function($kasir){
              $total_bayar = 0;
              if($kasir->total_bayar !=null){
                $total_bayar = $kasir->total_bayar;
              }
              $tanggal_bayar = ($kasir->tanggal_bayar=="") ? date('d-m-Y'):tgl_full($kasir->tanggal_bayar,'');
              $aksi = '<div class="btn-group">'.
        							'<button class="btn btn-xs btn-success" onclick="edit_table('.$kasir->id_kasir.')" title="Edit Data"  style="color:white;"><i class="fa fa-money"></i> </button></div>'.
        							'<input type="hidden" id="table_id'.$kasir->id_kasir.'" value="'.$kasir->id_kasir.'">'.
        							'<input type="hidden" id="table_nofaktur'.$kasir->id_kasir.'" value="'.$kasir->no_faktur.'">'.
        							'<input type="hidden" id="table_idpelanggan'.$kasir->id_kasir.'" value="'.$kasir->id_pelanggan.'">'.
        							'<input type="hidden" id="table_namapelanggan'.$kasir->id_kasir.'" value="'.$kasir->nama_pelanggan.'">'.
        							'<input type="hidden" id="table_idgudang'.$kasir->id_kasir.'" value="'.$kasir->id_gudang.'">'.
        							'<input type="hidden" id="table_namagudang'.$kasir->id_kasir.'" value="'.$kasir->nama_gudang.'">'.
        							'<input type="hidden" id="table_tanggal'.$kasir->id_kasir.'" value="'.tgl_full($kasir->tanggal,'').'">'.
        							'<input type="hidden" id="table_tanggal_tempo'.$kasir->id_kasir.'" value="'.tgl_full($kasir->tanggal_tempo,'').'">'.
        							'<input type="hidden" id="table_tanggal_faktur'.$kasir->id_kasir.'" value="'.tgl_full($kasir->tanggal_faktur,'').'">'.
                      '<input type="hidden" id="table_tanggal_bayar'.$kasir->id_kasir.'" value="'.$tanggal_bayar.'">'.
        							'<input type="hidden" id="table_total_tagihan'.$kasir->id_kasir.'" value="'.$kasir->total_tagihan.'">'.
        							'<input type="hidden" id="table_total_potongan'.$kasir->id_kasir.'" value="'.$kasir->total_potongan.'">'.
        							'<input type="hidden" id="table_total_uangmuka'.$kasir->id_kasir.'" value="'.$kasir->uang_muka.'">'.
        							'<input type="hidden" id="table_total_ongkir'.$kasir->id_kasir.'" value="'.$kasir->ongkos_kirim.'">'.
        							'<input type="hidden" id="table_total_pembayaran'.$kasir->id_kasir.'" value="'.$total_bayar.'">'.
        							'<input type="hidden" id="table_keterangan'.$kasir->id_kasir.'" value="'.$kasir->keterangan.'">'.
        							'<input type="hidden" id="table_carabayar'.$kasir->id_kasir.'" value="'.$kasir->carabayar.'">'.
        							'<input type="hidden" id="table_status'.$kasir->id_kasir.'" value="'.$kasir->status.'">';
              return $aksi;
            })
            ->rawColumns(['nomor','tanggal','pelanggan','gudang','tempo','pelanggan','status','aksi'])
            ->make(true);
   }

   public function listData_lama(Request $request){
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
      $draw = $request->get('draw');
      $start = $request->get('start');
      $length = $request->get('length');
      $filter = $request->get('search');
      $search = (isset($filter['value']))? strtolower($filter['value']) : false;
      $tanggal = (isset($filter['value']))? tgl_full($filter['value'],'99') : false;

      /*$kasir = DB::table('tbl_kasir as tk')->leftjoin('m_pelanggan as mp','tk.id_pelanggan','mp.id')->leftjoin('ref_gudang as rg','tk.id_gudang','rg.id')->where('tk.jenis_transaksi','1')->where('tk.status','1')->where('tk.carabayar','3')->whereIn('tk.id_gudang',$id_gudang)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang'))->orderBy('tk.tanggal','DESC');*/
      $kasir = DB::table('tbl_kasir as tk')->leftjoin('m_pelanggan as mp','tk.id_pelanggan','mp.id')->leftjoin('ref_gudang as rg','tk.id_gudang','rg.id')->where('tk.jenis_transaksi','1')->where('tk.carabayar','3')->whereIn('tk.id_gudang',$id_gudang)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang'))->orderByRaw('tk.tanggal DESC','tk.no_faktur DESC','tk.status ASC');
      $c_kasir = DB::table('tbl_kasir as tk')->leftjoin('m_pelanggan as mp','tk.id_pelanggan','mp.id')->leftjoin('ref_gudang as rg','tk.id_gudang','rg.id')->where('tk.jenis_transaksi','1')->where('tk.carabayar','3')->whereIn('tk.id_gudang',$id_gudang)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang'))->orderByRaw('tk.tanggal DESC','tk.no_faktur DESC','tk.status ASC');
      if($search){
      $kasir = DB::table('tbl_kasir as tk')->leftjoin('m_pelanggan as mp','tk.id_pelanggan','mp.id')->leftjoin('ref_gudang as rg','tk.id_gudang','rg.id')->where('tk.jenis_transaksi','1')->where('tk.carabayar','3')
      		->orwhere('tk.no_faktur','like','%'.$search.'%')
      		->orwhere('mp.nama','like','%'.$search.'%')
      		->orwhere('rg.nama','like','%'.$search.'%')
      		->whereIn('tk.id_gudang',$id_gudang)
      		->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang'))->orderByRaw('tk.tanggal DESC','tk.no_faktur DESC','tk.status ASC');
      $c_kasir = DB::table('tbl_kasir as tk')->leftjoin('m_pelanggan as mp','tk.id_pelanggan','mp.id')->leftjoin('ref_gudang as rg','tk.id_gudang','rg.id')->where('tk.jenis_transaksi','1')->where('tk.carabayar','3')
      		->orwhere('tk.no_faktur','like','%'.$search.'%')
      		->orwhere('mp.nama','like','%'.$search.'%')
      		->orwhere('rg.nama','like','%'.$search.'%')
      		->whereIn('tk.id_gudang',$id_gudang)
      		->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang'))->orderByRaw('tk.tanggal DESC','tk.no_faktur DESC','tk.status ASC');
  	  }
  	 // $totalrecord = count($kasir->get());
  	  $totalrecord = $c_kasir->count();
      $kasir_2 = $kasir->offset($start)->limit($length);
      $no=($start==0)?0:$start;
      $arr = array();
        foreach ($kasir_2->get() as $list){
        	$total_bayar = 0;
        	if($list->total_bayar !=null){
        		$total_bayar = $list->total_bayar;
        	}
          $tanggal_bayar = ($list->tanggal_bayar=="") ? date('d-m-Y'):tgl_full($list->tanggal_bayar,'');
        	$no++;
            $arr[] = array('nomor'		=> $no,
        					'tanggal'	=> tgl_full($list->tanggal,''),
        					'no_faktur' => $list->no_faktur,
        					'pelanggan'	=> $list->nama_pelanggan,
        					'gudang'	=> $list->nama_gudang,
        					'tempo'		=> tgl_full($list->tanggal_tempo,''),
        				// 	'tagihan'	=> "Rp. ".format_angka($list->total_tagihan),
        				    'tagihan'	=> "Rp. ".format_angka($list->total_tagihan-($list->total_potongan+$list->ongkos_kirim)),
        					/*'pembayaran'=> "Rp. ".format_angka($list->total_bayar),*/
        					'status'	=> $this->get_status2($list->status),
        					'aksi'		=> '<div class="btn-group">
        								<button class="btn btn-xs btn-success" onclick="edit_table('.$list->id_kasir.')" title="Edit Data"  style="color:white;"><i class="fa fa-money"></i> </button></div>'.
        							'<input type="hidden" id="table_id'.$list->id_kasir.'" value="'.$list->id_kasir.'">'.
        							'<input type="hidden" id="table_nofaktur'.$list->id_kasir.'" value="'.$list->no_faktur.'">'.
        							'<input type="hidden" id="table_idpelanggan'.$list->id_kasir.'" value="'.$list->id_pelanggan.'">'.
        							'<input type="hidden" id="table_namapelanggan'.$list->id_kasir.'" value="'.$list->nama_pelanggan.'">'.
        							'<input type="hidden" id="table_idgudang'.$list->id_kasir.'" value="'.$list->id_gudang.'">'.
        							'<input type="hidden" id="table_namagudang'.$list->id_kasir.'" value="'.$list->nama_gudang.'">'.
        							'<input type="hidden" id="table_tanggal'.$list->id_kasir.'" value="'.tgl_full($list->tanggal,'').'">'.
        							'<input type="hidden" id="table_tanggal_tempo'.$list->id_kasir.'" value="'.tgl_full($list->tanggal_tempo,'').'">'.
        							'<input type="hidden" id="table_tanggal_faktur'.$list->id_kasir.'" value="'.tgl_full($list->tanggal_faktur,'').'">'.
                      '<input type="hidden" id="table_tanggal_bayar'.$list->id_kasir.'" value="'.$tanggal_bayar.'">'.
        							'<input type="hidden" id="table_total_tagihan'.$list->id_kasir.'" value="'.$list->total_tagihan.'">'.
        							'<input type="hidden" id="table_total_potongan'.$list->id_kasir.'" value="'.$list->total_potongan.'">'.
        							'<input type="hidden" id="table_total_uangmuka'.$list->id_kasir.'" value="'.$list->uang_muka.'">'.
        							'<input type="hidden" id="table_total_ongkir'.$list->id_kasir.'" value="'.$list->ongkos_kirim.'">'.
        							'<input type="hidden" id="table_total_pembayaran'.$list->id_kasir.'" value="'.$total_bayar.'">'.
        							'<input type="hidden" id="table_keterangan'.$list->id_kasir.'" value="'.$list->keterangan.'">'.
        							'<input type="hidden" id="table_carabayar'.$list->id_kasir.'" value="'.$list->carabayar.'">'.
        							'<input type="hidden" id="table_status'.$list->id_kasir.'" value="'.$list->status.'">'

        				);
      }
      /*'<div class="btn-group"><a  href="'.url('kasirpembayaran_bayar/'.$list->id_kasir).'" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="botttom" title="Print Data"  style="color:white;" target="_blank"><i class="fa fa-money"></i></div>'*/

      $data = array(
            'draw' => $draw,
            'recordsTotal' => $totalrecord,
            'recordsFiltered' => $totalrecord,
            'data' => $arr
        );
      return response()->json($data);
   }

   public function get_detail(Request $request){
  	  $id = $request->get('id');
      $d_data = DB::table('tbl_kasir_detail_produk as tpd')->leftjoin('m_produk as mp','tpd.id_produk','mp.id')->leftjoin('tbl_satuan as ts2','tpd.id_satuan','ts2.satuan_id')->where('tpd.id_kasir',$id)->select('tpd.*','mp.nama as nama_produk','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')->orderBy('tpd.id_kasir_detail_produk','asc');
      $d_barang = DB::table('tbl_kasir_detail as tkd')->leftjoin('tbl_barang as tb','tkd.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts2','tkd.id_satuan','ts2.satuan_id')->where('tkd.id_kasir',$id)->where('tkd.id_detail_kasir_produk','0')->select('tkd.*','tb.barang_nama as nama_barang','tb.barang_kode as kode_barang','tb.barang_alias as alias_barang','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')->orderBy('tkd.id_detail_kasir','asc');
      if($d_data->count() > 0){
        foreach($d_data->get() as $d){
            $arr['produk'][] = array('id' => $d->id_kasir_detail_produk,
                            'id_detail_kasir'  => "",
                            'id_kasir'  => $d->id_kasir,
                            'id_log_stok'  => $d->id_log_stok,
                            'id_produk'     => $d->id_produk,
                            'nama_produk'   => $d->nama_produk,
                            'id_barang'     => "",
                            'nama_barang'   => "",
                            'kode_barang'   => "",
                            'alias_barang'  => "",
                            'jumlah'        => $d->jumlah,
                            'id_satuan'     => $d->id_satuan,
                            'nama_satuan'   => $d->satuan_satuan,
                            'satuan_satuan' => $d->satuan_satuan,
                            'harga'         => $d->harga,
                            'total'         => $d->total,
                            'status'        => "1");
        }
      }else{
        $arr['produk'] = array();
      }

      if($d_barang->count() > 0){
        foreach($d_barang->get() as $d){
            $arr['barang'][] = array('id' => $d->id_detail_kasir,
                            'id_kasir'  => $d->id_kasir,
                            'id_log_stok'  => $d->id_log_stok,
                            'id_produk'     => '',
                            'nama_produk'   => '',
                            'id_barang'     => $d->id_barang,
                            'nama_barang'   => $d->nama_barang,
                            'kode_barang'   => $d->kode_barang,
                            'alias_barang'  => $d->alias_barang,
                            'jumlah'        => $d->jumlah,
                            'id_satuan'     => $d->id_satuan,
                            'nama_satuan'   => $d->satuan_satuan,
                            'satuan_satuan' => $d->satuan_satuan,
                            'harga'         => $d->harga,
                            'total'         => $d->total,
                            'status'        => "2");
        }
      }else{
        $arr['barang'] = array();
      }

      return response()->json($arr);
   }

   public function simpan(Request $request){
    	$id_kasir = $request->get('popup_id_table');
      $data['status'] = $request->get('popup_status');
      $data['total_bayar']   = $request->get('td_bayar');
      $data['tanggal_bayar'] = tgl_full($request->get('popup_tanggal_bayar'),'99');
      DB::table('tbl_kasir')->where('id_kasir',$id_kasir)->update($data);
      trigger_log($id_kasir, "Mengubah Data Piutang Outlet / Pembayaran", 2);
      return response()->json(array('status' => '1'));
   }

   public function get_status($status){
      switch ($status) {
        case '1':
          $html = '<label class="label label-sm label-success">Lunas</label>';
          break;
        case '2':
          $html = '<label class="label label-sm label-warning">Proses Bayar</label>';
          break;
        case '3':
          $html = '<label class="label label-sm label-danger">Belum Bayar</label>';
          break;
        default:
          $html = '<label class="label label-sm label-danger">Belum Bayar</label>';
          break;
      }
      return $html;
   }

   public function get_status2($status){
      switch ($status) {
        case '1':
          $html = '<label class="label label-sm label-danger">Belum Bayar</label>';
          break;
        case '2':
          $html = '<label class="label label-sm label-success">Lunas</label>';
          break;
        default:
          $html = '<label class="label label-sm label-danger">Belum Bayar</label>';
          break;
      }
      return $html;
   }
}

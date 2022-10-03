<?php

namespace App\Http\Controllers\Pembelian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Yajra\DataTables\DataTables;

class PembayaranPembelianController extends Controller
{
    //
    public function index(){
    	return view('admin.pembayaran.index_pembelianhutang');
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
      $hutang = DB::table('tbl_pembelian as tp')
			->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')
			->leftjoin('ref_gudang as rg','tp.id_gudang','rg.id')
			->whereIn('tp.carabayar',['2','3'])
			->when($id_profil, function($query,$keyword) use($group){
				if($keyword != ''){
				if($group == 6 || $group == 5){
					$query->where('rg.id_profil',$keyword);
				}
				}
			})
			->when($search,function($query,$keyword){
				if($keyword != ''){
					$query->where(function($query) use($keyword){
						$tanggal = tgl_full($keyword,'99');
						$query->orwhere('tp.no_faktur','like','%'.$keyword.'%');
						$query->orwhere('ts.supplier_nama','like','%'.$keyword.'%');
						$query->orwhere('rg.nama','like','%'.$keyword.'%');
						if($tanggal){
							$query->orwhere('tp.tanggal','like','%'.$tanggal.'%');
						}
					});
				}
			})
			// ->offset($start)->limit($length)
      		->select(DB::raw('@rownum := @rownum + 1 AS rownum,tp.id_pembelian, tp.id_supplier, ts.supplier_nama as nama_supplier, tp.no_faktur, tp.tanggal, tp.tanggal_tempo, tp.tanggal_faktur, tp.tanggal_bayar, tp.status_bayar, tp.total_tagihan, tp.total_potongan, tp.total_subtotal, tp.pajak, tp.total_bayar, tp.uang_muka, tp.ongkos_kirim, tp.id_gudang, rg.nama as nama_gudang, tp.keterangan, tp.carabayar'))
			->orderBy("tp.tanggal","DESC")
			->orderBy("tp.no_faktur","ASC")
			->orderBy("tp.status_bayar","ASC")
			->get();
		
		$datatables =  DataTables::of($hutang)
			->addColumn('nomor',function($hutang){
              	return $hutang->rownum;
            })
			->addColumn('tanggal',function($hutang){
				return tgl_full($hutang->tanggal,'');
			})
			->addColumn('supplier',function($hutang){
				return $hutang->nama_supplier;
			})
			->addColumn('gudang',function($hutang){
				return $hutang->nama_gudang;
			})
			->addColumn('tempo',function($hutang){
				return tgl_full($hutang->tanggal_tempo,'');
			})
			->addColumn('tagihan',function($hutang){
				return "Rp. ".format_angka($hutang->total_tagihan);
			})
			->addColumn('status',function($hutang){
				return $this->get_status($hutang->status_bayar);
			})
			->addColumn('aksi',function($hutang){
				$total_bayar = 0;
				if($hutang->total_bayar !=null){
					$total_bayar = $hutang->total_bayar;
				}
				$tanggal_bayar = ($hutang->tanggal_bayar=="") ? date('d-m-Y'):tgl_full($hutang->tanggal_bayar,'');
				$aksi = '<div class="btn-group">
        				<button class="btn btn-xs btn-success" onclick="edit_table('.$hutang->id_pembelian.')" title="Edit Data"  style="color:white;"><i class="fa fa-money"></i> </button></div>'.
        				'<input type="hidden" id="table_id'.$hutang->id_pembelian.'" value="'.$hutang->id_pembelian.'">'.
        				'<input type="hidden" id="table_nofaktur'.$hutang->id_pembelian.'" value="'.$hutang->no_faktur.'">'.
        				'<input type="hidden" id="table_idsupplier'.$hutang->id_pembelian.'" value="'.$hutang->id_supplier.'">'.
        				'<input type="hidden" id="table_namasupplier'.$hutang->id_pembelian.'" value="'.$hutang->nama_supplier.'">'.
        				'<input type="hidden" id="table_idgudang'.$hutang->id_pembelian.'" value="'.$hutang->id_gudang.'">'.
        				'<input type="hidden" id="table_namagudang'.$hutang->id_pembelian.'" value="'.$hutang->nama_gudang.'">'.
        				'<input type="hidden" id="table_tanggal'.$hutang->id_pembelian.'" value="'.tgl_full($hutang->tanggal,'').'">'.
        				'<input type="hidden" id="table_tanggal_tempo'.$hutang->id_pembelian.'" value="'.tgl_full($hutang->tanggal_tempo,'').'">'.
        				'<input type="hidden" id="table_tanggal_faktur'.$hutang->id_pembelian.'" value="'.tgl_full($hutang->tanggal_faktur,'').'">'.
                      	'<input type="hidden" id="table_tanggal_bayar'.$hutang->id_pembelian.'" value="'.$tanggal_bayar.'">'.
        				'<input type="hidden" id="table_total_tagihan'.$hutang->id_pembelian.'" value="'.$hutang->total_tagihan.'">'.
        				'<input type="hidden" id="table_subtotal'.$hutang->id_pembelian.'" value="'.$hutang->total_subtotal.'">'.
        				'<input type="hidden" id="table_total_potongan'.$hutang->id_pembelian.'" value="'.$hutang->total_potongan.'">'.
        				'<input type="hidden" id="table_total_uangmuka'.$hutang->id_pembelian.'" value="'.$hutang->uang_muka.'">'.
        				'<input type="hidden" id="table_total_ongkir'.$hutang->id_pembelian.'" value="'.$hutang->ongkos_kirim.'">'.
        				'<input type="hidden" id="table_total_pajak'.$hutang->id_pembelian.'" value="'.$hutang->pajak.'">'.
                      	'<input type="hidden" id="table_total_bayar'.$hutang->id_pembelian.'" value="'.$total_bayar.'">'.
        				'<input type="hidden" id="table_keterangan'.$hutang->id_pembelian.'" value="'.$hutang->keterangan.'">'.
        				'<input type="hidden" id="table_carabayar'.$hutang->id_pembelian.'" value="'.$hutang->carabayar.'">'.
        				'<input type="hidden" id="table_status'.$hutang->id_pembelian.'" value="'.$hutang->status_bayar.'">';
				return $aksi;
			})
			->rawColumns(['nomor','tanggal','tempo','tagihan','status','aksi']);
			return $datatables->make(true);
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

      $hutang = DB::table('tbl_pembelian as tp')->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')->leftjoin('ref_gudang as rg','tp.id_gudang','rg.id')->where('tp.carabayar','3')->orwhere('tp.carabayar','2')->whereIn('tp.id_gudang',$id_gudang)->select(DB::raw('tp.id_pembelian, tp.id_supplier, ts.supplier_nama as nama_supplier, tp.no_faktur, tp.tanggal, tp.tanggal_tempo, tp.tanggal_faktur, tp.tanggal_bayar, tp.status_bayar, tp.total_tagihan, tp.total_potongan, tp.total_subtotal, tp.pajak, tp.total_bayar, tp.uang_muka, tp.ongkos_kirim, tp.id_gudang, rg.nama as nama_gudang, tp.keterangan, tp.status_bayar, tp.carabayar'))->orderByRaw('tp.tanggal DESC','tp.no_faktur','tp.status_bayar ASC');
      if($search){
      $hutang = DB::table('tbl_pembelian as tp')->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')->leftjoin('ref_gudang as rg','tp.id_gudang','rg.id')->where('tp.carabayar','3')
      		->orwhere('tp.carabayar','2')
      		->orwhere('tp.no_faktur','like','%'.$search.'%')
      		->orwhere('ts.nama','like','%'.$search.'%')
      		->orwhere('rg.nama','like','%'.$search.'%')
      		->orwhere('tp.tanggal','like','%'.$tanggal.'%')
      		->whereIn('tp.id_gudang',$id_gudang)
      		->select(DB::raw('tp.id_pembelian, tp.id_supplier, ts.supplier_nama as nama_supplier, tp.no_faktur, tp.tanggal, tp.tanggal_tempo, tp.tanggal_faktur, ttp.tanggal_bayar, tp.status_bayar, tp.total_tagihan, tp.total_potongan, tp.total_subtotal, tp.pajak, tp.total_bayar, tp.uang_muka, tp.ongkos_kirim, tp.id_gudang, rg.nama as nama_gudang, tp.keterangan, tp.status_bayar, tp.carabayar'))->orderByRaw('tp.tanggal DESC','tp.no_faktur','tp.status_bayar ASC');

  	  }
  	  $totalrecord = count($hutang->get());
      $hutang_2 = $hutang->offset($start)->limit($length);
      $no=($start==0)?0:$start;
      $arr = array();
        foreach ($hutang_2->get() as $list){
        	$no++;
          $total_bayar = 0;
          if($list->total_bayar !=null){
            $total_bayar = $list->total_bayar;
          }
          $tanggal_bayar = ($list->tanggal_bayar=="") ? date('d-m-Y'):tgl_full($list->tanggal_bayar,'');
            $arr[] = array('nomor'		=> $no,
        					'tanggal'	=> tgl_full($list->tanggal,''),
        					'no_faktur' => $list->no_faktur,
        					'supplier'	=> $list->nama_supplier,
        					'gudang'	=> $list->nama_gudang,
        					'tempo'		=> tgl_full($list->tanggal_tempo,''),
        					'tagihan'	=> "Rp. ".format_angka($list->total_tagihan),
        					'status'	=> $this->get_status($list->status_bayar),
        					'aksi'		=> '<div class="btn-group">
        								<button class="btn btn-xs btn-success" onclick="edit_table('.$list->id_pembelian.')" title="Edit Data"  style="color:white;"><i class="fa fa-money"></i> </button></div>'.
        							'<input type="hidden" id="table_id'.$list->id_pembelian.'" value="'.$list->id_pembelian.'">'.
        							'<input type="hidden" id="table_nofaktur'.$list->id_pembelian.'" value="'.$list->no_faktur.'">'.
        							'<input type="hidden" id="table_idsupplier'.$list->id_pembelian.'" value="'.$list->id_supplier.'">'.
        							'<input type="hidden" id="table_namasupplier'.$list->id_pembelian.'" value="'.$list->nama_supplier.'">'.
        							'<input type="hidden" id="table_idgudang'.$list->id_pembelian.'" value="'.$list->id_gudang.'">'.
        							'<input type="hidden" id="table_namagudang'.$list->id_pembelian.'" value="'.$list->nama_gudang.'">'.
        							'<input type="hidden" id="table_tanggal'.$list->id_pembelian.'" value="'.tgl_full($list->tanggal,'').'">'.
        							'<input type="hidden" id="table_tanggal_tempo'.$list->id_pembelian.'" value="'.tgl_full($list->tanggal_tempo,'').'">'.
        							'<input type="hidden" id="table_tanggal_faktur'.$list->id_pembelian.'" value="'.tgl_full($list->tanggal_faktur,'').'">'.
                      '<input type="hidden" id="table_tanggal_bayar'.$list->id_pembelian.'" value="'.$tanggal_bayar.'">'.
        							'<input type="hidden" id="table_total_tagihan'.$list->id_pembelian.'" value="'.$list->total_tagihan.'">'.
        							'<input type="hidden" id="table_subtotal'.$list->id_pembelian.'" value="'.$list->total_subtotal.'">'.
        							'<input type="hidden" id="table_total_potongan'.$list->id_pembelian.'" value="'.$list->total_potongan.'">'.
        							'<input type="hidden" id="table_total_uangmuka'.$list->id_pembelian.'" value="'.$list->uang_muka.'">'.
        							'<input type="hidden" id="table_total_ongkir'.$list->id_pembelian.'" value="'.$list->ongkos_kirim.'">'.
        							'<input type="hidden" id="table_total_pajak'.$list->id_pembelian.'" value="'.$list->pajak.'">'.
                      '<input type="hidden" id="table_total_bayar'.$list->id_pembelian.'" value="'.$total_bayar.'">'.
        							'<input type="hidden" id="table_keterangan'.$list->id_pembelian.'" value="'.$list->keterangan.'">'.
        							'<input type="hidden" id="table_carabayar'.$list->id_pembelian.'" value="'.$list->carabayar.'">'.
        							'<input type="hidden" id="table_status'.$list->id_pembelian.'" value="'.$list->status_bayar.'">'

        				);
      }

      $data = array(
            'draw' => $draw,
            'recordsTotal' => $totalrecord,
            'recordsFiltered' => $totalrecord,
            'data' => $arr
        );
      return response()->json($data);
    }


    /*public function bayar($id){
   		$d_data = DB::table('tbl_pembelian as tp')->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')->leftjoin('ref_gudang as rg','tp.id_gudang','rg.id')->where('tk.id_pembelian','3')->select(DB::raw('tp.id_pembelian, tp.id_supplier, ts.supplier_nama as nama_supplier, tp.no_faktur, tp.tanggal, tp.tanggal_tempo, tp.tanggal_faktur, tp.status_bayar, tp.total_tagihan, tp.total_potongan, tp.total_subtotal, tp.pajak, tp.uang_muka, tp.ongkos_kirim, tp.id_gudang, rg.nama as nama_gudang, tp.keterangan'))->orderByRaw('tp.tanggal DESC','tp.no_faktur','tp.status_bayar ASC')->first();
   		$data['data'] = $this->data($d_data);
   		return view('admin.pembayaran.bayar_kasir')->with('data',$data);
   }

   public function data($data = array()){
   		if($data != null){
   			$store['id_pembelian']	= $data->id_pembelian;
   			$store['tanggal']		= tgl_full($data->tanggal,'');
   			$store['tanggal_tempo']	= tgl_full($data->tanggal_tempo,'');
   			$store['tanggal_faktur']= tgl_full($data->tangga_faktur,'');
        $store['tanggal_bayar'] = tgl_full($data->tangga_bayar,'');
   			$store['nomor']			    = $data->no_faktur;
   			$store['id_supplier']	  = $data->id_supplier;
   			$store['nama_supplier']	= $data->nama_supplier;
   			$store['id_gudang']		= $data->id_gudang;
   			$store['nama_gudang']	= $data->nama_gudang;
   			$store['uang_muka']		= $data->uang_muka;
   			$store['pajak']			  = $data->pajak;
   			$store['td_ongkir']		= $data->ongkos_kirim;
   			$store['td_potongan']	= $data->total_potongan;
   			$store['td_subtotal']	= $data->total_subtotal;
   			$store['td_tagihan']	= $data->total_tagihan;
   			$store['keterangan']	= $data->keterangan;
   		}else{
   			$store['id_pembelian']	= "";
   			$store['tanggal']		    = date('d-m-Y');
   			$store['tanggal_tempo']	= date('d-m-Y');
   			$store['tanggal_faktur']= date('d-m-Y');
        $store['tanggal_bayar'] = date('d-m-Y');
   			$store['nomor']			    = "";
   			$store['id_supplier']	  = "";
   			$store['nama_supplier']	= "";
   			$store['id_gudang']		= "";
   			$store['nama_gudang']	= "";
   			$store['uang_muka']		= "";
   			$store['pajak']			  = "";
   			$store['td_ongkir']		= "";
   			$store['td_potongan']	= "";
   			$store['td_subtotal']	= "";
   			$store['td_tagihan']	= "";
   			$store['keterangan']	= "";

   		}
   		return $store;
   }*/

   public function get_detail(Request $request){
   		$id = $request->get('id');
   		$d_data = DB::table('tbl_pembelian_detail as tpd')->leftjoin('tbl_barang as tb','tpd.id_barang','tb.barang_id')
   				->leftjoin('tbl_satuan as ts','tpd.id_satuan','ts.satuan_id')->where('tpd.id_pembelian','=',$id)
   				->select(DB::raw('tpd.id_detail_pembelian, tpd.id_pembelian, tpd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, tpd.id_satuan, ts.satuan_nama as nama_satuan, ts.satuan_satuan, tpd.jumlah, tpd.harga, tpd.id_log_stok'))->orderBy('tpd.id_detail_pembelian','asc');
   		if($d_data->count() > 0){
   			foreach($d_data->get() as $d){
   				$total = $d->jumlah*$d->harga;
   				$arr[] = array('id'      => $d->id_detail_pembelian,
   								'id_pembelian'   => $d->id_pembelian,
   								'id_barang'      => $d->id_barang,
   								'nama_barang'    => $d->nama_barang,
   								'kode_barang'    => $d->kode_barang,
   								'alias_barang'   => $d->alias_barang,
   								'id_satuan'      => $d->id_satuan,
   								'nama_satuan'    => $d->nama_satuan,
   								'satuan_satuan'  => $d->satuan_satuan,
   								'jumlah'		     => $d->jumlah,
   								'harga'			     => $d->harga,
   								'total'			     => $total,
   								'id_log_stok'	   => $d->id_log_stok
   								);
   			}
   		}else{
   			$arr = array();
   		}

   		return response()->json($arr);
   }

   public function simpan(Request $request){
   		$id_pembelian = $request->get('popup_id_table');
   		$data['status_bayar'] = $request->get('popup_status');
      $data['tanggal_bayar'] = tgl_full($request->get('popup_tanggal_bayar'),'99');
      $data['total_bayar'] = $request->get('td_bayar');
   		DB::table('tbl_pembelian')->where('id_pembelian',$id_pembelian)->update($data);
        trigger_log($id_pembelian,'Mengubah data Menu Pembayaran',2,null);
   		return response()->json(array('status'=>'1'));
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

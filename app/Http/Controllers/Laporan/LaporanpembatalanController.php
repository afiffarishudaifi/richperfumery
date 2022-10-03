<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Exports\LaporanViewExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanpembatalanController extends Controller
{
    //
    public function index(){
	    $id_profil = Auth::user()->id_profil;
	    $group = Auth::user()->group_id;
	    $where = "";
	        if($group == 5){
	          $where = "WHERE id_profil='$id_profil'";
	        }
	    $data['gudang'] = DB::select(base_gudang($where));
	    $data['group']	= Auth::user()->group_id;
	    return view('admin.laporan.index_laporan_pembatalan')->with('data',$data);
    }
    
    public function get_barang(Request $request){
    	$term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }

        $search = strtolower($term);
        $barangs= DB::select("SELECT
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
                WHERE (b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%' OR b.barang_alias LIKE '%$search%') AND b.barang_id_parent != '0'");

        return \Response::json($barangs);
    }
    
	public function cetaklaporan_pembatalan(Request $request)
	{
    	require(public_path('fpdf1813/Mc_table.php'));
		
		$gudang = $request->get('gudang_pembatalan');
    	$tanggal = tgl_full($request->get('tanggalAwal'),'99');
    	$tanggal2 = tgl_full($request->get('tanggalAkhir'),'99'); 
    	$kategori = $request->get('kategori'); 
    	$barang = 0;
    	if(isset($request['barang'])){
    		$barang = $request->get('barang');
    	}

        $d_gudang = DB::table('ref_gudang')->where('id',$gudang)->select(DB::Raw('id as id_gudang,nama as nama_gudang'))->first();
		trigger_log(NULL, "Cetak Laporan Pembatalan PDF", 7);
    	switch ($kategori) { 
			case '1':
				//pernota
				if($gudang != "" && $tanggal != ""){
					$where = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
				}elseif($gudang != "" && $tanggal == ""){
					$where = "WHERE tk.id_gudang = '".$gudang."'";
				}else{
					$where = "";
				}

				$data['data'] = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang, u.name as nama_hapus FROM tbl_kasir_batal AS tk  JOIN users AS u ON tk.deleted_iduser = u.id LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id $where AND tk.jenis_transaksi=1 AND tk.id_gudang IN ($gudang) ORDER BY tk.tanggal_faktur DESC");

				$data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
				$data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
				$data['hari']    = tgl_full($request->tanggal,'hari');
				$data['kasir']   = Auth::user()->name;
				$data['gudang']	 = $d_gudang->nama_gudang;
				$data['jumlah'] = count($data['data']);

				$html = view('admin.laporan.cetak_laporan_pembatalangrosir_rekap')->with('data',$data);
				return response($html)->header('Content-Type', 'application/pdf');
	    
    			break; 
        case '2':
                
			if($gudang != "" && $tanggal != ""){
				$where = "tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
			}elseif($gudang != "" && $tanggal == ""){
				$where = "tk.id_gudang = '".$gudang."'";
			}else{
				$where = "";
			}

			$d_data = DB::table('tbl_kasir_detail_produk_batal as tpd')
				->leftjoin('m_produk as mp','tpd.id_produk','mp.id')
				->leftjoin('tbl_satuan as ts2','tpd.id_satuan','ts2.satuan_id')
				->join('tbl_kasir_detail_batal as tdb', 'tdb.id_kasir', 'tpd.id_kasir')
				->join('tbl_kasir_batal as tk', 'tk.id_kasir','tdb.id_kasir')
				->join('ref_gudang as rg', 'tk.id_gudang', 'rg.id')
				->leftjoin('m_pelanggan as mpel','tk.id_pelanggan','mpel.id')
				->join('users as us','us.id','tk.deleted_iduser')
				// ->where('tpd.id_kasir',$id)
				->whereRaw($where)
				->select('us.name as nama_hapus','rg.nama as nama_gudang','tk.no_faktur','mpel.telp as telp_pelanggan','tdb.cretaed_at as tanggal','mpel.nama as nama_pelanggan','tpd.*','mp.id_type_ukuran','mp.nama as nama_produk','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')
				->groupBy('tpd.id_produk')
				->orderBy('tpd.id_kasir_detail_produk','asc');

			$d_barang = DB::table('tbl_kasir_detail_batal as tkd')
				->leftjoin('tbl_barang as tb','tkd.id_barang','tb.barang_id')
				->leftjoin('tbl_satuan as ts2','tkd.id_satuan','ts2.satuan_id')
				->join('tbl_kasir_batal as tk', 'tk.id_kasir','tkd.id_kasir')
				->join('ref_gudang as rg', 'tk.id_gudang', 'rg.id')
				->leftjoin('m_pelanggan as mpel','tk.id_pelanggan','mpel.id')
				->join('users as us','us.id','tk.deleted_iduser')
				// ->where('tkd.id_kasir',$id)
				->whereRaw($where)
				->where('tkd.id_detail_kasir_produk','0')
				->select('us.name as nama_hapus','rg.nama as nama_gudang','tk.no_faktur','mpel.telp as telp_pelanggan','tkd.cretaed_at as tanggal','mpel.nama as nama_pelanggan','tkd.*','tb.barang_nama as nama_barang','tb.barang_kode as kode_barang','tb.barang_alias as alias_barang','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')
				->groupBy('tkd.id_barang')
				->orderBy('tkd.id_detail_kasir','asc');
			
			if($d_data->count() > 0){
				foreach($d_data->get() as $d){
					$arr['produk'][] = array('id' => $d->id_kasir_detail_produk,
									'id_detail_kasir'  => "",
									'id_kasir'  	=> $d->id_kasir,
									'id_produk'     => $d->id_produk,
									'nama_produk'   => $d->nama_produk,
									'jumlah'        => $d->jumlah,
									'id_satuan'     => $d->id_satuan,
									'nama_satuan'   => $d->satuan_satuan,
									'satuan_satuan' => $d->satuan_satuan,
									'harga'         => $d->harga,
									'total'         => $d->total,
									'poin'          => $d->poin,
									'potongan'      => ($d->harga == 0) ? 0:$d->potongan,
									'potongan_total'=> ($d->harga == 0) ? 0:$d->potongan_total,
									'id_tipe'       => $d->id_type_ukuran ?? '',
									'status'        => "1",
									'telp_pelanggan' => $d->telp_pelanggan,
									'nama_pelanggan' 	=> $d->nama_pelanggan,
									'tanggal'		=> $d->tanggal,
									'no_faktur'		=> $d->no_faktur,
									'nama_gudang'	=> $d->nama_gudang,
									'nama_hapus'	=> $d->nama_hapus,
									'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem));
				}
			}else{
				$arr['produk'] = array();
			}

			if($d_barang->count() > 0){
				foreach($d_barang->get() as $d){
					$arr['barang'][] = array('id' 	=> $d->id_detail_kasir,
									'id_kasir'  	=> $d->id_kasir,
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
									'poin'          => "0",
									'potongan'      => "0",
									'potongan_total'=> "0",
									'id_tipe'       => "3",
									'status'        => "2",
									'telp_pelanggan' => $d->telp_pelanggan,
									'nama_pelanggan' 	=> $d->nama_pelanggan,
									'tanggal'		=> $d->tanggal,
									'no_faktur'		=> $d->no_faktur,
									'nama_gudang'	=> $d->nama_gudang,
									'nama_hapus'	=> $d->nama_hapus,
									'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem));
				}
			}else{
				$arr['barang'] = array();
			}
			
			$total_produk = count($arr['produk']);
			$total_barang = count($arr['barang']);

			$no = 1;
			$arrdata = array();
			if($total_barang != 0) {
				foreach ($arr['barang'] as $d){
					if($d['telp_pelanggan'] != '' || $d['telp_pelanggan'] != null){
						$pelanggan = $d['nama_pelanggan'].'/'.$d['telp_pelanggan'];
					}else{
						$pelanggan = $d['nama_pelanggan'];
					}
					$arrdata[] = array(
									'tanggal' 			=> tgl_full($d['tanggal'],'0'),
									'no_faktur'			=> $d['no_faktur'],
									'telp_pelanggan'	=> $d['telp_pelanggan'],
									'pelanggan'			=> $d['nama_pelanggan'],
									'gudang'			=> $d['nama_gudang'],
									'nama_produk'		=> $d['nama_barang'],
									'jumlah'			=> $d['jumlah'],
									'total'				=> 'Rp '.format_angka($d['total']),
									'dibatalkan'		=> $d['nama_hapus']);
				}
			}

			if($total_produk != 0) {
				foreach ($arr['produk'] as $key => $s){
					if($s['telp_pelanggan'] != '' || $s['telp_pelanggan'] != null){
						$pelanggan = $s['nama_pelanggan'].'/'.$s['telp_pelanggan'];
					}else{
						$pelanggan = $s['nama_pelanggan'];
					}
					$arrdata[] = array(
									'tanggal' 			=> tgl_full($s['tanggal'],'0'),
									'no_faktur'			=> $s['no_faktur'],
									'telp_pelanggan'	=> $s['telp_pelanggan'],
									'pelanggan'			=> $s['nama_pelanggan'],
									'gudang'			=> $s['nama_gudang'],
									'nama_produk'		=> $s['nama_produk'],
									'jumlah'			=> $s['jumlah'],
									'total'				=> 'Rp '.format_angka($s['total']),
									'dibatalkan'		=> $s['nama_hapus']);
				}
			}

			if($total_produk+$total_barang > 0 ){
				$datas = $arrdata;
			}else{
				$datas = array("0"=>[
									'tanggal' 			=> '',
									'no_faktur'			=> '',
									'telp_pelanggan'			=> '',
									'pelanggan'			=> '',
									'gudang'			=> '',
									'nama_produk'		=> '',
									'harga'				=> '',
									'jumlah'			=> '',
									'total'				=> '',
									'dibatalkan_oleh'	=> '']);
			}
				
			$data['data'] = $datas;
			// dd($data['data']);
			$data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
			$data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
			$data['hari']    = tgl_full($request->tanggal,'hari');
			$data['kasir']   = Auth::user()->name;
			$data['gudang']	 = $d_gudang->nama_gudang;
			$html = view('admin.laporan.cetak_laporan_pembatalangrosir_pernota')->with('data',$data);
			return response($html)->header('Content-Type', 'application/pdf');
    		break;
    	default:
    			# code...
    			break;
    	}
    }

    public function excel_pembatalan($d_gudang,$d_tanggalAwal, $d_tanggalAkhir, $d_kategori, $d_barang){
    	$gudang 	= $d_gudang;
		$nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
    	$tanggal 	= tgl_full($d_tanggalAwal,'99');
    	$tanggal2 	= tgl_full($d_tanggalAkhir,'99');
    	$kategori 	= $d_kategori;
    	$barang 	= $d_barang;
		trigger_log(NULL, "Cetak Laporan Pembatalan Excel", 7);
    	switch ($kategori) {
    		case '1':
				if($gudang != "" && $tanggal != ""){
					$where = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
				}elseif($gudang != "" && $tanggal == ""){
					$where = "WHERE tk.id_gudang = '".$gudang."'";
				}else{
					$where = "";
				}

				$d_data = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang, u.name as nama_hapus FROM tbl_kasir_batal AS tk  JOIN users AS u ON tk.deleted_iduser = u.id LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id $where AND tk.jenis_transaksi=1 AND tk.id_gudang IN ($gudang) ORDER BY tk.tanggal_faktur DESC");

				$no = 1;
				$arr = array();
					foreach ($d_data as $key => $d){
						$arr[] = array('NO.'=>$no++,
										'TANGGAL' 				=> tgl_full($d->tanggal,'0'),
										'NO FAKTUR'				=> $d->no_faktur,
										'PELANGGAN'				=> $d->nama_pelanggan,
										'GUDANG'				=> $d->nama_gudang,
										'TOTAL PENJUALAN'		=> 'Rp '.format_angka($d->total_tagihan-($d->total_potongan-$d->ongkos_kirim)),
										'DIBATALKAN OLEH'		=> $d->nama_hapus,
										'CATATAN'				=> $d->catatan);
					}

				if(count($arr) > 0){
					$data = $arr;
				}else{
					$data = array("0"=>['NO.'					=> '',
									'TANGGAL' 				=> '',
									'NO FAKTUR'				=> '',
									'PELANGGAN'				=> '',
									'GUDANG'				=> '',
									'TOTAL PENJUALAN'		=> '',
									'DIBATALKAN OLEH'		=> '',
									'CATATAN'				=> '']);
				}
				// dd($data);
				return Excel::download(new LaporanViewExport($data), 'Laporan_Pembatalan_Rekap_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');

    		break;
    		case '2':
				
				if($gudang != "" && $tanggal != ""){
					$where = "tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
				}elseif($gudang != "" && $tanggal == ""){
					$where = "tk.id_gudang = '".$gudang."'";
				}else{
					$where = "";
				}

				$nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;

				$d_data = DB::table('tbl_kasir_detail_produk_batal as tpd')
					->leftjoin('m_produk as mp','tpd.id_produk','mp.id')
					->leftjoin('tbl_satuan as ts2','tpd.id_satuan','ts2.satuan_id')
					->join('tbl_kasir_detail_batal as tdb', 'tdb.id_kasir', 'tpd.id_kasir')
					->join('tbl_kasir_batal as tk', 'tk.id_kasir','tdb.id_kasir')
					->join('ref_gudang as rg', 'tk.id_gudang', 'rg.id')
					->leftjoin('m_pelanggan as mpel','tk.id_pelanggan','mpel.id')
					->join('users as us','us.id','tk.deleted_iduser')
					// ->where('tpd.id_kasir',$id)
					->whereRaw($where)
					->select('us.name as nama_hapus','rg.nama as nama_gudang','tk.no_faktur','mpel.telp as telp_pelanggan','tdb.cretaed_at as tanggal','mpel.nama as nama_pelanggan','tpd.*','mp.id_type_ukuran','mp.nama as nama_produk','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan','tk.catatan')
					->groupBy('tpd.id_produk')
					->orderBy('tpd.id_kasir_detail_produk','asc');

				$d_barang = DB::table('tbl_kasir_detail_batal as tkd')
					->leftjoin('tbl_barang as tb','tkd.id_barang','tb.barang_id')
					->leftjoin('tbl_satuan as ts2','tkd.id_satuan','ts2.satuan_id')
					->join('tbl_kasir_batal as tk', 'tk.id_kasir','tkd.id_kasir')
					->join('ref_gudang as rg', 'tk.id_gudang', 'rg.id')
					->leftjoin('m_pelanggan as mpel','tk.id_pelanggan','mpel.id')
					->join('users as us','us.id','tk.deleted_iduser')
					// ->where('tkd.id_kasir',$id)
					->whereRaw($where)
					->where('tkd.id_detail_kasir_produk','0')
					->select('us.name as nama_hapus','rg.nama as nama_gudang','tk.no_faktur','mpel.telp as telp_pelanggan','tkd.cretaed_at as tanggal','mpel.nama as nama_pelanggan','tkd.*','tb.barang_nama as nama_barang','tb.barang_kode as kode_barang','tb.barang_alias as alias_barang','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan','tk.catatan')
					->groupBy('tkd.id_barang')
					->orderBy('tkd.id_detail_kasir','asc');
				
				if($d_data->count() > 0){
					foreach($d_data->get() as $d){
						$arr['produk'][] = array('id' => $d->id_kasir_detail_produk,
										'id_detail_kasir'  => "",
										'id_kasir'  	=> $d->id_kasir,
										'id_produk'     => $d->id_produk,
										'nama_produk'   => $d->nama_produk,
										'jumlah'        => $d->jumlah,
										'id_satuan'     => $d->id_satuan,
										'nama_satuan'   => $d->satuan_satuan,
										'satuan_satuan' => $d->satuan_satuan,
										'harga'         => $d->harga,
										'total'         => $d->total,
										'poin'          => $d->poin,
										'potongan'      => ($d->harga == 0) ? 0:$d->potongan,
										'potongan_total'=> ($d->harga == 0) ? 0:$d->potongan_total,
										'id_tipe'       => $d->id_type_ukuran ?? '',
										'status'        => "1",
										'telp_pelanggan' => $d->telp_pelanggan,
										'nama_pelanggan' 	=> $d->nama_pelanggan,
										'tanggal'		=> $d->tanggal,
										'no_faktur'		=> $d->no_faktur,
										'nama_gudang'	=> $d->nama_gudang,
										'nama_hapus'	=> $d->nama_hapus,
										'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem));
					}
				}else{
					$arr['produk'] = array();
				}

				if($d_barang->count() > 0){
					foreach($d_barang->get() as $d){
						$arr['barang'][] = array('id' 	=> $d->id_detail_kasir,
										'id_kasir'  	=> $d->id_kasir,
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
										'poin'          => "0",
										'potongan'      => "0",
										'potongan_total'=> "0",
										'id_tipe'       => "3",
										'status'        => "2",
										'telp_pelanggan' => $d->telp_pelanggan,
										'nama_pelanggan' 	=> $d->nama_pelanggan,
										'tanggal'		=> $d->tanggal,
										'no_faktur'		=> $d->no_faktur,
										'nama_gudang'	=> $d->nama_gudang,
										'nama_hapus'	=> $d->nama_hapus,
										'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem));
					}
				}else{
					$arr['barang'] = array();
				}
				
				$total_produk = count($arr['produk']);
				$total_barang = count($arr['barang']);
	  
				$no = 1;
				$arrdata = array();
				if($total_barang != 0) {
					foreach ($arr['barang'] as $d){
						if($d['telp_pelanggan'] != '' || $d['telp_pelanggan'] != null){
							$pelanggan = $d['nama_pelanggan'].'/'.$d['telp_pelanggan'];
						}else{
							$pelanggan = $d['nama_pelanggan'];
						}
						$arrdata[] = array('NO.'=>$no++,
										'TANGGAL' 				=> tgl_full($d['tanggal'],'0'),
										'NO FAKTUR'				=> $d['no_faktur'],
										'PELANGGAN'				=> $d['nama_pelanggan'],
										'GUDANG'				=> $d['nama_gudang'],
										'NAMA PRODUK'			=> $d['nama_barang'],
										'JUMLAH'				=> $d['jumlah'],
										'TOTAL'				=> 'Rp '.format_angka($d['total']),
										'DIBATALKAN OLEH'		=> $d['nama_hapus']);
					}
				}

				if($total_produk != 0) {
					foreach ($arr['produk'] as $key => $s){
						if($s['telp_pelanggan'] != '' || $s['telp_pelanggan'] != null){
							$pelanggan = $s['nama_pelanggan'].'/'.$s['telp_pelanggan'];
						}else{
							$pelanggan = $s['nama_pelanggan'];
						}
						$arrdata[] = array('NO.'=>$no++,
										'TANGGAL' 				=> tgl_full($s['tanggal'],'0'),
										'NO FAKTUR'				=> $s['no_faktur'],
										'PELANGGAN'				=> $s['nama_pelanggan'],
										'GUDANG'				=> $s['nama_gudang'],
										'NAMA PRODUK'			=> $s['nama_produk'],
										'JUMLAH'				=> $s['jumlah'],
										'TOTAL'					=> 'Rp '.format_angka($s['total']),
										'DIBATALKAN OLEH'		=> $s['nama_hapus']);
					}
				}

				if($total_produk+$total_barang > 0 ){
					$data = $arrdata;
				}else{
					$data = array("0"=>['NO.'					=> '',
										'TANGGAL' 				=> '',
										'NO FAKTUR'				=> '',
										'PELANGGAN'				=> '',
										'GUDANG'				=> '',
										'NAMA PRODUK'			=> '',
										'HARGA'					=> '',
										'JUMLAH'				=> '',
										'TOTAL'					=> '',
										'DIBATALKAN OLEH'		=> '']);
				}

				return Excel::download(new LaporanViewExport($data), 'Laporan_Pembatalan_PerNota_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    		break;
    		default:
    			# code...
    			break;
    	}
    	
    }

    public function hasil_pembatalan($d_gudang,$d_tanggalAwal, $d_tanggalAkhir, $d_kategori, $d_barang){
    	ini_set('max_execution_time', '0');
		$gudang 	= $d_gudang;
		$nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
    	$tanggal 	= tgl_full($d_tanggalAwal,'99');
    	$tanggal2 	= tgl_full($d_tanggalAkhir,'99');  
    	$kategori 	= $d_kategori;
    	$barang 	= $d_barang;

    	switch ($kategori) {
    		case '1':
				if($gudang != "" && $tanggal != ""){
					$where = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
				}elseif($gudang != "" && $tanggal == ""){
					$where = "WHERE tk.id_gudang = '".$gudang."'";
				}else{
					$where = "";
				}

				$d_data = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang, u.name as nama_hapus FROM tbl_kasir_batal AS tk  JOIN users AS u ON tk.deleted_iduser = u.id LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id $where AND tk.jenis_transaksi=1 AND tk.id_gudang IN ($gudang) ORDER BY tk.tanggal_faktur DESC");

				$data['tanggal_awal'] = $tanggal;
				$data['tanggal_akhir'] = $tanggal2;
				$data['hari']    = tgl_full(date('Y-m-d'),'hari');
				$data['kasir']   = Auth::user()->name;
				$data['gudang']	 = $nama_gudang;

				$no = 1;
				$arr = array();
					foreach ($d_data as $key => $d){
						$arr[] = array('NO.'=>$no++,
										'TANGGAL' 				=> tgl_full($d->tanggal,'0'),
										'NO FAKTUR'				=> $d->no_faktur,
										'PELANGGAN'				=> $d->nama_pelanggan,
										'GUDANG'				=> $d->nama_gudang,
										'TOTAL PENJUALAN'		=> 'Rp '.format_angka($d->total_tagihan-($d->total_potongan-$d->ongkos_kirim)),
										'DIBATALKAN OLEH'		=> $d->nama_hapus,
										'CATATAN'				=> $d->catatan);
					}

				if(count($arr) > 0){
				$data = $arr;
				}else{
				$data = array("0"=>['NO.'					=> '',
									'TANGGAL' 				=> '',
									'NO FAKTUR'				=> '',
									'PELANGGAN'				=> '',
									'GUDANG'				=> '',
									'TOTAL PENJUALAN'		=> '',
									'DIBATALKAN OLEH'		=> '',
									'CATATAN'				=> '']);
				}

				return view('admin.laporan.hasil_laporan',compact('data'));
				break;
    		case '2':
				if($gudang != "" && $tanggal != ""){
					$where = "tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
				}elseif($gudang != "" && $tanggal == ""){
					$where = "tk.id_gudang = '".$gudang."'";
				}else{
					$where = "";
				}

				$d_data = DB::table('tbl_kasir_detail_produk_batal as tpd')
					->leftjoin('m_produk as mp','tpd.id_produk','mp.id')
					->leftjoin('tbl_satuan as ts2','tpd.id_satuan','ts2.satuan_id')
					->join('tbl_kasir_detail_batal as tdb', 'tdb.id_kasir', 'tpd.id_kasir')
					->join('tbl_kasir_batal as tk', 'tk.id_kasir','tdb.id_kasir')
					->join('ref_gudang as rg', 'tk.id_gudang', 'rg.id')
					->leftjoin('m_pelanggan as mpel','tk.id_pelanggan','mpel.id')
					->join('users as us','us.id','tk.deleted_iduser')
					// ->where('tpd.id_kasir',$id)
					->whereRaw($where)
					->select('us.name as nama_hapus','rg.nama as nama_gudang','tk.no_faktur','mpel.telp as telp_pelanggan','tdb.cretaed_at as tanggal','mpel.nama as nama_pelanggan','tpd.*','mp.id_type_ukuran','mp.nama as nama_produk','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')
					->groupBy('tpd.id_produk')
					->orderBy('tpd.id_kasir_detail_produk','asc');

				$d_barang = DB::table('tbl_kasir_detail_batal as tkd')
					->leftjoin('tbl_barang as tb','tkd.id_barang','tb.barang_id')
					->leftjoin('tbl_satuan as ts2','tkd.id_satuan','ts2.satuan_id')
					->join('tbl_kasir_batal as tk', 'tk.id_kasir','tkd.id_kasir')
					->join('ref_gudang as rg', 'tk.id_gudang', 'rg.id')
					->leftjoin('m_pelanggan as mpel','tk.id_pelanggan','mpel.id')
					->join('users as us','us.id','tk.deleted_iduser')
					// ->where('tkd.id_kasir',$id)
					->whereRaw($where)
					->where('tkd.id_detail_kasir_produk','0')
					->select('us.name as nama_hapus','rg.nama as nama_gudang','tk.no_faktur','mpel.telp as telp_pelanggan','tkd.cretaed_at as tanggal','mpel.nama as nama_pelanggan','tkd.*','tb.barang_nama as nama_barang','tb.barang_kode as kode_barang','tb.barang_alias as alias_barang','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')
					->groupBy('tkd.id_barang')
					->orderBy('tkd.id_detail_kasir','asc');
				
				if($d_data->count() > 0){
					foreach($d_data->get() as $d){
						$arr['produk'][] = array('id' => $d->id_kasir_detail_produk,
										'id_detail_kasir'  => "",
										'id_kasir'  	=> $d->id_kasir,
										'id_produk'     => $d->id_produk,
										'nama_produk'   => $d->nama_produk,
										'jumlah'        => $d->jumlah,
										'id_satuan'     => $d->id_satuan,
										'nama_satuan'   => $d->satuan_satuan,
										'satuan_satuan' => $d->satuan_satuan,
										'harga'         => $d->harga,
										'total'         => $d->total,
										'poin'          => $d->poin,
										'potongan'      => ($d->harga == 0) ? 0:$d->potongan,
										'potongan_total'=> ($d->harga == 0) ? 0:$d->potongan_total,
										'id_tipe'       => $d->id_type_ukuran ?? '',
										'status'        => "1",
										'telp_pelanggan' => $d->telp_pelanggan,
										'nama_pelanggan' 	=> $d->nama_pelanggan,
										'tanggal'		=> $d->tanggal,
										'no_faktur'		=> $d->no_faktur,
										'nama_gudang'	=> $d->nama_gudang,
										'nama_hapus'	=> $d->nama_hapus,
										'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem));
					}
				}else{
					$arr['produk'] = array();
				}

				if($d_barang->count() > 0){
					foreach($d_barang->get() as $d){
						$arr['barang'][] = array('id' 	=> $d->id_detail_kasir,
										'id_kasir'  	=> $d->id_kasir,
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
										'poin'          => "0",
										'potongan'      => "0",
										'potongan_total'=> "0",
										'id_tipe'       => "3",
										'status'        => "2",
										'telp_pelanggan' => $d->telp_pelanggan,
										'nama_pelanggan' 	=> $d->nama_pelanggan,
										'tanggal'		=> $d->tanggal,
										'no_faktur'		=> $d->no_faktur,
										'nama_gudang'	=> $d->nama_gudang,
										'nama_hapus'	=> $d->nama_hapus,
										'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem));
					}
				}else{
					$arr['barang'] = array();
				}
				
				$total_produk = count($arr['produk']);
				$total_barang = count($arr['barang']);

				$no = 1;
				$arrdata = array();
				if($total_barang != 0) {
					foreach ($arr['barang'] as $d){
						if($d['telp_pelanggan'] != '' || $d['telp_pelanggan'] != null){
							$pelanggan = $d['nama_pelanggan'].'/'.$d['telp_pelanggan'];
						}else{
							$pelanggan = $d['nama_pelanggan'];
						}
						$arrdata[] = array('NO.'=>$no++,
										'TANGGAL' 				=> tgl_full($d['tanggal'],'0'),
										'NO FAKTUR'				=> $d['no_faktur'],
										'PELANGGAN'				=> $d['nama_pelanggan'],
										'GUDANG'				=> $d['nama_gudang'],
										'NAMA PRODUK'			=> $d['nama_barang'],
										'JUMLAH'				=> $d['jumlah'],
										'TOTAL'				=> 'Rp '.format_angka($d['total']),
										'DIBATALKAN OLEH'		=> $d['nama_hapus']);
					}
				}

				if($total_produk != 0) {
					foreach ($arr['produk'] as $key => $s){
						if($s['telp_pelanggan'] != '' || $s['telp_pelanggan'] != null){
							$pelanggan = $s['nama_pelanggan'].'/'.$s['telp_pelanggan'];
						}else{
							$pelanggan = $s['nama_pelanggan'];
						}
						$arrdata[] = array('NO.'=>$no++,
										'TANGGAL' 				=> tgl_full($s['tanggal'],'0'),
										'NO FAKTUR'				=> $s['no_faktur'],
										'PELANGGAN'				=> $s['nama_pelanggan'],
										'GUDANG'				=> $s['nama_gudang'],
										'NAMA PRODUK'			=> $s['nama_produk'],
										'JUMLAH'				=> $s['jumlah'],
										'TOTAL'					=> 'Rp '.format_angka($s['total']),
										'DIBATALKAN OLEH'		=> $s['nama_hapus']);
					}
				}

				if($total_produk+$total_barang > 0 ){
					$data = $arrdata;
				}else{
					$data = array("0"=>['NO.'					=> '',
										'TANGGAL' 				=> '',
										'NO FAKTUR'				=> '',
										'PELANGGAN'				=> '',
										'GUDANG'				=> '',
										'NAMA PRODUK'			=> '',
										'HARGA'					=> '',
										'JUMLAH'				=> '',
										'TOTAL'					=> '',
										'DIBATALKAN OLEH'		=> '']);
				}

				return view('admin.laporan.hasil_laporan',compact('data'));
    			break;
    		default:
    			# code...
    			break;
    	}	
    }   
}
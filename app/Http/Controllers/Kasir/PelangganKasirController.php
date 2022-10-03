<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent;

class PelangganKasirController extends Controller
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
        $where_pelanggan = "";
	        if(sizeof($id_gudang) > 0){
	          $gudang = implode(',',$id_gudang);
	          $where_gudang = "WHERE rf.id IN ($gudang)";
	          $where_pelanggan = "where id_gudang IN ($gudang)";
	        }

    	$data['gudang'] = DB::select(base_gudang($where));
        $data['group']  = Auth::user()->group_id;
        $data['pelanggan'] = DB::SELECT("Select * from m_pelanggan $where_pelanggan");

    	if ($this->agent->isMobile()){
    		return view('admin_mobile.kasir.index_pelanggan')->with('data',$data);
    	}else{
    		return view('admin.kasir.index_pelanggan')->with('data',$data);
    	}

        // return view('admin_mobile.kasir.index_pelanggan')->with('data',$data);
    }

    public function list_data(Request $request){
        $gudang     = $request->gudang;
        $pelanggan  = $request->pelanggan;
        $status = $request->status;
        

        if($gudang != "" && $pelanggan != ""){
        	$where = "WHERE id_gudang = '".$gudang."' and id_pelanggan = '".$pelanggan."'";
        }else{
        	$where = "";
        }

        $d_kasir = DB::SELECT("SELECT * FROM tbl_kasir $where");
        $id_kasir = array();
        foreach ($d_kasir as $k) {
        	# code...
        	$id_kasir[] = $k->id_kasir;
        }
        $where_produk = "";
        $where_barang = "";
        if(sizeof($id_kasir) > 0){
        	$kasir = implode(',',$id_kasir);
        	$where_produk = "WHERE tpd.id_kasir IN ($kasir)";
        	$where_barang = "AND tkd.id_kasir IN ($kasir)";
        }

        $data_produk = DB::SELECT("SELECT tpd.id_kasir_detail_produk, tk.tanggal_faktur, tpd.id_kasir, mp.nama as nama_produk, tpd.harga as harga, tpd.jumlah as jumlah, ts.satuan_satuan as nama_satuan  FROM tbl_kasir_detail_produk AS tpd LEFT JOIN tbl_kasir AS tk ON tpd.id_kasir=tk.id_kasir LEFT JOIN m_produk AS mp ON tpd.id_produk=mp.id LEFT JOIN tbl_satuan AS ts ON tpd.id_satuan=ts.satuan_id $where_produk");
        $data_barang = DB::SELECT("SELECT tkd.id_detail_kasir, tk.tanggal_faktur, tkd.id_kasir, tb.barang_nama as nama_barang, tkd.harga as harga, tkd.jumlah as jumlah, ts.satuan_satuan as nama_satuan  FROM tbl_kasir_detail AS tkd LEFT JOIN tbl_kasir AS tk ON tkd.id_kasir=tk.id_kasir LEFT JOIN tbl_barang AS tb ON tkd.id_barang=tb.barang_id LEFT JOIN tbl_satuan AS ts ON tkd.id_satuan=ts.satuan_id WHERE tkd.id_detail_kasir_produk='0' $where_barang");

        $arr = array();
        $arr2= array();
        if(count($data_produk) > 0){
        	foreach($data_produk as $d){
        		$arr[] = array("id" => $d->id_kasir_detail_produk,
                                "tanggal_faktur"=>tgl_full($d->tanggal_faktur,""),
        						"id_kasir" => $d->id_kasir,
        						"nama" => $d->nama_produk,
        						"jumlah" => $d->jumlah,
        						"satuan" => $d->nama_satuan,
        						"harga" => $d->harga,
        						"total" => ((float)$d->jumlah*(float)$d->harga));
        	}
        }
        if(count($data_barang) > 0){
        	foreach($data_barang as $d){
        		$arr2[] = array("id" => $d->id_detail_kasir,
                                "tanggal_faktur" => tgl_full($d->tanggal_faktur,""),
        						"id_kasir" => $d->id_kasir,
        						"nama" => $d->nama_barang,
        						"jumlah" => $d->jumlah,
        						"satuan" => $d->nama_satuan,
        						"harga" => $d->harga,
        						"total" => ((float)$d->jumlah*(float)$d->harga));
        	}
        }
        $marge = array_merge($arr,$arr2);
        $data = array();
        $data1 = array();
        $no = 1;
        if($status=="2"){
            foreach ($marge as $k) {
            	$data[] = array("id"=>$k['id'],
            					"id_kasir"=>$k['id_kasir'],
            					"nama"=>$k['nama'],
            					"jumlah"=>$k['jumlah'],
            					"satuan"=>$k['satuan'],
            					"harga"=>$k['harga'],
            					"total"=>$k['total']);
                $total= $k['harga']*$k['jumlah'];
                $row = array();
                if ($this->agent->isMobile()) {
                $row['no']      = $no++;
                $row['tanggal'] = $k['tanggal_faktur'];
                $row['nama']    = $k['nama'];
                $row['harga']   = "Rp ".format_angka($k['harga']);
                $row['jumlah']  = format_angka($k['jumlah'])." ".$k['satuan'];
                $row['total']   = "Rp ".format_angka($total);
                }else{
                $row[] = $no++;
                $row[] = $k['tanggal_faktur'];
                $row[] = $k['nama'];
                $row[] = "Rp ".format_angka($k['harga']);
                $row[] = format_angka($k['jumlah'])." ".$k['satuan'];
                $row[] = "Rp ".format_angka($total);
                }
                $data1[] = $row; 

            }
        }  
            
            $output = array("data" => $data1);
            return response()->json($output);
    }
}

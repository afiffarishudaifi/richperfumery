<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\BarangModel;
use App\SatuanModel;
use Intervention\Image\ImageManagerStatic as Image;
use QrCode;

class BarangPrintController extends Controller
{
    //	
    public function index(){
    	$id_profil = Auth::user()->id_profil;
	    $group = Auth::user()->group_id;
	    $where = "";
	        if($group == 5 || $group == 6){
	          $where = "WHERE id_profil='$id_profil'";
	        }
	    $data['gudang'] = DB::select(base_gudang($where));
	    $data['group']	= Auth::user()->group_id;
        $data['barang'] = BarangModel::where('barang_id_parent','!=','0')->where('barang_status_bahan','=','1')->get();
        $data['satuan'] = SatuanModel::orderBy('satuan_id', 'DESC')->get();
    	return view('admin.barang.index_barcode')->with("data",$data);
    }

    public function cetak_onemany(Request $request){
    	$id_barang = $request->get('barcode_barang');
        $id_satuan = $request->get('barcode_satuan');
        $barang = DB::table("tbl_barang")->where('barang_id',$id_barang)->first();
        $satuan = DB::table("tbl_satuan")->where('satuan_id',$id_satuan)->first();
        $n_data = $barang->barang_id."||".$barang->barang_nama."||".$satuan->satuan_id."||".$satuan->satuan_nama;
        $image = \QrCode::format('png')
                         ->merge(public_path('richperfumery2.png'), 0.2, true)
                         ->size(500)->errorCorrection('H')
                         ->generate($n_data);
        $barcode = base64_encode($image);
        $name = $barang->barang_id."_".$barang->barang_nama."_".$satuan->satuan_id."_".$satuan->satuan_nama;
        $file = public_path('barcode/') . $name . '.png';

        if(!file_exists($file)){
            $image_resize = Image::make($barcode);              
            $image_resize->resize(120, 120);
            $image_resize->save($file);
        }
        
        require(public_path('fpdf1813/Mc_table.php'));
        $data['barcode'] = $file;
        $data['barang'] = DB::table("tbl_barang")->where('barang_id',$id_barang)->first();
        $data['satuan'] = DB::table("tbl_satuan")->where('satuan_id',$id_satuan)->first();
        $html = view('admin.barang.cetak_barcode_perbarang')->with('data',$data);
        return response($html)->header('Content-Type', 'application/pdf');
    }

    public function cetak(Request $request){
        $e_barang= $request->get('id_barang');        
        $id_satuan = $request->get('id_satuan');

        if($e_barang[0] == "barang_all"){
            $cek_b = DB::table("tbl_barang")->where('barang_id_parent','!=','0')->where('barang_status_bahan','1')->get();
            foreach($cek_b as $key => $b){
                $id_barang[$key] = $b->barang_id;
            }
        }else{
            $id_barang = $request->get('id_barang');
        }
        
        $data = array();
        define('UPLOAD_DIR', public_path('barcode/'));
        
        $urut = 0;
        if(count($id_barang) > 0){
        for($i=0;$i<count($id_barang);$i++){
            $barang = DB::table("tbl_barang")->where('barang_id',$id_barang[$i])->first();
            $satuan = DB::table("tbl_satuan")->where('satuan_id',$id_satuan)->first();

            $n_data[$i] = $barang->barang_id."||".$barang->barang_nama."||".$satuan->satuan_id."||".$satuan->satuan_nama;
            $name[$i] = $barang->barang_id."_".preg_replace('/[^a-zA-Z0-9-]/', '', $barang->barang_nama)."_".$satuan->satuan_id."_".$satuan->satuan_nama;
            $file[$i] = public_path('barcode/') . $name[$i] . '.png';

            if(!file_exists($file[$i])){
            $image[$i] = \QrCode::format('png')
                         ->merge(public_path('richperfumery2.png'), 0.2, true)
                         ->size(500)->errorCorrection('H')
                         ->generate($n_data[$i]);
            $barcode[$i] = base64_encode($image[$i]);
                  $image_resize = Image::make($barcode[$i]);
                  $image_resize->resize(120, 120);
                  $image_resize->save($file[$i]);
            }
            
            $baris = $i / 6;
            $urut++;
            if ($urut==6) {
                $urut =0;
            }

            $input[$baris][$urut]['id_barang'] = $barang->barang_id;
            $input[$baris][$urut]['nama_barang'] = $barang->barang_nama;
            $input[$baris][$urut]['kode_barang'] = $barang->barang_kode;
            $input[$baris][$urut]['alias_barang'] = $barang->barang_alias;
            $input[$baris][$urut]['id_satuan'] = $satuan->satuan_id;
            $input[$baris][$urut]['nama_satuan'] = $satuan->satuan_nama;
            $input[$baris][$urut]['alias_satuan'] = $satuan->satuan_satuan;
            $input[$baris][$urut]['barcode'] = $file[$i];
            
        }
        }else{
            $input[$baris][$urut]['id_barang'] = "";
            $input[$baris][$urut]['nama_barang'] = "";
            $input[$baris][$urut]['kode_barang'] = "";
            $input[$baris][$urut]['alias_barang'] = "";
            $input[$baris][$urut]['id_satuan'] = "";
            $input[$baris][$urut]['nama_satuan'] = "";
            $input[$baris][$urut]['alias_satuan'] = "";
            $input[$baris][$urut]['barcode'] = "";
        }

        require(public_path('fpdf1813/Mc_table.php'));
        $data['data'] = $input;
        $html = view('admin.barang.cetak_barcode')->with('data',$data);
        return response($html)->header('Content-Type', 'application/pdf');

        
    }
}

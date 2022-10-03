<?php

namespace App\Http\Controllers\Pengiriman;

use Illuminate\Http\Request;
use DB;
use App\InvPengiriman;
use Redirect;
use App\Http\Controllers\Controller;
use Auth;

class IPengirimanController extends Controller
{
      /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $id_group = Auth::user()->group_id;
    $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
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
    // dd($gudang);
        $gudang['outlet'] = DB::select('SELECT
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
          WHERE p.jenis_outlet =2
        ORDER BY
          g.id ASC');
      $gudang['user_group'] = Auth::user()->group_id;
      $gudang['tombol_create'] = tombol_create('',$group_where->group_aktif,1);
    return view('admin.pengiriman.index', compact('gudang'));
  }
  
  public function hapus(Request $request)
  {
    DB::beginTransaction();
    $id = $request['id'];
    try{
    $menu = InvPengiriman::find($id);
    $menu -> delete();
    trigger_log($id,'Menghapus data Menu Nota Pengiriman',3,null);
    $id_log_stok = array();
    $id_detail = array();
    $detail = DB::table('pengiriman_detail')->where('id_inv_pengiriman',$id);
    if($detail->count()>0){
      foreach ($detail->get() as $key => $d) {
        DB::table('tbl_log_stok')->where(array('log_stok_id'=>$d->id_log_stok))->delete();
        DB::table('tbl_log_stok')->where(array('log_stok_id'=>$d->id_log_stok_penerimaan))->delete();
        DB::table('pengiriman_detail')->where(array('id'=>$d->id))->delete();
        $id_log_stok[] = $d->id_log_stok;
        $id_detail[] = $d->id;
      }
    }
    $status = array('status'=>1);
    DB::commit();
    }catch(\Exception $e){
      DB::rollback();
      $status = array('status'=>0);
    }
    return $status;
    //print_r($id_detail);exit();
      // return response()->json(array('status'=>'1'));
  }
  
  public function destroy($id)
  {
    //$id = "285";
    $menu = InvPengiriman::find($id);
    $menu -> delete();
    trigger_log($id,'Menghapus data Menu Header Pengiriman',3,null);
    
    /*$detail = DB::select("select * from pengiriman_detail where id_inv_pengiriman='$id'");
    foreach ($detail as $key => $value) {
      $d = DB::table('pengiriman_retur')->where(array('id_detail_pengiriman' => $value->id))->first();
      //log_stok retur
      if ($d) {
      DB::table('tbl_log_stok')->where(array('log_stok_id' => $d->id_log_stok))->delete();
      DB::table('tbl_log_stok')->where(array('log_stok_id' => $d->id_log_stok_penerimaan))->delete();

      }
      //logstok detail
      DB::table('tbl_log_stok')->where(array('log_stok_id' => $value->id_log_stok))->delete();
      DB::table('tbl_log_stok')->where(array('log_stok_id' => $value->id_log_stok_penerimaan))->delete();
      //detail
      DB::table('pengiriman_detail')->where(array('id' => $value->id))->delete();
      //retur
      DB::table('pengiriman_retur')->where(array('id_detail_pengiriman' => $value->id))->delete();

    }*/
    $id_log_stok = array();
    $id_detail = array();
    $detail = DB::table('pengiriman_detail')->where('id_inv_pengiriman',$id);
    if($detail->count()>0){
      foreach ($detail->get() as $key => $d) {
        DB::table('tbl_log_stok')->where(array('log_stok_id'=>$d->id_log_stok))->delete();
        DB::table('tbl_log_stok')->where(array('log_stok_id'=>$d->id_log_stok_penerimaan))->delete();
        DB::table('pengiriman_detail')->where(array('id'=>$d->id))->delete();
        $id_log_stok[] = $d->id_log_stok;
        $id_detail[] = $d->id;
      }
    }
    //print_r($id_detail);exit();
    return response()->json(array('status'=>'1'));
  }
  public function store(Request $request){

    $crud = $request['crud'];
    $a=0;
    $alert=array("","");
    // print_r($crud);exit;
    //1 = sedang dikirim
    //2 = terkirim
    //3 = diterima sebagian
    //4 = dikembalikan
    /*if ($crud =='tambah') {
      
      $program = new InvPengiriman;
      $program->kode_pengiriman = $request['kode'];
      $program->gudang_awal = $request['gudang_awal'];
      $program->gudang_tujuan = $request['tujuan'];
      $program->id_pengiriman = $request['pengiriman'];
      $program->status_pengiriman = 0;
      $program->tanggal_pengiriman = $request['tanggal'];
      $program->keterangan = $request['keterangan'];
      // $program->jumlah = $request['jumlah'];
      $a = $program-> save();
      // print_r($program);exit;
      $alert=array("Failed to create new data","New data created successfully");
    }elseif ($crud == 'edit') {
      $id = $request['id'];
      $program = InvPengiriman::find($id);
      $program->kode_pengiriman = $request['kode'];
      $program->gudang_awal = $request['gudang_awal'];
      $program->gudang_tujuan = $request['tujuan'];
      $program->id_pengiriman = $request['pengiriman'];
      // $program->status_pengiriman = 1;
      $program->tanggal_pengiriman = $request['tanggal'];
      $program->keterangan = $request['keterangan'];
      // $program->jumlah = $request['jumlah'];
      // print_r($program);exit;
      $a = $program -> update();
      $alert=array("Failed to update data","Data updated successfully");

      $d_data = DB::table('pengiriman_detail')->where('id_inv_pengiriman',$id);
      if($d_data->count() > 0){
        foreach($d_data->get() as $d){
          $data['tanggal'] = $request['tanggal'];
          DB::table('tbl_log_stok')->where('log_stok_id',$d->id_log_stok)->update($data);
        }
      }

    }*/
    // echo json_encode(array('result'=>$a,'alert'=>$alert[$a]));
    // print_r($request['kode']);exit;
    
    $data['kode_pengiriman']    = $request['kode'];
    $data['gudang_awal']        = $request['gudang_awal'];
    $data['gudang_tujuan']      = $request['tujuan'];
    $data['id_pengiriman']      = $request['pengiriman'];
    $data['tanggal_pengiriman'] = tgl_full($request['tanggal'],'99');
    $data['keterangan']         = $request['keterangan'];

    DB::beginTransaction();
    try{
        if($crud == 'tambah'){
          $data['status_pengiriman']  = 0;
        //   DB::table('pengiriman')->insert($data);
          $id = DB::table('pengiriman')->insertGetId($data);
          trigger_log($id,'Menambah data Menu Header Pengiriman',1,null);
          $status = array('status'=>'1');
        }else{ 
          $id = $request['id'];
          DB::table('pengiriman')->where('id',$id)->update($data);
    
          $d_data = DB::table('pengiriman_detail')->where('id_inv_pengiriman',$id);
          trigger_log($id,'Mengubah data Menu Header Pengiriman',2,null);
          
            if($d_data->count() > 0){
              foreach($d_data->get() as $d){
                $input['tanggal'] = $request['tanggal'];
                DB::table('tbl_log_stok')->where('log_stok_id',$d->id_log_stok)->update($input);
              }
            }
    
          $status = array('status'=>'2');
        }
        DB::commit();
    }catch(\Exception $e){
        DB::rollback();
        $status = array('status'=>'0');
    }
    
    return $status;
  }
  public function listData(){
    //   $program = InvPengiriman::orderBy('id', 'DESC')->get();
      $id_group = Auth::user()->group_id;
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $program = DB::select("SELECT
        i.id,
        i.kode_pengiriman,
        i.gudang_awal,
        i.gudang_tujuan,
        i.id_pengiriman,
        i.status_pengiriman,
        i.tanggal_pengiriman,
        r.nama AS gudang,
        r.alamat,
        g.nama AS tujuan,
        p.nama pengirim,
        pd.total,
        i.keterangan
        FROM
        pengiriman AS i
        JOIN ref_gudang AS r ON i.gudang_awal = r.id
        JOIN ref_gudang AS g ON i.gudang_tujuan = g.id
        LEFT JOIN m_pengirim AS p ON p.id = i.id_pengiriman
        LEFT JOIN (SELECT id_inv_pengiriman as id, sum(jumlah) as jumlah, sum(harga) as harga, sum(total) as total FROM pengiriman_detail GROUP BY id_inv_pengiriman) pd ON i.id=pd.id
        WHERE i.tanggal_pengiriman > DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) 
        AND i.tanggal_pengiriman <= DATE_SUB(DATE(NOW()), INTERVAL 0 DAY)");
      $no = 0;
      $data = array();
      foreach ($program as $list) {
          //0 = tambahkan detail barang
           //1 = sedang dikirim
           //2 = terkirim
           //3 = diterima sebagian
           //4 = dikembalikan
        if ($list->status_pengiriman == 0) {
               # code...
         $status = "<span class='label label-default'>Tambahkan Detail Barang</span";
        }elseif ($list->status_pengiriman == 1) {
           $status = "<span class='label label-warning'>sedang dikirim</span";
        }elseif ($list->status_pengiriman == 2) {
            $status = "<span class='label label-success'>terkirim</span";
        }elseif ($list->status_pengiriman == 3) {
            $status = "<span class='label label-primary'>Diterima Sebagian</span";
            # code...
        }elseif ($list->status_pengiriman == 4) {
            $status = "<span class='label label-danger'>Dikembalikan</span";
            # code...
        }
          $tanggal = date("d-m-Y", strtotime($list->tanggal_pengiriman));
          $tanggal1 = date("Y-m-d", strtotime($list->tanggal_pengiriman));
          $id_a = enc($list->id);
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = $list->kode_pengiriman;
          $row[] = $list->gudang;
          $row[] = $list->tujuan;
          $row[] = $tanggal;
          $row[] = $status ;
          if(Auth::user()->group_id==1||Auth::user()->group_id==6){
            $row[] = ($list->total==null)?"Rp. 0":"Rp. ".format_angka($list->total);
            }
          if($group_where->group_aktif == '2'){
          $row[] = '
          <a class=" btn btn-xs btn-success" href="detail_pengiriman?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-plus"></i></a>
          
          <a data-id="'.$list->id.'" data-kode_pengirimana="'.$list->kode_pengiriman.'"  data-gudang="'.$list->gudang.'" data-gudang_awal="'.$list->gudang_awal.'" data-tujuan="'.$list->tujuan.'" data-tanggal="'.$tanggal1 .'" data-alamat="'.$list->alamat.'" data-pengirim="'.$list->pengirim.'" data-nama="'.$list->gudang.'" data-gudang_tujuan="'.$list->gudang_tujuan.'" data-id_pengiriman="'.$list->id_pengiriman.'" data-keterangan="'.$list->keterangan.'" id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
          
          <a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>

          <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="botttom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>


          ';
        }else{
          $row[] = '
          <a class=" btn btn-xs btn-success" href="detail_pengiriman?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-plus"></i></a>
          
          <a data-id="'.$list->id.'" data-kode_pengirimana="'.$list->kode_pengiriman.'"  data-gudang="'.$list->gudang.'" data-gudang_awal="'.$list->gudang_awal.'" data-tujuan="'.$list->tujuan.'" data-tanggal="'.$tanggal1 .'" data-alamat="'.$list->alamat.'" data-pengirim="'.$list->pengirim.'" data-nama="'.$list->gudang.'" data-gudang_tujuan="'.$list->gudang_tujuan.'" data-id_pengiriman="'.$list->id_pengiriman.'" data-keterangan="'.$list->keterangan.'" id="btn_detail" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa  fa-eye"></i></a>

          <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="botttom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>


          ';
        }
          $data[] = $row;

      }
      $output = array("data" => $data);
      return response()->json($output);
  }

  public function searchtanggal(Request $request){
      $id_group = Auth::user()->group_id;
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $tanggalrange = explode('s.d.',$request->get('tanggal'));
      $tanggal_start  = tgl_full($tanggalrange[0],99);
      $tanggal_end    = tgl_full($tanggalrange[1],99);
      
      if($tanggal_start == $tanggal_end){
        $where = "WHERE DATE_FORMAT(i.tanggal_pengiriman,'%Y-%m-%d') = '$tanggal_end'";
      }else{
         $where = "WHERE DATE_FORMAT(i.tanggal_pengiriman,'%Y-%m-%d') >= '$tanggal_start'
        AND DATE_FORMAT(i.tanggal_pengiriman,'%Y-%m-%d') <= '$tanggal_end'";
      }
      $program = DB::select("SELECT
        i.id,
        i.kode_pengiriman,
        i.gudang_awal,
        i.gudang_tujuan,
        i.id_pengiriman,
        i.status_pengiriman,
        i.tanggal_pengiriman,
        r.nama AS gudang,
        r.alamat,
        g.nama AS tujuan,
        p.nama pengirim,
        pd.total,
        i.keterangan
        FROM
        pengiriman AS i
        JOIN ref_gudang AS r ON i.gudang_awal = r.id
        JOIN ref_gudang AS g ON i.gudang_tujuan = g.id
        LEFT JOIN m_pengirim AS p ON p.id = i.id_pengiriman
        LEFT JOIN (SELECT id_inv_pengiriman as id, sum(jumlah) as jumlah, sum(harga) as harga, sum(total) as total FROM pengiriman_detail GROUP BY id_inv_pengiriman) pd ON i.id=pd.id
        $where
        ");
      $no = 0;
      $data = array();
      foreach ($program as $list) {
          //0 = tambahkan detail barang
           //1 = sedang dikirim
           //2 = terkirim
           //3 = diterima sebagian
           //4 = dikembalikan
        if ($list->status_pengiriman == 0) {
               # code...
         $status = "<span class='label label-default'>Tambahkan Detail Barang</span";
        }elseif ($list->status_pengiriman == 1) {
           $status = "<span class='label label-warning'>sedang dikirim</span";
        }elseif ($list->status_pengiriman == 2) {
            $status = "<span class='label label-success'>terkirim</span";
        }elseif ($list->status_pengiriman == 3) {
            $status = "<span class='label label-primary'>Diterima Sebagian</span";
            # code...
        }elseif ($list->status_pengiriman == 4) {
            $status = "<span class='label label-danger'>Dikembalikan</span";
            # code...
        }
          $tanggal = date("d-m-Y", strtotime($list->tanggal_pengiriman));
          $tanggal1 = date("Y-m-d", strtotime($list->tanggal_pengiriman));
          $id_a = enc($list->id);
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = $list->kode_pengiriman;
          $row[] = $list->gudang;
          $row[] = $list->tujuan;
          $row[] = $tanggal;
          $row[] = $status ;
          if(Auth::user()->group_id==1||Auth::user()->group_id==6){
            $row[] = ($list->total==null)?"Rp. 0":"Rp. ".format_angka($list->total);
            }
          if($group_where->group_aktif=='2'){
          $row[] = '
          <a class=" btn btn-xs btn-success" href="detail_pengiriman?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-plus"></i></a>
          
          <a data-id="'.$list->id.'" data-kode_pengirimana="'.$list->kode_pengiriman.'"  data-gudang="'.$list->gudang.'" data-gudang_awal="'.$list->gudang_awal.'" data-tujuan="'.$list->tujuan.'" data-tanggal="'.$tanggal1 .'" data-alamat="'.$list->alamat.'" data-pengirim="'.$list->pengirim.'" data-nama="'.$list->gudang.'" data-gudang_tujuan="'.$list->gudang_tujuan.'" data-id_pengiriman="'.$list->id_pengiriman.'" data-keterangan="'.$list->keterangan.'" id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa  fa-edit"></i></a>

          <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="botttom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>


          ';
        }else{
          $row[] = '
          <a class=" btn btn-xs btn-success" href="detail_pengiriman?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-plus"></i></a>
          
          <a data-id="'.$list->id.'" data-kode_pengirimana="'.$list->kode_pengiriman.'"  data-gudang="'.$list->gudang.'" data-gudang_awal="'.$list->gudang_awal.'" data-tujuan="'.$list->tujuan.'" data-tanggal="'.$tanggal1 .'" data-alamat="'.$list->alamat.'" data-pengirim="'.$list->pengirim.'" data-nama="'.$list->gudang.'" data-gudang_tujuan="'.$list->gudang_tujuan.'" data-id_pengiriman="'.$list->id_pengiriman.'" data-keterangan="'.$list->keterangan.'" id="btn_detail" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-eye"></i></a>
          
          <a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>

          <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="botttom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>


          ';
        }
          $data[] = $row;

      }
      $output = array("data" => $data);
      return response()->json($output);
  }

  function action_status(){
    $status = '<a class=" btn btn-xs btn-info" href="detail_pengiriman?id='.$list->id.'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-search"></i></a>
          <a data-id="'.$list->id.'" data-alamat="'.$list->alamat.'" data-nama="'.$list->gudang.'" id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
          
          <a  data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>
          ';
      return $status;
  }
  // function detail_barang(Request $request){
  //   dd($request['id']);
  //   $gudang['gudang'] = DB::table('ref_gudang')->where('jenis_gudang', '1')->get();
  //   $gudang['outlet'] = DB::table('ref_gudang')->where('jenis_gudang', '1')->get();
  //   return view('admin.pengiriman.detail', compact('gudang'));
  // }
  function select2(Request $request){
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
        $barangs= DB::select("SELECT * from m_pengirim p WHERE LOWER(p.nama) LIKE '%$search%' ");

        // $formatted_tags = [];

        // foreach ($barangs as $barang) {
        //     $formatted_tags[] = ['id' => $barang->barang_id,'satuan'=>$barang->satuan_nama, 'text' => $barang->barang_kode];
        // }

        return \Response::json($barangs);
  }
  public function cetak(Request $request)
  {
   $id = dec($request['id']);
   $data['pengirim'] = DB::select("SELECT
        i.id,
        i.kode_pengiriman,
        i.gudang_awal,
        i.gudang_tujuan,
        i.id_pengiriman,
        i.status_pengiriman,
        i.tanggal_pengiriman,
        r.nama AS gudang,
        r.alamat,
        g.nama AS tujuan,
        p.nama pengirim
        FROM
        pengiriman AS i
        JOIN ref_gudang AS r ON i.gudang_awal = r.id
        JOIN ref_gudang AS g ON i.gudang_tujuan = g.id
        LEFT JOIN m_pengirim AS p ON p.id = i.id_pengiriman where i.id='$id'");
        // print_r($data['pengirim']);exit;
      $data['barang'] = DB::select("SELECT
          d.id,
          d.id_inv_pengiriman,
          d.id_barang,
          d.nama,
          d.jumlah,
          d.`status`,
          d.retur,
          d.diterima,
          b.barang_kode,
          b.barang_nama,
          s.satuan_nama,
          s.satuan_satuan 
        FROM
          pengiriman_detail AS d
          LEFT JOIN tbl_barang AS b ON d.id_barang = b.barang_id
          LEFT JOIN tbl_satuan AS s ON b.satuan_id = s.satuan_id 
        WHERE
          d.id_inv_pengiriman = '$id'");
    trigger_log($id,'Mencetak nota pengiriman',7,null);

    return view('admin.pengiriman.cetakno',compact('data'));
  }

 
   
}
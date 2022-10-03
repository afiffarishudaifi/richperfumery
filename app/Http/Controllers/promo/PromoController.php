<?php

namespace App\Http\Controllers\promo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PromoController extends Controller
{
    //
    public function index()
    {
        // $data = DB::table('m_produk')->get();
        if(request()->ajax()){
            $data = DB::table('m_produk_mapping')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $actionBtn = 
                    // '<button type="button" class="delete btn btn-warning btn-sm" id="btn_edit" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i> Edit</button>'.
                    // '<button type="button" class="delete btn btn-danger btn-sm" id="btn_hapus" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i> Hapus</button>'.
                    // '<input type="hidden" id="id'.$row->id.'" value="'.$row->id.'">';

                    '<div class="btn-group">
                        <button type="button" class="btn btn-primary btn-xs" id="btn_edit" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>
                    </div>';
                
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }else{
            return view('admin.promo.index');
        }
    }
    public function edit($id){
        $data = DB::table('m_produk_mapping')->where('id', $id)->first();

        return response()->json($data);
    }
    public function simpan(Request $req){
        $id = $req->get('id');
        $data['id_type_ukuran'] = $req->get('id_type_ukuran');
        $data['harga'] = $req->get('harga');
        $data['updated_at'] = Carbon::now();

        DB::table('m_produk_mapping')->where('id', $id)->update($data);
    }
}

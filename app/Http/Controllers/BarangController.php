<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use RealRashid\SweetAlert\Facades\Alert;
use App\Barang;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('barang.index');
    }

    public function store(Request $request)
    {
        $name = Barang::where('nama_barang',$request->name)->first();
        if($name != null){
            Alert::error('Error', 'Barang sudah tersedia!');
            return redirect()->back();
        }
        $price = str_replace(".", "", $request->harga);
        $admin = Auth::id();    
        $insert = Barang::insert([
            'nama_barang' => $request->name,
            'harga' => $price,
            'stock' => $request->stock,
            'admin_id' => $admin,
            'created_at' => now()
        ]);
        return redirect()->back()->with('success', 'Barang berhasil dibuat !');
    }

    public function update(Request $request)
    {
        $name = Barang::where('id',$request->id)->first();
        $price = str_replace(".", "", $request->harga);
        if($name != null){
            if($name->nama_barang == $request->name){
                $admin = Auth::id();    
                $insert = Barang::where('id',$request->id)->update([
                    'nama_barang' => $request->name,
                    'harga' => $price,
                    'stock' => $request->stock,
                    'admin_id' => $admin,
                    'updated_at' => now()
                ]);
                return redirect()->back()->with('success', 'Barang berhasil diubah !');
            }else{                
                Alert::error('Error', 'Barang sudah tersedia , Gagal diubah!');
                return redirect()->back();
            }
        }

        $admin = Auth::id();    
        $insert = Barang::where('id',$request->id)->update([
            'nama_barang' => $request->name,
            'harga' => $price,
            'admin_id' => $admin,
            'updated_at' => now()
        ]);
        return redirect()->back()->with('success', 'Barang berhasil diubah !');
    }

    public function delete($id)
    {
        $hapus = Barang::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus !');
    }

    public function table(Request $request)
    {
        if($request['kosong'] != null){
            $data = Barang::where('stock',0)->get();
        }else{
            $data = Barang::get();
        }
        

        return datatables($data)
        ->addColumn('nama_barang', function($data){
            return $data->nama_barang;
        })
        ->editColumn('harga', function($data){
            return "Rp.".number_format($data->harga, 0, ',', '.');
        })
        ->editColumn('stock', function($data){
            return $data->stock;
        })
        ->addColumn('action', function($data){
            $hapus = "Apakah kamu yakin menghapus ini ?";
            return '<td>
                    <button 
                    data-url="'.route('barang.update').'"
                    data-id="'.$data->id.'" 
                    data-name="'.$data->nama_barang.'"
                    data-harga="'.$data->harga.'"
                    class="btn btn-success btn-xs" data-bs-toggle="modal" data-bs-target="#updateData">Edit</button>

                    <a class="btn btn-danger btn-xs" 
                        href="'.route('barang.delete',[$data->id]).'"
                        onclick="return confirm('.$hapus.')">Hapus</a>
                    </td>';
        })
        ->rawColumns(['nama_barang','action']) ->addIndexColumn()->toJson();
    }
}

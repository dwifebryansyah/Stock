<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use RealRashid\SweetAlert\Facades\Alert;
use App\Stock;
use App\Barang;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $dataBarang = Barang::get();
        return view('stock.index',['dataBarang' => $dataBarang]);
    }

    public function store(Request $request)
    {
        if($request->jumlah <= 0){
            Alert::error('Error', 'Tidak bisa menambahkan stock kosong!');
            return redirect()->back();
        }
        
        $kdtrx = Stock::latest('id')->first();
        if ($kdtrx != null) {
            $substring = substr($kdtrx->kode_trx, 3);
        }else{
            $substring = 0;
        }

        $kode = $substring + 1;
        $price = str_replace(".", "", $request->harga);
        $admin = Auth::id();

        $dataBarang = Barang::where('id',$request->barang)->first();
        if($request->flexRadioDefault == "tambah"){
            $type = "1";
            $stok = $dataBarang->stock + $request->jumlah;
        }else{
            $type = "0";
            $stok = $dataBarang->stock - $request->jumlah;
            if($stok < 0){
                Alert::error('Error', 'Stock tidak tersedia!');
                return redirect()->back();
            }
        }
        // dd($type);

        $insertStock = Stock::insert([
            'kode_trx' =>'Trx'.$kode,
            'barang_id' => $request->barang,
            'date' => $request->date,
            'type' => $type,
            'harga' => $price,
            'jumlah' => $request->jumlah,
            'stock' => $stok,
            'admin_id' => $admin,
            'created_at' => now()
        ]);

        $updateBarang = Barang::where('id',$request->barang)->update([
            'stock' =>$stok,
        ]);
        return redirect()->back()->with('success', 'Stock berhasil dibuat !');
    }

    public function update(Request $request)
    {
        if($request->jumlah <= 0){
            Alert::error('Error', 'Tidak bisa menambahkan stock kosong!');
            return redirect()->back();
        }

        $price = str_replace(".", "", $request->harga);
        $admin = Auth::id();

        $dataStock = Stock::where('id',$request->id)->first();
        // dd($request->barang);
        if($request->flexRadioDefault == "1"){            
    
            $type = "1";
        
            // Cek Kesamaan Barang Duls
            if($dataStock->barang_id == $request->barang){

                $dataBarang = Barang::where('id',$request->barang)->first();
                $stockTampung = $dataStock->jumlah;
                $stockBarang = $dataBarang->stock;
                $stockUpdate = $request->jumlah;
                $totalStock = ($stockBarang + $stockUpdate) - $stockTampung;

                // Cek Total Stock apakah menjadi 0 
                if($totalStock < 0){
                    Alert::error('Error', 'Stok yang tersedia lebih kecil dari yang dihilangkan!');
                    return redirect()->back(); 
                }
    
                $updateData = Barang::where('id',$request->barang)->update([
                    'stock' => $totalStock
                ]);
    
                $updateStock = Stock::where('id',$request->id)->update([
                    'jumlah' => $request->jumlah,
                    'barang_id' => $request->barang,
                    'date' => $request->date,
                    'type' => $type,
                    'harga' => $price,
                    'stock' => $totalStock,
                    'admin_id' => $admin,
                ]);
    
            }else{
    
                $dataBarangOld = Barang::where('id',$dataStock->barang_id)->first();
                $stockTampung = $dataBarangOld->stock;
                $stockHapus = $dataStock->jumlah;
                $stockSum = $stockTampung - $stockHapus;

                // Cek Total Stock apakah menjadi 0 
                if($stockSum < 0){
                Alert::error('Error', 'Stok barang kamu yang sebelum update kurang dari 0!');
                    return redirect()->back(); 
                }
    
                $dataBarang = Barang::where('id',$request->barang)->first();
                $stockBarang = $dataBarang->stock;
                $stockUpdate = $request->jumlah;
                $totalStock = $stockBarang + $stockUpdate;
    
                $updateOldData = Barang::where('id',$dataStock->barang_id)->update([
                    'stock' => $stockSum
                ]);
    
                $updateNewData = Barang::where('id',$request->barang)->update([
                    'stock' => $totalStock
                ]);
    
                $updateStock = Stock::where('id',$request->id)->update([
                    'jumlah' => $request->jumlah,
                    'barang_id' => $request->barang,
                    'date' => $request->date,
                    'type' => $type,
                    'harga' => $price,
                    'stock' => $stockSum,
                    'admin_id' => $admin,
                ]);

            }


        }else{
            
            $type = "0";
        
            // Cek Kesamaan Barang Duls
            if($dataStock->barang_id == $request->barang){

                $dataBarang = Barang::where('id',$request->barang)->first();
                $stockTampung = $dataStock->jumlah;
                $stockBarang = $dataBarang->stock;
                $stockUpdate = $request->jumlah;
                $totalStock = ($stockBarang - $stockUpdate) + $stockTampung;

                // Cek Total Stock apakah menjadi 0 
                if($totalStock < 0){
                    Alert::error('Error', 'Stok yang tersedia lebih kecil dari yang dihilangkan!');
                    return redirect()->back(); 
                }
    
                $updateData = Barang::where('id',$request->barang)->update([
                    'stock' => $totalStock
                ]);
    
                $updateStock = Stock::where('id',$request->id)->update([
                    'jumlah' => $request->jumlah,
                    'barang_id' => $request->barang,
                    'date' => $request->date,
                    'type' => $type,
                    'stock' => $totalStock,
                    'harga' => $price,
                    'admin_id' => $admin,
                ]);
    
            }else{
    
                $dataBarangOld = Barang::where('id',$dataStock->barang_id)->first();
                $stockTampung = $dataBarangOld->stock;
                $stockHapus = $dataStock->jumlah;
                $stockSum = $stockTampung + $stockHapus;

                // Cek Total Stock apakah menjadi 0 

                $dataBarang = Barang::where('id',$request->barang)->first();
                $stockBarang = $dataBarang->stock;
                $stockUpdate = $request->jumlah;
                $totalStock = $stockBarang - $stockUpdate;

                if($totalStock < 0){
                    Alert::error('Error', 'Stok barang kamu yang sebelum update kurang dari 0!');
                        return redirect()->back(); 
                }
    
                $updateOldData = Barang::where('id',$dataStock->barang_id)->update([
                    'stock' => $stockSum
                ]);
    
                $updateNewData = Barang::where('id',$request->barang)->update([
                    'stock' => $totalStock
                ]);
    
                $updateStock = Stock::where('id',$request->id)->update([
                    'jumlah' => $request->jumlah,
                    'barang_id' => $request->barang,
                    'date' => $request->date,
                    'type' => $type,
                    'harga' => $price,
                    'stock' => $stockSum,
                    'admin_id' => $admin,
                ]);

            }
            
        }

        return redirect()->back()->with('success', 'Stock berhasil diupdate !');
    }

    public function delete($id)
    {
        $dataStock = Stock::where('id',$id)->first();
        $dataBarang = Barang::where('id',$dataStock->barang_id)->first();

        $stockDeleted = $dataStock->jumlah;
        $stockNow = $dataBarang->stock;

        if($dataStock->type == 1){
            $stockUpdate = $stockNow - $stockDeleted;
            if($stockUpdate < 0){
                Alert::error('Error', 'Stock kamu sudah kepakai!');
                return redirect()->back();
            }
        }else{
            $stockUpdate = $stockNow + $stockDeleted;
        }

        $update = Barang::where('id',$dataStock->barang_id)->update([
            'stock' => $stockUpdate
        ]);
        $delete = Stock::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus !');
    }

    public function detailData(Request $request){
        $dataBarang = Barang::where('id',$request->barangid)->get();

        foreach($dataBarang as $key => $value){
            $dataBarang[$key]->hargafix = number_format($value->harga, 0, ',', '.');
        }
        return response($dataBarang);
    }

    public function table()
    {
        $data = Stock::orderBy('id','desc')->get();
        $barang = Barang::get();

        return datatables($data)
        ->addColumn('kd_trx', function($data){
            return $data->kode_trx;
        })
        ->editColumn('barang', function($data){
            $barang = Barang::where('id',$data->barang_id)->first();
            return $barang->nama_barang;
        })
        ->editColumn('tanggal', function($data){
            return $data->date;
        })
        ->addColumn('type', function($data){
            if($data->type == "1"){
                return '<span class="badge badge-success" style="color:blue;"> Penambahan </span>';
            }else{
                return '<span class="badge badge-info" style="color:red;"> Pengurangan </span>';
            }
        })
        ->editColumn('harga', function($data){
            return "Rp.".number_format($data->harga, 0, ',', '.');
        })
        ->editColumn('jumlah', function($data){
            return $data->jumlah;
        })
        ->editColumn('stock', function($data){
            return $data->stock;
        })
        ->addColumn('action', function($data){
            $hapus = "Apakah kamu yakin menghapus ini ?";
            $barang = Barang::where('id',$data->barang_id)->first();
            $dataBarang = Barang::where('id','!=',$data->barang_id)->get();
            return '<td>
                    <button 
                    data-url="'.route('stok.update').'"
                    data-id="'.$data->id.'" 
                    data-barangid="'.$data->barang_id.'"
                    data-barang="'.$barang->nama_barang.'"
                    data-semuaBarang="'.$dataBarang.'"
                    data-harga="'.$data->harga.'"
                    data-stock="'.$barang->stock.'"
                    data-jumlah="'.$data->jumlah.'"
                    data-type="'.$data->type.'"
                    data-date="'.$data->date.'"
                    class="btn btn-success btn-xs" data-bs-toggle="modal" data-bs-target="#updateData">Edit</button>

                    <a class="btn btn-danger btn-xs" 
                        href="'.route('stok.delete',[$data->id]).'"
                        onclick="return confirm('.$hapus.')">Hapus</a>
                    </td>';
        })
        ->rawColumns(['nama_barang','type','action']) ->addIndexColumn()->toJson();
    }
}

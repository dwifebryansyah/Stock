<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use App\Stock;
use App\Barang;

use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(Excel $excel)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if($_GET){
            $harga = DB::table('stocks')
                ->where('date','>=',$_GET['mulai'])
                ->where('date','<=',$_GET['akhir'])
                ->sum(\DB::raw('jumlah * harga'));
        }else{
            $harga = DB::table('stocks')
                ->sum(\DB::raw('jumlah * harga'));
        }
        
        // dd($harga);
        return view('report.index',['total'=>$harga]);
    }

    public function table(Request $request)
    {
        if($request['mulai'] != null && $request['akhir'] != null){
            $data = Stock::where('date','>=',$request['mulai'])
                    ->where('date','<=',$request['akhir'])
                    ->orderBy('id','desc')->get();
        }else{
            $data = Stock::orderBy('id','desc')->get();
        }

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
            $barang = Barang::where('id',$data->barang_id)->first();
            return "Rp.".number_format($barang->harga, 0, ',', '.');
        })
        ->editColumn('stock', function($data){
            return $data->jumlah;
        })
        ->rawColumns(['nama_barang','type']) ->addIndexColumn()->toJson();
    }

    public function excel($mulai, $akhir)
    {
        return Excel::download(new LaporanExport($mulai,$akhir), 'Laporan.xlsx');
    }
}

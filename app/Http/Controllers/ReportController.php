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
            $dataStock = DB::table('stocks')
                        ->where('date','>=',$_GET['mulai'])
                        ->where('date','<=',$_GET['akhir'])
                        ->where('barang_id','<=',$_GET['barang'])
                        ->orderBy('id','desc')
                        ->first();
            if($dataStock!= null){
                $harga = $dataStock->harga * $dataStock->stock;
            }else{
                $harga = 0;
            }
        }else{
            $harga = DB::table('stocks')
                ->sum(\DB::raw('jumlah * harga'));
        }
        
        $dataBarang = Barang::get();
        return view('report.index',['total'=>$harga , 'dataBarang' => $dataBarang]);
    }

    public function table(Request $request)
    {
        if($request['mulai'] != null && $request['akhir'] != null){
            $data = Stock::where('date','>=',$request['mulai'])
                    ->where('date','<=',$request['akhir'])
                    ->where('barang_id',$request['barang'])
                    ->orderBy('id','desc')->get();
        }else{
            $data = DB::table('stocks')
                    ->select('barang_id')
                    ->distinct()
                    ->get();
            foreach($data as $key => $value){
                $dataStock = Stock::where('barang_id',$value->barang_id)->orderBy('id','desc')->first();
                $data[$key]->kode_trx = $dataStock->kode_trx;
                $data[$key]->date = $dataStock->date;
                $data[$key]->id = $dataStock->id;
                $data[$key]->type = $dataStock->type;
                $data[$key]->harga = $dataStock->harga;
                $data[$key]->jumlah = $dataStock->jumlah;
                $data[$key]->stock = $dataStock->stock;
            }
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
            return "Rp.".number_format($data->harga, 0, ',', '.');
        })
        ->editColumn('jumlah', function($data){
            return $data->jumlah;
        })
        ->editColumn('stock', function($data){
            return $data->stock;
        })
        ->rawColumns(['nama_barang','type']) ->addIndexColumn()->toJson();
    }

    public function excel($mulai, $akhir, $barang)
    {
        return Excel::download(new LaporanExport($mulai,$akhir,$barang), 'Laporan.xlsx');
    }
}

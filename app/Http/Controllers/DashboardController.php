<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Barang;
use App\Stock;
use App\Transaksi;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = Barang::where('stock',0)->count();
        return view('dashboard', ['data' => $data]);
    }
}

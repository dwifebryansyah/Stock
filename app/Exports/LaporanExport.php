<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use App\Stock;
use App\Barang;

class LaporanExport implements FromCollection , WithHeadings
{
    protected $mulai;
    protected $akhir;

    function __construct($mulai,$akhir,$barang) {
            $this->mulai = $mulai;
            $this->akhir = $akhir;
            $this->barang = $barang;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->mulai != 'null' && $this->akhir != 'null'){
            $data = Stock::where('date','>=',$this->mulai)
                    ->where('date','<=',$this->akhir)
                    ->where('barang_id',$this->barang)
                    ->orderBy('id','desc')->get();
        }else{
            $data = Stock::orderBy('id','desc')->get();
        }

        $data = collect($data)->map(function ($data, $key) {
            $collect = (object)$data;
            $barang = Barang::where('id',$collect->barang_id)->first();
            $total = $collect->harga * $collect->jumlah;
            if($collect->type == 1){
                $type = "Penambahan";
            }else{
                $type = "Pengurangan";
            }
            
            return [
                'KodeTransaksi' => $collect->kode_trx,
                'Tanggal' => $collect->date,
                'Barang' => $barang->nama_barang,
                'Jenis' => $type,
                'Harga' => $collect->harga,
                'Jumlah' => $collect->jumlah,
                'Total' => $total,
                'SisaStock' => $collect->stock
            ];
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            "KodeTransaksi",
            "Tanggal",
            "Barang",
            "Jenis",
            "Harga",
            "Jumlah",
            "Total",
            "SisaStock"
        ];
    }
}

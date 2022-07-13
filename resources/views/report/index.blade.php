@extends('layouts.nav')

@section('content')

@include('sweetalert::alert')

    <form action="{{ route('laporan.index') }}" method="GET">
        <div class="row mb-2">
            <div class="col-md-4">
                <label for="exampleInputEmail1" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" name="mulai" value="{{ @$_GET['mulai'] }}">
            </div>
            <div class="col-md-4">
                <label for="exampleInputEmail1" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" name="akhir" value="{{ @$_GET['akhir'] }}">
            </div>
            <div class="col-md-4">
                <label for="exampleInputEmail1" class="form-label">foreach</label>
                <select name="barang" id="barang" data-placeholder="Pilih Barang" class="form-control chosen-select" id="" required>
                    @foreach($dataBarang as $barang)
                    <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-xs">Mulai</button>
            </div>
            <div class="col-md-2">
                @if($_GET)
                @php
                    $mulai = $_GET['mulai'];
                    $akhir = $_GET['akhir'];
                    $barang = $_GET['barang'];
                @endphp
                <a href="{{ route('laporan.excel', ['mulai' => $mulai , 'akhir' => $akhir , 'barang_id' => $barang] )}}" type="button" class="btn btn-success btn-xs">Download Excel</a>
                @else
                <a href="{{ route('laporan.excel', ['mulai' => 'null' , 'akhir' => 'null', 'barang_id' => 'null' ] )}}"  type="button" class="btn btn-success btn-xs">Download Excel</a>
                @endif
            </div>
        </div>
    </form>
    <br>
    @if($_GET)
    <h4>Nilai Inventory adalah : Rp. {{ number_format($total, 0, ',', '.') }}</h4>
    @endif
    <table id="dataTable" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Jenis</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Sisa Stock</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
@endsection

@section('js')
<script>
        var $_GET = <?php echo json_encode($_GET); ?>;
        if($_GET){
            console.log('ada');   
            var mulai = $_GET['mulai'];
            var akhir = $_GET['akhir'];
            var barang = $_GET['barang'];
            if(mulai > akhir){
                alert("Periode akhir tidak sesuai lebih besar!");
                history.back()
            }
        }else{
            console.log('tidak ada');
            var mulai = null;
            var akhir = null;
            var barang = null;
        }
        $('.chosen-select').chosen({width:"100%"});

        $(document).ready(function() {
            dataProperty = $('#dataTable').DataTable({
                processing: true,
                scrollX: true,
                serverSide: true,
                ajax: {
                    url: '/tableLaporan',
                    type: 'GET',
                    data:{
                        "mulai": mulai,
                        "akhir": akhir,
                        "barang": barang,
                    },
                },
                pageLength: 25,
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kd_trx',
                        name: 'kd_trx',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'barang',
                        name: 'barang',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'harga',
                        name: 'harga',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'stock',
                        name: 'stock',
                        orderable: false,
                        searchable: true
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });
        });
    </script>
@endsection

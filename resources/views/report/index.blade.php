@extends('layouts.nav')

@section('content')

@include('sweetalert::alert')

    <form action="{{ route('laporan.index') }}" method="GET">
        <div class="row mb-2">
            <div class="col-md-6">
                <label for="exampleInputEmail1" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" name="mulai" value="{{ @$_GET['mulai'] }}">
            </div>
            <div class="col-md-6">
                <label for="exampleInputEmail1" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" name="akhir" value="{{ @$_GET['akhir'] }}">
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
                @endphp
                <a href="{{ route('laporan.excel', ['mulai' => $mulai , 'akhir' => $akhir] )}}" type="button" class="btn btn-success btn-xs">Download Excel</a>
                @else
                <a href="{{ route('laporan.excel', ['mulai' => 'null' , 'akhir' => 'null' ] )}}"  type="button" class="btn btn-success btn-xs">Download Excel</a>
                @endif
            </div>
        </div>
    </form>
    <br>
    <h4>Nilai Inventory adalah : Rp. {{ number_format($total, 0, ',', '.') }}</h4>
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
            if(mulai > akhir){
                alert("Periode akhir tidak sesuai lebih besar!");
                history.back()
            }
        }else{
            console.log('tidak ada');
            var mulai = null;
            var akhir = null;
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

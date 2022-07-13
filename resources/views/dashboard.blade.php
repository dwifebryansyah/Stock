@extends('layouts.nav')

@section('content')

    
        <div class="row">
            <div class="col-md-12 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h3>Selamat Datang , {{ Auth::user()->name }}</h3>

                        <div class="card">
                            <div class="card-header">
                                E1NSCIGO STORE
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Warning</h5>
                                <p class="card-text">Selalu Periksa Stok Anda.</p>
                            </div>
                        </div>
                        <br>
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Barang Habis ({{ $data }})</h5>
                                <p class="card-text">Segera Untuk Menambahkan Stok</p>
                                <a href="{{ route('barang.index','habis=true') }}" class="btn btn-primary">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection
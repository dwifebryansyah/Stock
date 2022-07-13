@extends('layouts.nav')

@section('content')

@include('sweetalert::alert')

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary mb-3 row" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Tambah Stock
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('stok.store') }}" method="post">
                {{ csrf_field() }}
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" id="date" placeholder="dd-mm-yyyy" value="" min="1997-01-01" max="2030-12-31">
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Barang</label>
                            <select name="barang" id="barang" data-placeholder="Choose a country..." class="form-control chosen-select" id="">
                                <option value="0"></option>
                                @foreach($dataBarang as $barang)
                                <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Harga</label>
                            <input type="text" class="form-control" name="harga" id="harga" required="" readonly="">
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Stok Sekarang</label>
                            <input type="text" class="form-control" name="stock" id="stock" required="" readonly="">
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Type</label>
                            <br>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" value="tambah">
                                <label class="form-check-label" for="flexRadioDefault1">
                                    Penambahan Stock
                                </label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" value="kurang" checked>
                                <label class="form-check-label" for="flexRadioDefault2">
                                    Pengurangan Stock
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Jumlah Stock</label>
                            <input type="number" class="form-control" name="jumlah" placeholder="Masukkan jumlah Stock">
                        </div>
                    </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                </div>
            </form>
        </div>
    </div>
    
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>

    <div class="modal fade" id="updateData" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="updateDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">
                <div class="modal-body">
                    Loading ...
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script>
        $('.chosen-select').chosen({width:"100%"});

        $(document).ready(function() {
            dataProperty = $('#dataTable').DataTable({
                processing: true,
                scrollX: true,
                serverSide: true,
                ajax: "/tableStock",
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
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });
        });
    </script>

    <script>
        $('#updateData').on('shown.bs.modal', function(e) {
        var token = $('meta[name="csrf-token"]').attr('content');
        
        
        // var databarang = '';
        // Array.prototype.forEach.call($(e.relatedTarget).data('databarang'), child => {
        //     console.log(child.nama_barang)
        // });

        var html = `
                    <div class="modal-header">
                        <h5 class="modal-title" id="buatPropertyLabel">Edit Property</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="${$(e.relatedTarget).data('url')}" method="post" enctype="multipart/form-data">
                    <input type="text" class="form-control" hidden="" name="_token" required="" value="${token}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control" id="date" value="${$(e.relatedTarget).data('date')}" placeholder="dd-mm-yyyy" value="" min="1997-01-01" max="2030-12-31">
                                <input type="text" class="form-control" name="id" id="id" hidden="" required="" readonly="" value="${$(e.relatedTarget).data('id')}"
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Barang</label>
                                <select name="barang" id="barang2" data-placeholder="Choose a country..." class="form-control chosen-select2" id="">
                                        <option value="${$(e.relatedTarget).data('barangid')}">${$(e.relatedTarget).data('barang')}</option>
                                    @foreach($dataBarang as $datanya)
                                        <option value="{{$datanya->id}}">{{$datanya->nama_barang}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Harga</label>
                                <input type="text" class="form-control" name="harga" id="hargaedit" required="" readonly="" value="${$(e.relatedTarget).data('harga')}">
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Stok Sekarang</label>
                                <input type="text" class="form-control" name="stock" id="stock2" required="" readonly="" value="${$(e.relatedTarget).data('stock')}">
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Type</label>
                                <br>`;
            if($(e.relatedTarget).data('type') == 1){
                var html1 =         `<div class="form-check">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" value="1" checked>
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Penambahan Stock
                                    </label>
                                    </div>`;
            }else{
                var html1 =         `<div class="form-check">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" value="0" checked>
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        Pengurangan Stock
                                    </label>
                                </div>`;
            }
            var html2 =         `</div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Jumlah Stock</label>
                                <input type="number" class="form-control" name="jumlah" placeholder="Masukkan jumlah Stock" value="${$(e.relatedTarget).data('jumlah')}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-xs">Simpan</button>
                        </div>
                    </form>
                `;
                
                $('#modal-content').html(html + html1 + html2);

                $('#barang2').on('change', function() {
                    console.log($('#barang').val());
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('stok.detail.barang') }}",
                        data : {
                            barangid:$('#barang2').val()
                        },
                        success: function(data) {
                            data.forEach(element => {
                                console.log(data);
                                $('#hargaedit').val(element.hargafix);
                                $('#stock2').val(element.stock);
                            });
                        }
                    });
                });

                $('.chosen-select2').chosen({width:"100%"});
            
        });

    </script>

    <script>
        $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#barang').on('change', function() {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('stok.detail.barang') }}",
                        data : {
                            barangid:$('#barang').val()
                        },
                        success: function(data) {
                            data.forEach(element => {
                                console.log(data);
                                $('#harga').val(element.hargafix);
                                $('#stock').val(element.stock);
                            });
                        }
                    });
                });

        });
    </script>

@endsection
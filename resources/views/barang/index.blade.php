@extends('layouts.nav')

@section('content')

@include('sweetalert::alert')

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary mb-3 row" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Tambah Buku
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('barang.store') }}" method="post">
                {{ csrf_field() }}
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" name="name" required="">
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Harga</label>
                            <input type="text" class="form-control" name="harga" id="harga" required="">
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Stock</label>
                            <input type="text" class="form-control" name="stock" value="0" readonly="">
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
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Stock</th>
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
        var $_GET = <?php echo json_encode($_GET); ?>;
        if($_GET){
            console.log('ada');   
            var habis = $_GET['habis'];
        }else{
            var habis = null;
        }

        $(document).ready(function() {
            dataProperty = $('#dataTable').DataTable({
                processing: true,
                scrollX: true,
                serverSide: true,
                ajax: {
                    url: '/tableBarang',
                    type: 'GET',
                    data:{
                        "kosong": habis,
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
                        data: 'nama_barang',
                        name: 'nama_barang',
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
        var html = `
                    <div class="modal-header">
                        <h5 class="modal-title" id="buatPropertyLabel">Edit Property</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="${$(e.relatedTarget).data('url')}" method="post" enctype="multipart/form-data">
                    <input type="text" class="form-control" hidden="" name="_token" required="" value="${token}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" name="name" required="" value="${$(e.relatedTarget).data('name')}">
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Harga</label>
                                <input type="text" class="form-control" name="harga" id="hargaedit" required="" value="${$(e.relatedTarget).data('harga')}">
                                <input type="text" class="form-control" name="id" id="id" hidden="" value="${$(e.relatedTarget).data('id')}">
                            </div>

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Stock</label>
                                <input type="text" class="form-control" name="stock" value="0" readonly="" ${$(e.relatedTarget).data('stock')}>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-xs">Simpan</button>
                        </div>
                    </form>
                `;

                $('#modal-content').html(html);
                
                var hargaedit = document.getElementById('hargaedit');

                hargaedit.addEventListener('keyup', function(e) {
                    hargaedit.value = formatRupiah(this.value, '');
                });


                /* Fungsi */
                function formatRupiah(angka, prefix) {
                    var number_string = angka.replace(/[^,\d]/g, '').toString(),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
                }
            
        });
    </script>

@endsection
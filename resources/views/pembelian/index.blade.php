{{-- resources/views/pembelian/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pembelian')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Pembelian</h5><small class="text-secondary">Manage purchase transactions</small>
                </div>
                <a href="{{ route('pembelian.create') }}" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i>
                    Pembelian Baru</a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white border rounded-3 p-3">
        <div class="table-responsive">
            {{-- Filter --}}
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" id="productSearch" class="form-control" placeholder="Search Pembelian...">
                </div>
            </div>
            <table id="purchaseTable" class="table table-bordered table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nama Pembeli</th>
                        <th>Total Harga</th>
                        <th style="width:130px;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    {{-- Delete modal --}}
    <div class="modal fade" id="purchaseDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Delete this purchase?</p>
                    <p class="mb-0"><strong id="purchaseDelNo">-</strong></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="purchaseDeleteForm" method="POST" action="">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger"><i class="bi bi-trash me-1"></i> Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>

    <script>
        $(function() {
            const table = $('#purchaseTable').DataTable({
                serverSide: true,
                processing: true,
                responsive: true,
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ route('pembelian.data') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [{
                        data: 'id'
                    }, {
                        data: 'no_transaksi'
                    }, {
                        data: 'tanggal'
                    }, {
                        data: 'nama_pembeli'
                    },
                    {
                        data: 'total_harga'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: "<'row mb-2'<'col-md-6'l><'col-md-6 text-end'B>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
                buttons: [{
                        extend: 'print',
                        className: 'btn btn-outline-dark btn-sm',
                        text: '<i class="bi bi-printer me-1"></i> Print'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-dark btn-sm',
                        text: '<i class="bi bi-filetype-csv me-1"></i> CSV'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-dark btn-sm',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel'
                    }
                ]
            });

            let t;
            $('#productSearch').on('input', function() {
                clearTimeout(t);
                const val = this.value;
                t = setTimeout(() => table.search(val).draw(), 300);
            });

            document.getElementById('purchaseDeleteModal').addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget;
                document.getElementById('purchaseDeleteForm').setAttribute('action', btn.getAttribute(
                    'data-route'));
                document.getElementById('purchaseDelNo').textContent = btn.getAttribute('data-no');
            });
        });
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Group Product')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Group Product</h5>
                    <small class="text-secondary">Manage product groups</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white border rounded-3 p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="mb-0">List Group Product</h6>
                <small class="text-secondary">Filter and manage group products</small>
            </div>
            <div>
                <a href="{{ route('groupproduct.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> New Group
                </a>
            </div>
        </div>

        <!-- Custom filter -->
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" id="gpSearch" class="form-control" placeholder="Search group (kode, name)...">
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="gpTable" class="table table-striped table-hover table-bordered align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>Kode</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="width:130px;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="modal fade" id="gpDeleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header bg-danger text-white">
                        <h6 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">Delete this group?</p>
                        <p class="mb-0"><strong id="gpDelName">-</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="gpDeleteForm" method="POST" action="">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger"><i class="bi bi-trash me-1"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('gpDeleteModal').addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget;
                const route = btn.getAttribute('data-route');
                const name = btn.getAttribute('data-name');
                document.getElementById('gpDeleteForm').setAttribute('action', route);
                document.getElementById('gpDelName').textContent = name ?? '-';
            });
        </script>
    </div>

    {{-- DataTables + Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(function() {
            const table = $('#gpTable').DataTable({
                serverSide: true,
                processing: true,
                responsive: true,
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ route('groupproduct.data') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'kode_groupproduct'
                    },
                    {
                        data: 'name_groupproduct'
                    },
                    {
                        data: 'status',
                        render: val => val === 'A' ?
                            '<span class="badge text-bg-success">Active</span>' :
                            '<span class="badge text-bg-secondary">Inactive</span>'
                    },
                    {
                        data: 'created_date'
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
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-outline-dark btn-sm',
                        text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF'
                    }
                ]
            });

            // Custom search
            $('#gpSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endsection

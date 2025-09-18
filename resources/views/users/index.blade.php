{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Users')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Users</h5>
                    <small class="text-secondary">Manage application users</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-success"><i class="bi bi-person-plus me-1"></i> New User</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white border rounded-3 p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="mb-0">List Users</h6>
                <small class="text-secondary">Filter and manage users</small>
            </div>
            <div>
                <a href="#" class="btn btn-success"><i class="bi bi-person-plus me-1"></i> New User</a>
            </div>
        </div>

        <!-- Custom filter -->
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" id="userSearch" class="form-control"
                    placeholder="Search user (username, email, fullname)...">
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="usersTable" class="table table-striped table-hover table-bordered align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Active</th>
                        <th>Created</th>
                        <th style="width:130px;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>


    {{-- DataTables + Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>

    <script>
        $(function() {
            const table = $('#usersTable').DataTable({
                serverSide: true,
                processing: true,
                responsive: true,
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ route('users.data') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'full_name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'role'
                    },
                    {
                        data: 'is_active',
                        render: val => val ?
                            '<span class="badge text-bg-success">Active</span>' :
                            '<span class="badge text-bg-secondary">Inactive</span>'
                    },
                    {
                        data: 'created_at'
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
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-secondary btn-sm'
                    }
                ]
            });

            // Custom search typing
            $('#userSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>

@endsection

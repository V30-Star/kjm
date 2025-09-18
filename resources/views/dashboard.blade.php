@extends('layouts.app')

@section('title', 'Dashboard')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Dashboard</h5>
                    <small class="text-secondary">Overview and quick actions</small>
                </div>
                <div class="d-none d-md-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Search...">
                    <button class="btn btn-sm btn-outline-secondary">Go</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-3">
                <div class="text-secondary small">Total Users</div>
                <div class="d-flex align-items-end justify-content-between mt-1">
                    <h4 class="mb-0">{{ number_format(\App\Models\User::count()) }}</h4>
                    <span class="badge text-bg-success">+2.1%</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-3">
                <div class="text-secondary small">Active Sessions</div>
                <div class="d-flex align-items-end justify-content-between mt-1">
                    <h4 class="mb-0">8</h4>
                    <span class="badge text-bg-primary">Live</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-3">
                <div class="text-secondary small">Orders (Today)</div>
                <div class="d-flex align-items-end justify-content-between mt-1">
                    <h4 class="mb-0">23</h4>
                    <small class="text-muted">~Rp 12.5jt</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-3">
                <div class="text-secondary small">Conversion</div>
                <div class="d-flex align-items-end justify-content-between mt-1">
                    <h4 class="mb-0">3.4%</h4>
                    <span class="badge text-bg-danger">-0.4%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-xl-8">
            <div class="bg-white border rounded-3 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Sales (Last 7 days)</h6>
                    <button class="btn btn-sm btn-outline-secondary">Export</button>
                </div>
                <div class="mt-3 d-grid" style="grid-template-columns: repeat(7, 1fr); gap:.5rem; height:220px;">
                    @php $bars=[24,36,18,44,28,40,52]; @endphp
                    @foreach ($bars as $b)
                        <div class="bg-success-subtle rounded-2 d-flex align-items-end">
                            <div class="w-100 bg-success rounded-2" style="height: {{ $b }}%;"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="bg-white border rounded-3 p-3">
                <h6 class="mb-2">Quick Actions</h6>
                <div class="row g-2">
                    <div class="col-6"><a href="#" class="btn btn-outline-secondary w-100"><i
                                class="bi bi-bag-plus me-1"></i>New Order</a></div>
                    <div class="col-6"><a href="#" class="btn btn-outline-secondary w-100"><i
                                class="bi bi-box-seam me-1"></i>Add Product</a></div>
                    <div class="col-6"><a href="#" class="btn btn-outline-secondary w-100"><i
                                class="bi bi-person-plus me-1"></i>Invite User</a></div>
                    <div class="col-6"><a href="#" class="btn btn-outline-secondary w-100"><i
                                class="bi bi-gear me-1"></i>Settings</a></div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-3 overflow-hidden">
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
            <h6 class="mb-0">Recent Activity</h6>
            <small class="text-muted">Last 10</small>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Action</th>
                        <th class="text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (range(1, 10) as $i)
                        <tr>
                            <td>2025-09-{{ sprintf('%02d', $i) }}</td>
                            <td>Admin</td>
                            <td>Login</td>
                            <td class="text-end"><span class="badge text-bg-success">OK</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

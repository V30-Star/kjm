<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'KJM Admin')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Alpine -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background: #f6f8fb;
        }

        .sidebar {
            width: 260px;
            background: #0f172a;
            color: #d1d5db;
        }

        .sidebar a.nav-link {
            color: #cbd5e1;
            border-radius: .5rem;
        }

        .sidebar a.nav-link.active,
        .sidebar a.nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, .08);
        }

        .brand {
            color: #fff;
        }

        @media (min-width: 992px) {
            .layout {
                padding-left: 260px;
            }

            .sidebar {
                position: fixed;
                inset: 0 auto 0 0;
            }
        }
    </style>
</head>

<body class="layout">

    <!-- Sidebar -->
    <aside class="sidebar border-end d-lg-block"
        :class="sidebarOpen ? 'position-fixed top-0 start-0 h-100 z-3' : 'd-none d-lg-block'">
        <div class="p-3 border-bottom border-secondary-subtle d-flex align-items-center gap-2">
            <div class="bg-success rounded-3 d-inline-flex align-items-center justify-content-center"
                style="width:40px;height:40px;">
                <i class="bi bi-speedometer2 text-white"></i>
            </div>
            <strong class="brand">Kembar Jaya Motor</strong>
            <button class="btn btn-sm btn-outline-light ms-auto d-lg-none" @click="sidebarOpen=false"><i
                    class="bi bi-x-lg"></i></button>
        </div>

        @php
            function isActive($pattern)
            {
                return request()->is($pattern) ? 'active' : '';
            }
        @endphp
        <nav class="p-3">
            <div class="nav nav-pills flex-column gap-1">
                <a class="nav-link {{ isActive('dashboard') }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door me-2"></i> Dashboard
                </a>
                <a class="nav-link {{ isActive('users*') }}" href="{{ route('users.index') }}">
                    <i class="bi bi-people me-2"></i> Users
                </a>
                <a class="nav-link {{ isActive('groupproduct*') }}" href="{{ route('groupproduct.index') }}">
                    <i class="bi bi-collection me-2"></i> Group Product
                </a>
                <a class="nav-link {{ isActive('merek*') }}" href="{{ route('merek.index') }}">
                    <i class="bi bi-tags me-2"></i> Merek
                </a>
                <a class="nav-link {{ isActive('product*') }}" href="{{ route('product.index') }}">
                    <i class="bi bi-box-seam me-2"></i> Product
                </a>
                <a class="nav-link {{ isActive('pembelian*') }}" href="{{ route('pembelian.index') }}">
                    <i class="bi bi-cart-plus me-2"></i> Pembelian
                </a>
                {{-- <a class="nav-link {{ isActive('orders*') }}" href="#">
                    <i class="bi bi-bag-check me-2"></i> Orders
                </a> --}}
            </div>
        </nav>

        <div class="position-absolute bottom-0 start-0 end-0 p-3 border-top border-secondary-subtle">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center me-2"
                    style="width:36px;height:36px;">
                    <span class="small fw-bold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                </div>
                <div class="small text-truncate">
                    <div class="text-white">{{ auth()->user()->username ?? 'User' }}</div>
                    <div class="text-secondary text-truncate">{{ auth()->user()->email ?? '' }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                    @csrf
                    <button class="btn btn-sm btn-outline-light"><i
                            class="bi bi-box-arrow-right me-1"></i>Logout</button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Topbar -->
    <nav class="navbar bg-white shadow-sm fixed-top d-lg-none">
        <div class="container-fluid">
            <button class="btn btn-outline-secondary" @click="sidebarOpen=true"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h1">KJM Admin</span>
            <div></div>
        </div>
    </nav>

    <!-- Main -->
    <div class="container-fluid" style="padding-top: 72px;">
        @if (session('success'))
            <div class="alert alert-success border mt-2 mx-2">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border mt-2 mx-2">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('headerbar')
        <main class="py-3">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

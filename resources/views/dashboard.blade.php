{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@php
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    // KPI ringkas
    $usersCount = \App\Models\User::count();
    $productsCount = \App\Models\Product::count();
    $lowStockCount = \App\Models\Product::where('qty', '<=', 5)->count();
    $salesToday = \App\Models\Purchase::whereDate('tanggal', now())->sum('total_harga');

    // Penjualan 7 hari terakhir (label + data)
    $labels = [];
    $series = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = Carbon::today()->subDays($i);
        $labels[] = $d->format('d M');
        $series[] = (float) \App\Models\Purchase::whereDate('tanggal', $d)->sum('total_harga');
    }

    // 8 transaksi terbaru
    $recentPurchases = \App\Models\Purchase::orderByDesc('id')->take(8)->get();

    // Top 5 produk paling banyak dibeli (akumulasi qty)
    $topProducts = DB::table('transaksi_pembelian_detail as tpd')
        ->join('product as p', 'p.id', '=', 'tpd.product_id')
        ->select('p.name_barang', DB::raw('SUM(tpd.qty) as total_qty'))
        ->groupBy('p.name_barang')
        ->orderByDesc('total_qty')
        ->limit(5)
        ->get();

    $maxQty = max($topProducts->pluck('total_qty')->all() ?: [1]);
@endphp

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Dashboard</h5>
                    <small class="text-secondary">Ringkasan & tindakan cepat</small>
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
    {{-- KPI --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-secondary small">Total Users</div>
                    <i class="bi bi-people text-muted"></i>
                </div>
                <h3 class="mb-1">{{ number_format($usersCount) }}</h3>
                <small class="text-muted">Akun terdaftar</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-secondary small">Produk</div>
                    <i class="bi bi-box-seam text-muted"></i>
                </div>
                <h3 class="mb-1">{{ number_format($productsCount) }}</h3>
                <small class="text-muted">Master barang</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-secondary small">Stok Rendah (&le;5)</div>
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                </div>
                <h3 class="mb-1">{{ number_format($lowStockCount) }}</h3>
                <small class="text-muted">Perlu restock</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-secondary small">Penjualan Hari Ini</div>
                    <i class="bi bi-cash-stack text-success"></i>
                </div>
                <h3 class="mb-1">Rp {{ number_format($salesToday, 2, ',', '.') }}</h3>
                <small class="text-muted">{{ now()->format('d M Y') }}</small>
            </div>
        </div>
    </div>
    {{-- Chart + Top/Low widgets --}}
    <div class="row g-3 mb-3">
        {{-- Doughnut kecil --}}
        <div class="col-12 col-xl-4">
            <div class="bg-white border rounded-3 h-100 d-flex flex-column">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Distribusi Penjualan</h6>
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                </div>
                <div class="p-3 d-flex justify-content-center align-items-center flex-grow-1">
                    <div style="max-width:240px; width:100%;">
                        <canvas id="salesPie" height="200"></canvas>
                    </div>
                </div>
                <div class="px-3 pb-3">
                    <div id="pieLegend" class="small text-muted"></div>
                </div>
            </div>
        </div>

        {{-- Top produk + aksi cepat --}}
        <div class="col-12 col-xl-8">
            <div class="d-grid gap-3" style="grid-template-columns: 1fr;">

                <div class="bg-white border rounded-3 p-3">
                    <h6 class="mb-2">Top Produk Terjual</h6>

                    @forelse ($topProducts as $tp)
                        @php
                            $pct = $maxQty > 0 ? round(($tp->total_qty / $maxQty) * 100) : 0;
                        @endphp
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium text-truncate me-2">{{ $tp->name_barang }}</span>
                                <span class="text-muted">{{ number_format($tp->total_qty) }}</span>
                            </div>
                            <div class="progress" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0"
                                aria-valuemax="100" style="height:6px;">
                                <div class="progress-bar" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada data pembelian.</div>
                    @endforelse
                </div>

                <div class="bg-white border rounded-3 p-3">
                    <h6 class="mb-2">Aksi Cepat</h6>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('pembelian.create') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-bag-plus me-1"></i> Pembelian
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('product.create') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-box-seam me-1"></i> Tambah Produk
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('users.create') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-person-plus me-1"></i> Tambah User
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('groupproduct.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-grid me-1"></i> Group Produk
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            // Data dummy aman; kalau punya data dari controller, ganti di sini:
            const labels = @json($pieLabels ?? ['Oli', 'Sparepart', 'Aksesoris']);
            const values = @json($pieValues ?? [45, 35, 20]);

            const ctx = document.getElementById('salesPie').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            'rgba(25,135,84,0.8)', // hijau
                            'rgba(13,110,253,0.8)', // biru
                            'rgba(255,193,7,0.8)' // kuning
                        ],
                        borderColor: [
                            'rgba(25,135,84,1)',
                            'rgba(13,110,253,1)',
                            'rgba(255,193,7,1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '70%', // donat tipis
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0) || 1;
                                    const val = ctx.parsed || 0;
                                    const pct = Math.round(val / total * 100);
                                    return ` ${ctx.label}: ${val} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Legend kecil di bawah chart
            const legend = labels.map((l, i) =>
                `<span class="me-3">
                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${chart.data.datasets[0].backgroundColor[i]}"></span>
                <span class="ms-1">${l}</span>
            </span>`
            ).join('');
            document.getElementById('pieLegend').innerHTML = legend;
        })();
    </script>


    {{-- Recent table --}}
    <div class="bg-white border rounded-3 overflow-hidden">
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
            <h6 class="mb-0">Pembelian Terbaru</h6>
            <small class="text-muted">8 terakhir</small>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentPurchases as $p)
                        <tr>
                            <td class="fw-medium">{{ $p->no_transaksi }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $p->nama_pembeli }}</td>
                            <td class="text-end">Rp {{ number_format($p->total_harga, 2, ',', '.') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-primary" href="{{ route('pembelian.edit', $p->id) }}"><i
                                            class="bi bi-pencil"></i></a>
                                    <a class="btn btn-outline-secondary" target="_blank"
                                        href="{{ route('pembelian.print', $p->id) }}"><i class="bi bi-printer"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
    <script>
        (() => {
            const ctx = document.getElementById('sales7');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Rp',
                        data: @json($series),
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => 'Rp ' + (ctx.raw ?? 0).toLocaleString('id-ID', {
                                    minimumFractionDigits: 2
                                })
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: (v) => 'Rp ' + Number(v).toLocaleString('id-ID')
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        })();
    </script>
@endpush

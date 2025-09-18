@extends('layouts.print')
@section('title', 'Nota Pembelian #' . $pembelian->no_transaksi)

@section('content')
    @php
        $chunks = $pembelian->items->chunk(12);
        $totalPages = $chunks->count();

        // kalau item pas mepet (tanda tangan butuh halaman baru)
        $needsExtraPage = false;
        if ($chunks->last()->count() > 8) {
            // misal sisa item terlalu banyak
            $needsExtraPage = true;
        }

        if ($needsExtraPage) {
            $totalPages++;
        }
    @endphp
    
    @foreach ($chunks as $chunkIndex => $chunk)
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <div style="font-weight:bold; font-size:18px;">Kembar Jaya Motor</div>
                <div class="muted" style="font-size:12px;">Jalan Madiun 12, Ganjar Agung, Metro Barat, Metro</div>
                <div class="hr my-1"></div>

                <div class="title">Nota Pembelian</div>
                <div class="muted">No: {{ $pembelian->no_transaksi }}</div>
                <div class="muted">Tanggal: {{ \Carbon\Carbon::parse($pembelian->tanggal)->format('d/m/Y') }}</div>
            </div>
            <div class="text-end">
                <div style="font-size:15px;"><strong>Pembeli:</strong> {{ $pembelian->nama_pembeli }}</div>
                @if ($pembelian->keterangan)
                    <div class="muted">Ket: {{ $pembelian->keterangan }}</div>
                @endif
            </div>
        </div>
        <div class="hr"></div>

        {{-- Tabel Items --}}
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th style="width:45%;">Produk</th>
                    <th class="text-end" style="width:10%;">Qty</th>
                    <th class="text-end" style="width:15%;">Harga</th>
                    <th class="text-end" style="width:10%;">Diskon%</th>
                    <th class="text-end" style="width:20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $it)
                    <tr>
                        <td>{{ $it->product->name_barang ?? '-' }}</td>
                        <td class="text-end">{{ $it->qty }}</td>
                        <td class="text-end">{{ number_format($it->harga_modal, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($it->diskon_percent ?? 0, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($it->subtotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>

            {{-- Footer hanya di halaman terakhir --}}
            @if ($loop->last)
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Qty</th>
                        <th class="text-end">{{ $pembelian->total_qty }}</th>
                    </tr>
                    @if (!is_null($pembelian->diskon_percent ?? null))
                        <tr>
                            <th colspan="4" class="text-end">Diskon (%)</th>
                            <th class="text-end">{{ number_format($pembelian->diskon_percent, 2, ',', '.') }}</th>
                        </tr>
                    @endif
                    <tr>
                        <th colspan="4" class="text-end">Total Harga (Rp)</th>
                        <th class="text-end">{{ number_format($pembelian->total_harga, 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>

        {{-- Tanda tangan + nomor halaman --}}

        @if ($loop->last)
            <div id="closingBlock" class="print-closing">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-center" style="width:200px;">
                        <strong>Penerima</strong>
                        <div style="height:50px;"></div>
                        <div>(...........................)</div>
                    </div>
                    <div class="text-end flex-grow-1">
                        <div class="muted">Halaman {{ $chunkIndex + 1 }}/{{ $totalPages }}</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Page break kalau bukan halaman terakhir --}}
        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach

    {{-- Pesan terima kasih hanya sekali --}}
    <div class="mt-2 text-center muted">Terima kasih</div>

    <script>
        // Saat print, kalau sisa ruang < 90px, paksa pindah halaman sebelum tanda tangan
        window.addEventListener('load', () => {
            const el = document.getElementById('closingBlock');
            if (!el) return;

            // tinggi halaman kira-kira (Chrome print) — bisa disetel kalau perlu
            const pageHeight = window.innerHeight;
            const rect = el.getBoundingClientRect();
            const remaining = pageHeight - rect.top;

            if (remaining < 90) { // threshold tinggi blok tanda tangan
                el.classList.add('force-break-before');
            }
        });
    </script>
@endsection

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Print')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap (opsional, biar tabel rapi) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Kertas A5 – default portrait */
        @page {
            size: A5;
            margin: 10mm;
            /* atur margin sesuai kebutuhan */
        }

        /* @page { size: A5 landscape; } */

        /* Area konten agar pas A5 */
        .sheet {
            background: white;
            margin: 0 auto;
            padding: 0;
        }

        /* Hilangkan elemen tak perlu saat print */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media print {
            .sheet {
                width: auto;
            }
        }

        /* Tabel ringkas untuk nota */
        table.table-sm td,
        table.table-sm th {
            padding: .35rem .5rem !important;
            vertical-align: middle;
        }

        /* Tipografi */
        body {
            font-size: 12px;
            color: #111;
        }

        .title {
            font-size: 16px;
            font-weight: 700;
        }

        .muted {
            color: #6c757d;
        }

        .hr {
            border-top: 1px dashed #999;
            margin: .5rem 0 1rem;
        }
    </style>
</head>

<body class="@yield('paper_orientation')"><!-- tambahkan 'landscape' kalau mau -->

    <div class="sheet">
        <div class="container-fluid p-3">
            @yield('content')
        </div>
    </div>

    {{-- Tombol manual (hanya tampil di layar) --}}
    <div class="no-print position-fixed bottom-0 start-0 p-3">
        <button onclick="window.print()" class="btn btn-sm btn-dark">Print</button>
    </div>

    <script>
        // Auto print saat halaman dibuka
        window.addEventListener('load', () => {
            // beri sedikit delay agar font/asset siap
            setTimeout(() => window.print(), 200);
        });
    </script>
</body>

</html>

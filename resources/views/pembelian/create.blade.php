{{-- resources/views/pembelian/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Pembelian Baru')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Pembelian Baru</h5><small class="text-secondary">Tambah banyak produk dengan
                        cepat</small>
                </div>
                <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary"><i
                        class="bi bi-arrow-left me-1"></i> Back</a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('pembelian.store') }}" id="purchaseForm">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="bg-white border rounded-3 p-3 mb-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Nama Pembeli</label>
                            <input type="text" name="nama_pembeli" class="form-control" value="{{ old('nama_pembeli') }}"
                                placeholder="Nama Pembeli" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}"
                                placeholder="(opsional)">
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Items</h6>
                        <div class="d-flex gap-2">
                            <button type="button" id="btnAddRow" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Baris
                            </button>
                            <button type="button" id="btnClearRows" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-circle me-1"></i> Bersihkan
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 420px;">Produk</th>
                                    <th style="width: 110px;">Qty</th>
                                    <th style="width: 160px;">Harga</th>
                                    <th style="width: 120px;">Diskon %</th>
                                    <th style="width: 160px;">Subtotal</th>
                                    <th style="width: 70px;">Act</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Subtotal (Rp)</th>
                                    <th id="totalHarga">0,00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Diskon (%)</th>
                                    <th>
                                        <input type="number" step="0.01" min="0" max="100"
                                            id="diskonPercent" class="form-control form-control-sm" value="0">
                                    </th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Grand Total (Rp)</th>
                                    <th id="grandTotal" class="fw-bold">0,00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-end">
                        <button type="button" id="btnSave" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Simpan Pembelian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function() {
            let rowIdx = 0;

            function rupiah(x) {
                return Number(x || 0).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function recalc() {
                let tQty = 0,
                    subTotal = 0;
                $('#itemsTable tbody tr').each(function() {
                    const qty = parseInt($(this).find('.it-qty').val() || 0);
                    const harga = parseFloat($(this).find('.it-harga').val() || 0);
                    const diskon = Math.max(0, Math.min(100, parseFloat($(this).find('.it-diskon').val() ||
                    0)));
                    const sub = qty * harga * (1 - (diskon / 100));

                    $(this).find('.it-subtotal').text(rupiah(sub));
                    tQty += qty;
                    subTotal += sub;
                });

                const dPct = parseFloat($('#diskonPercent').val() || 0);
                const potPct = Math.max(0, Math.min(100, dPct)) / 100 * subTotal;
                let grand = subTotal - potPct;
                if (grand < 0) grand = 0;

                $('#totalQty').text(tQty);
                $('#totalHarga').text(rupiah(subTotal));
                $('#grandTotal').text(rupiah(grand));

                if (!$('#_total_qty').length) {
                    $('<input type="hidden" id="_total_qty" name="__total_qty">').appendTo('#purchaseForm');
                    $('<input type="hidden" id="_subtotal" name="__subtotal">').appendTo('#purchaseForm');
                    $('<input type="hidden" id="_diskon_percent" name="__diskon_percent">').appendTo('#purchaseForm');
                    $('<input type="hidden" id="_grand_total" name="__grand_total">').appendTo('#purchaseForm');
                }
                $('#_total_qty').val(tQty);
                $('#_subtotal').val(subTotal.toFixed(2));
                $('#_diskon_percent').val((Math.max(0, Math.min(100, dPct))).toFixed(2));
                $('#_grand_total').val(grand.toFixed(2));
            }

            function addRow(prefill = null) {
                const idx = rowIdx++;
                const tr = $(`
<tr>
  <td>
    <select class="form-select it-product" name="items[${idx}][product_id]" required style="width:100%;"></select>
    <input type="hidden" class="it-product-text" name="items[${idx}][product_text]">
  </td>
  <td>
    <input type="number" class="form-control it-qty" name="items[${idx}][qty]" value="0" min="0" required>
    <small class="text-muted it-stock-info d-block">Stok tersedia: -</small>
  </td>
  <td>
    <input type="number" step="0.01" class="form-control it-harga" name="items[${idx}][harga_modal]" value="0" required>
  </td>
  <td>
    <input type="number" step="0.01" min="0" max="100" class="form-control it-diskon" name="items[${idx}][diskon]" value="0">
  </td>
  <td class="it-subtotal text-end">0,00</td>
  <td class="text-center">
    <button type="button" class="btn btn-sm btn-outline-danger it-remove"><i class="bi bi-trash"></i></button>
  </td>
</tr>
`);
                $('#itemsTable tbody').append(tr);

                const $sel = tr.find('.it-product').select2({
                    ajax: {
                        delay: 200,
                        url: '{{ route('products.search') }}',
                        dataType: 'json',
                        data: params => ({
                            term: params.term || ''
                        }),
                        processResults: data => data
                    },
                    placeholder: 'Cari produk...',
                    allowClear: true,
                    width: 'resolve'
                });

                // Prefill from old()
                if (prefill) {
                    const option = new Option(prefill.text || ('#' + prefill.id), prefill.id, true, true);
                    $sel.append(option).trigger('change');
                    tr.find('.it-product-text').val(prefill.text || '');
                    if (prefill.harga_modal != null) tr.find('.it-harga').val(prefill.harga_modal);
                    if (prefill.qty != null) tr.find('.it-qty').val(prefill.qty);
                    if (prefill.diskon != null) tr.find('.it-diskon').val(prefill.diskon);
                    if (prefill.stock != null) {
                        tr.find('.it-stock-info').text(`Stok tersedia: ${prefill.stock}`);
                        tr.find('.it-qty').attr('max', prefill.stock);
                    }
                }

                // Save label on select
                $sel.on('select2:select', function(e) {
                    const data = e.params.data || {};
                    tr.find('.it-product-text').val(data.text || '');
                    if (data.harga_modal !== undefined) tr.find('.it-harga').val(data.harga_modal);
                    if (data.stock !== undefined) {
                        tr.find('.it-stock-info').text(`Stok tersedia: ${data.stock}`);
                        tr.find('.it-qty').attr('max', data.stock);
                    }
                    recalc();
                });

                $sel.on('select2:clear', function() {
                    tr.find('.it-product-text').val('');
                    tr.find('.it-stock-info').text('Stok tersedia: -');
                    tr.find('.it-qty').removeAttr('max');
                });

                tr.on('input', '.it-qty, .it-harga, .it-diskon', recalc);

                tr.find('.it-remove').on('click', function() {
                    const $tbody = $('#itemsTable tbody');
                    if ($tbody.find('tr').length <= 1) {
                        tr.find('.it-product').val(null).trigger('change');
                        tr.find('.it-product-text').val('');
                        tr.find('.it-qty').val(0);
                        tr.find('.it-harga').val(0);
                        tr.find('.it-diskon').val(0);
                        tr.find('.it-stock-info').text('Stok tersedia: -');
                    } else {
                        tr.remove();
                    }
                    recalc();
                });

                recalc();
            }

            // ===== SAFER: pass raw old('items') only, then map in JS =====
            const oldItemsRaw = @json(old('items', []));
            const oldItems = (Array.isArray(oldItemsRaw) ? oldItemsRaw : []).map(function(it) {
                return {
                    id: (it && it.product_id) ?? null,
                    text: (it && it.product_text) ?? null,
                    qty: Number((it && it.qty) ?? 0),
                    harga_modal: Number((it && it.harga_modal) ?? 0),
                    diskon: Number((it && it.diskon) ?? 0),
                    stock: (it && it.stock) ?? null
                };
            });

            const oldDiskonPercent = @json(old('__diskon_percent', null));

            // Initial build
            if (oldItems.length > 0) {
                $('#itemsTable tbody').empty();
                oldItems.forEach(function(it) {
                    addRow(it);
                });
                if (oldDiskonPercent !== null) {
                    $('#diskonPercent').val(oldDiskonPercent);
                }
                recalc();
            } else {
                addRow(); // fresh form: 1 blank row
            }

            // Actions
            $('#btnAddRow').on('click', function() {
                addRow();
            });

            $('#btnClearRows').on('click', function() {
                $('#itemsTable tbody').empty();
                addRow(); // keep at least one row
                recalc();
            });

            $('#diskonPercent').on('input', recalc);

            $('#btnSave').on('click', function() {
                // prune invalid rows before submit
                $('#itemsTable tbody tr').each(function() {
                    const $row = $(this);
                    const prod = $row.find('.it-product').val();
                    const qty = parseInt($row.find('.it-qty').val() || 0);
                    if (!prod || qty <= 0) {
                        $row.remove();
                    }
                });

                if ($('#itemsTable tbody tr').length === 0) {
                    alert('Tambah minimal 1 item yang valid (produk dipilih & qty >= 1).');
                    return;
                }

                $('#itemsTable tbody .it-product').attr('required', true);
                $('#purchaseForm')[0].submit();
            });
        })();
    </script>

@endsection

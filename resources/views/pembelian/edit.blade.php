{{-- resources/views/pembelian/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Pembelian')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Edit Pembelian</h5><small class="text-secondary">Ubah data & item</small>
                </div>
                <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('pembelian.update', $purchase) }}" id="purchaseForm">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-lg-12">
                <div class="bg-white border rounded-3 p-3 mb-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ old('tanggal', $purchase->tanggal) }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Nama Pembeli</label>
                            <input type="text" name="nama_pembeli" class="form-control"
                                value="{{ old('nama_pembeli', $purchase->nama_pembeli) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control"
                                value="{{ old('keterangan', $purchase->keterangan) }}">
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
                                    <th style="width:420px;">Produk</th>
                                    <th style="width:120px;">Qty</th>
                                    <th style="width:160px;">Harga</th>
                                    <th style="width:120px;">Diskon %</th>
                                    <th style="width:160px;">Subtotal</th>
                                    <th style="width:70px;">Act</th>
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
                                            id="diskonPercent" class="form-control form-control-sm"
                                            value="{{ old('__diskon_percent', $purchase->diskon_percent ?? 0) }}">
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
                        {{-- pakai button manual + pre-submit cleanup --}}
                        <button type="button" id="btnSave" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Update Pembelian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

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

                // hidden untuk backend (opsional)
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

            function initSelect2($el, prefill = null) {
                $el.select2({
                    ajax: {
                        delay: 200,
                        url: '{{ route('products.search') }}',
                        dataType: 'json',
                        data: params => ({
                            term: params.term || ''
                        }),
                        processResults: data => data // backend sudah return {results:[...]}
                    },
                    placeholder: 'Cari produk...',
                    allowClear: true,
                    width: 'resolve'
                });

                if (prefill) {
                    const opt = new Option(prefill.text, prefill.id, true, true);
                    $el.append(opt).trigger('change');
                }
            }

            function addRow(prefill) {
                const idx = rowIdx++;
                const tr = $(`
    <tr>
      <td>
        <select class="form-select it-product" name="items[${idx}][product_id]" required style="width:100%;"></select>
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

                const $sel = tr.find('.it-product');
                initSelect2($sel, prefill ? {
                    id: prefill.id,
                    text: prefill.text
                } : null);

                // apply prefill values
                if (prefill) {
                    if (prefill.harga != null) tr.find('.it-harga').val(prefill.harga);
                    if (prefill.qty != null) tr.find('.it-qty').val(prefill.qty);
                    if (prefill.diskon != null) tr.find('.it-diskon').val(prefill.diskon);
                    if (prefill.stock != null) {
                        tr.find('.it-stock-info').text(`Stok tersedia: ${prefill.stock}`);
                        tr.find('.it-qty').attr('max', prefill.stock);
                    }
                    recalc();
                }

                // on select product: set harga & stok
                $sel.on('select2:select', function(e) {
                    const data = e.params.data || {};
                    if (data.harga_modal !== undefined) tr.find('.it-harga').val(data.harga_modal);
                    if (data.stock !== undefined) {
                        tr.find('.it-stock-info').text(`Stok tersedia: ${data.stock}`);
                        tr.find('.it-qty').attr('max', data.stock);
                    }
                    recalc();
                });

                $sel.on('select2:clear', function() {
                    tr.find('.it-stock-info').text('Stok tersedia: -');
                    tr.find('.it-qty').removeAttr('max');
                    recalc();
                });

                tr.on('input', '.it-qty, .it-harga, .it-diskon', recalc);
                tr.find('.it-remove').on('click', function() {
                    tr.remove();
                    recalc();
                });

                recalc();
            }

            // Prefill dari controller (id, text, qty, harga, diskon)
            const items = @json($prefillItems ?? []);
            if (items.length) {
                items.forEach(function(it) {
                    addRow(it);
                });
            } else {
                addRow();
                addRow();
            }

            $('#btnAddRow').on('click', () => addRow());
            $('#btnClearRows').on('click', () => {
                $('#itemsTable tbody').empty();
                recalc();
            });
            $('#diskonPercent').on('input', recalc);

            // Pre-submit cleanup: hapus baris kosong, pastikan minimal 1 item valid
            $('#btnSave').on('click', function() {
                $('#itemsTable tbody tr').each(function() {
                    const $row = $(this);
                    const prod = $row.find('.it-product').val();
                    const qty = parseInt($row.find('.it-qty').val() || 0);
                    if (!prod || qty <= 0) $row.remove();
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

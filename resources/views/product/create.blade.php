{{-- resources/views/product/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Create Product')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Create Product</h5><small class="text-secondary">Add new product</small>
                </div>
                <a href="{{ route('product.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white border rounded-3 p-4">
                <form method="POST" action="{{ route('product.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Group Product</label>
                        <select name="groupproduct_id" class="form-select select2">
                            <option value="">-- pilih group --</option>
                            @foreach ($groups as $g)
                                <option value="{{ $g->id }}"
                                    {{ old('groupproduct_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->kode_groupproduct }} - {{ $g->name_groupproduct }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Merek</label>
                        <select name="merek_id" class="form-select select2">
                            <option value="">-- pilih merek --</option>
                            @foreach ($mereks as $m)
                                <option value="{{ $m->id }}" {{ old('merek_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->kode_merek }} - {{ $m->name_merek }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="name_barang"
                            class="form-control @error('name_barang') is-invalid @enderror"
                            value="{{ old('name_barang') }}" required>
                        @error('name_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Qty</label>
                            <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror"
                                value="{{ old('qty', 0) }}" required>
                            @error('qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Harga Modal</label>
                            <input type="number" step="0.01" name="harga_modal"
                                class="form-control @error('harga_modal') is-invalid @enderror"
                                value="{{ old('harga_modal', 0) }}" required>
                            @error('harga_modal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Harga Akhir</label>
                            <input type="number" step="0.01" name="harga_akhir"
                                class="form-control @error('harga_akhir') is-invalid @enderror"
                                value="{{ old('harga_akhir', 0) }}" required>
                            @error('harga_akhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('product.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button class="btn btn-success"><i class="bi bi-save me-1"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
        });
    </script>
@endsection

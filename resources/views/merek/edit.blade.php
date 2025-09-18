{{-- resources/views/merek/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Merek')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Edit Merek</h5><small class="text-secondary">Update merek</small>
                </div>
                <a href="{{ route('merek.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-7">
            <div class="bg-white border rounded-3 p-4">
                <form method="POST" action="{{ route('merek.update', $m) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Kode Merek</label>
                        <input type="text" name="kode_merek"
                            class="form-control @error('kode_merek') is-invalid @enderror"
                            value="{{ old('kode_merek', $m->kode_merek) }}" required>
                        @error('kode_merek')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Merek</label>
                        <input type="text" name="name_merek"
                            class="form-control @error('name_merek') is-invalid @enderror"
                            value="{{ old('name_merek', $m->name_merek) }}" required>
                        @error('name_merek')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="A" {{ old('status', $m->status) == 'A' ? 'selected' : '' }}>Active</option>
                            <option value="I" {{ old('status', $m->status) == 'I' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('merek.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button class="btn btn-success"><i class="bi bi-save me-1"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

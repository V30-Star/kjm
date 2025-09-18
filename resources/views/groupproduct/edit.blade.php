@extends('layouts.app')
@section('title', 'Edit Group Product')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Edit Group Product</h5><small class="text-secondary">Update group</small>
                </div>
                <a href="{{ route('groupproduct.index') }}" class="btn btn-outline-secondary">
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
                <form method="POST" action="{{ route('groupproduct.update', $gp) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Kode Group</label>
                        <input type="text" name="kode_groupproduct"
                            class="form-control @error('kode_groupproduct') is-invalid @enderror"
                            value="{{ old('kode_groupproduct', $gp->kode_groupproduct) }}" required>
                        @error('kode_groupproduct')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Group</label>
                        <input type="text" name="name_groupproduct"
                            class="form-control @error('name_groupproduct') is-invalid @enderror"
                            value="{{ old('name_groupproduct', $gp->name_groupproduct) }}" required>
                        @error('name_groupproduct')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="A" {{ old('status', $gp->status) == 'A' ? 'selected' : '' }}>Active</option>
                            <option value="I" {{ old('status', $gp->status) == 'I' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('groupproduct.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button class="btn btn-success"><i class="bi bi-save me-1"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

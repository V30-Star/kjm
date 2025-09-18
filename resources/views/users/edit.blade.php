@extends('layouts.app')

@section('title', 'Edit User')

@section('headerbar')
    <div class="row">
        <div class="col-12">
            <div class="bg-white border rounded-3 p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Edit User</h5>
                    <small class="text-secondary">Update user information</small>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
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
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username', $user->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                            value="{{ old('full_name', $user->full_name) }}">
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password (optional)</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Kosongkan jika tidak diganti</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User
                                </option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Active
                                </option>
                                <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>
                                    Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layout')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-4">
        <i class="bi bi-person me-2"></i> Admin Profile
    </h5>
</div>

<div class="d-flex justify-content-center">
    <div class="content-box" style="max-width:600px; width:100%;">
        <form method="POST" action="/admin/profile">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="bi bi-envelope me-1"></i> Email Address
                </label>
                <input type="email"
                       name="email"
                       class="form-control"
                       value="{{ $admin->email }}"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="bi bi-lock me-1"></i> New Password
                </label>
                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Leave blank to keep current password">
                <div class="form-text">Enter a new password to change it, or leave empty to keep the current one.</div>
            </div>

            <button class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Update Profile
            </button>
        </form>
    </div>
</div>

@endsection

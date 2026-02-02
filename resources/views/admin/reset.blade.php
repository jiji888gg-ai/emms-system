@extends('admin.layout')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-4">
        <i class="bi bi-arrow-clockwise me-2"></i> System Reset - New Semester
    </h5>
</div>

<div class="d-flex justify-content-center">
    <div class="content-box" style="max-width:520px; width:100%;">
        {{-- Academic Session (Dummy) --}}
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="bi bi-calendar me-1"></i> Select Academic Session
            </label>
            <select class="form-select">
                <option selected>2024/2025 Semester 1</option>
                <option>2024/2025 Semester 2</option>
                <option>2025/2026 Semester 1</option>
            </select>
        </div>

        {{-- Button (UI ONLY) --}}
        <button class="btn btn-danger w-100" disabled>
            <i class="bi bi-exclamation-triangle me-1"></i> RESET ALL MERIT POINTS
        </button>

        {{-- Warning --}}
        <div class="alert alert-danger mt-3">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <strong>Attention:</strong>
            This action will permanently reset all organizer and student merit
            points for the new semester.
        </div>
    </div>
</div>

@endsection

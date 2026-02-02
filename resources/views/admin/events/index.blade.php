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
        <i class="bi bi-calendar-event me-2"></i> Manage Events
    </h5>
</div>

<!-- Filter Buttons -->
<div class="content-box mb-4">
    <div class="d-flex gap-2">
        <a href="/admin/events"
           class="btn btn-sm {{ !$filter ? 'btn-primary' : 'btn-outline-primary' }}">
           <i class="bi bi-grid me-1"></i> All
        </a>

        <a href="/admin/events?status=pending"
           class="btn btn-sm {{ $filter=='pending' ? 'btn-primary' : 'btn-outline-primary' }}">
           <i class="bi bi-clock me-1"></i> New Requests <span class="badge bg-danger">{{ $pendingCount }}</span>
        </a>

        <a href="/admin/events?status=approved"
           class="btn btn-sm {{ $filter=='approved' ? 'btn-primary' : 'btn-outline-primary' }}">
           <i class="bi bi-check-circle me-1"></i> Approved Events
        </a>
    </div>
</div>

<!-- Events Table -->
<div class="content-box">
    <table class="table align-middle table-hover mb-0">
        <thead class="text-muted">
            <tr>
                <th>No.</th>
                <th>Event Name</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $i => $e)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <i class="bi bi-circle-fill text-success me-2" style="font-size:8px"></i>
                    {{ $e->title }}
                </td>
                <td>
                    {{ $e->created_at ? $e->created_at->format('Y-m-d H:i') : '-' }}
                </td>
                <td>
                    @if($e->status == 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @elseif($e->status == 'approved')
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </td>
                <td>
                    <a href="/admin/events/{{ $e->e_id }}"
                       class="btn btn-sm btn-info">
                       <i class="bi bi-eye me-1"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-1"></i>
                    No events found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

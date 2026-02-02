@extends('organizer.layout')

@section('content')
@if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Stat Cards -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="stat-card bg-merit">
            <h6>Total Proposals</h6>
            <h2 class="fw-bold">{{ $totalProposals }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg-rank">
            <h6>Approved Events</h6>
            <h2 class="fw-bold">{{ $approvedEvents }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg-event">
            <h6>Pending Events</h6>
            <h2 class="fw-bold">{{ $pendingEvents }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card" style="background: #dc3545; color: #fff;">
            <h6>Rejected Events</h6>
            <h2 class="fw-bold">{{ $rejectedEvents }}</h2>
        </div>
    </div>

</div>

<!-- Recent Events -->
<div class="content-box">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-clock-history me-1"></i> Recent Proposals
    </h6>

    <table class="table align-middle table-hover">
        <thead class="text-muted">
            <tr>
                <th>Event Name</th>
                <th>Status</th>
                <th>Date Created</th>
            </tr>
        </thead>

        <tbody>
            @forelse($recentEvents as $event)
            <tr>
                <td>
                    <i class="bi bi-circle-fill text-success me-2" style="font-size:8px"></i>
                    {{ $event->title }}
                </td>
                <td>
                    <span class="badge 
                        @if($event->status == 'approved') bg-success
                        @elseif($event->status == 'pending') bg-warning
                        @elseif($event->status == 'rejected') bg-danger
                        @endif">
                        {{ ucfirst($event->status) }}
                    </span>
                </td>
                <td>
                    {{ $event->created_at->format('Y-m-d') }}
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-1"></i>
                    No recent proposals found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

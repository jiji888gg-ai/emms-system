@extends('organizer.layout')

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





    <h5 class="fw-bold mb-4">
        <i class="bi bi-file-earmark-text me-2"></i> Event Proposals
    </h5>
    


<!-- Proposals Table -->
<div class="content-box">
    <a href="/organizer/proposals/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Submit New Proposal
    </a><br><hr>
    <table class="table align-middle table-hover mb-0">
        <thead class="text-muted">
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proposals as $p)
            <tr>
                <td>
                    <i class="bi bi-circle-fill text-success me-2" style="font-size:8px"></i>
                    {{ $p->title }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($p->start_time)->format('Y-m-d') }}
                </td>
                <td>
                    <span class="badge 
                        @if($p->status == 'approved') bg-success
                        @elseif($p->status == 'pending') bg-warning
                        @elseif($p->status == 'rejected') bg-danger
                        @endif">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ url('/organizer/proposals/'.$p->e_id) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye me-1"></i> View
                        </a>
                        <a href="{{ url('/organizer/proposals/'.$p->e_id.'/edit') }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                        <form action="{{ url('/organizer/proposals/'.$p->e_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Adakah anda pasti mahu memadam cadangan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-1"></i>
                    No proposals found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

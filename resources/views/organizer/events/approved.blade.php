@extends('organizer.layout')

@section('content')

{{-- SUCCESS MESSAGE --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ERROR MESSAGE --}}
@if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-4">
        <i class="bi bi-check-circle me-2"></i> Approved Events
    </h5>
</div>

{{-- APPROVED EVENTS TABLE --}}
<div class="content-box">
    <table class="table align-middle table-hover mb-0">
        <thead class="text-muted">
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Merit Points</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

        @forelse($events as $e)
            <tr>
                <td>
                    <i class="bi bi-circle-fill text-success me-2" style="font-size:8px"></i>
                    {{ $e->title }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($e->start_time)->format('Y-m-d') }}
                </td>

                <td>
                    <span class="badge bg-primary">
                        {{ $e->merit_value }} Points
                    </span>
                </td>

                <td>
                    <button 
                        class="btn btn-sm btn-info viewEventBtn"
                        data-bs-toggle="modal"
                        data-bs-target="#eventDetailModal"
                        data-title="{{ $e->title }}"
                        data-description="{{ $e->description }}"
                        data-location="{{ $e->location_name }}"
                        data-start="{{ $e->start_time }}"
                        data-status="{{ ucfirst($e->status) }}"
                        data-pdf="{{ $e->proposal_path ? asset('storage/'.$e->proposal_path) : '' }}"
                        data-merit="{{ $e->merit_value }}"
                    >
                        <i class="bi bi-eye me-1"></i> View Details
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-1"></i>
                    No approved events found.
                </td>
            </tr>
        @endforelse

        </tbody>
    </table>
</div>

{{-- MODAL --}}
<div class="modal fade" id="eventDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-event me-1"></i> Event Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><strong>Title:</strong> <span id="modalTitle"></span></p>
                <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                <p><strong>Location:</strong> <span id="modalLocation"></span></p>
                <p><strong>Start Time:</strong> <span id="modalStartTime"></span></p>
                <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                <p><strong>Merit Points:</strong> <span id="modalMerit"></span></p>
                <p><strong>Proposal PDF:</strong> <span id="modalPdf"></span></p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.viewEventBtn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('modalTitle').innerText = this.dataset.title;
            document.getElementById('modalDescription').innerText = this.dataset.description;
            document.getElementById('modalLocation').innerText = this.dataset.location;
            document.getElementById('modalStartTime').innerText = this.dataset.start;
            document.getElementById('modalStatus').innerText = this.dataset.status;
            document.getElementById('modalMerit').innerText = this.dataset.merit + ' Points';
            
            if (this.dataset.pdf) {
                document.getElementById('modalPdf').innerHTML = '<a href="' + this.dataset.pdf + '" target="_blank" class="btn btn-sm btn-success">View PDF</a>';
            } else {
                document.getElementById('modalPdf').innerHTML = '<span class="text-muted">No file uploaded</span>';
            }
        });
    });
});
</script>

@endsection

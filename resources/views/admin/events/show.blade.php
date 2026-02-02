@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-event"></i> Event Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-tag text-primary"></i> Event Name
                                </label>
                                <p class="form-control-plaintext border-bottom">{{ $event->title }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-file-text text-info"></i> Description
                                </label>
                                <p class="form-control-plaintext border-bottom">{{ $event->description ?: 'No description provided' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-geo-alt text-success"></i> Location
                                </label>
                                <p class="form-control-plaintext border-bottom">{{ $event->location_name ?: 'Location not specified' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-clock text-warning"></i> Date & Time
                                </label>
                                <p class="form-control-plaintext border-bottom">{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('l, F j, Y \a\t g:i A') : 'Date not set' }}</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-file-earmark-pdf text-danger"></i> Proposal PDF
                                </label>
                                <div>
                                    @if($event->proposal_path)
                                        <a href="{{ asset('storage/'.$event->proposal_path) }}"
                                           target="_blank"
                                           class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-eye"></i> View PDF
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            <i class="bi bi-dash-circle"></i> No file uploaded
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-check-circle text-secondary"></i> Status
                                </label>
                                <div>
                                    @if($event->status == 'approved')
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-check-circle-fill"></i> Approved
                                        </span>
                                        @if($event->merit_value)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-star-fill text-warning"></i> Merit Points: <strong>{{ $event->merit_value }}</strong>
                                                </small>
                                            </div>
                                        @endif
                                        @if($event->qr_path)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/'.$event->qr_path) }}"
                                                   download="event_{{ $event->e_id }}_qr.svg"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-download"></i> Download QR Code (SVG)
                                                </a>
                                            </div>
                                        @endif
                                    @elseif($event->status == 'rejected')
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-x-circle-fill"></i> Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark fs-6">
                                            <i class="bi bi-clock-fill"></i> Pending
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($event->status == 'pending')
                            <div class="mb-3">
                                <label class="form-label fw-bold">Actions</label>
                                <div>
                                    <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                    <form action="/admin/events/{{ $event->e_id }}/reject" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="/admin/events" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="bi bi-check-circle text-success"></i> Approve Event
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/admin/events/{{ $event->e_id }}/approve" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="merit_value" class="form-label fw-bold">
                            <i class="bi bi-star text-warning"></i> Merit Points
                        </label>
                        <input type="number" class="form-control" id="merit_value" name="merit_value"
                               min="1" max="100" required placeholder="Enter merit points (1-100)">
                        <div class="form-text">Points awarded to students who attend this event.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Approve Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

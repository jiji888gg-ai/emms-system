@extends('organizer.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text"></i> Proposal Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-tag text-primary"></i> Title
                                </label>
                                <p class="form-control-plaintext border-bottom">{{ $proposal->title }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-file-text text-info"></i> Description
                                </label>
                                <p class="form-control-plaintext border-bottom">{{ $proposal->description ?: 'No description provided' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-geo-alt text-success"></i> Location
                                </label>
                                <p class="form-control-plaintext border-bottom">{{ $proposal->location_name ?: 'Location not specified' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-clock text-warning"></i> Start Time
                                </label>
                                <p class="form-control-plaintext border-bottom">{{ $proposal->start_time ? \Carbon\Carbon::parse($proposal->start_time)->format('l, F j, Y \a\t g:i A') : 'Date not set' }}</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-file-earmark-pdf text-danger"></i> Proposal PDF
                                </label>
                                <div>
                                    @if($proposal->proposal_path)
                                        <a href="{{ asset('storage/'.$proposal->proposal_path) }}"
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
                                    @if($proposal->status == 'approved')
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-check-circle-fill"></i> Approved
                                        </span>
                                        @if($proposal->merit_value)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-star-fill text-warning"></i> Merit Points: <strong>{{ $proposal->merit_value }}</strong>
                                                </small>
                                            </div>
                                        @endif
                                        @if($proposal->qr_path)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/'.$proposal->qr_path) }}"
                                                   download="event_{{ $proposal->e_id }}_qr.svg"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-download"></i> Download QR Code (SVG)
                                                </a>
                                            </div>
                                        @endif
                                    @elseif($proposal->status == 'rejected')
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-x-circle-fill"></i> Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark fs-6">
                                            <i class="bi bi-clock-fill"></i> Pending Review
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="/organizer/proposals" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Proposals
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

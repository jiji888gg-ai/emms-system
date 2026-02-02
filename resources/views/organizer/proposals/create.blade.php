@extends('organizer.layout')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@section('content')
<div class="container mt-4">

    
    <h5 class="fw-bold mb-4">
        <i class="bi bi-plus-circle me-2"></i> Submit Event Proposal
    </h5>

    <form method="POST" action="/organizer/proposals" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Event Title</label>
        <input type="text" name="title" class="form-control">
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <!-- removed merit_value input -->

    <div class="mb-3">
        <label>Location</label>
        <input type="text" name="location_name" class="form-control">
    </div>

    <div class="mb-3">
        <label>Start Date & Time</label>
        <input type="datetime-local" name="start_time" class="form-control">
    </div>
    <div class="mb-3">
    <label>End Date & Time</label>
    <input type="datetime-local" name="end_time" class="form-control">
    </div>

    <div class="mb-3">
        <label>Upload Proposal (PDF)</label>
        <input type="file" name="proposal" accept="application/pdf" class="form-control">
    </div>

    <button class="btn btn-primary">Submit Proposal</button>
</form>

</div>
@endsection

@extends('organizer.layout')

@section('content')
<div class="container mt-4">
    <h4>Edit Proposal</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ url('/organizer/proposals/'.$proposal->e_id.'/update') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Event Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $proposal->title) }}" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required>{{ old('description', $proposal->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location_name" class="form-control" value="{{ old('location_name', $proposal->location_name) }}" required>
        </div>

        <div class="mb-3">
            <label>Start Time</label>
            <input type="datetime-local" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($proposal->start_time)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="mb-3">
            <label>End Time</label>
            <input type="datetime-local" name="end_time" class="form-control" value="{{ \Carbon\Carbon::parse($proposal->end_time)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="mb-3">
            <label>Replace Proposal (PDF)</label>
            <input type="file" name="proposal" accept="application/pdf" class="form-control">
            @if($proposal->proposal_path)
                <p class="mt-2">Current file: <a href="{{ asset('storage/'.$proposal->proposal_path) }}" target="_blank">View</a></p>
            @endif
        </div>

        <button class="btn btn-primary">Save Changes</button>
        <a href="/organizer/proposals" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
@extends('student.layout')

@section('content')

<!-- Stat Cards -->
<div class="row g-4 mb-4">

    <div class="col-md-4">
        <div class="stat-card bg-merit">
            <h6>Total Merit Points</h6>
            <h2 class="fw-bold">{{ $totalMerit }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card {{ $ranking <= 130 ? 'bg-rank' : 'bg-danger' }}">
            <h6>Current Ranking</h6>
            <h2 class="fw-bold">{{ $ranking }}/130</h2>
            <p class="mb-0 mt-2 small">
                @if($ranking <= 130)
                    You are eligible to receive hostel accommodation for the next semester.
                @else
                    You will not be eligible for hostel accommodation next semester.
                @endif
            </p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card bg-event">
            <h6>Events Attended</h6>
            <h2 class="fw-bold">{{ $eventsAttended }}</h2>
        </div>
    </div>

</div>

<!-- Recent Events -->
<!-- Recent Events -->
<div class="content-box">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-clock-history me-1"></i> Recent Events
    </h6>

    <table class="table align-middle table-hover">
        <thead class="text-muted">
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Merit</th>
                <th>Points Earned</th>
            </tr>
        </thead>

        <tbody>
            @forelse($participations as $p)
            <tr>
                <td>
                    <i class="bi bi-circle-fill text-success me-2" style="font-size:8px"></i>
                    {{ $p->event_name }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($p->event_date)->format('Y-m-d') }}
                </td>
                <td>
                    {{ $p->merit_value }}
                </td>
                <td>
                    <span class="badge bg-primary">
                        {{ $p->merit_value }} Points
                    </span>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-1"></i>
                    No recent events found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

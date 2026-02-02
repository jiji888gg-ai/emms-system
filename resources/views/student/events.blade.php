@extends('student.layout')

@section('content')

<h5 class="fw-bold mb-3">
    <i class="bi bi-calendar-event me-2"></i> Available Events
</h5>

<!-- SEARCH -->
<form method="GET" action="{{ url('/student/events') }}" class="mb-4">
    <div class="input-group w-50">
        <input 
            type="text" 
            name="search" 
            class="form-control"
            placeholder="Search events..."
            value="{{ request('search') }}"
        >
        <button class="btn btn-primary">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>
<div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-primary">All</button>
        <button class="btn btn-sm btn-outline-secondary">Academic</button>
        <button class="btn btn-sm btn-outline-secondary">Club</button>
    </div>
<br>
<!-- EVENT CARDS -->
<div class="row g-4">

    @forelse($events as $event)
    <div class="col-md-4">

        <div class="card shadow-sm border-0 h-100" style="border-radius:15px">
            <div class="card-body d-flex flex-column">

                <!-- Title + Merit -->
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold">{{ $event->title }}</h6>
                    <span class="badge bg-primary">
                        {{ $event->merit_value }} Points
                    </span>
                </div>

                <!-- Date -->
                <p class="text-muted mb-1">
                    <i class="bi bi-calendar-date me-1"></i>
                    {{ \Carbon\Carbon::parse($event->start_time)->format('Y-m-d') }}
                </p>

                <!-- Location -->
                <p class="text-muted mb-3">
                    <i class="bi bi-geo-alt me-1"></i>
                    {{ $event->location_name ?? 'TBA' }}
                </p>

                <!-- Button -->
                <div class="mt-auto">
                    <button 
                        class="btn btn-primary btn-sm w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#eventModal"
                        data-title="{{ $event->title }}"
                        data-date="{{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y h:i A') }}"
                        data-location="{{ $event->location_name ?? 'TBA' }}"
                        data-merit="{{ $event->merit_value }}"
                        data-description="{{ $event->description ?? 'No description provided.' }}"
                    >
                        View Details
                    </button>
                </div>

            </div>
        </div>

    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-info-circle me-1"></i>
        No events available.
    </div>
    @endforelse

</div>

<!-- EVENT DETAIL MODAL -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <p class="text-muted mb-1">
                    <i class="bi bi-calendar-event me-1"></i>
                    <span id="modalDate"></span>
                </p>

                <p class="text-muted mb-2">
                    <i class="bi bi-geo-alt me-1"></i>
                    <span id="modalLocation"></span>
                </p>

                <span class="badge bg-primary mb-3">
                    <span id="modalMerit"></span> Points
                </span>

                <hr>

                <p id="modalDescription"></p>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<!-- BOOTSTRAP JS + MODAL SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('eventModal').addEventListener('show.bs.modal', function (event) {

    let button = event.relatedTarget;

    document.getElementById('modalTitle').textContent = button.getAttribute('data-title');
    document.getElementById('modalDate').textContent = button.getAttribute('data-date');
    document.getElementById('modalLocation').textContent = button.getAttribute('data-location');
    document.getElementById('modalMerit').textContent = button.getAttribute('data-merit');
    document.getElementById('modalDescription').textContent = button.getAttribute('data-description');

});
</script>

@endsection

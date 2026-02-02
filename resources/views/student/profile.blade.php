@extends('student.layout')

@section('content')
<div class="container mt-4">
    <div class="row">

        <!-- LEFT PROFILE CARD -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">

                    <!-- Avatar -->
                    <div class="rounded-circle bg-primary text-white d-flex 
                                justify-content-center align-items-center mx-auto"
                         style="width:80px;height:80px;font-size:32px;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>

                    <h5 class="mt-3">{{ $student->name }}</h5>
                    <p class="text-muted mb-1">{{ $student->num_matrics }}</p>
                    <p class="text-muted mb-1">{{ $student->email }}</p>
                    <p class="text-muted">{{ $student->phone }}</p>
                </div>
            </div>
        </div>

        <!-- RIGHT CONTENT -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="mb-4">Update Personal & Security Information</h4>

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- UPDATE PROFILE -->
                    <form method="POST" action="{{ url('/student/profile/update') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name', $student->name) }}"
               placeholder="Enter full name">
    </div>

    <div class="mb-3">
        <label class="form-label">Mobile Number</label>
        <input type="text"
               name="phone"
               class="form-control"
               value="{{ old('phone', $student->phone) }}"
               placeholder="Enter mobile number">
    </div>

    <button type="submit" class="btn btn-primary">
        Update
    </button>
</form>

                    <hr class="my-4">

                    <!-- UPDATE PASSWORD -->
                    <form method="POST" action="{{ url('/student/profile/password') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Enter new password">
                        </div>

                        <button type="submit" class="btn btn-secondary">
                            Update Password
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

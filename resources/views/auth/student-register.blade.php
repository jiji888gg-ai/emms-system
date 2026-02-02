<!DOCTYPE html>
<html>
<head>
    <title>Student Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="text-center mb-4">Student Sign Up</h4>

                    {{-- Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="/student/register">
                        @csrf

                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label>UiTM Email</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="2021123456@student.uitm.edu.my"
                                   value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label>Mobile Number</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" required>
                        </div>

                        <button class="btn btn-primary w-100">
                            Register
                        </button>
                    </form>

                    <p class="text-center mt-3">
                        Already have an account? <a href="/login">Login</a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>

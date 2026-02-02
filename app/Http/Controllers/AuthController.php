<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

// 🔥 TAMBAH INI
use App\Models\Student;
use App\Models\Organizer;
use App\Models\Admin;

class AuthController extends Controller
{
   public function login(Request $request)
{
    $request->validate([
        'role' => 'required',
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if ($request->role === 'student') {
        $user = Student::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->pass_hash)) {
            session(['role' => 'student', 'user_id' => $user->s_id]);
            return redirect('/student/dashboard');
        }
    }

    if ($request->role === 'organizer') {

        $organizer = Organizer::where('email', $request->email)->first();

        if ($organizer && Hash::check($request->password, $organizer->pass_hash)) {

            session([
                'role' => 'organizer',
                'organizer_id' => $organizer->o_id   // ikut PK kau
            ]);

            return redirect('/organizer/dashboard');
        }

        return back()->withErrors([
            'login' => 'Invalid organizer credentials'
        ]);
    }

    if ($request->role === 'admin') {
        $user = Admin::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->pass_hash)) {
            session(['role' => 'admin', 'admin_id' => $user->a_id]);
            return redirect('/admin/dashboard');
        }
    }

    return back()->withErrors([
        'login' => 'Invalid credentials for selected role'
    ]);
}

public function showLogin()
{
    return view('auth.login');
}

public function logout(Request $request)
{
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}

public function showStudentRegister()
    {
        return view('auth.student-register');
    }

    // Handle signup
    public function studentRegister(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'unique:students,email',
                'regex:/^[0-9]+@student\.uitm\.edu\.my$/'
            ],
            'phone' => 'required|digits_between:10,11',
            'password' => 'required|min:8|confirmed'
        ]);

        // Extract matric number from email
        $num_matrics = explode('@', $request->email)[0];

        // Create student
        Student::create([
            'num_matrics' => $num_matrics,
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'pass_hash'   => Hash::make($request->password),
            'total_merit' => 0
        ]);

        return redirect('/login')->with('success', 'Account created successfully. Please login.');
    }

   // Show organizer signup page
public function showOrganizerRegister()
{
    return view('auth.organizer-register');
}

// Handle organizer signup
public function organizerRegister(Request $request)
{
    $request->validate([
        'club_name' => 'required|string|max:150',
        'pic_name'          => 'required|string|max:100',
        'email'             => 'required|email|unique:organizers,email',
        'phone'             => 'required|digits_between:10,11',
        'password'          => 'required|min:8|confirmed'
    ]);

    Organizer::create([
        'club_name' => $request->club_name,
        'pic_name'          => $request->pic_name,
        'email'             => $request->email,
        'phone'             => $request->phone,
        'pass_hash'         => Hash::make($request->password),
        'status'            => 'pending' // tunggu HEP approve (best practice)
    ]);

    return redirect('/login')
        ->with('success', 'Organizer account created. Please wait for admin approval.');
}

}

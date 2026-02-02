<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

// 🔥 TAMBAH INI
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\MeritLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function dashboard()
{
    // ambil student login
    $studentId = session('user_id');
    $student = Student::findOrFail($studentId);

    // 1️⃣ TOTAL MERIT (SUM dari merit_logs)
    $totalMerit = DB::table('merit_logs')
        ->where('s_id', $studentId)
        ->sum('points_added');

    // 2️⃣ EVENTS ATTENDED
    $eventsAttended = Attendance::where('s_id', $studentId)->count();

    // 3️⃣ RANKING (simple & konsisten)
    $ranking = DB::table('students')
        ->leftJoin('merit_logs', 'merit_logs.s_id', '=', 'students.s_id')
        ->select(
            'students.s_id',
            DB::raw('COALESCE(SUM(merit_logs.points_added),0) as total_merit')
        )
        ->groupBy('students.s_id')
        ->orderByDesc('total_merit')
        ->pluck('students.s_id')
        ->search($studentId);

    // ranking bermula dari 1
    $ranking = $ranking !== false ? $ranking + 1 : '-';

    $participations = DB::table('attendances')
    ->join('events', 'attendances.e_id', '=', 'events.e_id')
    ->where('attendances.s_id', $studentId)
    ->orderByDesc('events.start_time')
    ->limit(5)
    ->select(
        'events.title as event_name',
        'events.start_time as event_date',
        'events.merit_value'
    )
    ->get();

    return view('student.dashboard', compact(
    'student',
    'totalMerit',
    'eventsAttended',
    'ranking',
    'participations'
));
}

  

    public function events(Request $request)
{
    $query = Event::where('status', 'approved');

    // 🔍 REAL SEARCH
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('location_name', 'like', '%' . $request->search . '%');
        });
    }

    $events = $query->orderBy('start_time', 'desc')->get();

    return view('student.events', compact('events'));
}

public function participation()
{
    $studentId = session('user_id');

    $participations = \DB::table('attendances')
        ->join('events', 'attendances.e_id', '=', 'events.e_id')
        ->where('attendances.s_id', $studentId)
        ->select(
            'events.title as event_name',
            'events.location_name',
            'events.start_time as event_date',
            'events.merit_value'
        )
        ->get();

    return view('student.participation', compact('participations'));
}

public function ranking()
{
    $studentId = session('user_id');

    /* =====================================================
       1️⃣ SIMPAN RANKING SNAPSHOT (SEKALI SEHARI)
       ===================================================== */
    $today = now()->toDateString();

    $snapshotExists = DB::table('ranking_snapshots')
        ->where('snapshot_date', $today)
        ->exists();

    if (!$snapshotExists) {

        // kira ranking semua student hari ini
        $studentsToday = DB::table('students')
            ->leftJoin('merit_logs', 'merit_logs.s_id', '=', 'students.s_id')
            ->select(
                'students.s_id',
                DB::raw('COALESCE(SUM(merit_logs.points_added),0) as total_merit')
            )
            ->groupBy('students.s_id')
            ->orderByDesc('total_merit')
            ->get();

        $rank = 1;
        foreach ($studentsToday as $s) {
            DB::table('ranking_snapshots')->insert([
                's_id' => $s->s_id,
                'rank' => $rank,
                'snapshot_date' => $today,
                'created_at' => now()
            ]);
            $rank++;
        }
    }

    /* =====================================================
       2️⃣ LEADERBOARD SEMASA
       ===================================================== */
    $students = DB::table('students')
        ->leftJoin('merit_logs', 'merit_logs.s_id', '=', 'students.s_id')
        ->select(
            'students.s_id',
            'students.name',
            DB::raw('COALESCE(SUM(merit_logs.points_added), 0) as total_merit')
        )
        ->groupBy('students.s_id', 'students.name')
        ->orderByDesc('total_merit')
        ->get();

    // ranking semasa student login
    $ranking = $students->pluck('s_id')->search($studentId);
    $ranking = $ranking !== false ? $ranking + 1 : '-';


    /* =====================================================
       3️⃣ DATA GRAF (RANKING TREND STUDENT LOGIN)
       ===================================================== */
    $rankingHistory = DB::table('ranking_snapshots')
        ->where('s_id', $studentId)
        ->orderBy('snapshot_date')
        ->get()
        ->map(function ($row) {
            return [
                'date' => $row->snapshot_date,
                'rank' => $row->rank
            ];
        })
        ->toArray();


    /* =====================================================
       4️⃣ RETURN VIEW
       ===================================================== */
    return view('student.ranking', compact(
        'students',
        'ranking',
        'rankingHistory'
    ));
}



public function profile()
{
    $student = Student::findOrFail(session('user_id'));
    return view('student.profile', compact('student'));
}

public function updateProfile(Request $request)
{
    $request->validate([
        'name'  => 'required|string|max:100',
        'phone' => 'required|digits_between:10,11'
    ]);

    $student = Student::findOrFail(session('user_id'));
    $student->name  = $request->name;
    $student->phone = $request->phone;
    $student->save();

    return back()->with('success', 'Profile updated successfully');
}



public function updatePassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6'
    ]);

    $student = Student::findOrFail(session('user_id'));
    $student->pass_hash = Hash::make($request->password);
    $student->save();

    return back()->with('success', 'Password updated successfully');
}


public function saveDailyRankingSnapshot()
{
    $students = DB::table('students')
        ->leftJoin('merit_logs', 'merit_logs.s_id', '=', 'students.s_id')
        ->select(
            'students.s_id',
            DB::raw('COALESCE(SUM(merit_logs.points_added),0) as total')
        )
        ->groupBy('students.s_id')
        ->orderByDesc('total')
        ->get();

    $rank = 1;

    foreach ($students as $s) {
        DB::table('ranking_snapshots')->insert([
            's_id' => $s->s_id,
            'rank' => $rank,
            'snapshot_date' => now()->toDateString()
        ]);
        $rank++;
    }
}

}


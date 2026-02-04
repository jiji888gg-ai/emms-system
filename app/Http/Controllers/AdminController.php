<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\Organizer;
use App\Models\Event;
use App\Models\Admin;
use App\Models\MeritLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;



class AdminController extends Controller
{
    public function dashboard()
{
    $totalStudents  = Student::count();
    $totalOrganizers = Organizer::count();
    $totalEvents    = Event::count();

    return view('admin.dashboard', compact(
        'totalStudents',
        'totalOrganizers',
        'totalEvents'
    ));
}


  public function viewMerit(Request $request)
{
    $search = $request->search;

    $students = DB::table('students')
        ->leftJoin('merit_logs', 'merit_logs.s_id', '=', 'students.s_id')
        ->select(
            'students.s_id',
            'students.name',
            'students.num_matrics',
            DB::raw('COALESCE(SUM(merit_logs.points_added), 0) as total_merit')
        )
        ->when($search, function ($q) use ($search) {
            $q->where('students.name', 'like', "%$search%")
              ->orWhere('students.num_matrics', 'like', "%$search%");
        })
        ->groupBy('students.s_id', 'students.name', 'students.num_matrics')
        ->orderByDesc('total_merit')
        ->get();

    return view('admin.merit.index', compact('students', 'search'));
}

public function exportMerit()
{
    $students = DB::table('students')
        ->leftJoin('merit_logs', 'merit_logs.s_id', '=', 'students.s_id')
        ->select(
            'students.name',
            'students.num_matrics',
            DB::raw('COALESCE(SUM(merit_logs.points_added), 0) as total_merit')
        )
        ->groupBy('students.s_id', 'students.name', 'students.num_matrics')
        ->orderByDesc('total_merit')
        ->get();

    $csvFileName = 'student-merit-list-' . date('Y-m-d') . '.csv';

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$csvFileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use($students) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['No', 'Student Name', 'Matric No', 'Total Merit']);

        foreach ($students as $index => $row) {
            fputcsv($file, [
                $index + 1,
                $row->name,
                $row->num_matrics,
                $row->total_merit
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function viewStudentMerit($id)
{
    $student = Student::findOrFail($id);

    $logs = DB::table('merit_logs')
    ->join('events', 'events.e_id', '=', 'merit_logs.e_id')
    ->where('merit_logs.s_id', $id)
    ->select(
        'events.title as event_title',
        'merit_logs.points_added',
        'merit_logs.created_at'
    )
    ->orderBy('merit_logs.created_at', 'asc')
    ->get();

$totalMerit = $logs->sum('points_added');

    return view('admin.merit.show', compact(
        'student',
        'logs',
        'totalMerit'
    ));
}




public function manageOrganizers(Request $request)
{
    $filter = $request->get('status'); // pending / approved

    $organizers = Organizer::when($filter, function ($q) use ($filter) {
            $q->where('status', $filter);
        })
        ->orderByDesc('created_at')
        ->get();

    $pendingCount = Organizer::where('status', 'pending')->count();

    return view('admin.organizers.index', compact(
        'organizers',
        'filter',
        'pendingCount'
    ));
}



public function approveOrganizer($id)
{
    $org = Organizer::findOrFail($id);
    $org->status = 'approved';
    $org->save();

    return back()->with('success', 'Organizer approved');
}


public function rejectOrganizer($id)
{
    $org = Organizer::findOrFail($id);
    $org->status = 'rejected';
    $org->save();

    return back()->with('success', 'Organizer rejected');
}

public function viewOrganizer($id)
{
    $organizer = Organizer::findOrFail($id);
    return view('admin.organizers.show', compact('organizer'));
}








    public function manageEvents(Request $request)
{
    $filter = $request->get('status'); // pending / approved

    $events = Event::when($filter, function ($q) use ($filter) {
            $q->where('status', $filter);
        })
        ->orderByDesc('created_at')
        ->get();

    $pendingCount = Event::where('status', 'pending')->count();

    return view('admin.events.index', compact(
        'events',
        'filter',
        'pendingCount'
    ));
}


public function approveEvent(Request $request, $id)
{
    if (!session('admin_id')) {
        abort(403, 'Unauthorized');
    }

    $request->validate([
        'merit_value' => 'required|integer|min:1|max:100'
    ]);

    $event = Event::findOrFail($id);

    // elak approve dua kali
    if ($event->status === 'approved') {
        return back()->with('info', 'Event already approved');
    }

    // 1. Set merit value
    $event->merit_value = $request->merit_value;

    // 2. Approve
    $event->status = 'approved';

    // 3. Generate token
    $event->qr_code_token = Str::uuid();

    // 4. QR link (sementara)
    $qrLink = $event->qr_code_token;

    try {
        // 5. Generate QR
        $qrImage = QrCode::format('svg')
        ->size(300)
        ->generate($qrLink);

        // 6. Simpan QR
        $path = 'qrcode/event_'.$event->e_id.'.svg';
        Storage::disk('public')->put($path, $qrImage);

        // 7. Simpan path
        $event->qr_path = $path;
    } catch (\Throwable $e) {
        // If QR fails, still approve but log error
        \Log::error('QR Code generation failed for event ' . $event->e_id . ': ' . $e->getMessage());
        // Still save without QR
    }

    $event->save();

    return back()->with('success', 'Event approved with ' . $request->merit_value . ' merit points & QR generated');
}



public function rejectEvent($id)
{
    if (!session('admin_id')) {
        abort(403, 'Unauthorized');
    }

    $event = Event::findOrFail($id);
    $event->status = 'rejected';
    $event->save();

    return back()->with('success', 'Event rejected');
}


public function viewEvent($id)
{
    if (!session('admin_id')) {
        abort(403, 'Unauthorized');
    }

    $event = Event::findOrFail($id);
    return view('admin.events.show', compact('event'));
}








    public function reset()
    {
        return view('admin.reset');
    }


    public function profile()
{
    $adminId = session('admin_id');
    if (!$adminId) {
        return redirect('/login')->with('error', 'Please login as Admin HEP.');
    }

    $admin = Admin::findOrFail($adminId);

    return view('admin.profile', compact('admin'));
}

    public function updateProfile(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'nullable|min:6'
    ]);

    $admin = Admin::findOrFail(session('admin_id'));

    // update email
    $admin->email = $request->email;

    // update password (ikut column sebenar)
    if ($request->filled('password')) {
        $admin->pass_hash = Hash::make($request->password);
    }

    $admin->save();

    return back()->with('success', 'Profile updated successfully');
}



public function showSendReminder()
    {
        return view('admin.send-reminder');
    }

    public function sendReminderToAll()
{
    $students = Student::orderByDesc('total_merit')->get();

    foreach ($students as $index => $student) {

        $rank = $index + 1;

        // DEFAULT MESSAGE
        $messageBody = "";

        if ($rank >= 1 && $rank <= 30) {

            $messageBody = "Congratulations {$student->name}!

You are currently ranked #{$rank} based on your merit points.

Excellent performance! Keep up the great work and maintain your position to secure hostel accommodation for the upcoming semester.";

        } elseif ($rank >= 31 && $rank <= 100) {

            $messageBody = "Dear {$student->name},

Your current merit ranking is #{$rank}.

You are in a competitive position. Continue participating in events to improve your merit points and strengthen your chances of obtaining hostel accommodation.";

        } elseif ($rank >= 101 && $rank <= 130) {

            $messageBody = "Dear {$student->name},

Your current merit ranking is #{$rank}.

Please be cautious. Only the top 130 students are eligible for hostel accommodation.
You are advised to actively participate in more events to maintain or improve your ranking.";

        } else {

            $messageBody = "Dear {$student->name},

Your current merit ranking is #{$rank}.

Unfortunately, your ranking is currently outside the hostel eligibility range.
You are encouraged to participate in more events to earn additional merit points and improve your chances of securing hostel accommodation next semester.";
        }

        // SEND EMAIL
        Mail::raw(
            $messageBody . "\n\nRegards,\nStudent Affairs Department",
            function ($message) use ($student) {
                $message->to($student->email)
                        ->subject('Merit Ranking & Hostel Accommodation Reminder');
            }
        );
    }

    return back()->with('success', 'Merit reminder emails sent successfully.');
}


}

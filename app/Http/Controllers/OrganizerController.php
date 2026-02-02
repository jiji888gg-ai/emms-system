<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OrganizerController extends Controller
{
    public function dashboard()
    {
        $organizerId = session('organizer_id');

        $totalProposals = Event::where('o_id', $organizerId)->count();
        $approvedEvents = Event::where('o_id', $organizerId)
                               ->where('status', 'approved')
                               ->count();
        $pendingEvents = Event::where('o_id', $organizerId)
                              ->where('status', 'pending')
                              ->count();
        $rejectedEvents = Event::where('o_id', $organizerId)
                               ->where('status', 'rejected')
                               ->count();

        $recentEvents = Event::where('o_id', $organizerId)
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        return view('organizer.dashboard', compact(
            'totalProposals',
            'approvedEvents',
            'pendingEvents',
            'rejectedEvents',
            'recentEvents'
        ));
    }

    public function proposalList()
    {
        $organizerId = session('organizer_id');

        $proposals = Event::where('o_id', $organizerId)->get();

        return view('organizer.proposals.index', compact('proposals'));
    }


public function createProposal()
{
   
    $organizerId = session('organizer_id');

    if (!$organizerId) {
        return redirect('/login')->with('error', 'Please login first.');
    }

    $organizer = Organizer::find($organizerId);

    if (!$organizer) {
        return redirect('/login')->with('error', 'Organizer not found.');
    }

    if ($organizer->status !== 'approved') {
        return redirect('/organizer/dashboard')
            ->with('error', 'Your account is pending approval by Admin HEP.');
    }

    return view('organizer.proposals.create');
}

   public function storeProposal(Request $request)
{
    
$request->validate([
    'title'        => 'required',
    'description'  => 'required',
    'location_name'=> 'required',
    'start_time'   => 'required|date',
    'end_time'     => 'required|date|after_or_equal:start_time',
    'proposal'     => 'nullable|file|mimes:pdf|max:5120'
]);

$data = [
    'o_id'          => session('organizer_id'),
    'title'         => $request->title,
    'description'   => $request->description,
    'merit_value'   => 0,
    'location_name' => $request->location_name,
    'start_time'    => Carbon::parse($request->start_time),
    'end_time'      => Carbon::parse($request->end_time),
    'status'        => 'pending'
];

$data += [
    'location_lat'  => 0,
    'location_long' => 0,
    'radius_meter'  => 0,
    'qr_code_token' => \Illuminate\Support\Str::random(32),
];

    if ($request->hasFile('proposal')) {
        // simpan di storage/app/public/proposals
        $path = $request->file('proposal')->store('proposals', 'public');
        $data['proposal_path'] = $path;
    }

    Event::create($data);

    return redirect('/organizer/proposals')
        ->with('success', 'Event proposal submitted successfully.');
}

    public function approvedEvents()
    {
        $organizerId = session('organizer_id');

        $events = Event::where('o_id', $organizerId)
                       ->where('status', 'approved')
                       ->get();

        return view('organizer.events.approved', compact('events'));
    }

public function profile()
{
    $organizerId = session('organizer_id');

    $organizer = \App\Models\Organizer::findOrFail($organizerId);

    return view('organizer.profile', compact('organizer'));
}

public function updateProfile(Request $request)
{
    $request->validate([
        'club_name' => 'required|string|max:150',
        'pic_name'  => 'required|string|max:100',
        'phone'     => 'required|digits_between:10,11'
    ]);

    $organizer = Organizer::findOrFail(session('organizer_id'));
    $organizer->club_name = $request->club_name;
    $organizer->pic_name  = $request->pic_name;
    $organizer->phone     = $request->phone;
    $organizer->save();

    return back()->with('success', 'Profile updated successfully');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6'
    ]);

    $organizer = Organizer::findOrFail(session('organizer_id'));
    $organizer->pass_hash = Hash::make($request->password);
    $organizer->save();

    return back()->with('success', 'Password updated successfully');
}
    
public function showProposal($id)
{
    $organizerId = session('organizer_id');

    $proposal = Event::where('e_id', $id)
    ->where('o_id', $organizerId)
    ->firstOrFail();

    return view('organizer.proposals.show', compact('proposal'));
}

public function editProposal($id)
{
    $organizerId = session('organizer_id');

    $proposal = Event::where('e_id', $id)
        ->where('o_id', $organizerId)
        ->firstOrFail();

    // Optional: block edits if already approved
    if ($proposal->status === 'approved') {
        return redirect('/organizer/proposals')->with('error', 'Approved proposals cannot be edited.');
    }

    return view('organizer.proposals.edit', compact('proposal'));
}

public function updateProposal(Request $request, $id)
{
    $organizerId = session('organizer_id');

    $proposal = Event::where('e_id', $id)
        ->where('o_id', $organizerId)
        ->firstOrFail();

    if ($proposal->status === 'approved') {
        return back()->with('error', 'Approved proposals cannot be edited.');
    }

    $request->validate([
        'title'        => 'required',
        'description'  => 'required',
        'location_name'=> 'required',
        'start_time'   => 'required|date',
        'end_time'     => 'required|date|after_or_equal:start_time',
        'proposal'     => 'nullable|file|mimes:pdf|max:5120'
    ]);

    $proposal->title = $request->title;
    $proposal->description = $request->description;
    $proposal->location_name = $request->location_name;
    $proposal->start_time = \Carbon\Carbon::parse($request->start_time);
    $proposal->end_time = \Carbon\Carbon::parse($request->end_time);

    // Handle replacement PDF
    if ($request->hasFile('proposal')) {
        // delete old file if exists
        if ($proposal->proposal_path) {
            Storage::disk('public')->delete($proposal->proposal_path);
        }
        $path = $request->file('proposal')->store('proposals', 'public');
        $proposal->proposal_path = $path;
    }

    $proposal->save();

    return redirect('/organizer/proposals')->with('success', 'Proposal updated successfully.');
}

public function deleteProposal($id)
{
    $organizerId = session('organizer_id');
    $proposal = Event::where('e_id', $id)->where('o_id', $organizerId)->first();

    if (!$proposal) {
        return redirect('/organizer/proposals')->with('error', 'Proposal not found.');
    }

    if ($proposal->status == 'approved') {
        return redirect('/organizer/proposals')->with('error', 'Approved proposals cannot be deleted.');
    }

    // Delete the proposal file if exists
    if ($proposal->proposal_path) {
        \Storage::disk('public')->delete($proposal->proposal_path);
    }

    $proposal->delete();

    return redirect('/organizer/proposals')->with('success', 'Proposal deleted successfully.');
}

}

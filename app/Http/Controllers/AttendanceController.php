<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function show($token)
    {
        $event = Event::where('qr_code_token', $token)->firstOrFail();

        return view('attendance.show', compact('event'));
    }

    public function confirm($token)
    {
        $event = Event::where('qr_code_token', $token)->firstOrFail();

        // BUAT SEMENTARA DULU (tanpa DB attendance)
        return view('attendance.success', compact('event'));
    }
}

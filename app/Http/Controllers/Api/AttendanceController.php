<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\MeritLog;
use App\Models\Student;

class AttendanceController extends Controller
{
    public function scan(Request $request)
    {
        // 1️⃣ VALIDATION
        $request->validate([
            's_id'      => 'required|integer',
            'token'     => 'required|string',
            'device_id' => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // 2️⃣ FIND EVENT BY TOKEN
        $event = Event::where('qr_code_token', $request->token)->first();

        if (!$event) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid QR code.'
            ], 404);
        }


        // 🚫 CHECK STUDENT DAH ATTEND EVENT INI
        $alreadyAttend = Attendance::where('s_id', $request->s_id)
            ->where('e_id', $event->e_id)
            ->exists();

        if ($alreadyAttend) {
            return response()->json([
                'status' => 'fail',
                'code'   => 'already_attended',
                'message'=> 'You have already recorded attendance for this event.'
            ], 403);
        }


        // 3️⃣ CHECK DUPLICATE DEVICE
        $exists = Attendance::where('e_id', $event->e_id)
            ->where('device_id', $request->device_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'fail',
                'code'   => 'device_duplicate',
                'message'=> 'This device has already scanned for this event.'
            ], 403);
        }



        // 4️⃣ CALCULATE DISTANCE
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $event->location_lat,
            $event->location_long
        );

        // 5️⃣ GEOFENCE CHECK
        if ($distance > $event->radius_meter) {
            return response()->json([
                'status'   => 'fail',
                'code'     => 'outside_area',
                'message'  => 'You are outside the allowed attendance area.',
                'distance' => round($distance, 2)
            ], 403);
        }

        // 6️⃣ SAVE ATTENDANCE
        Attendance::create([
            's_id'      => $request->s_id,
            'e_id'      => $event->e_id,
            'device_id' => $request->device_id,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'distance'  => round($distance, 2),
            'status'    => 'success',
            'scan_time' => now(),
        ]);

        // 7️⃣ MERIT
        MeritLog::create([
            's_id' => $request->s_id,
            'e_id' => $event->e_id,
            'points_added' => $event->merit_value,
        ]);

        Student::where('s_id', $request->s_id)
            ->increment('total_merit', $event->merit_value);

        return response()->json([
            'status'  => 'success',
            'code'    => 'success',
            'message' => 'Attendance recorded successfully.',
            'event' => $event->title,
            'distance'=> round($distance, 2)
        ]);
    }



    // 📐 HAVERSINE (meter)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earth = 6371000;

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        return acos(
            sin($lat1) * sin($lat2) +
            cos($lat1) * cos($lat2) * cos($lon2 - $lon1)
        ) * $earth;
    }

    public function history($s_id)
{
    $history = \DB::table('attendances')
        ->join('events', 'events.e_id', '=', 'attendances.e_id')
        ->where('attendances.s_id', $s_id)
        ->where('attendances.status', 'success')
        ->orderByDesc('attendances.scan_time')
        ->select(
            'events.title as event_title',
            'attendances.scan_time'
        )
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $history
    ]);
}
}

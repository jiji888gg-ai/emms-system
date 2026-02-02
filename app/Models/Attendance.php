<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $primaryKey = 'att_id';

    protected $fillable = [
    's_id',
    'e_id',
    'scan_time',
    'device_id',
    'latitude',
    'longitude',
    'distance',
    'status'
];

    public function student()
    {
        return $this->belongsTo(Student::class, 's_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'e_id');
    }
}

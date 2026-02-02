<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 's_id';

    protected $fillable = [
        'num_matrics',
        'name',
        'email',
        'phone',
        'pass_hash',
        'total_merit'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 's_id');
    }

    public function meritLogs()
    {
        return $this->hasMany(MeritLog::class, 's_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeritLog extends Model
{
    protected $table = 'merit_logs';
    protected $primaryKey = 'm_id';

    protected $fillable = [
        's_id',
        'e_id',
        'points_added'
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

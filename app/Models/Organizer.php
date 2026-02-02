<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    protected $primaryKey = 'o_id';

    protected $fillable = [
        'club_name',
        'pic_name',
        'email',
        'phone',
        'pass_hash',
        'status'
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'o_id');
    }
}

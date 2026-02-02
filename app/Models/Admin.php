<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'a_id';

    protected $fillable = [
        'email',
        'pass_hash'
    ];
}


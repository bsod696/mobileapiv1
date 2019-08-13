<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $table = 'guest_access'; //table name
    protected $fillable = [
        'house', 
        'studentmail', 
        'studentid', 
        'temporaryPIN', 
        'mobile',
        'access_type'
    ];
}

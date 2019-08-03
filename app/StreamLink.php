<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StreamLink extends Model
{
    protected $table = 'stream_det'; //table name
	protected $fillable = [
	    'strlink',
		'status'
	];
}

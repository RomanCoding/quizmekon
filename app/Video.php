<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['youtube_id', 'start_time', 'end_time', 'pause_time'];
}

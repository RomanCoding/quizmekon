<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'series_id');
    }
}

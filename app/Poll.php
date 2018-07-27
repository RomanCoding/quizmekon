<?php

namespace App;

use App\Observers\PollObserver;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $guarded = [];

    public static function boot()
    {
        self::observe(new PollObserver());
        parent::boot();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}

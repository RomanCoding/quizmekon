<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = ['user_id', 'value'];

    public static function boot() {
        parent::boot();

        static::created(function (Vote $vote) {
            if ($vote->voteable instanceof Post) {
                //event('eloquent.updated: App\Post', $vote->voteable);
            }
        });
        static::updated(function (Vote $vote) {
            if ($vote->voteable instanceof Post) {
                $vote->voteable->updateScore();
                //event('eloquent.updating: App\Post', $vote->voteable);
                //$vote->voteable->setScoreAttribute();
            }
        });
    }

    /**
     * A user who voted.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the owning voteable models.
     */
    public function voteable()
    {
        return $this->morphTo();
    }

}

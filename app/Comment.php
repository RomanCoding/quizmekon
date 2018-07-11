<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id', 'body'
    ];

    protected $with = ['votes', 'comments', 'user'];

    protected $appends = ['usersVote', 'votesTotal'];

    public static function boot() {
        parent::boot();

        static::created(function (Comment $comment) {
            $comment->votes()->create([
                'user_id' => $comment->user_id,
                'value' => 1
            ]);
        });
    }

    /**
     * A user who wrote a comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Comment has votes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function votes()
    {
        return $this->morphMany('App\Vote', 'voteable')->orderByDesc('id');
    }

    /**
     * Get all of the owning commentable models.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Comment has comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    public function scopeInFiveMinutes($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subMinutes(5));
    }

    public function getVotesTotalAttribute()
    {
        return $this->votes->sum('value');
    }

    /**
     * Check if given user has voted for this post.
     *
     * @return Model|null|object|static
     */
    public function getUsersVoteAttribute()
    {
        return auth()->user() ? $this->votes->where('user_id', auth()->user()->id)->first() : null;
    }
}

<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $with = ['category', 'votes', 'user'];

    protected $appends = ['usersVote', 'votesTotal'];

    /**
     * Post belongs to a category (channel / subreddit)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Post belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Post has votes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes()
    {
        return $this->hasMany(Vote::class, 'post_id');
    }

    /**
     * Post has comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable')->orderByDesc('id');
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

    public function getVotesTotalAttribute()
    {
        return $this->votes->sum('value');
    }
}

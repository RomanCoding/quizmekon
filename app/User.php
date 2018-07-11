<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'email_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function voteForPost(Post $post, $vote = -1)
    {
        return $post->votes()->updateOrCreate([
            'user_id' => $this->id,
        ], [
            'user_id' => $this->id,
            'value' => $vote
        ]);
    }

    public function voteForComment(Comment $comment, $vote = -1)
    {
        return $comment->votes()->updateOrCreate([
            'user_id' => $this->id,
        ], [
            'user_id' => $this->id,
            'value' => $vote
        ]);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderByDesc('id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class)->orderByDesc('id');
    }

    /**
     * Mark user as verified.
     */
    public function verify()
    {
        $this->confirmed = 1;
        $this->save();
    }
}

<?php

namespace App;

use Carbon\Carbon;
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

    protected $appends = ['showFirstTime'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function voteForQuiz(Quiz $quiz, $vote = -1)
    {
        return $quiz->votes()->updateOrCreate([
            'user_id' => $this->id,
        ], [
            'user_id' => $this->id,
            'value' => $vote
        ]);
    }

    /**
     * Check if user has polled in given quiz.
     *
     * @param Quiz $quiz
     * @return bool
     */
    public function hasPolledForQuiz(Quiz $quiz)
    {
        return (bool) $quiz->polls()
            ->where('user_id', $this->id)
            ->count();
    }

    public function poll(Quiz $quiz, $index)
    {
        return $quiz->polls()->firstOrCreate([
            'user_id' => $this->id,
        ], [
            'user_id' => $this->id,
            'index' => $index
        ])->setAttribute('correct', $quiz->correct_index);
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

    public function getShowFirstTimeAttribute()
    {
        return (!$this->confirmed && now()->diffInSeconds($this->created_at) < 30);
    }
}

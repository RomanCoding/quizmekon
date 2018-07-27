<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class Quiz extends Model
{
    protected $fillable = [
        'video_id',
        'type',
        'question',
        'answers',
        'correct_index',
        'category_id',
        'user_id',
        'series_id',
        'order'
    ];

    protected $hidden = ['correct_index'];

    protected $with = ['video', 'category', 'votes', 'user'];

    protected $appends = ['usersVote', 'votesTotal'];

    protected $casts = [
        'answers' => 'array',
    ];

    // these are types of questions, integer values for storing in database
    const WHAT_HAPPENS_NEXT = 1;
    const QUESTION_THEN_VIDEO = 2;
    const VIDEO_THEN_QUESTION = 3;

    /**
     * Post has comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable')->orderByDesc('id');
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    /**
     * Quiz belongs to a category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Quiz has votes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function votes()
    {
        return $this->morphMany(Vote::class, 'voteable')->orderByDesc('id');
    }

    /**
     * Quiz belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

    public function getVotesTotalAttribute()
    {
        return $this->votes->sum('value');
    }

    public function getCorrectAttribute()
    {
        return $this->usersPoll ? $this->correct_index : null;
    }

    /**
     * Check if auth user has voted for this quiz.
     *
     * @return Model|null|object|static
     */
    public function getUsersVoteAttribute()
    {
        return auth()->user() ? $this->votes->where('user_id', auth()->user()->id)->first() : null;
    }

    /**
     * Check if auth user participated in poll.
     *
     * @return Model|null|object|static
     */
    public function getUsersPollAttribute()
    {
        return auth()->user() ? $this->polls->where('user_id', auth()->user()->id)->first() : null;
    }

    /**
     * Аналогично (с ограничениями) withCount только находит сумму связанного атрибута.
     * @param Builder $query
     * @param $relation
     * @param $attribute
     * @param null $constraints
     * @return Builder
     */
    public function scopeWithSum(Builder $query, $relation, $attribute, $constraints = null)
    {
        if (is_null($query->getQuery()->columns)) {
            $query->getQuery()->select([$query->getQuery()->from.'.*']);
        }
        $name = $relation;
        $relation = Relation::noConstraints(function () use ($relation) {
            return $this->{$relation}();
        });
        $subQuery = $relation->getRelationExistenceQuery(
            $relation->getRelated()->newQuery(), $query, new Expression("sum({$attribute})")
        )->setBindings([], 'select');
        $subQuery = $subQuery->tap($constraints ?? function (){})->mergeConstraintsFrom($relation->getQuery())->toBase();
        if (count($subQuery->columns) > 1) {
            $subQuery->columns = [$subQuery->columns[0]];
        }
        $column = Str::snake(sprintf('%s_%s_sum', $name, $attribute));
        $query->selectSub($subQuery, $column);
    }

    /**
     * Update score for "Hot" sorting.
     * Details on algorithm
     * https://medium.com/hacking-and-gonzo/how-reddit-ranking-algorithms-work-ef111e33d0d9
     */
    public function updateScore()
    {
        $b = new Carbon('2015-12-8 07:46:43');

        $t = $b->diffInSeconds($this->created_at);

        $x = $this->votesTotal;
        $y = ($x > 0) ? 1 : (($x === 0) ? 0 : -1);

        $z = abs($x) >= 1 ? abs($x) : 1;

        $this->update([
            'score' => log($z, 10) + ($y * $t / 45000)
        ]);
    }
}

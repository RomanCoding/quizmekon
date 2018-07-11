<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $with = ['category', 'votes', 'user'];

    protected $appends = ['usersVote', 'votesTotal'];

    protected $guarded = [];

    const POST = 0;
    const LINK = 1;
    const YOUTUBE = 2;

    public static function boot() {
        parent::boot();

        static::created(function (Post $post) {
            $post->votes()->create([
                'user_id' => $post->user_id,
                'value' => 1
            ]);
            $post->updateScore();
        });

//        static::updating(function (Post $post) {
//            $post->updateScore();
//        });


    }

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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function votes()
    {
        return $this->morphMany('App\Vote', 'voteable')->orderByDesc('id');
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

    public function scopeInSevenMinutes($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subMinutes(7));
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

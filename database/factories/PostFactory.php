<?php

use App\{User, Post, Category};
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'category_id' => 1,
        'title' => $faker->text(rand(6, 20)),
        'body' => $faker->text(rand(150, 200))
    ];
});

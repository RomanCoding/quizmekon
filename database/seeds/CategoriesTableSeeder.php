<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $channels = [
            ['name' => 'Art', 'slug' => 'art'],
            ['name' => 'Film', 'slug' => 'film'],
            ['name' => 'Funny', 'slug' => 'funny'],
            ['name' => 'Geography', 'slug' => 'geography'],
            ['name' => 'History', 'slug' => 'history'],
            ['name' => 'Music', 'slug' => 'music'],
            ['name' => 'Religion', 'slug' => 'religion'],
            ['name' => 'Science', 'slug' => 'science'],
            ['name' => 'Sports', 'slug' => 'sports'],
            ['name' => 'Television', 'slug' => 'television'],
            ['name' => 'What happens next', 'slug' => 'WhatHappensNext'],
        ];
        foreach ($channels as $channel) {
            DB::table('categories')->insert($channel);
        }
    }
}

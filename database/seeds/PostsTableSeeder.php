<?php

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = \App\Category::all();
        $users = \App\User::all();

        $postsCount = 100;

        for ($i = 0; $i < $postsCount; $i++) {
            $user = $users->random();
            $category = $categories->random();

            factory(\App\Post::class, 1)->create([
                'user_id' => $user->id,
                'category_id' => $category->id
            ]);
        }
    }
}

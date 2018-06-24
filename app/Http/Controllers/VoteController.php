<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Give an up- or down- vote for a post.
     *
     * @param Request $request
     * @param Post $post
     */
    public function update(Request $request, Post $post)
    {
        return $request->user()->voteForPost($post, $request->get('up', -1));
    }
}

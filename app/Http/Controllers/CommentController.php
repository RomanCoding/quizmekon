<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        return $post->comments;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     *
     * @param Post $post
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storePost(Post $post, Request $request)
    {
        return $post->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->get('body'),
        ])->loadMissing('user');
    }

    /**
     *
     * @param Comment $comment
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storeComment(Comment $comment, Request $request)
    {
        return $comment->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->get('body'),
        ])->loadMissing('user');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }
}

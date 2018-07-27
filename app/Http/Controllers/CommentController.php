<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\StoreComment;
use App\Quiz;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Quiz $quiz)
    {
        return $quiz->comments;
    }

    /**
     * Add a comment to the quiz.
     *
     * @param Quiz $quiz
     * @param StoreComment $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storeQuiz(Quiz $quiz, StoreComment $request)
    {
        return $quiz->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ])->loadMissing('user');
    }

    /**
     * Add a comment to another quiz.
     *
     * @param Comment $comment
     * @param StoreComment $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storeComment(Comment $comment, StoreComment $request)
    {
        return $comment->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->get('body'),
        ])->loadMissing('user');
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

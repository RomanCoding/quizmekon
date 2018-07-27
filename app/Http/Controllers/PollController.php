<?php

namespace App\Http\Controllers;

use App\Category;
use App\Quiz;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PollController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Quiz $quiz
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Quiz $quiz)
    {
        $this->validate($request, [
            'index' => 'required|min:0|max:2'
        ]);
        return response()->json([
            'poll' => $request->user()->poll($quiz, $request->index),
            'comments' => $quiz->comments
        ], 201);
    }
}

<?php

namespace App\Http\Controllers;

use App\Category;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = Post::query()->withCount('comments');
        if ($request->get('category')) {
            $posts = Category::where('slug', $request->get('category'))->first()->posts();
        }
        switch ($request->get('s', 'new')) {
            case 'hot': {
                return $posts->orderBy('score', 'desc')->paginate();
            }
            case 'voted': {
                return $posts->withSum('votes', 'value')->orderBy('votes_value_sum', 'desc')->paginate();
            }
            default: {
                return $posts->orderByDesc('id')->paginate();
            }
        }

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => [
                'required',
                Rule::in(['post', 'youtube'])
            ]
        ]);
        $methodName = "store" . ucfirst($request->get('type'));
        return $this->$methodName($request);
    }

    private function storePost(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
            'title' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);
        $post = $request->user()->posts()->create([
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'type' => Post::POST,
            'category_id' => $request->get('category_id'),
        ])->loadMissing('category');
        return $post;
    }

    private function storeYoutube(Request $request)
    {
        $this->validate($request, [
            'link' => [
                'required',
                'regex:/(youtu\.be\/|youtube\.com\/(watch\?(.*&)?v=|(embed|v)\/))([^\?&"\'>]+)/'
            ],
            'title' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);
        $match = [];
        preg_match('/(youtu\.be\/|youtube\.com\/(watch\?(.*&)?v=|(embed|v)\/))([^\?&"\'>]+)/', $request->get('link'), $match);
        if ($match[5]) {
            $link = $match[5];
        } else if (str_contains($request->get('link'), 'youtu.be')) {
            $link = str_after($request->get('link'), 'youtu.be/');
        }
        $post = $request->user()->posts()->create([
            'title' => $request->get('title'),
            'link' => $link,
            'type' => POST::YOUTUBE,
            'category_id' => $request->get('category_id'),
        ])->loadMissing('category');
        return $post;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return $post->loadMissing('comments.user');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }


}

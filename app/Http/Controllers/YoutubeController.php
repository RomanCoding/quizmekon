<?php

namespace App\Http\Controllers;

use App\Exceptions\Youtube\NotFoundException;
use App\Services\Youtube;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    /* @var Client */
    protected $client;

    public function search(Request $request, Youtube $youtube)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $response = $youtube->search($request->get('id'));

        return json_encode($response);
    }

    public function videos(Request $request, Youtube $youtube)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $response = $youtube->videos($request->get('id'));

        return json_encode($response);
    }
}

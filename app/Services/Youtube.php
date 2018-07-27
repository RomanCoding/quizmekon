<?php

namespace App\Services;


use App\Exceptions\Youtube\NotFoundException;
use GuzzleHttp\Client;

class Youtube
{
    /* @var Client */
    protected $http;

    /**
     * @param $videoID
     * @return mixed
     * @throws NotFoundException
     */
    public function search($videoID)
    {
        $response = $this->getHttpClient()->request('GET', 'search', [
            'query' => [
                'key' => env('YOUTUBE_API_KEY', null),
                'maxResults' => 1,
                'type' => 'video',
                'id' => $videoID,
                'part' => 'snippet',
                'q' => $videoID
            ]
        ])->getBody()->getContents();

        $data = json_decode($response);

        if (!$data->items || !count($data->items)) {
            throw new NotFoundException();
        }

        return $data;
    }

    /**
     * @param $videoID
     * @return mixed
     * @throws NotFoundException
     */
    public function videos($videoID)
    {
        $response = $this->getHttpClient()->request('GET', 'videos', [
            'query' => [
                'id' => $videoID,
                'part' => 'contentDetails',
                'key' => env('YOUTUBE_API_KEY', null)
            ]
        ])->getBody()->getContents();

        $data = json_decode($response);

        if (!$data->items || !count($data->items)) {
            throw new NotFoundException();
        }

        return $data;
    }

    private function getHttpClient()
    {
        return new Client([
            'base_uri' => 'https://www.googleapis.com/youtube/v3/',
        ]);
    }
}

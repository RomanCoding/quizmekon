<?php

namespace App\Http\Requests;

use App\Rules\YoutubeVideo;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuiz extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|in:1,2,3',
            'category_id' => 'required|exists:categories,id',
            'start_time' => 'required|integer|min:0',
            'pause_time' => 'required_if:type,1|gt:start_time|lt:end_time|integer|min:1',
            'end_time' => 'required|integer|gt:start_time|min:0',
            'youtube_id' => ['required', new YoutubeVideo],
            'question' => 'required|min:5|max:255',
            'correct_answer' => 'required|min:3|max:255',
            'false_answer' => 'required|min:3|max:255',
            'false_answer_2' => 'required|min:3|max:255',
        ];
    }
}

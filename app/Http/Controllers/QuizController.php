<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\StoreQuiz;
use App\Quiz;
use App\Series;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $quizzes = Quiz::query()->withCount('comments');
        if ($request->get('category')) {
            $quizzes = Category::where('slug', $request->category)->first()->quizzes();
        }
        switch ($request->get('s', 'new')) {
            case 'hot': {
                return $quizzes->orderBy('score', 'desc')->paginate();
            }
            case 'voted': {
                return $quizzes->withSum('votes', 'value')->orderBy('votes_value_sum', 'desc')->paginate();
            }
            default: {
                return $quizzes->latest()->paginate();
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeBulk(Request $request)
    {
        $this->validate($request, [
            'series' => 'required|array'
        ]);
        $rules = (new StoreQuiz())->rules();
        foreach ($request->series as $series) {
            $validator = Validator::make($series, $rules);

            if ($validator->fails()) {
                return response([
                    'errors' => $validator->getMessageBag()
                ], 422);
            }
        }
        $parent = Series::create();
        $firstId = null;
        foreach ($request->series as $key => $series) {
            $video = Video::create($series);
            $series = new Request($series);
            $answers = $this->generateAnswers(
                $series->only(['correct_answer', 'false_answer', 'false_answer_2']),
                $series->get('correct_answer')
            );
            $series->merge([
                'video_id' => $video->id,
                'user_id' => $request->user()->id,
                'answers' => $answers['text'],
                'correct_index' => $answers['index'],
                'series_id' => $parent->id,
                'order' => $key + 1
            ]);
            $quiz = Quiz::create($series->all())->id;
            $firstId = $firstId ?: $quiz;
        }

        return Quiz::find($firstId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreQuiz $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuiz $request)
    {
        $video = Video::create($request->all());
        $answers = $this->generateAnswers(
            $request->only(['correct_answer', 'false_answer', 'false_answer_2']),
            $request->get('correct_answer')
        );
        $request->merge([
            'video_id' => $video->id,
            'user_id' => $request->user()->id,
            'answers' => $answers['text'],
            'correct_index' => $answers['index'],
        ]);
        $quiz = Quiz::create($request->all())->loadMissing('category');

        return $quiz;
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  \App\Quiz $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Quiz $quiz)
    {
        if ($quiz->series_id && !$request->has('single')) {
            $series = $quiz->series()->with('quizzes')->first();
            if (($request->user() && $request->user()->hasPolledForQuiz($series->quizzes->first()))) {
                $series->quizzes->first()->loadMissing('comments.user')->append(['usersPoll', 'correct']);
            }
            return $series;
        }
        return ($request->user() && $request->user()->hasPolledForQuiz($quiz)) ?
            $quiz->loadMissing('comments.user')->append(['usersPoll', 'correct'])
            : $quiz;
    }

    private function generateAnswers($answers, $correctAnswer)
    {
        shuffle($answers);

        return [
            'text' => json_encode($answers),
            'index' => array_search($correctAnswer, $answers)
        ];
    }
}

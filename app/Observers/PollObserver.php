<?php

namespace App\Observers;

use App\Poll;
use App\User;

class PollObserver
{
    public function created(Poll $poll)
    {
        $experience = $poll->user->experience + ($poll->index === $poll->quiz->correct_index) ? 2 : 1;
        User::where('id', $poll->user_id)->update([
            'experience' => $experience
        ]);
    }
}

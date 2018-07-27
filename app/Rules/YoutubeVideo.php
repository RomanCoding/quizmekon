<?php

namespace App\Rules;

use App\Services\Youtube;
use Illuminate\Contracts\Validation\Rule;

class YoutubeVideo implements Rule
{
    /* @var Youtube */
    protected $youtube;

    /**
     * Create a new rule instance.
     *
     */
    public function __construct()
    {
        $this->youtube = app()->resolveProvider(Youtube::class);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $this->youtube->search($value);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid youtube video.';
    }
}

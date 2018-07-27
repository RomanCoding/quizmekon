<?php

namespace App\Exceptions\Youtube;

use Exception;

class NotFoundException extends Exception
{
    /**
     * NotFoundException constructor.
     */
    public function __construct()
    {
        $this->message = 'Video not found!';
        $this->code = 404;
    }
}

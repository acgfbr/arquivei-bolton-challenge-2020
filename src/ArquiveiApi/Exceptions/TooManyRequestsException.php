<?php 

namespace Src\ArquiveiApi\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;

class TooManyRequestsException extends Exception {

    /**
     * @var \Illuminate\Support\MessageBag
     */
    protected $error;

    public function __construct(string $error, $message='', $code=0, $previous=null)
    {
        $this->error = $error;

        parent::__construct($message, $code, $previous);
    }

    public function getError()
    {
        return $this->error;
    }

}